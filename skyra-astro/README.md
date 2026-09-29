# Skyra Astro — bağımsız PDF Brand Kit

**Ana teslimat:** [Skyra-Astro-Brand-Kit.pdf](Skyra-Astro-Brand-Kit.pdf). Claude için tek başına okunabilir, 28 sayfalık marka ve uygulama rehberi. Brand Kit web sitesinin bir sayfası olarak uygulanmamalıdır. Önceki HTML önizleme keşif çalışmasıdır; sonraki tasarımı bağlayan nihai ekran onayı değildir.

PDF; mevcut site analizi, istenen 20 marka başlığı, görsel paletler, bileşen örnekleri, WordPress notları ve Claude devam talimatını içerir. `brand-kit/brand-book.html` baskı kaynağıdır; site şablonu değildir.

PDF’yi tekrar üretmek için `python3 skyra-astro/scripts/build-brand-pdf.py`, ardından Playwright/Chromium kurulu ortamda `node skyra-astro/scripts/export-brand-pdf.cjs` çalıştırılır. Export aracı sayfa taşmalarını ve PDF sayfa sayısını kontrol eder.

## Önceki araştırma ve önizleme paketi

İlk aşama: mevcut site analizi + 20 başlıklı Brand Kit. İkinci aşama: tüm mevcut/yeni sayfa ailelerinin WordPress uyumlu UI/UX sistemi ve etkileşimli, responsive önizleme. Canlı sitenin header logo dosyası değiştirilmeden kullanılır.

## İnceleme sırası

1. [Mevcut site analizi](docs/01-mevcut-site-analizi.md)
2. [Brand Kit](docs/02-brand-kit.md)
3. [Sayfa ve etkileşim sistemi](docs/03-web-ui-ux.md)
4. [WordPress uygulama devri](docs/04-wordpress-handoff.md)
5. [Tarayıcı önizlemesi](index.html) · [UI katalogu](index.html?page=kit)

## Önizlemeyi aç

Depo kökünden:

```sh
python3 -m http.server 8080 --directory skyra-astro
```

Tarayıcıda `http://localhost:8080` aç. Harici fontlar için internet gerekir; font erişimi yoksa sistem sans fallback kullanılır. Build adımı, bağımlılık veya API anahtarı gerekmez. `index.html` doğrudan da açılabilir.

Header, footer ve kartlardan sayfalara ulaşılır. `?page=kit` açık/koyu paletleri, tipografiyi, butonları, formları ve kart ailesini gösterir. `?page=arama` arama örneği; tanınmayan rota 404 şablonudur. JavaScript kapalıyken doküman bağlantıları görünür.

## Ekran görüntüleri

[Desktop](docs/home-desktop.png) · [Mobile](docs/home-mobile.png) · [Dark Brand Kit](docs/kit-dark.png)

## Uygulama dosyaları

- `tokens.json`: renk, spacing, radius, font, shadow ve gradient kaynağı.
- `tokens.css`: aynı tokenların runtime light/dark karşılığı.
- `styles.css`: responsive layout ve component stilleri.
- `app.js`: hafif sayfa şablonları ve demo etkileşimleri.
- `wordpress/theme.json`: mevcut blok tema ile birleştirilecek global styles örneği.
- `wordpress/night.json`: koyu style variation örneği.
- `assets/skyra-logo-original.png`: canlı siteden alınan, değiştirilmemiş header logosu. Kaynağı [asset kaydında](assets/SOURCES.md).

Bu bir kurulabilir WordPress teması değildir. CMS kurulumu, astrolojik hesaplama, yer arama ve newsletter backend’i uygulanmadı. UI’da bu sınırlar açıkça belirtilir. Örnek yazılar/olaylar gerçek yayın veya güncel gökyüzü verisi gibi kullanılmamalı. Hukuki sayfalar yalnız tasarım şablonudur.

## Kontroller

```sh
node --check skyra-astro/app.js
python3 skyra-astro/scripts/verify.py
```

`verify.py` önemli renk çiftlerinin WCAG kontrastını, token/CSS/WordPress palette eşleşmesini, yerel dosyaları ve korunan logo hash’ini kontrol eder. Sonuçlar [doğrulama kaydında](docs/05-dogrulama.md). Üretim kabul listesi WordPress devir belgesindedir.
