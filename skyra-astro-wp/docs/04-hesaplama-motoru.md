# 04 · Hesaplama motoru

`plugins/skyra-core/includes/astro/` — saf PHP, WordPress'ten bağımsız, dış servis veya API anahtarı gerektirmez.

## Yöntem

| Adım | Yöntem |
| --- | --- |
| Zaman | Yerel saat → IANA saat dilimi (PHP'nin tarihsel veritabanı: yaz saati, Türkiye'nin 2016'daki kalıcı UTC+3 geçişi dahil) → UT → ΔT (Espenak & Meeus) → TT |
| Güneş, Ay, gezegenler | Paul Schlyter yörünge elemanları; Ay için 12 boylam + 5 enlem düzeltmesi; Jüpiter–Satürn–Uranüs karşılıklı pertürbasyonları; Plüton için Schlyter uyarlaması |
| Görünen konum | Işık zamanı (2 yineleme), yıllık sapma, boylamda nütasyon (ana terimler); ekliptik ve ekinoks tarihin kendisi (tropikal zodyak), geosentrik |
| Hız / retro | ±6 saatlik (Ay: ±1,2 saat) sayısal türev |
| Ev sistemi | ASC/MC yerel yıldız zamanından; Placidus yarı-yay tanımının yinelemeli çözümü; |enlem| ≥ 66,56° ise Porphyry |
| Ay fazı | Ay–Güneş uzanımı; aydınlanma `(1 − cos ψ)/2`; sonraki evreler Newton yinelemesiyle |
| Yeni ay / dolunay | Ortalama lunasyon + kök bulma |
| Tutulma | Meeus *Astronomical Algorithms* bölüm 54 (γ, u): tam / halkalı / hibrit / parçalı Güneş; tam / parçalı / yarıgölge Ay |
| Burç geçişi | Günlük örnekleme + ikiye bölme (dakika altı) |
| İstasyon (retro başlangıç/bitiş) | Günlük hız işaret değişimi + hız üzerinde ikiye bölme |
| Doğum yeri | GeoNames cities15000 alt kümesi: Türkiye'de 15 000+ nüfuslu tüm yerler (432; 81 il dahil), yurt dışı Türk nüfusunun yoğun olduğu ülkelerde 100 000+, dünyada 250 000+ ve tüm başkentler; Türkçe adlar ve eksonimler (Londra, Köln, Lefkoşa…) |

## Doğruluk (`npm run test:engine`)

Referans: [astronomy-engine](https://github.com/cosinekitty/astronomy) 2.1.19 (VSOP87 / NOVAS tabanlı, JPL'e karşı ±1 yay dakikası). 1900–2100 arası 400 ayrı an:

| Cisim | Ortalama sapma | En büyük sapma |
| --- | --- | --- |
| Güneş | 0,14′ | 0,55′ |
| Ay | 1,28′ | 4,55′ |
| Merkür | 0,15′ | 0,88′ |
| Venüs | 0,17′ | 1,59′ |
| Mars | 0,40′ | 3,46′ |
| Jüpiter | 0,38′ | 1,59′ |
| Satürn | 0,79′ | 2,61′ |
| Uranüs | 0,71′ | 1,72′ |
| Neptün | 0,47′ | 1,45′ |
| Plüton | 0,38′ | 1,12′ |

Bir yay dakikası derecenin 1/60'ıdır; en büyük Ay sapması (4,5′) Ay'ın yaklaşık 8 dakikalık hareketine karşılık gelir. Burç, derece ve açı işleri için bu hassasiyet fazlasıyla yeterlidir.

2024–2027 olayları:

- Yeni ay ve dolunay zamanları (99 adet): en çok 6,7 dakika fark.
- Tutulmalar: 17 tutulmanın 16'sı aynı gün ve aynı türle bulundu. Bulunamayan tek tutulma 18 Temmuz 2027 yarıgölge Ay tutulmasıdır (gölgeye neredeyse hiç girmez, gözle fark edilmez); Meeus yöntemi bu kadar sınırda olayları atlayabiliyor. Takvim altında bu not yazılı.
- Retro istasyonları: 8 gezegenin tüm istasyonları doğru sırada ve türde, en çok 3,6 saat fark.
- Burç geçişleri: referans konum, bulduğumuz anda sınırdan en çok 0,45′ uzakta.
- Ay aydınlanması %0,1 içinde; İstanbul 15.05.1990 14:30 için ASC/MC bağımsız yıldız zamanıyla 0,003° içinde; Placidus evleri tanım denklemini 0,00003 hata ile sağlıyor; 1990 yaz saati (+03:00) doğru uygulanıyor.

Tarayıcıdan bağımsız bir örnek: 1 Ocak 2000 İzmir → Ay Akrep'te (gerçek gökyüzüyle uyumlu).

## Yorumlar nasıl üretiliyor?

Yorum metinleri `class-readings.php`'de; veri değil, veriye bağlı sembolik okumalardır:

- **Günlük burç:** Ay'ın güneş burcuna göre evi (12 metin) + gün içinde Ay burç değiştiriyorsa saati ve yeni ev + günün en sıkı gezegen açısı (gezegen alanları × açı tonu) + Güneş'in evi (dönem teması). Editör yorumu varsa o gösterilir.
- **Doğum haritası:** gezegen işlevi × burç üslubu × ev alanı; açılar için iki gezegen alanı × açı tonu; Güneş/Ay/Yükselen için 36 özel metin. Dış gezegenlerde "kuşak etkisi" notu.
- **Uyarılar:** saat bilinmiyorsa yükselen ve evler gösterilmez, Ay o gün burç değiştirdiyse bu söylenir; yükselen burç sınırına 1,5°'den yakınsa saat hassasiyeti uyarısı; kutup enlemlerinde ev sistemi notu.
- **Uyum:** iki güneş burcu arasındaki açı (7 ilişki) + element çifti (10) + ortak nitelik; kişisel sinastri olmadığı açıkça yazılı.

## Sınırlar

- Konumlar geosentriktir; topo-sentrik Ay paralaksı uygulanmaz (astrolojide yaygın uygulama).
- Kuzey Ay Düğümü ortalama düğümdür (gerçek düğüm değil); tabloda "Kuzey Ay Düğümü" olarak yer alır.
- 1900 öncesi ve 2100 sonrası tarihler kabul edilmez.
- 1970 öncesi saat dilimi kayıtları IANA veritabanında bazı ülkeler için belirsizdir; bu, tüm astroloji yazılımlarının ortak sınırıdır.
- Yer listesi 3 558 kayıttır; küçük ilçeler yoksa kullanıcıya bağlı olduğu ili seçmesi önerilir (birkaç km, haritayı anlamlı ölçüde değiştirmez). Daha geniş liste gerekiyorsa `tools/build-places.py` eşiği düşürülerek yeniden üretilebilir.
