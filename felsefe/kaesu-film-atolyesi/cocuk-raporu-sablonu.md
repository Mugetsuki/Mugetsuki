# Çocuk raporu · şablon ve yöntem

Kampı tamamlayan her çocuk için moderatöre (Genel Müdür) yazılan 2–3 sayfalık rapor. Filya renkleri, logo yok.
Üretici: `gorseller/cocuk_rapor.py` · Stil: `gorseller/filya.css` (“Çocuk raporu” bloğu).

## Gizlilik (kesin kural)

- Çocukların yazdıkları, transkriptler, rapor metinleri, rapor PDF'leri ve önizlemeler **git'e girmez**.
- Hepsi yalnızca `E:\Users\pc\Kaesu-Felsefe-Atolyesi\` altında durur:
  - `Cocuk-Formlari\<Ad>\01…10-*.jpg` ve `Cocuk-Formlari\<Ad>\transkript.md`
  - `Cocuk-Raporlari\<Ad>-rapor.md` ve `Cocuk-Raporlari\<Ad>-rapor.pdf`
  - Önizleme ve kırpıntılar: `Cocuk-Raporlari\_calisma\` (iş bitince silinebilir)
- Git'te yalnızca bu şablon, üretici ve stil var. `.gitignore` çocuk verisi kalıplarını dışarıda tutar.
- Raporlarda kamp adı kullanılır, gerçek ad yazılmaz.

## Adım 1 · Transkript (`Cocuk-Formlari/<Ad>/transkript.md`)

Defterin 10 fotoğrafı sırayla: 01 başlangıç ön, 02 başlangıç arka, 03–07 gün 1–5, 08 varış 1, 09 varış 2 (büyük soru), 10 varış 3 (değerlendirme).

- El yazısı **olduğu gibi** aktarılır. Yazım hatası düzeltilmez.
- Okunamayan yer: `[okunamadı]`. Emin olunmayan kelime: kelimeden sonra `[?]`.
- Silinmiş ya da üstü çizili yazı ayrıca belirtilir: okunabiliyorsa “(silinmiş, okunabilen: …)”.
- Günün sorusu aynen yazılmışsa not düşülür: “(Günün sorusunun aynısı.)”
- Ölçekler: başlangıç A–E, varış A–E, her günün “atölye nasıldı” puanı, kamp genel puanı. Ölçek dışına çizilen işaretler (kendi yuvarlağı, “∞” gibi) olduğu gibi tarif edilir.
- Varış 8. madde (izin) olduğu gibi yazılır. Hiçbir kutu işaretli değilse açıkça belirtilir: formdaki kurala göre kullanılmaz. Çizgiye ad yazılıp kutu boş bırakıldıysa bu da yazılır.
- Dosyanın başına bir özet tablosu konur (ölçekler, puanlar, izin).

## Adım 2 · Rapor metni (`Cocuk-Raporlari/<Ad>-rapor.md`)

Üretici bu yapıyı okur. Bölüm başlıkları aynen kalmalı (eksik bölüm varsa üretici durur).

```markdown
---
kamp_adi: <kamp adı>
yas: <yaş>
okul: <ilkokul | ortaokul | lise>
tamamlanan_gun: <0–5>
olcek_baslangic: A? B? C? D? E?
olcek_varis: A? B? C? D? E?
izin: <ad-yas | adsiz | yok | isaretsiz>
kaynak: Cocuk-Formlari/<Ad>/transkript.md
---

## Başlangıç ve varış

### Başlangıç
“<başlangıç cevabı, olduğu gibi>”

**Çünkü:** “<çünkü satırı>”

### Varış
“<varış 1. madde, olduğu gibi>”

### Ne değişti
<1–2 paragraf. Yalnızca yazılanlar karşılaştırılır. Çocuğun “Ne değişti, neden?” cevabı alıntılanır.>

## Ölçek
<1–2 cümle: hangi cümle değişti, hangisi aynı kaldı. Yorum yok, sayı var.>

## Gün gün

### 1 · <o günün atölye puanı>
- Cevap: “<kısa alıntı, gerekirse …>”
- Karıştıran: “<kısa alıntı>” ya da “Günün sorusunu aynen yazmış.”
- Gözlem: <tek satır>

### 2 · …
### 3 · …
### 4 · …
### 5 · …
(5. gün yaprağında kamp sorusu alanı yok: “Bu yaprakta kamp sorusu alanı yok; cevabı Varış'ta.”)

## Düşünme izleri

### Gerekçe kuruyor mu?
### Ayrım yapıyor mu?
### Örnek ya da karşı örnek veriyor mu?
### Kendi hayatına taşıyor mu?
### Soru üretiyor mu?
(Her biri 2–4 cümle, her gözlem bir alıntıyla.)

## Benim büyük sorum
**Sorusu:** “…”

**Şimdilik cevabı:** “…”

<1 kısa paragraf: soru nereden geliyor, cevap neye bağlı.>

## Güçlü yanlar
- <3 madde, her biri yazılandan>

## Sorulabilecek iki soru
1. “<çocuğun kendi cümlesinden çıkan soru>” <kaynağı ve Worley aracı>
2. “…”

## Moderatöre not
- <1–2 madde>

## İzin
**Seçimi:** “<8. maddedeki seçim>”

<Buna göre alıntı nerede kullanılabilir.>

**Tanıtımda kullanılabilir:** …

**Rapor içinde kalsın:** …
```

PDF: `~/.local/share/kaesu-gorsel-venv/bin/python gorseller/cocuk_rapor.py "<E:>/Cocuk-Raporlari/<Ad>-rapor.md" --onizleme "<E:>/Cocuk-Raporlari/_calisma/onizleme"`
PDF `.md` ile aynı klasöre yazılır. Hedef 2–3 sayfa; her sayfa önizlemeden kontrol edilir.

## Gözlem dili: Worley araçları

Düşünme izleri Felsefe Makinesi'nin araçlarıyla okunur (sayfa numaraları kitabın Türkçe baskısı, bkz. `felsefe-makinesi-yontem.md`):

| İz | Neye bakılır | Worley aracı |
|---|---|---|
| Gerekçe | “çünkü”, “eğer … o zaman”, fikrinin neden değiştiğini söylemesi | Gerekçe düzeyi rubriği (ölçme ve değerlendirme, Araç 2) |
| Ayrım | İki kavramı karşı karşıya koyması, “… mı, yoksa … mı?” | Ya… ya da… (s. 59–60), ikili karşıtlıklar (s. 56) |
| Örnek / karşı örnek | Somut durum vermesi; bir genellemeyi çürüten örnek | Yanlışlama ve karşı örnekler (s. 51) |
| Hayata taşıma | Film dışı bir an, yer, kişi; “hayattan bir soru” satırları | Kodlama anahtarı 2 (ölçme ve değerlendirme) |
| Soru üretme | Kendi sorusunu mu yazmış, günün sorusunu mu aktarmış, atölyedeki soruyu mu yeniden kurmuş | Soru kalitesi rubriği (ölçme ve değerlendirme, Araç 2) |

Bir sorunun atölyeden mi geldiğini anlamak için o günün modülündeki Açık Soru'lara bakılır (`moduller/`). Atölyede sorulmuş bir soruyu çocuğun kendi sorusu gibi sunma.

## Yazım kuralları

- **Okur moderatör.** Sade, samimi dil. Resmî ve klinik dil yok.
- **Tanı koyma, psikolojik yorum yapma.** “Kaygılı”, “özgüvenli”, “içine kapanık” gibi nitelemeler yok. Davranış değil, yazı anlatılır.
- **Notlandırma ve karşılaştırma yok.** Rapor tek çocuğa bakar; başka çocukla kıyaslanmaz, düzey puanı verilmez.
- **Yalnızca yazılanlar.** Çocuğun yazmadığı bir şeyi ona söyletme. Okuma yorumu gerekiyorsa yazının kendisine bağlanır, çocuğun iç dünyasına değil.
- **Alıntılar düzeltilmez.** Kısaltma “…” ile gösterilir, `[?]` işaretleri korunur.
- **Yapay uyarı ve kollayıcı cümle yok.**
- **Parantez kuralı:** Filmden bir kişi, yer, sahne ya da kavram geçerse ardından tek cümlelik hatırlatıcı parantez.
- **Yaşa göre okuma:** Okul ve yaş kimlik şeridinde yazar. Küçük bir çocuk için kısa ya da boş satırlar eksik değil, o yaşın yazısıdır; aynı ölçütle değil, kendi yazdıklarıyla okunur.
- **İzin:** Varış 8. maddeye göre. Ad kullanımı için veli onay formunda da aynı izin gerekir (ölçme ve değerlendirme, Araç 3). Hassas ya da kişisel içerik izin olsa da tanıtımda kullanılmaz.
- **Başka çocuklar:** Bir çocuğun yazısında başka bir çocuğun kamp adı geçiyorsa, o cümle yalnızca rapor içinde kalır.
