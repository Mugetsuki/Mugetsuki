const {chromium}=require(process.env.PLAYWRIGHT_PATH || 'playwright');
const path=require('node:path');const fs=require('node:fs');const {pathToFileURL}=require('node:url');
(async()=>{
 const root=path.resolve(__dirname,'..');
 const browser=await chromium.launch({headless:true});
 const page=await browser.newPage({viewport:{width:1000,height:1300},deviceScaleFactor:1});
 await page.goto(pathToFileURL(path.join(root,'brand-kit/brand-book.html')).href,{waitUntil:'networkidle'});
 await page.evaluate(()=>document.fonts.ready);
 // Print layout and page content must stay above the fixed footer.
 await page.emulateMedia({media:'print'});
 const fit=await page.locator('.page').evaluateAll(pages=>pages.map((p,i)=>{
  const content=p.querySelector('.content'),footer=p.querySelector('.page-footer');
  return {page:i+1,title:p.querySelector('h1').textContent,contentBottom:Math.round(content.getBoundingClientRect().bottom-p.getBoundingClientRect().top),footerTop:Math.round(footer.getBoundingClientRect().top-p.getBoundingClientRect().top),overflow:content.getBoundingClientRect().bottom>footer.getBoundingClientRect().top-8};
 }));
 const bad=fit.filter(p=>p.overflow);
 fs.writeFileSync(path.join(root,'brand-kit/pdf-layout-check.json'),JSON.stringify(fit,null,2)+'\n');
 console.log('LAYOUT',JSON.stringify(bad));
 if(bad.length){await browser.close();process.exitCode=1;return;}
 await page.pdf({path:path.join(root,'Skyra-Astro-Brand-Kit.pdf'),preferCSSPageSize:true,printBackground:true,tagged:true,outline:true});
 for(const n of [1,7,9,17,25])await page.locator('.page').nth(n-1).screenshot({path:path.join('/tmp',`skyra-pdf-page-${n}.png`)});
 const text=await page.locator('body').innerText();fs.writeFileSync(path.join(root,'brand-kit/pdf-text-content.txt'),text);
 const bytes=fs.readFileSync(path.join(root,'Skyra-Astro-Brand-Kit.pdf'));
 const count=(bytes.toString('latin1').match(/\/Type \/Page\b/g)||[]).length;
 if(count!==fit.length)throw new Error(`PDF page count mismatch: ${count} vs ${fit.length}`);
 console.log(`PDF: ${count} pages; ${bytes.length} bytes; embedded selectable text; no page content overlaps.`);
 await browser.close();
})().catch(e=>{console.error(e);process.exit(1);});
