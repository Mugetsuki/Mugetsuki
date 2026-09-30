# 01 · Mimari

## Neden Gutenberg blok teması + eklenti?

Mevcut site zaten bir blok teması (`wp-theme-skyra-editorial is-block-theme`) üzerinde. Brief'teki tercih sırası da Gutenberg + özel bloklar. Bu yüzden:

- **Tema sunumdan sorumlu:** tokenlar (`theme.json`), header/footer, şablonlar, global CSS/JS. Page builder yok, üçüncü taraf eklenti gerekmiyor.
- **Eklenti veriden ve işlevden sorumlu:** içerik tipleri, hesaplama motoru, REST, dinamik bloklar, formlar, SEO. Tema değişse bile içerik ve araçlar kalır (Brand Kit: "Hesaplama backend'i theme'den ayrı plugin/service olur").

Tüm Skyra blokları **dinamiktir** (PHP ile sunucuda render edilir). Böylece günlük gökyüzü verisi her zaman günceldir, içerik HTML'de tam olarak bulunur (SEO, JS kapalı) ve editörde canlı önizlenir.

## Klasörler

```
themes/skyra/
  theme.json            Brand Kit tokenları (renk, font, boşluk, gölge, radius) + core blok stilleri
  styles/night.json     Site genelinde koyu stil varyasyonu (editör seçimi)
  templates/            front-page, page, single, home (blog), archive, search, 404,
                        archive-skyra_sign (/burclar), single-skyra_sign (/burclar/koc)
  parts/                header, footer (tema blokları skyra-theme/site-header|site-footer)
  patterns/             skyra/hero, skyra/about, skyra/home
  blocks/               site-header, site-footer (menü: Görünüm → Menüler)
  assets/css/skyra.css  token takma adları, koyu mod, temel, header, footer, hero, şablonlar
  assets/js/skyra.js    header durumu, mobil menü, tema anahtarı, kaydırma belirmeleri (~4 KB)
  assets/fonts/         Quicksand + DM Sans WOFF2 (latin, latin-ext) + OFL
  assets/img/           korunan logo + kırpılmış amblem

plugins/skyra-core/
  includes/astro/       Saf PHP motor (WordPress bağımsız, tek başına test edilebilir)
    class-ephemeris.php   Güneş, Ay, gezegenler (konum, hız), ΔT, nütasyon, sapma, ışık zamanı
    class-houses.php      ASC, MC, Placidus (kutup: Porphyry)
    class-events.php      Ay fazı, yeni ay/dolunay, tutulma (Meeus 54), burç geçişi, istasyon, retro dönemleri
    class-chart.php       Doğum haritası: tarihsel saat dilimi, açılar, element/nitelik dengesi
    class-sky.php         Anlık/günlük gökyüzü, yıl olayları, retro takvimi
    class-readings.php    Türkçe yorum metinleri (günlük okuma, yerleşim, açı, uyum, olay, Ay fazı)
    class-zodiac.php      Burç/gezegen/açı verisi ve Türkçe ek biçimleri (Koç'ta, Koç'a, Koç'tan)
  includes/             WordPress katmanı
    class-data.php        Önbellekli erişim, editoryal öncelik, Türkçe tarih biçimleri
    class-places.php      GeoNames tabanlı doğum yeri arama (Türkçe harf katlama)
    class-rest.php        skyra/v1 uçları
    class-results.php     Araç sonuç panelleri (REST ve JS'siz POST aynı HTML'i kullanır)
    class-wheel.php       SVG harita çizici (hero, örnek harita, doğum haritası, transit)
    class-view.php        Ortak işaretleme, doğum formu, Ay çizimi, üretilmiş kapaklar
    class-post-types.php  CPT'ler, meta alanlar, meta kutuları, yönetim sütunları
    class-forms.php       Bülten (çift onay) ve iletişim; REST + admin-post
    class-seo.php         JSON-LD, meta açıklama, breadcrumb
    class-settings.php    Ayarlar → Skyra Astro
    class-setup.php       Kurulum: sayfalar, burçlar, gezegenler, yazılar, notlar, menüler
    content/              Başlangıç metinleri (burçlar, yazılar, sayfalar)
  blocks/<ad>/          block.json + render.php (24 blok)
  assets/css/blocks.css Blok bileşenleri (yalnızca Skyra bloğu olan sayfada yüklenir)
  assets/js/            tools.js (araçlar, ~13 KB), ui.js (etkileşimler, ~8 KB), editor.js
  data/places.json      3 558 yer (81 il + ilçeler + dünya şehirleri), yalnız sunucuda
```

## Bloklar

| Blok | Nerede | Not |
| --- | --- | --- |
| `sky-strip`, `sky-wheel` | Hero | Şu anki gerçek konumlar; harita dekor değil, veri |
| `today-sky` | Ana sayfa | Ay burcu/derece/geçiş saati, faz, Güneş, retro listesi, günün açısı, tema (Veri/Yorum etiketli) |
| `daily-horoscopes` | Ana sayfa, `/gunluk-burc-yorumlari`, `/burclar` | Mobilde kaydırmalı liste + düğmeler, masaüstünde 4 kolon |
| `birth-chart` | Ana sayfa (adım adım), `/dogum-haritasi` (tam) | Özetten tam haritaya geçiş sessionStorage ile |
| `tools-grid`, `daily-energy`, `moon-phase`, `upcoming-events`, `zodiac-explorer`, `editorial-grid`, `newsletter`, `social-links` | Ana sayfa ve diğer sayfalar | Başlık/açıklama editörden değiştirilebilir |
| `sign-profile`, `sign-reading`, `sign-faq` | Burç ve günlük yorum sayfaları | FAQ, FAQPage şemasıyla aynı içerik |
| `astro-calendar`, `retro-calendar`, `transits` | Takvim ve transit sayfaları | `?yil=` / `?tarih=&saat=` ile sunucu tarafı |
| `rising-sign`, `moon-sign`, `compatibility`, `contact-form`, `breadcrumbs` | İlgili sayfalar | |

Her blok editörde yan panelden ayarlanır (başlık, açıklama, çeşit) ve `ServerSideRender` ile canlı önizlenir. Derleme adımı yoktur.

## İçerik tipleri

| CPT | URL | Amaç |
| --- | --- | --- |
| `skyra_sign` (Burç) | `/burclar/{burç}/` | Burç sayfasının uzun metni, özeti, görseli |
| `skyra_planet` (Gezegen) | `/gezegenler/{gezegen}/` | Gezegen açıklamaları |
| `skyra_horoscope` (Burç yorumu) | — | Editörün günlük/haftalık yorumu; yayınlandığında o gün için otomatik okumanın yerine geçer |
| `skyra_event` (Gökyüzü olayı notu) | `/astroloji-takvimi/{not}/` | Hesaplanan olaya (tür + gezegen + tarih ±1 gün) bağlanan editoryal yazı |
| `skyra_transit` (Transit yazısı) | `/transitler/{yazı}/` | Gezegen o burçtayken Transitler sayfasında listelenir |
| `skyra_moon_phase` (Ay fazı metni) | — | 8 faz için yorum; ana sayfadaki Ay bölümünde kullanılır |
| `skyra_subscriber`, `skyra_message` | yalnız yönetim paneli | Bülten aboneleri, iletişim mesajları |
| `post` + kategoriler | `/blog/…`, `/kategori/…` | Astroloji 101, Gezegenler, Burçlar, İlişkiler, Gökyüzü Gündemi, Astrolojik Olaylar |

ACF gerekmez: meta alanlar `register_post_meta` ile REST'e açık ve yerel meta kutularıyla düzenlenir.

## REST API (`/wp-json/skyra/v1/`)

| Uç | Yöntem | Önbellek |
| --- | --- | --- |
| `sky` | GET | public, 10 dk |
| `events?year=` | GET | public, 1 gün |
| `horoscope/{burç}?date=` | GET | public, 1 saat |
| `places?q=` | GET | public, 1 gün |
| `compatibility?a=&b=` | GET | public, 1 hafta |
| `chart`, `moon-sign` | POST | `no-store, private` |
| `newsletter`, `newsletter/confirm`, `newsletter/unsubscribe`, `contact` | POST/GET | `no-store` |

## Doğum verisinin yolu (gizlilik)

1. Tarayıcı → `POST /wp-json/skyra/v1/chart` (JSON gövde; adres çubuğunda yok).
2. Sunucu: yer kimliğinden koordinat + IANA saat dilimi → tarihsel UTC ofseti → hesap → HTML + veri.
3. Yanıt `Cache-Control: no-store, private`. Veri veritabanına yazılmaz, loglanmaz.
4. Ana sayfa özetinden tam sayfaya geçişte giriş bilgileri yalnızca o sekmenin `sessionStorage`'ında tutulur ve tam sayfada okunduktan sonra silinir.
5. JavaScript kapalıysa form aynı sayfaya POST edilir; POST istekleri tam sayfa önbelleğine girmez.

## Önbellek

Hesaplamalar deterministiktir; `Data` sınıfı sonuçları transient olarak saklar: anlık gökyüzü 10 dakikalık dilimlerle, günlük gökyüzü 1 gün, yıl olayları ve retro takvimi 30 gün. Motor çıktısı değişirse `Data::CACHE_VERSION` artırılır. Tam sayfa önbelleği kullanılıyorsa ana sayfa ve araç sayfaları için süre 1 saati geçmemeli; bloklar "Güncelleme" saatini gösterdiği için eski veri görünür olur.
