# 05 · Kalite ve yayın

Tüm ölçümler 30 Eylül 2026'da, WordPress 7.1.2 + PHP 8.3 (Playground, PHP-WASM + SQLite) üzerinde alındı.

## Otomatik testler

| Komut | Kapsam | Sonuç |
| --- | --- | --- |
| `npm run test:php` | 56 PHP dosyası sözdizimi | 0 hata |
| `npm run test:engine` | Konum, lunasyon, tutulma, istasyon, geçiş, aydınlanma, ASC/MC, Placidus, tarihsel saat dilimi | 29/29 |
| `npm run test:e2e` | 24 rota × 360/390/430/768/1024/1440 px (HTTP 200, tek H1, yatay taşma yok, JS hatası yok); araç akışları; formlar; menü ve tema; axe WCAG 2.2 AA (açık + koyu, 6 sayfa) | 49/49 |

Elle doğrulananlar: JavaScript kapalıyken doğum haritası, Ay burcu ve uyum araçları form POST'u ile sonuç veriyor; bülten ve iletişim formları admin-post ile çalışıyor; ana sayfa blok editöründe geçersiz blok uyarısı olmadan açılıyor ve 13 Skyra bloğu canlı önizleniyor.

## Lighthouse (mobil, yavaş 4G simülasyonu)

| Sayfa | Performans | Erişilebilirlik | En iyi uygulamalar | SEO | LCP | CLS | TBT |
| --- | --- | --- | --- | --- | --- | --- | --- |
| `/` | 88 | 100 | 100 | 100 | 3,4 sn | 0 | 0 ms |
| `/dogum-haritasi/` | 94 | 100 | 100 | 100 | 2,9 sn | 0 | 0 ms |

Playground sunucusu PHP'yi WebAssembly'de çalıştırır ve sıkıştırma yapmaz: ilk bayt 0,8–1 sn sürüyor, HTML sıkıştırılmadan gidiyor (ana sayfa 169 KB, gzip ile 30 KB). Gerçek hosting'de sayfa önbelleği + gzip/brotli ile LCP'nin 2,5 sn altına inmesi beklenir; bu yayından sonra gerçek kullanıcı verisiyle (CrUX / Search Console) doğrulanmalıdır.

Bütçe (Brand Kit hedefleri): ana sayfa JS 26 KB (gzip 8,8 KB; hedef ≤ 30 KB) · CSS gzip 21 KB (hedef ≤ 35 KB) · fontlar 116 KB (hedef ≤ 150 KB) · üçüncü taraf betik, video, iframe veya harici font yok · görsellerin tamamı satır içi SVG.

## Erişilebilirlik

- Her sayfada tek H1; bölüm başlıkları H2, kart başlıkları H3.
- Klavye: tüm menü, form, sekme, filtre ve combobox klavyeyle kullanılabilir; odak her zaman görünür (3 px + 3 px offset); "İçeriğe atla" bağlantısı; mobil menü Escape ile kapanır ve odağı düğmeye döndürür (modal olmadığı için odak tuzağı yok).
- Formlar: görünür label, hata metni alanın altında ve `aria-describedby` ile bağlı, ilk hatalı alana odak, girdi korunur, yükleniyor durumu `aria-busy`.
- Haritalar `role="img"` + başlık/açıklama; her haritanın yanında metin tablosu. Dekoratif SVG'ler `aria-hidden`.
- Kontrast: axe taramasında açık ve koyu temada 0 ihlal (küçük pembe metin için `--color-accent-text`, bkz. [02](02-tasarim-sistemi.md)).
- `prefers-reduced-motion`: tüm animasyonlar kapanır, içerik aynı kalır.

## SEO

- Temiz, Türkçe URL'ler ve kırıntı (breadcrumb); her sayfada meta açıklama (özet alanından), Open Graph, `lang="tr"`.
- JSON-LD `@graph`: Organization, WebSite (+ SearchAction), BreadcrumbList; yazılarda, olay notlarında ve transit yazılarında Article; burç sayfalarında sayfadaki SSS ile birebir aynı FAQPage.
- Tüm içerik sunucuda render edilir; animasyonlar içeriği DOM'dan kaldırmaz.
- Yoast veya Rank Math etkinse Skyra'nın SEO çıktısı kendiliğinden kapanır (`skyra_output_seo` filtresi).

## Yayından önce

- [ ] Hukuki metinlerdeki köşeli parantezli alanlar (veri sorumlusu unvanı/adresi, barındırma sağlayıcısı, mesaj saklama süresi) doldurulmalı ve metinler hukukçuya onaylatılmalı.
- [ ] Site dili Türkçe; SMTP ayarı (bülten onay e-postası); iletişim alıcısı.
- [ ] TikTok ve YouTube adresleri (Ayarlar → Skyra Astro). Instagram, canlı sitedeki `instagram.com/skyra.astro` ile geliyor.
- [ ] Logo için yetkili SVG / yüksek çözünürlüklü master (mevcut PNG 150 × 79).
- [ ] Başlangıç yazıları ve burç metinleri editör tarafından gözden geçirilmeli; gerçek yazar profilleri (kurulum "Skyra Editörü" adlı, parolası bilinmeyen bir yazar hesabı açar).
- [ ] Eski sitenin URL'leri (haftalık yorum, doğal taşlar, tarot, danışmanlık) için 301 yönlendirme haritası; bu içerikler bu teslimatın kapsamında değil.
- [ ] Sayfa önbelleği: ana sayfa ve araç sayfaları ≤ 1 saat; `wp-json/skyra/v1/chart` ve `moon-sign` önbelleğe alınmamalı (yanıtlar zaten `no-store`).
- [ ] Gerçek cihazlarda (iOS Safari, Android Chrome) ve gerçek kullanıcılarla deneme; bu teslimatta yalnız otomatik tarayıcı testleri yapıldı.

## Bilinen durumlar

- Playground'da kök düzeydeki var olmayan adresler (ör. `/xyz/`) 404 yerine ana sayfaya yönleniyor; WordPress bu isteklerde yolu boş görüyor. 404 şablonu `/blog/olmayan-yazi/` gibi adreslerde doğrulandı; gerçek sunucuda da kontrol edilmeli.
- Bu ortamda `downloads.w.org`, `github.com` ve `playground.wordpress.net` erişilemediği için testler, `wordpress.org`'dan indirilen WordPress 7.1.2 zip'i yerel bir HTTP sunucusundan verilerek ve dil paketi adımı çıkarılarak çalıştırıldı. Normal ağda `npm start` yeterlidir.
- Headless tarayıcı İngilizce yerel ayarla çalıştığı için ekran görüntülerinde tarih/saat alanlarının yer tutucusu `mm/dd/yyyy` görünür; Türkçe tarayıcıda `gg.aa.yyyy` görünür.
