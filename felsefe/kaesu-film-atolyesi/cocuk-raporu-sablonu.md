# Çocuk raporu · şablon ve yöntem

Kampı tamamlayan her çocuk için moderatöre (Genel Müdür) yazılan kısa rapor. Hedef 1,5–2 sayfa. Filya renkleri, logo yok.
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
- Varış 8. madde (izin) olduğu gibi yazılır. Hiçbir kutu işaretli değilse açıkça belirtilir. Çizgiye ad yazılıp kutu boş bırakıldıysa bu da yazılır. (İzin transkriptte kalır, rapora girmez.)
- Dosyanın başına bir özet tablosu konur (ölçekler, puanlar, izin).

## Adım 2 · Rapor metni (`Cocuk-Raporlari/<Ad>-rapor.md`)

Üretici bu yapıyı okur. Bölüm başlıkları aynen kalmalı; eksik bölüm ya da sınırı aşan madde sayısı varsa üretici durur.

```markdown
---
kamp_adi: <kamp adı>
yas: <yaş>
okul: <ilkokul | ortaokul | lise>
tamamlanan_gun: <0–5>
olcek_baslangic: A? B? C? D? E?
olcek_varis: A? B? C? D? E?
kaynak: Cocuk-Formlari/<Ad>/transkript.md
---

## Başlangıç ve varış

### Başlangıç
“<başlangıç cevabı, olduğu gibi>”

**Çünkü:** “<çünkü satırı>”

### Varış
“<varış 1. madde, olduğu gibi>”

### Ne değişti
<En fazla 3 cümle. Yalnızca yazılanlar karşılaştırılır; gerekirse çocuğun “Ne değişti, neden?” cevabından kısa alıntı.>

## Ölçek
<Tek cümle: hangi cümle değişti, hangisi aynı kaldı.>

## Gün gün

### 1 · <o günün atölye puanı>
- Cevap: “<kısa alıntı, gerekirse …>”
- Karıştıran: “<kısa alıntı>” ya da “Günün sorusunu aynen yazmış.”
- Gözlem: <tek kısa cümle>

### 2 · …
### 3 · …
### 4 · …
### 5 · …
(5. gün yaprağında kamp sorusu alanı yok: “Bu yaprakta kamp sorusu alanı yok; cevabı Varış'ta.”)

## Düşünme izleri
- **<kısa başlık>:** <tek cümle> “<kısa alıntı>”
(3–4 madde)

## Benim büyük sorum
**Sorusu:** “…”

**Şimdilik cevabı:** “…”

<1–2 cümle açıklama.>

## Güçlü yanlar
- <en fazla 3 madde, her biri yazılandan>
```

PDF: `~/.local/share/kaesu-gorsel-venv/bin/python gorseller/cocuk_rapor.py "<E:>/Cocuk-Raporlari/<Ad>-rapor.md" --onizleme "<E:>/Cocuk-Raporlari/_calisma/onizleme"`
PDF `.md` ile aynı klasöre yazılır. Her sayfa önizlemeden kontrol edilir.

## Düşünme izleri: neye bakılır

Gözlemler yöntem adı ya da kitap atfı vermeden, düz dille yazılır. Aşağıdakilerden çocukta en belirgin 3–4 tanesi seçilir:

- **Gerekçe:** “çünkü”, “eğer … olmazsa” diyor mu; fikrinin neden değiştiğini söylüyor mu?
- **Ayrım:** İki kavramı karşı karşıya koyuyor mu (“… mı, yoksa … mı?”)?
- **Örnek ya da karşı örnek:** Somut bir durum veriyor mu; bir genellemeyi çürüten bir örnek yazıyor mu?
- **Hayata taşıma:** Film dışından bir an, yer ya da kişi yazıyor mu; “hayattan bir soru” satırları dolu mu?
- **Soru üretme:** Kendi sorusunu mu yazmış, günün sorusunu mu aktarmış, atölyede sorulan bir soruyu mu yeniden kurmuş?

Bir sorunun atölyeden mi geldiğini anlamak için o günün modülündeki sorulara bakılır (`moduller/`). Atölyede sorulmuş bir soruyu çocuğun kendi sorusu gibi sunma.

## Yazım kuralları

- **Kısa.** Hedef 1,5–2 sayfa. Alıntılar kısa, gözlemler tek cümle.
- **Okur moderatör.** Sade, samimi dil. Resmî ve klinik dil yok. Yöntem terimi, kitap adı ve sayfa numarası yok.
- **Tanı koyma, psikolojik yorum yapma.** “Kaygılı”, “özgüvenli”, “içine kapanık” gibi nitelemeler yok. Davranış değil, yazı anlatılır.
- **Notlandırma ve karşılaştırma yok.** Rapor tek çocuğa bakar; başka çocukla kıyaslanmaz, düzey puanı verilmez.
- **Yalnızca yazılanlar.** Çocuğun yazmadığı bir şeyi ona söyletme. Okuma yorumu gerekiyorsa yazının kendisine bağlanır, çocuğun iç dünyasına değil.
- **Alıntılar düzeltilmez.** Kısaltma “…” ile gösterilir, `[?]` işaretleri korunur.
- **Yapay uyarı ve kollayıcı cümle yok.**
- **Parantez kuralı:** Filmden bir kişi, yer, sahne ya da kavram geçerse ardından kısa bir hatırlatıcı parantez.
- **Yaşa göre okuma:** Okul ve yaş kimlik şeridinde yazar. Küçük bir çocuk için kısa ya da boş satırlar eksik değil, o yaşın yazısıdır; kendi yazdıklarıyla okunur.
- **Başka çocuklar:** Bir çocuğun yazısında başka bir çocuğun kamp adı geçiyorsa, o cümle rapora alınmaz.
- **Raporda olmayanlar:** izin bölümü, moderatöre not, sonraki atölye için soru önerisi (Genel Müdür kararı, 2026-11-24).
