# WordPress uygulama devri

Bu depo bir profil deposudur; teslimat bir tasarım paketi ve statik önizlemedir, çalışan WordPress sitesi veya kurulabilir tema değildir. Mevcut blok temaya geçiş için Gutenberg ilk öneridir. Elementor ve Bricks aynı token/component sistemiyle alternatif uygulamalardır; üçünün aynı anda kurulması önerilmez.

## Bileşen eşlemesi

| Bileşen | Gutenberg | Elementor | Bricks |
| --- | --- | --- | --- |
| Header/footer | Template part + Navigation | Theme Builder header/footer | Header/footer template |
| Hero / CTA / newsletter | Group/Columns/Button pattern | Container + global widgets | Section/container + component |
| Burç / gökyüzü / araç | CSS grid + pattern; veri için dynamic block | Loop/grid + dynamic fields | Query loop + dynamic data |
| Blog arşivi/detay | Query Loop + single.html | Loop + single template | Query loop + single template |
| Rehber/tarot/taş | CPT archive/single template | CPT templates | CPT templates |
| Harita/transit | Özel plugin’in dynamic block’u | Plugin shortcode/widget wrapper | Plugin shortcode/component wrapper |
| Forms | Güvenli form/plugin block’u | Form widget veya backend plugin | Form element veya backend plugin |

## Tokenlar ve dosyalar

`tokens.json` kaynak; `tokens.css` light/dark runtime karşılığıdır. `wordpress/theme.json` global palette/typography/spacing/gradient önerisidir; mevcut temanın dosyasıyla birleştirilir, körlemesine üzerine yazılmaz. `wordpress/night.json` style variation örneğidir; temanın `styles/night.json` konumuna taşınabilir. Tema varyasyonu editör seçimiyle site genelinde değişir; kullanıcı bazlı runtime dark-mode toggle ayrıca CSS/JS ister. JSON font ailelerini tanımlar, font dosyalarını kaydetmez; WordPress font library veya yerel @font-face ile gerçek WOFF2 yükleme yapılmalıdır. [WordPress’in global styles rehberi](https://developer.wordpress.org/themes/global-settings-and-styles/).

Global stilleri kullan: primary vb. renkleri widget bazında tekrar yazma. Gutenberg patterns header, hero, sky-today, zodiac-grid, chart-cta, tools-grid, editorial-grid, experience, newsletter ve footer olarak bölünür. Layout sabitken metin editörü güvenli içerik alanlarını düzenler. Site editor’da critical layout block locking uygulanabilir.

## İçerik ve veri modeli

Mevcut CMS envanterini dışa aktar; tema kaynağı Skyra Content eklentisini anıyor fakat plugin verisi incelenmedi. Yeni model mevcut CPT’lerle eşlenmeli, aynı içerik iki kez oluşturulmamalı.

- Horoscope: burç taxonomisi, period (daily/weekly), period start/end, timezone, publish/update, author, theme, content.
- Sky event: event type, UTC start/exact/end, planet(s), angle/orb, ephemeris version, source, local display timezone.
- Rehber/blog: topic taxonomy, gerçek author, reviewed date, kaynaklar, ilgili içerik, image alt/lisans.
- Tarot/stone: existing slug, isim, sınıflandırma, açıklama, görsel, kaynak.
- Chart result: doğum bilgisi ve koordinat/geçmiş timezone çözümü; anonim kullanıcı için kalıcı saklama varsayılan değil. Görsel harita yanında semantik tablo.

Günlük gökyüzü hesaplaması efemeris ve doğrulanmış saat dilimi çözümü gerektirir. Natal hesaplama sunucu plugin/service içinde; UTC dönüşümü doğum tarihindeki yaz saati/geçmiş dilimle yapılır. Yayın kontrolü: veri → editör yorumu → onay → zamanlı publish; başarısız güncellemede eski verinin tarihi açık gösterilir. Temanın işi sunumdur.

## Performans bütçesi

Hedef (ölçüm değildir): gerçek kullanıcı p75 LCP ≤2.5s, INP ≤200ms, CLS ≤0.1. Kaynak: [Web Vitals](https://web.dev/articles/vitals). Başlangıç bütçesi: compressed critical CSS ≤35KB, ana sayfa JS ≤30KB, ilk ekran medya ≤200KB, fontlar toplam ≤150KB; uygulamada ölçülüp revize edilir. Hero video yok, ana görsel lazy-load edilmez; aşağıdaki görseller lazy. Tüm medyada width/height veya aspect-ratio. Font swap ve uygun preload; gereksiz font ağırlığı/ikon fontu yok. Astro backend yalnız araç sayfasında yüklenir; builder add-on yığınından kaçınılır.

Full-page cache halka açık yorumlar için; kişisel harita ve form yanıtı cache dışında. Günlük/transit cache expiry veri güncellemeyle eşlenir. Newsletter double opt-in, backend validation, erişilebilir hata, rate limit ve spam önlemi. Preview formu veri göndermez ve gerçek kayıt yaptığı izlenimi vermez.

## SEO ve geçiş

Mevcut URL’leri crawl/export ile tamamla; eski haftalık/blog/rehber URL’leri korunur. Zorunlu değişikliklerde tek adımlı 301, canonical ve sitemap güncellemesi; redirect zinciri yok. Article yapısal veri yalnız gerçek author/date değerleriyle; FAQ yalnız gerçek görünür soru-cevap varsa. Hreflang yalnız tamamlanmış diller için. Test içerikleri ve prototype noindex; üretimde örnek astro verileri kaldırılır. Üretim copy ve görsel kaynakları editoryal kontrol alır.

## Kabul ve teslim sınırı

Önizlemede responsive ana sayfa, sayfa aileleri, tema toggle, mobil menü, burç detayı, form doğrulama, newsletter demo sonucu ve UI katalogu bulunur. Astrolojik veriler örnektir. Harita hesaplaması, autocomplete servisi, WordPress kurulumu, newsletter entegrasyonu ve hukuki metin yazımı bu tasarım teslimatında uygulanmamıştır. Gerçek hesaplama sonucu uydurulmaz.

Yayına çıkış kontrolleri: CMS sayfa envanteri; logo master dosyası; tüm eski URL’lerin mapping’i; font glyph ve lisans; mobile/tablet/desktop görsel QA; keyboard/reader/zoom; form failure/unknown-time; verified ephemeris fixtures; Lighthouse + field vitals; newsletter double opt-in; cache izolasyonu; editoryal temizleme. Bunlar planlanan üretim kontrolleridir; tamamlanmış test gibi işaretlenmez.
