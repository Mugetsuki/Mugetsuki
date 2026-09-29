"""Build a self-contained, paginated brand book source for Chromium PDF export."""
from pathlib import Path
import re, html, json, base64
root=Path(__file__).resolve().parents[1]
kit=(root/'docs/02-brand-kit.md').read_text()
audit=(root/'docs/01-mevcut-site-analizi.md').read_text()
ux=(root/'docs/03-web-ui-ux.md').read_text()
handoff=(root/'docs/04-wordpress-handoff.md').read_text()
tokens=json.loads((root/'tokens.json').read_text())
logo='data:image/png;base64,'+base64.b64encode((root/'assets/skyra-logo-original.png').read_bytes()).decode()

def inline(s):
 s=html.escape(s)
 s=re.sub(r'\[([^\]]+)\]\((https?://[^)]+)\)',r'<a href="\2">\1</a>',s)
 s=re.sub(r'`([^`]+)`',r'<code>\1</code>',s)
 s=re.sub(r'\*\*([^*]+)\*\*',r'<strong>\1</strong>',s)
 return s

def md(s):
 lines=s.strip().splitlines();out=[];i=0
 while i<len(lines):
  line=lines[i].strip()
  if not line:i+=1;continue
  if line.startswith('|'):
   rows=[]
   while i<len(lines) and lines[i].strip().startswith('|'):
    cells=[c.strip() for c in lines[i].strip().strip('|').split('|')]
    if not all(re.fullmatch(r'[:\- ]+',c) for c in cells):rows.append(cells)
    i+=1
   out.append('<table><thead><tr>'+''.join('<th>'+inline(c)+'</th>' for c in rows[0])+'</tr></thead><tbody>'+''.join('<tr>'+''.join('<td>'+inline(c)+'</td>' for c in row)+'</tr>' for row in rows[1:])+'</tbody></table>');continue
  if line.startswith('#'):
   level=len(line)-len(line.lstrip('#'));out.append(f'<h{min(level+1,4)}>{inline(line.lstrip("# "))}</h{min(level+1,4)}>');i+=1;continue
  if re.match(r'^\d+\. ',line):
   out.append('<ol>')
   while i<len(lines) and re.match(r'^\d+\. ',lines[i].strip()):out.append('<li>'+inline(re.sub(r'^\d+\. ','',lines[i].strip()))+'</li>');i+=1
   out.append('</ol>');continue
  if line.startswith('- '):
   out.append('<ul>')
   while i<len(lines) and lines[i].strip().startswith('- '):out.append('<li>'+inline(lines[i].strip()[2:])+'</li>');i+=1
   out.append('</ul>');continue
  para=[line];i+=1
  while i<len(lines) and lines[i].strip() and not lines[i].strip().startswith(('|','#','- ')):para.append(lines[i].strip());i+=1
  out.append('<p>'+inline(' '.join(para))+'</p>')
 return ''.join(out)

def section(doc,title):
 m=re.search(r'^## '+re.escape(title)+r'\n(.*?)(?=^## |\Z)',doc,re.M|re.S)
 assert m,title
 return m.group(1).strip()

def ks(prefix):
 for match in re.finditer(r'^## ([^\n]+)\n(.*?)(?=^## |\Z)',kit,re.M|re.S):
  if match.group(1).startswith(prefix):return md(match.group(2))
 raise ValueError(prefix)

pages=[]
def add(title,category,body,theme='',subtitle=''):
 pages.append({'title':title,'category':category,'body':body,'theme':theme,'subtitle':subtitle})

def callout(text):return '<div class="callout">'+text+'</div>'
def swatches(mode):
 colors=tokens['color'][mode]
 labels={'primary':'Primary','secondary':'Secondary','accent':'Accent','background':'Background','surface':'Surface','raised':'Surface raised','text':'Text primary','muted':'Text secondary','border':'Border','control':'Control border','hover':'Hover','onprimary':'On primary','night':'Night','focus':'Focus','error':'Error','success':'Success','warning':'Warning'}
 return '<div class="swatches">'+''.join(f'<div class="swatch"><div style="background:{value}"></div><p>{labels[key]}<code>{value}</code></p></div>' for key,value in colors.items())+'</div>'

add('Gökyüzünü anla.<br>Kendine alan aç.','SKYRA ASTRO',f'''<div class="cover-logo"><img src="{logo}" alt="Korunan Skyra Astro logosu"><span>skyra <small>ASTRO</small></span></div><div class="cover-orbit"><i></i><i></i><i></i><b>☾</b></div><div class="cover-bottom"><p>BRAND KIT<br>& WEB DESIGN SYSTEM</p><div>Bağımsız marka rehberi<br>Claude için tasarım ve uygulama devri<br>v1.1 · 30 Eylül 2026</div></div>''','cover', 'Soft celestial × premium editorial × modern digital product')
add('Bu belgenin kullanımı','OKUMA REHBERİ',callout('<strong>Birinci aşama teslimatı.</strong> Bu PDF markanın ayrı referans belgesidir. Marka rehberi son kullanıcı web sitesinin içine yerleştirilmez. Claude, ikinci aşamadaki sayfa tasarımlarını bu sistemi inceleyerek üretir.')+'''<div class="contents"><div><h3>01 — Mevcut durum</h3><p>Kaynak incelemesi, görsel kimlik, içerik, kullanıcı yolları ve öncelikler.</p><h3>02 — Marka temeli</h3><p>Kişilik, konumlandırma, değerler, anahtar kelimeler ve ses tonu.</p><h3>03 — Görsel kimlik</h3><p>Açık/koyu renkler, tipografi, korunan logo, ikonlar ve görsel dil.</p></div><div><h3>04 — Tasarım sistemi</h3><p>UI ilkeleri, radius, shadow, spacing, gradient, buton, kart, form ve navigasyon.</p><h3>05 — Uygulama devri</h3><p>Ana sayfa yaklaşımı, WordPress eşlemesi, Claude talimatı, tokenlar ve kabul ölçütleri.</p></div></div>'''+md('''## Kararların statüsü
Bu belgedeki tasarım sistemi önerilen v1.1 marka standardıdır; kullanıcı tarafından nihai onay verildiği iddia edilmez. Logo yapısı korunmalıdır. Kedi amblemi canlı header dosyasında doğrulandı; farklı bir onaylı logo master’ı varsa uygulamadan önce onunla karşılaştırılmalıdır.

## Tek başına kullanılabilir
Tüm HEX değerleri, ölçüler, font rolleri ve uygulama kuralları PDF içinde bulunur. Ek kod dosyaları zorunlu değildir. Önceki site prototipi yalnız keşif çalışmasıdır; bu PDF’nin yerine geçmez.
'''))
visual=section(audit,'Mevcut marka ve görsel sistem')
lines=visual.splitlines();rows=[x for x in lines if x.startswith('|')]
add('Mevcut görsel kimlik','MEVCUT SİTE ANALİZİ',md('\n'.join(rows[:6]))+callout('<strong>İnceleme kaynağı:</strong> skyraastro.com ana sayfa HTML/CSS ve header logo dosyası. İnceleme tarihi 30 Eylül 2026. Canlı sitenin Lighthouse, conversion veya kullanıcı araştırması ölçümü yapılmadı. Kaynaktaki değerler ölçülmüş performans olarak yorumlanmamalı.'))
add('Görsel dil & içerik yapısı','MEVCUT SİTE ANALİZİ',md('\n'.join(rows[:2]+rows[6:]))+md('## İçerik hiyerarşisi\n'+section(audit,'İçerik ve sayfa hiyerarşisi')))
add('Kullanıcı yolları & öncelikler','MEVCUT SİTE ANALİZİ',md(section(audit,'Kullanıcı yolculukları'))+md('## Öncelikler\n'+section(audit,'Öncelikler'))+md('## Kanıtın sınırları\n'+section(audit,'Kanıt ve sınırlar')))
add('Sakin, özenli, çağdaş.','01–04 · MARKA TEMELİ',''.join('<h3>'+title+'</h3>'+ks(prefix) for prefix,title in [('1.','1. Marka kişiliği'),('2.','2. Marka konumlandırması'),('3.','3. Marka değerleri'),('4.','4. Brand keywords')])+callout('<strong>Marka vaadi:</strong> Günlük gökyüzü ritmini ve kişisel astrolojik sembolleri sade, özenli bir dijital deneyimde keşfetmek.'))
add('Sesi yakın. Sözü açık.','05 · TONE OF VOICE',ks('5.'))
add('Açık renk dünyası','06 · ANA RENK PALETİ',swatches('light')+md('''## Uygulama dengesi
Yüzeylerin yaklaşık %75’i krem/nötr, %20’si lavanta/gece moru, %5’i pembe vurgu. Bu oran bir sanat yönetimi rehberidir; piksel bazlı zorunluluk değildir. Logo sarısı amblem içinde kalır.

Primary ana aksiyon; secondary yardımcı yüzey; accent küçük editoryal vurgu. Border dekoratif ayırıcıdır; form kontrolleri control border kullanır. On primary yalnız primary buton yazısıdır.

Pudra #F1E1E7 ve açık bej #EEE6DA dekoratif yardımcı tonlardır. Küçük yazıda kullanılmaz. Hover, focus ve semantik durumlar ayrı tokenlarla tanımlanır.
'''))
add('Gece, daha sakin.','07 · DARK MODE',swatches('dark')+md('''## Koyu tema kuralları
Primary #CDB8E7 üstünde yazı #241E2E. Açık modun beyaz primary yazısı koyu moda doğrudan taşınmaz. Katman farkını surface ve surface raised kurar; gölgeye tek başına güvenilmez.

Kedi amblemi açık krem bir logo yüzeyinde korunur. Otomatik invert veya hue filtresi uygulanmaz. Gece arka planında lavanta bir odak, küçük pembe vurgu; neon ve yoğun galaksi yok.

Tema seçimi kullanıcı tercihine göre çalışabilir. WordPress editör style variation’ı ile ziyaretçi bazlı runtime tema kontrolü farklı mekanizmalardır.
'''),'dark')
add('Quicksand × DM Sans','08 · TYPOGRAPHY',ks('8.'))
add('Logo karakterini koru.','09 · LOGO KULLANIMI',f'<div class="logo-demo"><div><img src="{logo}" alt="Orijinal Skyra Astro header logosu"></div><div class="night-logo"><img src="{logo}" alt="Koyu yüzeyde korunan logo"></div></div>'+ks('9.')+callout('<strong>Kimlik ayrımı:</strong> orbital çizimler dekorasyondur; yeni bir logo olarak kullanılmaz. Quicksand canlı marka metni, raster amblemin yeniden çizimi değildir.'))
add('İnce çizgi, net işlev.','10 · ICONOGRAPHY',ks('10.')+'''<div class="icon-demo"><svg viewBox="0 0 24 24"><circle cx="10" cy="10" r="6"/><path d="m15 15 5 5"/></svg><svg viewBox="0 0 24 24"><path d="M3 6h18M3 12h18M3 18h18"/></svg><svg viewBox="0 0 24 24"><path d="M6 18 18 6M6 6h12v12"/></svg><svg viewBox="0 0 24 24"><path d="M17 3A9 9 0 1 0 21 17 10 10 0 0 1 17 3Z"/></svg></div>'''+md('''## Zodiac çizim standardı
12 sembol aynı viewBox ve optik ağırlıkla hazırlanır. Küçük boyutta çizgi kopması olmamalı. Burç adları mutlaka metin olarak görünür. Unicode glyphler final ikon dosyası sayılmaz.

## Kullanım bağlamları
Arama, menü ve kapatma gibi yalnız ikon içeren kontroller erişilebilir isim taşımalı. Transit verisinde gezegen sembolü yanında gezegen adı bulunmalı. İkonlar CTA metninin anlamını destekler; anlatımın yerine geçmez.
'''))
add('Atmosfer, ölçülü detaylarda.','11 · ILLUSTRATION & IMAGERY',ks('11.')+'''<div class="mood-grid"><div class="mood lunar">Lunar light</div><div class="mood arch">Natural texture</div><div class="mood orbital">Orbital line art</div></div>'''+callout('Yukarıdaki kompozisyonlar görsel yön örneğidir. Nihai fotoğraf seçimi, kullanım lisansı ve crop kontrolü uygulama aşamasında yapılır.'))
add('Estetik, kullanım kolaylığıyla.','12 · UI DESIGN PRENSİPLERİ',ks('12.')+md('''## Üç temel kullanıcı işi
- “Bugün gökyüzünde ne var?” — tarihli, kaynaklı veri kartlarına hızlı erişim.
- “Burcum için bugün ne söyleniyor?” — tek burç seçimiyle günlük yorum.
- “Doğum haritamı nasıl görebilirim?” — açık CTA ve kısa form.

## İçerik ve durum
Veri, editoryal yorum ve hesaplama yöntemi ayrı etiketlenir. Empty, error ve loading durumları boş bırakılmaz. Kullanıcı girdisi hatada korunur. Sonucu tamamlamak için gereksiz hesap oluşturma adımı eklenmez.

## Ölçülebilir hedefler
Klavye erişimi; 320 CSS px reflow; 200% yazı zoom; reduced-motion desteği. Bunlar üretim kabul hedefleridir. Mevcut canlı sitenin tam WCAG uyumluluğu doğrulanmadı.
'''))
add('Katmanlar & köşeler','13–14 · RADIUS & SHADOW', '<h3>13. Border radius</h3>'+ks('13.')+'<div class="radius-grid">'+''.join(f'<div style="border-radius:{v}px"><b>{v}px</b><small>{k}</small></div>' for k,v in tokens['radius'].items())+'</div><h3>14. Shadow sistemi</h3>'+ks('14.')+'<div class="shadow-grid">'+''.join(f'<div style="box-shadow:{v}">{k}</div>' for k,v in tokens['shadow'].items())+'</div>')
add('Boşluk, sistemin ritmi.','15 · SPACING & RESPONSIVE',ks('15.')+'<div class="spacing-scale">'+''.join(f'<div><span>{v}px</span><i style="width:{v}px"></i></div>' for v in tokens['spacing'])+'</div>'+md('''| Ortam | Breakpoint | Grid | Yatay kenar |
| --- | --- | --- | --- |
| Mobile | 320–767px | 4 kolon / 16px gutter | 20px; 320px’de 16px |
| Tablet | 768–1023px | 8 kolon / 20px gutter | 28px |
| Desktop | 1024px ve üstü | 12 kolon / 24px gutter | 40px |

Mobilde burçlar ve gökyüzü kartları iki kolon; araçlar, formlar ve uzun içerik tek kolon. Menü 1024px altında açılır paneldir. 1200px container büyüyen ekranda sabit üst sınırdır.
'''))
add('Işık, bir odak yaratır.','16 · GRADIENT SİSTEMİ','<div class="gradient-grid">'+''.join(f'<div style="background:{v};color:{"#F5F0F8" if k in ("night","dark-aura") else "#292333"}"><b>{k}</b></div>' for k,v in tokens['gradient'].items())+'</div>'+ks('16.')+callout('Gradient arkasında metin varsa tüm geçiş boyunca kontrast doğrulanır. Dekoratif gradient tek başına okunur metin yüzeyi kabul edilmez.'))
add('Aksiyonun ağırlığı belli.','17 · BUTTON SİSTEMİ','''<div class="buttons"><span class="btn primary">Haritamı keşfet ↗</span><span class="btn secondary">Günlük yorumum</span><span class="btn ghost">Daha fazla ↗</span><span class="btn icon">◐</span></div>'''+ks('17.')+md('''## Durum matrisi
| Durum | Görsel davranış | Etkileşim |
| --- | --- | --- |
| Default | Rolüne uygun renk | Link veya native button |
| Hover | Yüzey/renk geçişi 160–220ms | Yalnız hover destekleyen cihazda |
| Focus | 3px focus outline + 3px offset | Her zaman görünür; renk tek sinyal değil |
| Active | En çok 1px aşağı kayma | Reduced motion’da hareket yok |
| Disabled | Muted + raised; hover yok | Native disabled; açıklama gerekirse yanında |
| Loading | Boyut korunur, durum metni | aria-busy; çift gönderim önlenir |
'''))
add('Kartlar, bilgiye düzen verir.','18 · CARD SİSTEMİ',ks('18.')+'''<div class="card-samples"><div><small>GÜNLÜK YORUM · ÖRNEK</small><h3>Terazi</h3><p>Dengeye alan aç.</p><b>Yorumu oku ↗</b></div><div><small>GENEL TRANSİT · ÖRNEK</small><h3>Venüs △ Jüpiter</h3><p>Tarih · orb · kaynak</p><b>Detaya bak ↗</b></div></div>''')
add('Formlar açık ve sakin.','EK · FORM ELEMENTS',md(section(ux,'Form sistemi'))+'''<div class="form-demo"><label>Doğum tarihi · gerekli</label><div>Gün / ay / yıl</div><label>Doğum saati</label><div>Saat : dakika</div><small>□ Saatimi bilmiyorum — yükselen ve evler gösterilmez.</small><label>Doğum yeri · gerekli</label><div>Şehir, ülke</div><span class="btn primary">Doğum haritamı keşfet ↗</span></div>''')
add('Üç işe hızlı erişim.','EK · NAVIGATION & MICRO-INTERACTION',md(section(ux,'Bilgi mimarisi ve navigasyon'))+md('## Hareket ve cihaz davranışı\n'+section(ux,'Responsive ve hareket'))+callout('<strong>Menü hiyerarşisi:</strong> Bugün · Burçlar · Araçlar · Dergi · Rehberler. Doğum haritası tek baskın CTA; tarot danışmanlığı alt gezinmede korunur.'))
add('Quiet observatory','19 · GENEL ART DIRECTION',ks('19.')+'''<div class="direction-board"><div class="direction-large"><span>ASTROLOGY × EDITORIAL × WELLNESS × DIGITAL PRODUCT</span><h3>Bilgiye yaklaş.<br>Kendine yer aç.</h3></div><div class="direction-small">Özenli boşluk<br>İnce çizgi<br>Sıcak nötrler<br>Sade veri</div></div>'''+md('''## Sanat yönetimi kararları
- Editorial ritim: kısa başlıklar, uzun okumada dar kolon, düzenli metadata.
- Wellness hissi: sakin yüzeyler, baskısız aksiyonlar, kullanıcı iradesini koruyan ses.
- SaaS disiplini: açık alan etiketleri, tutarlı kontrol durumları, hızlı geri bildirim.
- Celestial atmosfer: ince orbital geometri, sınırlı ışık, seyrek sembol.

## Görsel sınırlar
Yoğun yıldız alanı, neon mor, galaksi fotoğraf yığını, ucuz zodiac görselleri, altın efektleri ve sürekli animasyonlar kullanılmaz. Mevcut logo sarısı paletin bütününe yayılmaz.
'''))
add('Ana sayfanın görsel ritmi','20 · ANA SAYFA ART DIRECTION',ks('20.')+md(section(ux,'Ana sayfa — dokuz bölüm')))
add('WordPress’e uygulanabilir sistem','EK · UYGULAMA DEVRİ',md(section(handoff,'Bileşen eşlemesi'))+md('## Ortak token sistemi\n'+section(handoff,'Tokenlar ve dosyalar')))
add('Performans & veri disiplini','EK · UYGULAMA DEVRİ',md(section(handoff,'İçerik ve veri modeli'))+md('## Performans hedefleri\n'+section(handoff,'Performans bütçesi')))
add('Claude için devam talimatı','EK · TASARIMDAN UYGULAMAYA',callout('<strong>PDF’yi önce incele.</strong> Kullanıcının istediği çıktı, bu bağımsız Brand Kit üzerinden WordPress uyumlu web UI/UX tasarımıdır. Brand Kit sayfası, token katalogu, debug notları veya tasarım devir metinleri son kullanıcı arayüzüne eklenmemelidir.')+md('''## Uygulama sırası
1. Mevcut WordPress kaynaklarını ve sayfa envanterini incele. Tema/builder, CPT’ler, plugin’ler, gerçek logo master’ı ve mevcut URL’leri doğrula.
2. PDF’deki renkleri, font rollerini, spacing, radius ve durum tokenlarını merkezi olarak tanımla. Tek tek widget değerleriyle parçalı bir sistem kurma.
3. Önce mobile-first ana sayfa, desktop/tablet adaptasyonu ve ortak header/footer’ı tasarla. Dokuz bölüm sırasını ve üç temel kullanıcı işini koru.
4. Burç arşivi/detayı, harita formu/sonucu, araçlar, transit/takvim, blog arşivi/yazı, astroloji/taş/tarot rehberleri, hakkında, danışmanlık, iletişim, arama ve 404 sayfalarını aynı bileşen ailesiyle tamamla.
5. Gerçek gökyüzü verisi ve hesaplama entegrasyonu varsa mevcut servisi doğrula. Yoksa bağlantı ihtiyacını açıkça belirt; sonuç uydurma. Demo uyarıları yalnız geliştirme/staging ortamında tutulur.
6. Önce içerik ve erişilebilirlik, ardından performans ve SEO geçiş kontrollerini tamamla.

## Sabit tutulacaklar
- Logo yapısı ve marka karakteri; kedi amblemi yeniden tasarlanmaz.
- Quicksand ana başlık/marka metni; DM Sans gövde ve ürün arayüzü.
- Açık/koyu semantik renk rolleri ve erişilebilir form sınırları.
- Günlük gökyüzü, burç yorumu ve kişisel haritaya hızlı erişim.
- Mevcut haftalık/blog/rehber URL’leri veya kontrollü 301 eşlemesi.

## Çözülmesi gereken girdiler
Yetkili logo SVG/master dosyası; CMS envanteri; efemeris ve tarihsel timezone çözümü; gerçek yayın takvimi ve yazarlar; görsel lisansları; newsletter servisi; onaylı hukuki metinler. Bu bilgiler bu PDF ile doğrulanmış sayılmaz.
'''))
add('Uygulama tokenları','EK · KOPYALANABİLİR DEĞERLER', '<pre>'+html.escape('''/* Typography */
--font-heading: "Quicksand", "Segoe UI", sans-serif;
--font-body: "DM Sans", "Segoe UI", sans-serif;
--h1: clamp(2.375rem, 5vw, 4rem);
--h2: clamp(1.75rem, 3vw, 2.5rem);

/* Radius */
--radius-xs: 6px;   --radius-sm: 10px;
--radius-md: 14px;  --radius-lg: 24px;
--radius-xl: 32px;  --radius-pill: 999px;

/* Spacing (px): 4 8 12 16 24 32 48 64 96 128 */
--container: 1200px;
--reading: 720px; /* body: 65ch maximum */

/* Motion */
--duration-ui: 180ms;
--easing-ui: ease-out;
/* prefers-reduced-motion: no animation or smooth scroll */

/* Form / action */
--control-height: 48px;
--target-minimum: 44px;
--focus-width: 3px;
--focus-offset: 3px;
''')+'</pre>'+callout('Renk tokenlarının tamamı açık ve koyu palet sayfalarında bulunur. Token isimleri platforma göre eşlenebilir; roller ve değerler tutarlı kalmalıdır. Shadow ve gradient CSS değerleri ilgili bölümlerde tam olarak yazılmıştır.'))
add('Kabul ölçütleri & kaynaklar','EK · SON KONTROL',md('''## Tasarım kabul ölçütleri
- Mevcut logo değişmemiş; görsel sınırlar ve çevre boşluğu tutarlı.
- Tek H1, açık menü, günlük bilgi/yorum/haritaya hızlı erişim.
- Light/dark ana metin çiftleri ≥4.5:1; büyük metin ve gerekli kontrol sınırları ≥3:1.
- Mobile 320–767, tablet 768–1023 ve desktop ≥1024px; 200% metin zoom ve klavye akışları kontrol edilmiş.
- Form hata, bekleme, boş sonuç, unknown-time ve backend failure durumları tamamlanmış.
- Astrolojik verilerde tarih/saat dilimi/kaynak; metinlerde gerçek yazar ve düzenleme tarihi.
- WordPress global tokens, tekrar kullanılabilir components; kişisel sonuç cache/analytics izolasyonu.

## Doğrulanan / doğrulanmayan
Bu palette önemli 24 renk çifti kontrast testinden geçti. Örnek uygulamada 35 rota, 5 genişlikte kontrol edildi; bu PDF’nin ikinci aşama tasarımına bağlayıcı bir ekran onayı olduğu anlamına gelmez. Canlı site Core Web Vitals ve gerçek hesaplama sonuçları ölçülmedi. Nihai uygulama ayrıca doğrulanmalıdır.

## Kaynaklar
- Mevcut site ve kaynak incelemesi: https://skyraastro.com/
- WordPress global styles: https://developer.wordpress.org/themes/global-settings-and-styles/
- WCAG 2.2 referansı: https://www.w3.org/WAI/WCAG22/quickref/
- Core Web Vitals: https://web.dev/articles/vitals
- Font aileleri: Quicksand ve DM Sans, Google Fonts.

Bu belge önerilen marka ve tasarım sistemini tanımlar. Marka rehberi son kullanıcı sitesine yerleştirilmez; bağımsız tasarım referansı olarak saklanır.
'''))

css='''@import url('https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600&family=Quicksand:wght@500;600&display=swap');
@page{size:A4;margin:0}*{box-sizing:border-box}html,body{margin:0;padding:0}body{background:#DFD8E3;color:#292333;font-family:"DM Sans","Segoe UI",sans-serif;font-size:10pt;line-height:1.6;-webkit-print-color-adjust:exact;print-color-adjust:exact}.page{width:210mm;height:297mm;background:#F8F5EF;position:relative;padding:18mm 17mm 16mm;margin:12mm auto;break-after:page;overflow:hidden}.page:last-child{break-after:auto}.page-header{display:flex;justify-content:space-between;border-bottom:1px solid #DAD1DF;padding-bottom:4mm;font-size:7pt;letter-spacing:.12em;color:#695E71}.kicker{font-size:8pt;letter-spacing:.12em;color:#665080;font-weight:600;margin:10mm 0 3mm}h1{font:600 28pt/1.18 "Quicksand",sans-serif;letter-spacing:-.03em;margin:0 0 7mm;max-width:170mm}h3,h4{font-family:"Quicksand",sans-serif;font-weight:600;line-height:1.3;color:#665080;margin:5mm 0 2mm;font-size:13pt}p{margin:0 0 3.5mm}strong{font-weight:600}ul{padding-left:5mm;margin:3mm 0}li{margin-bottom:2mm}table{width:100%;border-collapse:collapse;font-size:8pt;line-height:1.5;margin:3mm 0 5mm}th{text-align:left;background:#E6DDED;color:#523E6B;padding:2.3mm;font-weight:600}td{border-bottom:1px solid #DAD1DF;padding:2.5mm;vertical-align:top}tr{break-inside:avoid}code{font-family:monospace;font-size:.88em;overflow-wrap:anywhere}a{color:#665080;text-decoration:underline;overflow-wrap:anywhere}.page-footer{position:absolute;bottom:10mm;left:17mm;right:17mm;border-top:1px solid #DAD1DF;padding-top:3mm;display:flex;justify-content:space-between;font-size:7pt;color:#695E71}.subtitle{color:#695E71;font-size:11pt;margin-bottom:6mm}.callout{background:#EEE7F3;border-left:2px solid #665080;padding:4mm 5mm;border-radius:0 3mm 3mm 0;margin:4mm 0 6mm;font-size:9pt}.contents{display:grid;grid-template-columns:1fr 1fr;gap:9mm;margin:6mm 0}.swatches{display:grid;grid-template-columns:repeat(4,1fr);gap:3mm;margin-bottom:6mm}.swatch{background:#FFFDF9;border:1px solid #DAD1DF;border-radius:3mm;overflow:hidden}.swatch>div{height:15mm}.swatch p{padding:2mm 3mm;margin:0;font-size:8pt;line-height:1.4}.swatch code{display:block;font-size:8pt;color:#695E71}.dark{background:#17131F;color:#F5F0F8}.dark h1,.dark h3,.dark .kicker{color:#CDB8E7}.dark .swatch{background:#241E2E;border-color:#4B4058}.dark .swatch code,.dark .page-footer,.dark .page-header{color:#C0B4CB}.dark .page-footer,.dark .page-header{border-color:#4B4058}.logo-demo{display:grid;grid-template-columns:1fr 1fr;gap:5mm;margin-bottom:7mm}.logo-demo>div{height:34mm;border:1px solid #DAD1DF;border-radius:4mm;display:grid;place-items:center}.logo-demo img{width:150px;background:#F8F5EF;border-radius:8px}.logo-demo .night-logo{background:#211B30}.icon-demo{display:flex;justify-content:space-around;padding:10mm 0;background:#E6DDED;border-radius:4mm;margin:8mm 0}.icon-demo svg{width:32px;height:32px;stroke:#665080;stroke-width:1.5;stroke-linecap:round;stroke-linejoin:round;fill:none}.radius-grid,.shadow-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:4mm;margin:6mm 0 9mm}.radius-grid>div,.shadow-grid>div{padding:5mm;background:#FFFDF9;border:1px solid #DAD1DF;text-align:center}.radius-grid small{display:block;color:#695E71}.shadow-grid>div{border-radius:4mm;min-height:20mm}.spacing-scale{display:grid;grid-template-columns:1fr 1fr;gap:2mm 5mm;background:#FFFDF9;padding:4mm;border-radius:4mm;margin:6mm 0}.spacing-scale>div{display:flex;align-items:center;gap:3mm;font-size:8pt}.spacing-scale span{width:12mm}.spacing-scale i{display:block;height:6px;background:#A45C74}.gradient-grid{display:grid;grid-template-columns:1fr 1fr;gap:4mm;margin:6mm 0}.gradient-grid>div{height:35mm;border-radius:4mm;padding:5mm;font:500 14pt "Quicksand"}.buttons{display:flex;flex-wrap:wrap;gap:3mm;margin:5mm 0 8mm}.btn{display:inline-flex;align-items:center;justify-content:center;font-size:9pt;font-weight:600;padding:3.5mm 5mm;border:1px solid transparent;border-radius:3mm}.primary{background:#665080;color:#FFFDF9}.secondary{background:#FFFDF9;color:#665080;border-color:#887790}.ghost{color:#665080}.icon{border-color:#DAD1DF;background:#FFFDF9;border-radius:50%;width:12mm}.card-samples{display:grid;grid-template-columns:1fr 1fr;gap:4mm;margin:6mm 0}.card-samples>div{background:#FFFDF9;border:1px solid #DAD1DF;border-radius:6mm;padding:5mm}.card-samples small{color:#695E71;font-size:7pt}.card-samples h3{margin:3mm 0}.card-samples b{font-size:8pt;color:#665080}.form-demo{width:110mm;background:#FFFDF9;border:1px solid #DAD1DF;padding:5mm;border-radius:6mm;margin:6mm auto}.form-demo label{display:block;font-size:8pt;margin:2mm 0}.form-demo>div{border:1px solid #887790;border-radius:2.5mm;padding:2mm 3mm;color:#695E71;font-size:9pt}.form-demo small{display:block;font-size:7pt;margin:3mm 0}.form-demo .btn{width:100%;margin-top:3mm}.mood-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:3mm;margin:7mm 0}.mood{height:38mm;position:relative;overflow:hidden;border-radius:4mm;display:flex;align-items:end;padding:3mm;font-size:8pt}.lunar{background:linear-gradient(145deg,#3B3047,#211B30);color:#F5F0F8}.lunar:before{content:'';position:absolute;width:22mm;height:22mm;border-radius:50%;border:1px solid #CDB8E7;box-shadow:4mm 0 #CDB8E7;left:10mm;top:5mm}.arch{background:#EEE6DA;color:#523E6B}.arch:before{content:'';position:absolute;height:38mm;width:32mm;border:4mm solid #D4BCAE;border-radius:30mm 30mm 0 0;top:5mm;left:10mm}.orbital{background:linear-gradient(145deg,#E6DDED,#C8B7D4);color:#523E6B}.orbital:before{content:'';position:absolute;inset:8mm 4mm;border:1px solid #887790;border-radius:50%;transform:rotate(-30deg)}.direction-board{display:grid;grid-template-columns:2fr 1fr;gap:4mm;margin:7mm 0}.direction-large{background:linear-gradient(135deg,#F8F5EF,#E6DDED,#F1E1E7);padding:8mm;border-radius:6mm}.direction-large span{font-size:6pt;letter-spacing:.1em}.direction-large h3{font-size:23pt}.direction-small{background:#211B30;color:#E6DDED;padding:8mm 5mm;line-height:2;border-radius:6mm;font-size:10pt}pre{white-space:pre-wrap;background:#211B30;color:#F5F0F8;border-radius:4mm;padding:7mm;font:9pt/1.75 monospace;overflow-wrap:anywhere}.cover{background:#211B30;color:#F5F0F8}.cover .page-header,.cover .page-footer{border-color:#4B4058;color:#C0B4CB}.cover .kicker{margin-top:27mm;color:#CDB8E7}.cover h1{font-size:37pt;font-weight:500;line-height:1.12;position:relative;z-index:2}.cover .subtitle{color:#C0B4CB;font-size:10pt;max-width:130mm;position:relative;z-index:2}.cover-logo{position:absolute;top:24mm;left:17mm;display:flex;align-items:center;gap:3mm}.cover-logo img{width:85px;height:auto;background:#F8F5EF;border-radius:3mm}.cover-logo span{font:600 23pt "Quicksand"}.cover-logo small{display:block;font:500 7pt "DM Sans";letter-spacing:.3em}.cover-orbit{position:absolute;top:139mm;left:43mm;width:128mm;height:90mm;color:#CDB8E7}.cover-orbit i{position:absolute;inset:10mm 5mm;border:1px solid #CDB8E766;border-radius:50%;transform:rotate(-30deg)}.cover-orbit i:nth-child(2){inset:4mm 18mm;transform:rotate(35deg)}.cover-orbit i:nth-child(3){inset:0 20mm;transform:none}.cover-orbit b{position:absolute;inset:0;display:grid;place-items:center;font-size:48pt;font-weight:400}.cover-bottom{position:absolute;left:17mm;right:17mm;bottom:26mm;display:flex;justify-content:space-between;gap:10mm;align-items:end}.cover-bottom p{font:500 18pt/1.4 "Quicksand";margin:0}.cover-bottom>div{font-size:9pt;color:#C0B4CB}.compact{font-size:9pt;line-height:1.5}.compact h1{font-size:26pt;margin-bottom:5mm}.compact table{font-size:7.5pt}.compact td{padding:2mm}.compact p{margin-bottom:3mm}.compact .form-demo{padding:3mm;margin:3mm auto}.compact .form-demo>div{padding:1.5mm 3mm}.compact h3{margin-top:4mm}.compact li{margin-bottom:1.5mm}@media print{body{background:none}.page{margin:0;box-shadow:none}}
'''
for i,p in enumerate(pages):
 if len(re.sub('<[^>]+>','',p['body']))>2800:p['theme']+=' compact'
sections=[]
for i,p in enumerate(pages,1):
 sections.append(f'''<section class="page {p['theme']}" id="page-{i}"><header class="page-header"><span>SKYRA ASTRO</span><span>BRAND KIT · v1.1</span></header><div class="kicker">{p['category']}</div><h1>{p['title']}</h1>{f'<p class="subtitle">{p["subtitle"]}</p>' if p['subtitle'] else ''}<div class="content">{p['body']}</div><footer class="page-footer"><span>Bağımsız marka rehberi · Tasarım & uygulama devri</span><span>{i:02d} / {len(pages):02d}</span></footer></section>''')
source='<!doctype html><html lang="tr"><head><meta charset="utf-8"><title>Skyra Astro — Brand Kit & Web Design System v1.1</title><style>'+css+'</style></head><body>'+''.join(sections)+'</body></html>'
(root/'brand-kit/brand-book.html').write_text(source)
(root/'brand-kit/page-manifest.json').write_text(json.dumps([{'page':i,'title':re.sub('<[^>]+>',' ',p['title']),'category':p['category']} for i,p in enumerate(pages,1)],ensure_ascii=False,indent=2)+'\n')
print(f'Built {len(pages)} pages')
