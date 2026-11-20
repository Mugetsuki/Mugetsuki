/**
 * Skyra end-to-end checks against a running site (npm start → Playground).
 *
 *   - every route: HTTP 200, exactly one H1, no horizontal overflow at
 *     360/390/430/768/1024/1440 px, no JavaScript errors
 *   - tools: birth chart (home stepper + hand-off), unknown time, rising sign,
 *     moon sign, compatibility, validation messages, URL free of birth data
 *   - forms: newsletter consent validation, contact form stored
 *   - navigation: mobile menu Escape/focus, persistent theme toggle
 *   - axe-core WCAG 2.2 AA scan in light and dark
 *
 * Usage: node tests/e2e.mjs http://127.0.0.1:9400
 */
import { createRequire } from 'node:module';
import fs from 'node:fs';

const require = createRequire( import.meta.url );
const { chromium } = require( 'playwright' );
const base = ( process.argv[ 2 ] || 'http://127.0.0.1:9400' ).replace( /\/$/, '' );
const axeSource = fs.readFileSync( require.resolve( 'axe-core/axe.min.js' ), 'utf8' );

const ROUTES = [
	'/', '/burclar/', '/burclar/koc/', '/burclar/balik/', '/gunluk-burc-yorumlari/', '/gunluk-burc-yorumlari/terazi/',
	'/dogum-haritasi/', '/yukselen-burc-hesaplama/', '/ay-burcu-hesaplama/', '/burc-uyumu/', '/transitler/',
	'/retro-takvimi/', '/astroloji-takvimi/', '/astroloji-araclari/', '/blog/', '/blog/dogum-haritasi-nasil-okunur/',
	'/kategori/gezegenler/', '/gezegenler/merkur/', '/hakkimizda/', '/iletisim/', '/kvkk/', '/gizlilik-politikasi/',
	'/cerez-politikasi/', '/?s=retro',
];
const WIDTHS = [ 360, 390, 430, 768, 1024, 1440 ];

let failures = 0;
const check = ( ok, label, detail = '' ) => {
	console.log( `${ ok ? 'PASS' : 'FAIL' }  ${ label }${ detail ? '  — ' + detail : '' }` );
	if ( ! ok ) {
		failures++;
	}
};

const browser = await chromium.launch();
const context = async ( opts = {} ) => {
	const ctx = await browser.newContext( { reducedMotion: 'reduce', ...opts } );
	// Playground auto-login would otherwise redirect the first request.
	await ctx.addCookies( [ { name: 'playground_auto_login_already_happened', value: '1', url: base } ] );
	return ctx;
};

try {
	/* Routes × widths */
	const ctx = await context();
	const page = await ctx.newPage();
	const errors = [];
	page.on( 'pageerror', ( e ) => errors.push( e.message ) );
	for ( const route of ROUTES ) {
		const res = await page.goto( base + route, { waitUntil: 'domcontentloaded' } );
		const h1 = await page.locator( 'h1' ).count();
		const bad = [];
		for ( const width of WIDTHS ) {
			await page.setViewportSize( { width, height: 900 } );
			const overflow = await page.evaluate( () => document.documentElement.scrollWidth - document.documentElement.clientWidth );
			if ( overflow > 0 ) {
				bad.push( `${ width }px:+${ overflow }` );
			}
		}
		check( res.status() === 200 && h1 === 1 && ! bad.length, `route ${ route }`, `HTTP ${ res.status() }, h1=${ h1 }${ bad.length ? ', overflow ' + bad.join( ' ' ) : '' }` );
	}
	check( ! errors.length, 'no JavaScript errors on routes', errors.slice( 0, 3 ).join( ' | ' ) );
	await ctx.close();

	/* Tools and forms (mobile) */
	const m = await context( { viewport: { width: 390, height: 844 } } );
	const p = await m.newPage();
	await p.goto( base + '/', { waitUntil: 'networkidle' } );
	const home = p.locator( '.sk-chartcta form' );
	await home.locator( '[data-step="1"] [data-next]' ).click();
	check( ( await home.locator( '[data-field=date] .sk-error' ).innerText() ).includes( 'gün, ay ve yıl' ), 'stepper validates the date' );
	await home.locator( 'input[name=date]' ).fill( '1990-05-15' );
	await home.locator( '[data-step="1"] [data-next]' ).click();
	await home.locator( 'input[name=time]' ).fill( '14:30' );
	await home.locator( '[data-step="2"] [data-next]' ).click();
	await home.locator( 'input[name=place_q]' ).fill( 'istanb' );
	await p.waitForSelector( '.sk-chartcta [role=option]:not(.is-empty)' );
	await p.keyboard.press( 'ArrowDown' );
	await p.keyboard.press( 'Enter' );
	check( ( await home.locator( 'input[name=place]' ).inputValue() ) === '745044', 'place combobox selects with the keyboard' );
	await home.locator( '[data-submit]' ).click();
	await p.waitForSelector( '.sk-chartcta .sk-result' );
	const mini = await p.locator( '.sk-chartcta .sk-mini3' ).innerText();
	check( /Boğa[\s\S]*Oğlak[\s\S]*Başak/.test( mini ), 'home chart summary (Güneş Boğa, Ay Oğlak, Yükselen Başak)', mini.replace( /\s+/g, ' ' ) );
	await Promise.all( [ p.waitForURL( '**/dogum-haritasi/**' ), p.locator( '[data-handoff]' ).click() ] );
	await p.waitForSelector( '#sonuc .sk-result', { timeout: 60000 } );
	check( ! /1990|14%3A30|14:30/.test( p.url() ), 'full chart opened without birth data in the URL', p.url() );
	check( await p.locator( '#sonuc .sk-table tbody tr' ).count() >= 12, 'positions table rendered' );

	await p.goto( base + '/ay-burcu-hesaplama/', { waitUntil: 'networkidle' } );
	const moon = p.locator( '.sk-tool form' );
	await moon.locator( 'input[name=date]' ).fill( '1990-05-15' );
	await moon.locator( '[data-time-unknown]' ).check();
	await moon.locator( 'input[name=place_q]' ).fill( 'Londra' );
	await p.waitForSelector( '.sk-tool [role=option]:not(.is-empty)' );
	await moon.locator( '[role=option]' ).first().dispatchEvent( 'mousedown' );
	await moon.locator( '[data-submit]' ).click();
	await p.waitForSelector( '#sonuc .sk-result' );
	check( ( await p.locator( '#sonuc' ).innerText() ).includes( 'burç değiştirdi' ), 'moon sign: both options shown when the Moon changed sign' );

	await p.goto( base + '/yukselen-burc-hesaplama/', { waitUntil: 'networkidle' } );
	const rising = p.locator( '.sk-tool form' );
	await rising.locator( 'input[name=date]' ).fill( '1990-05-15' );
	await rising.locator( '[data-submit]' ).click();
	check( await rising.locator( '[data-field=time] .sk-error' ).isVisible(), 'rising sign requires a birth time' );

	await p.goto( base + '/burc-uyumu/', { waitUntil: 'networkidle' } );
	await p.locator( '.sk-tool select[name=b]' ).selectOption( 'aslan' );
	await p.waitForSelector( '#sonuc .sk-result' );
	check( ( await p.locator( '#sonuc .sk-result__title' ).innerText() ) === 'Doğal uyum', 'compatibility Koç + Aslan' );

	await p.goto( base + '/', { waitUntil: 'networkidle' } );
	const nl = p.locator( '.sk-newsletter:not(.is-compact) form' );
	await nl.locator( 'input[name=email]' ).fill( 'test@example.com' );
	await p.waitForTimeout( 2100 );
	await nl.locator( 'button[type=submit]' ).click();
	await p.waitForFunction( () => document.querySelector( '.sk-newsletter:not(.is-compact) [data-status]' ).textContent.length > 0 );
	check( ( await nl.locator( '[data-status]' ).innerText() ).includes( 'aydınlatma' ), 'newsletter requires consent' );

	await p.goto( base + '/iletisim/', { waitUntil: 'networkidle' } );
	const c = p.locator( 'form.sk-contact' );
	await c.locator( 'input[name=name]' ).fill( 'Test' );
	await c.locator( 'input[name=email]' ).fill( 'test@example.com' );
	await c.locator( 'textarea' ).fill( 'Bu bir otomatik test mesajıdır.' );
	await c.locator( 'input[name=consent]' ).check();
	await p.waitForTimeout( 2100 );
	await c.locator( 'button[type=submit]' ).click();
	await p.waitForFunction( () => document.querySelector( 'form.sk-contact [data-status]' ).textContent.length > 0 );
	check( ( await c.locator( '[data-status]' ).innerText() ).includes( 'ulaştı' ), 'contact form stored' );

	await p.goto( base + '/', { waitUntil: 'networkidle' } );
	await p.locator( '[data-menu-toggle]' ).click();
	const open = await p.locator( '[data-menu-toggle]' ).getAttribute( 'aria-expanded' );
	await p.keyboard.press( 'Escape' );
	const focusBack = await p.evaluate( () => document.activeElement.hasAttribute( 'data-menu-toggle' ) );
	check( open === 'true' && focusBack, 'mobile menu opens, Escape closes and returns focus' );
	await p.locator( '[data-theme-toggle]' ).click();
	await p.reload( { waitUntil: 'domcontentloaded' } );
	check( ( await p.evaluate( () => document.documentElement.dataset.theme ) ) === 'dark', 'theme choice persists' );
	await m.close();

	/* Accessibility */
	for ( const scheme of [ 'light', 'dark' ] ) {
		const a = await context( { viewport: { width: 1280, height: 900 }, colorScheme: scheme } );
		const ap = await a.newPage();
		for ( const route of [ '/', '/burclar/koc/', '/dogum-haritasi/', '/astroloji-takvimi/', '/blog/dogum-haritasi-nasil-okunur/', '/iletisim/' ] ) {
			await ap.goto( base + route, { waitUntil: 'networkidle' } );
			await ap.addScriptTag( { content: axeSource } );
			const v = await ap.evaluate( async () => ( await window.axe.run( document, { runOnly: { type: 'tag', values: [ 'wcag2a', 'wcag2aa', 'wcag21a', 'wcag21aa', 'wcag22aa' ] } } ) ).violations.map( ( x ) => `${ x.id } ×${ x.nodes.length }` ) );
			check( ! v.length, `axe WCAG 2.2 AA ${ scheme } ${ route }`, v.join( ', ' ) );
		}
		await a.close();
	}
} finally {
	await browser.close();
}
console.log( failures ? `\n${ failures } check(s) failed.` : '\nAll end-to-end checks passed.' );
process.exit( failures ? 1 : 0 );
