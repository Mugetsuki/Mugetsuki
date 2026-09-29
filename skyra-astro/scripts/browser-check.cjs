// Run with PLAYWRIGHT_PATH pointing to the installed playwright package when needed.
const { chromium } = require(process.env.PLAYWRIGHT_PATH || 'playwright');
const path = require('node:path');
const fs = require('node:fs');
const assert = require('node:assert/strict');
const { pathToFileURL } = require('node:url');
(async () => {
 const root = path.resolve(__dirname, '..');
 const url = pathToFileURL(path.join(root, 'index.html')).href;
 const browser = await chromium.launch({ headless: true });
 const errors = [];
 const p = await browser.newPage({ viewport:{width:1440,height:1000}, reducedMotion:'reduce', colorScheme:'light' });
 p.on('pageerror', e => errors.push(e.message));
 const routes=['home','kit','bugun','burclar','haftalik','burc','profil','araclar','harita','yukselen','ay','uyum','transitler','retro','takvim','olay','dergi','yazi','rehberler','astroloji','taslar','tarot','rehber','tas','tarot-detay','hakkinda','yontem','danismanlik','iletisim','kvkk','gizlilik','cerez','erisilebilirlik','arama','404'];
 for (const width of [320,390,768,1024,1440]) {
  await p.setViewportSize({width,height:900});
  for (const route of routes) {
   await p.goto(`${url}?page=${route}`,{waitUntil:'domcontentloaded'});
   assert.equal(await p.locator('h1').count(),1,`One H1: ${route}`);
   assert.equal(await p.locator('#main').evaluate(el=>el.scrollWidth>document.documentElement.clientWidth),false,`Overflow ${route}/${width}`);
   const ids=await p.locator('[id]').evaluateAll(els=>els.map(el=>el.id));
   assert.equal(new Set(ids).size,ids.length,`Unique IDs: ${route}`);
  }
 }
 await p.setViewportSize({width:390,height:844});await p.goto(url);
 await p.locator('#menu-toggle').click();assert.equal(await p.locator('#menu-toggle').getAttribute('aria-expanded'),'true');
 await p.keyboard.press('Escape');assert.equal(await p.locator('#menu-toggle').getAttribute('aria-expanded'),'false');
 assert.equal(await p.locator('#menu-toggle').evaluate(el=>el===document.activeElement),true);
 await p.locator('#theme-toggle').click();assert.equal(await p.locator('html').getAttribute('data-theme'),'dark');
 await p.reload();assert.equal(await p.locator('html').getAttribute('data-theme'),'dark');
 await p.locator('#theme-toggle').click();
 await p.goto(`${url}?page=harita`);await p.locator('.chart-form button[type=submit]').click();assert.equal(await p.locator('#birth-date').getAttribute('aria-invalid'),'true');
 await p.locator('#birth-date').fill('1993-04-17');await p.locator('[name=unknownTime]').check();assert.equal(await p.locator('#birth-time').isDisabled(),true);
 await p.locator('#birth-place').fill('İstanbul, Türkiye');await p.locator('.chart-form button[type=submit]').click();await p.waitForURL('**?page=sonuc');
 assert.equal(p.url().includes('1993'),false);assert.equal(await p.locator('tbody tr').count(),4);
 await p.goto(url);await p.locator('#newsletter button').click();assert.equal(await p.locator('#newsletter-email').getAttribute('aria-invalid'),'true');
 await p.locator('#newsletter-email').fill('ornek@example.com');await p.locator('#newsletter button').click();assert.match(await p.locator('#newsletter-status').textContent(),/oluşturulmadı/);
 await p.goto(`${url}?page=retro`);await p.locator('#event-filter').selectOption('venus');assert.match(await p.locator('#event-results').textContent(),/bulunamadı/);
 await p.goto(`${url}?page=uyum`);await p.locator('#sign-1').selectOption('6');await p.locator('#compatibility button').click();assert.match(await p.locator('#compatibility .status').textContent(),/Koç × Terazi/);
 await p.goto(`${url}?page=arama`);await p.locator('#search-query').fill('<script>');await p.locator('#search button').click();assert.equal(await p.locator('#search-results script').count(),0);
 await p.goto(url);await p.setViewportSize({width:1440,height:1000});await p.evaluate(()=>document.fonts.ready);await p.screenshot({path:path.join(root,'docs/home-desktop.png'),fullPage:true});
 await p.setViewportSize({width:390,height:844});await p.screenshot({path:path.join(root,'docs/home-mobile.png'),fullPage:true});
 await p.goto(`${url}?page=kit`);await p.locator('#theme-toggle').click();await p.setViewportSize({width:1440,height:1000});await p.screenshot({path:path.join(root,'docs/kit-dark.png'),fullPage:true});
 assert.deepEqual(errors,[],'No JavaScript runtime errors');
 const summary = {routeCount:routes.length,widths:[320,390,768,1024,1440],checks:['one H1','no horizontal main overflow','unique IDs','menu Escape and focus','theme persistence','form invalid and unknown time','result without private data in URL','newsletter demo states','filter empty state','compatibility demo','search escaping'],runtimeErrors:errors};
 fs.writeFileSync(path.join(root,'docs/browser-results.json'),JSON.stringify(summary,null,2)+'\n');
 console.log('PASS',JSON.stringify(summary));await browser.close();
})().catch(e=>{console.error(e);process.exit(1);});
