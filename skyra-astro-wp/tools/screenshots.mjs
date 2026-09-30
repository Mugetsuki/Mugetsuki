/**
 * Documentation screenshots (JPEG) of a running site.
 * Usage: node tools/screenshots.mjs http://127.0.0.1:9400 docs/screenshots
 */
import { createRequire } from 'node:module';
const require = createRequire( import.meta.url );
const { chromium } = require( 'playwright' );
const [ , , base = 'http://127.0.0.1:9400', out = 'docs/screenshots' ] = process.argv;

const shots = [
	{ name: 'home-desktop', path: '/', width: 1440, height: 900, full: true },
	{ name: 'home-mobile', path: '/', width: 390, height: 844, full: true },
	{ name: 'home-desktop-dark', path: '/', width: 1440, height: 900, dark: true },
	{ name: 'home-mobile-dark', path: '/', width: 390, height: 844, dark: true },
	{ name: 'sign-koc', path: '/burclar/koc/', width: 1440, height: 1100 },
	{ name: 'daily-terazi-mobile', path: '/gunluk-burc-yorumlari/terazi/', width: 390, height: 844, full: true },
	{ name: 'astro-calendar', path: '/astroloji-takvimi/', width: 1440, height: 1100 },
	{ name: 'retro-calendar', path: '/retro-takvimi/', width: 1440, height: 1500 },
	{ name: 'transits', path: '/transitler/', width: 1440, height: 1300 },
	{ name: 'blog', path: '/blog/', width: 1440, height: 1300 },
];

const browser = await chromium.launch();
for ( const s of shots ) {
	const ctx = await browser.newContext( { viewport: { width: s.width, height: s.height }, colorScheme: s.dark ? 'dark' : 'light', reducedMotion: 'reduce' } );
	await ctx.addCookies( [ { name: 'playground_auto_login_already_happened', value: '1', url: base } ] );
	const page = await ctx.newPage();
	await page.goto( base + s.path, { waitUntil: 'networkidle' } );
	await page.evaluate( () => document.fonts.ready );
	await page.screenshot( { path: `${ out }/${ s.name }.jpg`, type: 'jpeg', quality: 78, fullPage: !! s.full } );
	await ctx.close();
	console.log( s.name );
}

// Birth chart result (mobile), computed through the real form.
const ctx = await browser.newContext( { viewport: { width: 390, height: 844 }, reducedMotion: 'reduce' } );
await ctx.addCookies( [ { name: 'playground_auto_login_already_happened', value: '1', url: base } ] );
const page = await ctx.newPage();
await page.goto( base + '/dogum-haritasi/', { waitUntil: 'networkidle' } );
const form = page.locator( '.sk-tool form' );
await form.locator( 'input[name=date]' ).fill( '1990-05-15' );
await form.locator( 'input[name=time]' ).fill( '14:30' );
await form.locator( 'input[name=place_q]' ).fill( 'istanbul' );
await page.waitForSelector( '[role=option]:not(.is-empty)' );
await form.locator( '[role=option]' ).first().dispatchEvent( 'mousedown' );
await form.locator( '[data-submit]' ).click();
await page.waitForSelector( '#sonuc .sk-result' );
await page.locator( '#sonuc .sk-result' ).screenshot( { path: `${ out }/chart-result-mobile.jpg`, type: 'jpeg', quality: 78 } );
console.log( 'chart-result-mobile' );
await browser.close();
