# Skyra Astro — WordPress sitesi

Brand Kit v1.1 → tasarım sistemi → UX mimarisi → çalışan WordPress sitesi.

Bu klasör üç parçadan oluşur:

| Parça | Görev |
| --- | --- |
| `themes/skyra-astro` | Blok teması (Gutenberg / Site Editor). Brand Kit tokenları `theme.json`'da; header, footer, şablonlar, gerçek koyu mod, mobil öncelikli CSS. |
| `plugins/skyra-core` | İçerik modeli (CPT'ler), astroloji hesaplama motoru, REST API, dinamik bloklar, iletişim formu, çerez onayı, yasal metinler, SEO/schema, tek tıkla kurulum. Bülten şimdilik kapalı (`skyra_newsletter_enabled` filtresi). |
| `plugins/skyra-tarot` | Bağımsız, etkileşimli tarot açılımı (`/tarot/`). Varsayılan olarak test modunda: sayfa özeldir, yalnızca yöneticiler görür. Ayrıntılar: [docs/06](docs/06-kvkk-cerez-tarot.md). |

Hesaplamalar gerçektir: gezegen konumları, Ay fazı, yükselen burç, evler, tutulmalar, retro istasyonları ve burç geçişleri sitenin kendi efemeris motoruyla sunucuda hesaplanır ve bağımsız bir referans kütüphaneyle test edilir. Hiçbir sonuç sabit metin ya da uydurma değildir.

![Ana sayfa, masaüstü](docs/screenshots/home-desktop.jpg)

## Hızlı başlangıç (yerel, PHP kurmadan)

WordPress Playground ile gerçek bir WordPress (PHP-WASM + SQLite) açılır, tema ve eklenti bağlanır, kurulum çalışır:

```sh
cd skyra-astro-wp
npm install
npm start            # http://127.0.0.1:9400 — yönetici olarak oturum açılır
```

`blueprint.json` sırasıyla Türkçe dil paketini kurar, eklentiyi ve temayı etkinleştirir, `Skyra\Core\Setup::run()` ile sayfaları, burçları, yazıları ve menüleri oluşturur.

Ağ kısıtlı bir ortamda `downloads.w.org` erişilemiyorsa WordPress zip'ini başka bir adresten verip dil adımını çıkarabilirsin: `npx wp-playground-cli server --wp <zip-url> …` (bkz. [docs/05-kalite-ve-yayin.md](docs/05-kalite-ve-yayin.md)).

## Gerçek bir WordPress'e kurulum

1. `themes/skyra-astro` → `wp-content/themes/skyra-astro`, `plugins/skyra-core` → `wp-content/plugins/skyra-core`.
   Klasör adı bilerek `skyra-astro`: sitede daha önce `skyra` adlı bir tema kullanıldığı için Site Editörü'nde kaydedilmiş eski şablonlar o ada bağlı; aynı adı kullanmak eski header, ana sayfa ve footer'ı geri getirir.
2. Eklentiyi, sonra temayı etkinleştir. PHP 8.1+, WordPress 6.6+ (7.1 ile test edildi).
3. **Araçlar → Skyra kurulum → Kurulumu çalıştır.** Var olan içerik (aynı kısa ad) değiştirilmez; tekrar çalıştırılabilir. WP-CLI: `wp skyra setup`.
4. **Ayarlar → Genel → Site dili: Türkçe.**
5. **Ayarlar → Skyra Astro:** sosyal hesaplar (TikTok ve YouTube adresleri bilinmediği için boş bırakıldı), iletişim alıcısı, marka cümlesi, CTA metinleri.
6. Bülten onay e-postaları için SMTP (ör. WP Mail SMTP) — `wp_mail` başarısız olursa form bunu kullanıcıya açıkça söyler.
7. Yayından önceki kontrol listesi: [docs/05-kalite-ve-yayin.md](docs/05-kalite-ve-yayin.md).

## Sayfa haritası

| URL | İçerik |
| --- | --- |
| `/` | Ana sayfa: hero (canlı gökyüzü), Bugün Gökyüzünde, günlük burçlar, doğum haritası, araçlar, günün enerjisi, Ay fazı, yaklaşan olaylar, burçlar, blog, Skyra nedir, bülten, topluluk |
| `/burclar/`, `/burclar/{koc…balik}/` | Burç arşivi (element gruplu) ve 12 burç sayfası (profil, bugün/yarın okuması, SSS, ilişkiler) |
| `/gunluk-burc-yorumlari/`, `/gunluk-burc-yorumlari/{burç}/` | 12 burcun günlük okuması; burç sayfalarında bugün/yarın sekmeleri |
| `/dogum-haritasi/` | Doğum haritası: SVG harita, konum tablosu, yerleşimler, açılar, element dengesi |
| `/yukselen-burc-hesaplama/`, `/ay-burcu-hesaplama/`, `/burc-uyumu/` | Hesaplama araçları |
| `/transitler/` | Şu anki (veya seçilen andaki) gezegen konumları ve açılar |
| `/retro-takvimi/`, `/astroloji-takvimi/` | Yılın retroları; yeni ay/dolunay, tutulma, istasyon ve burç geçişleri (`?yil=2027`) |
| `/astroloji-araclari/` | Araçlar merkezi |
| `/blog/`, `/blog/{yazı}/`, `/kategori/{kategori}/` | Editoryal içerik |
| `/astroloji-takvimi/{not}/`, `/transitler/{yazı}/`, `/gezegenler/{gezegen}/` | Olay notları, transit yazıları, gezegen sayfaları |
| `/hakkimizda/` (+ `#yontem`), `/iletisim/`, `/gizlilik-politikasi/`, `/kvkk/`, `/cerez-politikasi/` | Kurumsal ve hukuki sayfalar |

## Belgeler

1. [Mimari](docs/01-mimari.md) — tema/eklenti ayrımı, bloklar, CPT'ler, REST, önbellek, gizlilik akışı
2. [Tasarım sistemi](docs/02-tasarim-sistemi.md) — Brand Kit → token eşlemesi, bileşenler, hareket, koyu mod, responsive
3. [İçerik yönetimi](docs/03-icerik-yonetimi.md) — editör rehberi: yorumlar, olay notları, Ay fazı metinleri, menüler, ayarlar
4. [Hesaplama motoru](docs/04-hesaplama-motoru.md) — yöntem, doğruluk ölçümleri, sınırlar
5. [Kalite ve yayın](docs/05-kalite-ve-yayin.md) — test sonuçları, SEO, performans, erişilebilirlik, yayın kontrol listesi

## Testler

```sh
npm test             # PHP sözdizimi + efemeris doğruluğu (astronomy-engine'e karşı)
npm run test:e2e     # çalışan siteye karşı: 24 rota × 6 genişlik, araç akışları, formlar, axe WCAG 2.2 AA
```

Son çalıştırma: efemeris 29/29, uçtan uca 49/49 geçti. Ayrıntılar ve Lighthouse sonuçları [docs/05](docs/05-kalite-ve-yayin.md)'te.

## Kaynaklar ve lisanslar

- Konum verisi: [GeoNames](https://www.geonames.org/) cities15000 (CC BY 4.0), `tools/build-places.py` ile üretildi; sitede atıf var.
- Fontlar: Quicksand ve DM Sans (SIL OFL 1.1), yerelde barındırılır; lisanslar `themes/skyra-astro/assets/fonts/`.
- Logo: canlı siteden alınan kedi amblemi değiştirilmedi (`skyra-logo-original.png`, SHA-256 `c37ece38…43d0fe`). `skyra-mark.png` yalnızca saydam kenarları kırpılıp ortalanmış kopyasıdır; piksel, renk ve çizgiye dokunulmadı.
- Yörünge elemanları ve düzeltme terimleri: Paul Schlyter, *How to compute planetary positions*; tutulma sınıflandırması: Jean Meeus, *Astronomical Algorithms*, bölüm 54.
