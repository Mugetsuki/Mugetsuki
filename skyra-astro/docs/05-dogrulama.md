# Doğrulama kaydı

30 Eylül 2026. Bu kayıt statik tasarım önizlemesine aittir; canlı WordPress site performansı veya tam erişilebilirlik sertifikası değildir.

## Tamamlanan kontroller

- `node --check app.js`: geçti.
- `scripts/verify.py`: iki temada 24 renk çifti geçti; light/dark tokenlar CSS ve WordPress palette ile eşleşiyor; yerel kaynaklar var; orijinal logo SHA-256 eşleşiyor.
- Headless Chromium / Playwright: **35 rota × 5 viewport** (320, 390, 768, 1024, 1440px). Her ekranda bir H1, benzersiz ID’ler ve ana içerikte yatay taşma kontrolü geçti.
- Mobil menü aç/kapa, Escape ve tetikleyiciye odak dönüşü geçti.
- Koyu tema seçimi ve reload sonrası tercih geçti.
- Boş form hata işaretleri, doğum saati bilinmiyor ve geçerli girdiyle sonuç şablonuna geçiş geçti. Doğum bilgisi sonuç URL’sine taşınmıyor.
- Newsletter geçersiz/geçerli email için açıklamalı demo durumları geçti.
- Olay filtresi boş durumu, uyum formu demo durumu, arama girdisinin script olarak çalışmaması geçti.
- JavaScript runtime hatası: **0**. Makine çıktısı: [browser-results.json](browser-results.json).
- Masaüstü, mobil ve dark kit ekran görüntüleri görsel olarak incelendi.

## Seçilmiş kontrast oranları

| Çift | Light | Dark |
| --- | --- | --- |
| Ana metin / background | 13.96:1 | 16.27:1 |
| İkincil metin / background | 5.61:1 | 9.25:1 |
| Primary buton metni / primary | 6.84:1 | 8.93:1 |
| İkincil metin / secondary | 4.63:1 | 6.85:1 |
| Form sınırı / surface | 4.06:1 | 5.20:1 |

İlk kontrolde light secondary üstünde muted 4.36:1 kaldı. Muted #695E71 olarak koyulaştırıldı; ilgili CSS, JSON ve Brand Kit birlikte güncellendi. Dekoratif border düşük kontrastlı olabilir; input sınırı için ayrı control tokenı kullanılır.

## Görsel çıktılar

- [Desktop ana sayfa](home-desktop.png)
- [Mobile ana sayfa](home-mobile.png)
- [Dark Brand Kit](kit-dark.png)

## Yeniden çalıştırma

Bağımlılıksız kontroller README’dedir. Tarayıcı kontrolleri için Playwright ve Chromium gerekir:

```sh
node skyra-astro/scripts/browser-check.cjs
```

Paket proje dışında kurulduysa `PLAYWRIGHT_PATH` ile `playwright` paketinin yolunu belirt. Bu ortamda Chromium için eksik nspr/nss/asound kütüphaneleri sisteme kurulmadan /tmp altında açıldı ve `LD_LIBRARY_PATH` ile kullanıldı. Paket tasarımın runtime bağımlılığı değildir. Testler yerel `file://` önizlemesine karşı çalıştırıldı.

## Üretimde yapılacaklar

Gerçek hesaplama sonuçları, backend timeout/rate limit, yer autocomplete, newsletter servisi ve double opt-in, tüm CMS içerikleri, iOS/Android gerçek cihazlar, ekran okuyucu, zoom, font lisans/master logo ve canlı Core Web Vitals henüz doğrulanmadı. Önizlemenin passing testleri bu kontrollerin yerine geçmez.
