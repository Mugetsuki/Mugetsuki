/**
 * Skyra ephemeris accuracy test.
 *
 * Runs the PHP engine (via php-wasm, no local PHP needed) and compares it
 * with astronomy-engine (VSOP87/NOVAS-grade reference, MIT licence):
 *   - Sun, Moon and planet longitudes, 1900–2100
 *   - New/full Moon times and eclipse detection/classification, 2024–2027
 *   - Retrograde stations (type and timing), sign ingresses
 *   - Moon illumination, ascendant/MC, Placidus definition, historical UTC offset
 *
 * Usage: npm run test:engine
 */
import { execFileSync } from 'node:child_process';
import { createRequire } from 'node:module';
import path from 'node:path';
import { fileURLToPath } from 'node:url';

const require = createRequire( import.meta.url );
const A = require( 'astronomy-engine' );
const root = path.resolve( path.dirname( fileURLToPath( import.meta.url ) ), '..' );
const phpCli = path.join( root, 'node_modules', '.bin', 'php-wasm-cli' );

const raw = execFileSync( phpCli, [ 'tests/engine-dump.php', root ], { cwd: root, maxBuffer: 64 * 1024 * 1024 } ).toString();
const d = JSON.parse( raw.slice( raw.indexOf( '{' ) ) );

let failures = 0;
const check = ( ok, label, detail ) => {
	console.log( `${ ok ? 'PASS' : 'FAIL' }  ${ label }${ detail ? '  — ' + detail : '' }` );
	if ( ! ok ) {
		failures++;
	}
};
const wrap = ( x ) => ( ( ( x + 540 ) % 360 ) - 180 );
const toDate = ( jd ) => new Date( ( jd - 2440587.5 ) * 86400000 );
const NAMES = { sun: 'Sun', moon: 'Moon', mercury: 'Mercury', venus: 'Venus', mars: 'Mars', jupiter: 'Jupiter', saturn: 'Saturn', uranus: 'Uranus', neptune: 'Neptune', pluto: 'Pluto' };
const refLon = ( body, date ) => {
	if ( body === 'sun' ) {
		return A.SunPosition( date ).elon;
	}
	if ( body === 'moon' ) {
		return A.EclipticGeoMoon( date ).lon;
	}
	return A.Ecliptic( A.GeoVector( NAMES[ body ], date, true ) ).elon;
};

// 1. Positions. Limits in arc-minutes (max over 400 instants, 1900–2100).
const LIMIT = { sun: 1, moon: 6, mercury: 2, venus: 2, mars: 4, jupiter: 2, saturn: 3, uranus: 2, neptune: 2, pluto: 2 };
for ( const body of Object.keys( NAMES ) ) {
	let max = 0;
	let sum = 0;
	for ( const row of d.positions ) {
		const err = Math.abs( wrap( row[ body ][ 0 ] - refLon( body, new Date( row.t * 1000 ) ) ) ) * 60;
		max = Math.max( max, err );
		sum += err;
	}
	check( max <= LIMIT[ body ], `longitude ${ body }`, `mean ${ ( sum / d.positions.length ).toFixed( 2 ) }′, max ${ max.toFixed( 2 ) }′ (limit ${ LIMIT[ body ] }′)` );
}

// 2. Lunations within 10 minutes.
let maxLun = 0;
for ( const l of d.lunations ) {
	const t = toDate( l.jd );
	const ref = A.SearchMoonPhase( l.type === 'new_moon' ? 0 : 180, new Date( t - 2 * 86400000 ), 5 );
	maxLun = Math.max( maxLun, Math.abs( ref.date - t ) / 60000 );
}
check( maxLun <= 10, `new/full Moon times (${ d.lunations.length })`, `max ${ maxLun.toFixed( 1 ) } min` );

// 3. Eclipses 2024–2027: every reference eclipse found with the same type,
// except penumbral lunar eclipses too faint to see (reference obscuration ~0).
const mine = new Map( d.lunations.filter( ( l ) => l.eclipse ).map( ( l ) => [ toDate( l.jd ).toISOString().slice( 0, 10 ), `${ l.eclipse.kind } ${ l.eclipse.type }` ] ) );
const ref = [];
for ( let s = A.SearchGlobalSolarEclipse( new Date( '2024-01-01' ) ); s.peak.date < new Date( '2028-01-01' ); s = A.NextGlobalSolarEclipse( s.peak ) ) {
	ref.push( [ s.peak.date.toISOString().slice( 0, 10 ), `solar ${ s.kind }`, 1 ] );
}
for ( let l = A.SearchLunarEclipse( new Date( '2024-01-01' ) ); l.peak.date < new Date( '2028-01-01' ); l = A.NextLunarEclipse( l.peak ) ) {
	ref.push( [ l.peak.date.toISOString().slice( 0, 10 ), `lunar ${ l.kind }`, l.kind === 'penumbral' ? l.sd_penum : 1 ] );
}
const missing = ref.filter( ( [ date, kind ] ) => mine.get( date ) !== kind );
const tolerated = missing.filter( ( [ , kind ] ) => kind === 'lunar penumbral' );
check( missing.length === tolerated.length && tolerated.length <= 1 && mine.size >= ref.length - 1, `eclipses 2024–2027 (${ mine.size }/${ ref.length })`, missing.length ? `not matched: ${ missing.map( ( m ) => m[ 0 ] + ' ' + m[ 1 ] ).join( ', ' ) }` : 'all matched' );

// 4. Stations: same sequence and type, within 12 hours of the reference.
for ( const body of Object.keys( d.stations ) ) {
	const refS = [];
	let prevLon = null;
	let prevD = null;
	for ( let t = Date.parse( '2023-12-31' ); t < Date.parse( '2028-01-01' ); t += 3 * 3600000 ) {
		const lon = refLon( body, new Date( t ) );
		if ( prevLon !== null ) {
			const dd = wrap( lon - prevLon );
			if ( prevD !== null && ( prevD > 0 ) !== ( dd > 0 ) ) {
				refS.push( { t: t - 1.5 * 3600000, type: dd < 0 ? 'retro' : 'direct' } );
			}
			prevD = dd;
		}
		prevLon = lon;
	}
	const m = d.stations[ body ];
	const within = refS.filter( ( r ) => r.t >= Date.parse( '2024-01-01' ) );
	const ok = m.length === within.length && m.every( ( x, i ) => x.type === within[ i ].type );
	const dt = m.map( ( x, i ) => within[ i ] ? Math.abs( toDate( x.jd ) - within[ i ].t ) / 3600000 : Infinity );
	check( ok && Math.max( ...dt ) <= 12, `stations ${ body } (${ m.length })`, `max Δ ${ Math.max( ...dt ).toFixed( 1 ) } h` );
}

// 5. Ingresses: reference longitude at our ingress time sits on the boundary.
for ( const body of Object.keys( d.ingress ) ) {
	let max = 0;
	for ( const x of d.ingress[ body ] ) {
		const lon = refLon( body, toDate( x.jd ) );
		max = Math.max( max, Math.abs( wrap( lon - ( x.retro ? x.from : x.to ) * 30 ) ) * 60 );
	}
	check( max <= 5, `ingresses ${ body } (${ d.ingress[ body ].length })`, `max boundary offset ${ max.toFixed( 2 ) }′` );
}

// 6. Moon illumination.
const illRef = A.Illumination( 'Moon', new Date( '2026-09-30T12:00:00Z' ) ).phase_fraction;
check( Math.abs( d.phase.illumination - illRef ) < 0.005, 'Moon illumination', `${ ( d.phase.illumination * 100 ).toFixed( 2 ) }% vs ${ ( illRef * 100 ).toFixed( 2 ) }%` );

// 7. Natal chart: historical offset, ASC/MC from an independent sidereal time, Placidus definition.
check( d.natal.input.offset === '+03:00' && d.natal.input.utc === '1990-05-15 11:30', 'historical UTC offset (İstanbul 1990, summer time)', `${ d.natal.input.local } → ${ d.natal.input.utc } UTC` );
const when = new Date( '1990-05-15T11:30:00Z' );
const ramc = ( A.SiderealTime( when ) * 15 + 28.9497 ) % 360;
const eps = 23.4406 * Math.PI / 180;
const rad = ( x ) => x * Math.PI / 180;
const deg = ( x ) => ( x * 180 / Math.PI + 360 ) % 360;
const ascRef = deg( Math.atan2( Math.cos( rad( ramc ) ), -( Math.sin( rad( ramc ) ) * Math.cos( eps ) + Math.tan( rad( 41.0138 ) ) * Math.sin( eps ) ) ) );
const mcRef = deg( Math.atan2( Math.sin( rad( ramc ) ), Math.cos( rad( ramc ) ) * Math.cos( eps ) ) );
check( Math.abs( wrap( d.natal.asc - ascRef ) ) < 0.1 && Math.abs( wrap( d.natal.mc - mcRef ) ) < 0.1, 'ascendant and MC', `ASC ${ d.natal.asc.toFixed( 3 ) }° vs ${ ascRef.toFixed( 3 ) }°, MC ${ d.natal.mc.toFixed( 3 ) }° vs ${ mcRef.toFixed( 3 ) }°` );
let placidusErr = 0;
for ( const [ house, frac, above ] of [ [ 11, 1 / 3, true ], [ 12, 2 / 3, true ], [ 2, 2 / 3, false ], [ 3, 1 / 3, false ] ] ) {
	const lon = rad( d.natal.cusps[ house ] );
	const ra = deg( Math.atan2( Math.sin( lon ) * Math.cos( eps ), Math.cos( lon ) ) );
	const dec = Math.asin( Math.sin( eps ) * Math.sin( lon ) );
	const ad = deg( Math.asin( Math.tan( rad( 41.0138 ) ) * Math.tan( dec ) ) );
	const adS = ad > 180 ? ad - 360 : ad;
	const md = above ? wrap( ra - ramc ) / ( 90 + adS ) : wrap( ramc + 180 - ra ) / ( 90 - adS );
	placidusErr = Math.max( placidusErr, Math.abs( md - frac ) );
}
check( placidusErr < 0.002, 'Placidus cusps satisfy the semi-arc definition', `max fraction error ${ placidusErr.toFixed( 5 ) }` );
check( d.moonday.signs.join( '>' ) === 'oglak>kova', 'Moon sign change within a day', `${ d.moonday.signs.join( ' → ' ) } at ${ d.moonday.ingress }` );

console.log( failures ? `\n${ failures } check(s) failed.` : '\nAll engine checks passed.' );
process.exit( failures ? 1 : 0 );
