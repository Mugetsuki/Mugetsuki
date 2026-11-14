# Ölçme ve değerlendirme

Kaesu Akademi Felsefe Atölyesi · 5 günlük kamp · 11–17 yaş
Hazırlayan: Felsefe Departmanı (desk-8) · 2026-11-12 · Defter temelli sürüm: 2026-11-14
İlgili dosyalar: **[kamp-omurgasi.md](kamp-omurgasi.md)** · [moduller/](moduller/) · [moderator-kagidi.md](moderator-kagidi.md) · [senaryo-B-durdurmali.md](senaryo-B-durdurmali.md)

---

## Neyi doğrulamak istiyoruz?

Genel Müdür kamp sonunda üç şeyi doğrulamak istiyor:

- **(a) Sevdiler ve sıkılmadılar.**
- **(b) Bir şeyler öğrendiler ve bunu kendi sözleriyle anlatabiliyorlar.**
- **(c) Felsefenin hayatın her alanında olduğunu fark ettiler ve kamptan bu bilinçle ayrıldılar.**

Bu üç hedef, omurgadaki **A noktasından B noktasına** yolculuğun ölçülebilir hâlidir (bkz. [kamp-omurgasi.md](kamp-omurgasi.md), 2. bölüm).

## Üç araç

Eski sürümde yedi ayrı form vardı: iki anket, çıkış kartı, Felsefe Her Yerde kartı, kartpostal, gözlem formu ve veli onayı. Genel Müdür'ün “karmaşık” eleştirisi üzerine üçe indi. Eski formlar görsel kitten de kaldırıldı.

| Araç | Kim kullanır | Ne zaman | (a) Sevdi | (b) Öğrendi | (c) Her yerde |
|---|---|---|---|---|---|
| **1. Düşünce Yolculuğum defteri** | Her çocuk | 1. gün başı, her gün sonu (5 dk), 5. gün sonu | ● | ●● | ●● |
| **2. Gözlem formu ve rubrik** | Moderatör ya da gözlemci | Her tartışmada | ● | ●● | ● |
| **3. Veli onay formu** | Veli | Kamptan önce | | | |

Defter dört eski formun yerini aldı. **Ön ve son ölçüm aynı defterde** (Başlangıç ve Varış sayfaları). Bu yüzden kod ya da eşleştirme gerekmiyor.

### Kamp adı

Defterin kapağına çocuğun **kamp adı** yazılır (“Bulut”, “Koşan” gibi). Adlar farklı uzunlukta olduğu için kutu yok, tek bir uzun satır var. Kamp adının tek görevi defteri her sabah sahibine geri vermektir.

**Dikkat:** Kamp adı tam bir gizlilik sağlamaz, kamp personeli kimin hangi adı taşıdığını bilir. Bu yüzden:
- Raporda ve tanıtım materyallerinde kamp adı da kullanılmaz.
- Alıntılar yalnızca Varış sayfasındaki izin kutusuna göre kullanılır.

### Uygulama takvimi

| Ne zaman | Ne yapılır |
|---|---|
| Kamptan önce | Veli onay formu toplanır. Defter yaprakları (Başlangıç, 1.–5. gün, Varış) ve gözlem formları basılır. Her çocuğa bir A4 şeffaf kapaklı telli dosya hazırlanır. |
| 1. gün, filmden önce | Başlangıç sayfası (12 dk) |
| Her gün, tartışmanın son 5 dakikası | Defterin o güne ait bloğu |
| Her akşam | Moderatör defterleri toplar ve 10 dakikada okur. Ertesi sabah için 2–3 “hayattan soru” seçer. Gözlem formunu tamamlar. |
| 5. gün sonu | Varış sayfası (15 dk) ve paylaşım çemberi |
| Kamp sonrası, 1 hafta içinde | Veriler girilir, açık uçlu cevaplar kodlanır, rapor şablonu doldurulur. |

---

## Araç 1: Düşünce Yolculuğum defteri

Gün gün dağıtılan yapraklar: Başlangıç (ön ve arka), 1.–5. gün yaprakları (tek yüz) ve Varış (iki yaprak, üç yüz). Basılı hâli: tek tek `gorseller/pdf/defter-yaprak-*.pdf`, hepsi bir arada `gorseller/pdf/defter-dusunce-yolculugum.pdf`. Yedek film kullanılırsa o günün yaprağı yerine yedek filmin yaprağı dağıtılır.

### FORM: Başlangıç yaprağı (ön ve arka)

> **Düşünce Yolculuğum**
> Kamp adım: ______________________________ Yaşım: ____ ☐ Ortaokul ☐ Lise
>
> **Kamp sorusu: İyi bir hayat nedir?**
> Daha hiçbir film izlemeden, şu an ne düşünüyorsun? Doğru cevap yok.
>
> Bence iyi bir hayat ______________________________________
> çünkü ______________________________________
>
> **Bu cümlelere ne kadar katılıyorsun?** (1 = hiç katılmıyorum · 5 = tamamen katılıyorum)
>
> | | 1 | 2 | 3 | 4 | 5 |
> |---|---|---|---|---|---|
> | A. Zor soruların tek bir doğru cevabı vardır. | ☐ | ☐ | ☐ | ☐ | ☐ |
> | B. Fikrimi söylerken nedenini de söylerim. | ☐ | ☐ | ☐ | ☐ | ☐ |
> | C. İyi bir gerekçe duyarsam fikrimi değiştiririm. | ☐ | ☐ | ☐ | ☐ | ☐ |
> | D. Felsefe filozofların ve büyüklerin işidir. | ☐ | ☐ | ☐ | ☐ | ☐ |
> | E. Yemek, oyun, kurallar gibi sıradan şeylerin altında da büyük sorular vardır. | ☐ | ☐ | ☐ | ☐ | ☐ |
>
> **Günlük hayatımda felsefe** ______________________________ **karşıma çıkar.** (Bilmiyorsan “bilmiyorum” yaz.)

### FORM: Gün yaprakları (1.–5. gün, her biri tek yaprak)

> **N. gün · [film adı]** · Bugünün sorusu: [günün kavram sorusu]
>
> **Kamp sorusuna bugünkü cevabım:** İyi bir hayat ______________________________ çünkü ______________________________
> (Değişmediyse: “Değişmedi, çünkü …”)
>
> **Bugün kafamı en çok karıştıran soru:** ______________________________
>
> **Hayattan bir soru** (kampta yakaladığım): ______________________________
>
> **Bugün atölye:** 1 ○ 2 ○ 3 ○ 4 ○ 5 ○ (1 = hiç sevmedim · 5 = çok sevdim)

### FORM: Varış yaprağı (iki yaprak)

*5. gün yaprağında “kamp sorusuna bugünkü cevap” alanı yoktur. O cevap Varış'ta yazılır.*

> **Varış** · Önce Başlangıç yaprağını aç ve ilk gün ne yazdığını oku. Sesli okumak yok.
>
> 1. **Şimdi:** Bence iyi bir hayat ______________________________ çünkü ______________________________
> 2. **Ne değişti, neden?** (Değişmediyse neden değişmediğini yaz.) ______________________________
> 3. **Benim büyük sorum:** (Yapraklarındaki “kafamı karıştıran sorulardan” birini seçebilirsin.) ______________________________
>    **Şimdilik cevabım:** ______________________________ çünkü ______________________________
> 4. **Bu soru hayatımda nerede karşıma çıkıyor?** (Bir yer, bir an, bir kişi) ______________________________
> 5. **Kamp genel olarak:** 1 ○ 2 ○ 3 ○ 4 ○ 5 ○
> 6. **Değişmesini istediğim bir şey:** ______________________________
> 7. Aynı beş cümle (A–E) ve “Günlük hayatımda felsefe … karşıma çıkar.”
> 8. **Yazdıklarım kullanılabilir mi?** (Birini işaretle. Hiçbirini işaretlemezsen kullanılmaz.)
>    ☐ Hiçbir yerde kullanılmasın. ☐ Adım olmadan kullanılabilir. ☐ Adım ve yaşımla kullanılabilir. Adım: ________

### Puanlama ve kodlama

**Beş cümle (A–E):**
- 1–5 puanlanır. **A** ve **D** ters maddelerdir: ortalama alırken çevirin (1↔5, 2↔4).
- A, B ve C maddeleri (b) hedefine, D ve E maddeleri (c) hedefine bakar.

**Kodlama anahtarı 1: Kamp sorusuna cevap (Başlangıç, günlük bloklar, Varış).** (b) hedefinin ana ölçüsü budur.

| Düzey | Tanım | Örnek |
|---|---|---|
| 0 | Boş ya da tek kelime, gerekçe yok | “Mutluluk.” |
| 1 | Bir liste ya da tek bir değer, gerekçesiz ya da gerekçe tekrar | “Ailemle mutlu olmak, çünkü mutluluk güzel.” |
| 2 | Bir değer ve gerçek bir gerekçe | “Kendin olabildiğin bir hayat, çünkü başkası gibi yaşarsan o hayat senin olmaz.” |
| 3 | En az bir gerilimi adlandırıyor ya da koşul koyuyor | “Mutlu olmak önemli ama mutluluk gerçek bir şeye dayanmalı. Yoksa Truman gibi olursun.” |

**Kodlama anahtarı 2: “Günlük hayatımda felsefe … karşıma çıkar” ve “Bu soru hayatımda nerede?”** (c) hedefinin ana ölçüsü budur.

| Puan | Tanım | Örnek |
|---|---|---|
| 0 | Boş, “bilmiyorum”, “hiçbir yerde” | |
| 1 | Yalnızca okul, kitap, film ya da filozof | “Felsefe dersinde.” |
| 2 | **Film dışı bir hayat alanının adını veriyor**, örnek yok | “Arkadaşlıkta.” |
| 3 | **Film dışı bir hayat alanında somut bir durum ya da soru veriyor** | “Arkadaşım bana kızgın değilim dediğinde, gerçekten öyle mi diye düşünürken.” |

**“Film dışı bir hayat alanı yazdı” = 2 ya da 3 puan.**

**Kodlama anahtarı 3: “Ne değişti, neden?”**
- **D+ (değişti, gerekçeli):** Neyin değiştiğini ve nedenini söylüyor.
- **S+ (değişmedi, gerekçeli):** “Değişmedi, çünkü …” Bu da başarılı bir cevaptır.
- **G (gerekçesiz):** “Çok şey değişti”, “Bilmiyorum.”

**“Hayattan bir soru” satırları ve “Benim büyük sorum”:** Araç 2'deki soru kalitesi rubriğiyle 1–4 puanlanır. Kamp hayatından gelip 3 ya da 4 alan satır **nitelikli hayat sorusu** sayılır.

*Kodlamayı mümkünse iki kişi ayrı ayrı yapsın, anlaşamadıkları cevapları birlikte konuşsun.*

---

## Araç 2: Gözlem formu ve rubrik

Her tartışmada bir gözlem formu doldurulur. **Mümkünse ikinci bir yetişkin gözlemci** olsun ve yalnızca bu formu doldursun. Gözlemci yoksa kolaylaştırıcı yalnızca soru merdiveninin 10 dakikalık bir diliminde puanlama yapar ve her gün aynı dilimi seçer. Basılı hâli: `gorseller/pdf/form-gozlem.pdf` (ön yüz form, arka yüz rubrik).

### Rubrik 1: Soru kalitesi

“Soruların altındaki soru” adımındaki öğrenci soruları, defterdeki “kafamı karıştıran soru”, “hayattan bir soru” ve “büyük sorum” satırları bu rubrikle puanlanır.

| Düzey | Adı | Gözlemlenebilir tanım | Örnek |
|---|---|---|---|
| 1 | Film sorusu | Cevabı filmde var. Bir olayı ya da bilgiyi soruyor. | “Truman neden tekneye bindi?” |
| 2 | Görüş sorusu | Kişisel tercih ya da tahmin soruyor, bir kavramı tartışmaya açmıyor. | “Sen Truman olsan ne yapardın?” |
| 3 | Felsefi soru | Genel ve tartışmaya açık. Bir kavram ya da değer içeriyor (adil, gerçek, doğru, iyi). | “Birini korumak için ona yalan söylemek doğru mu?” |
| 4 | Derin felsefi soru | Bir kavramın ne olduğunu sorguluyor, iki kavramı karşılaştırıyor ya da bir varsayımı açığa çıkarıyor. | “Bir şeyin gerçek olduğunu bilmek için ne gerekir?” |

### Rubrik 2: Gerekçe düzeyi

Soru merdivenindeki her konuşma bir puan alır.

| Düzey | Adı | Gözlemlenebilir tanım | Örnek |
|---|---|---|---|
| 1 | Yalnızca iddia | Bir fikir söylüyor, nedenini söylemiyor. | “Bence yanlış.” |
| 2 | Tek gerekçe | “Çünkü” ile bir neden veriyor. | “Yanlış, çünkü yalan söylemek kötü.” |
| 3 | Gerekçe ve destek | Nedene bir örnek, filmden bir an ya da hayattan bir durum ekliyor. | “…çünkü Christof ona yalan söyledi ve Truman otuz yıl kendi hayatını yaşayamadı.” |
| 4 | Gerekçe ve karşı görüş | Başka birinin fikrine cevap veriyor, karşı örnek veriyor ya da fikrinin geçerli olduğu koşulu belirtiyor. | “Elif korumak için yalan söylenebilir dedi. Ama bence koruma da yalanı haklı çıkarmaz, çünkü…” |

### FORM: Gözlem formu (ön yüz)

> **Gözlem formu** · Gün: ① ② ③ ④ ⑤ · Model: ☐ B ☐ C · Gözlemci: ________
> Katılımcı sayısı: ____ · Gözlenen dilim: dk ____ – dk ____
>
> **1. Gerekçe düzeyi:** Her konuşmada ilgili satıra bir çentik. Düzey 1, 2, 3 ve 4 için ayrı satırlar, en altta “3 ve 4'ün oranı: %____”.
>
> **2. Soruların altındaki soru:** Okunan öğrenci soruları (kısaca) ve düzeyleri (1–4). En altta “3 ve 4'ün oranı: %____”.
>
> **3. Davranış göstergeleri** (çentik):
> - Başka bir öğrencinin fikrine atıf yaptı
> - Fikrini değiştirip nedenini söyledi
> - Filmden günlük hayata bağlantı kurdu
> - Moderatöre “Doğrusu ne?” diye sordu (A noktası göstergesi; günler içinde azalması beklenir)
>
> **4. Katılım:** En az bir kez konuşan öğrenci ____ / ____ (%____) · Ortaokullu ____ · Liseli ____
>
> **5. Kapanış ikilemi:** A ____ · B ____ · Ortada ____ · Gerekçeyle yer değiştiren ____
>
> **6. Duvara yazılanlar:** Bugün eklediğimiz parça ______ · Açık kalan soru ______
>
> **7. Not:** Dikkat çeken bir cümle (isimsiz), grubun enerjisi, değişmesi gereken bir şey.

---

## Araç 3: Veli onayı ve etik not

**1. Veli onayı (kamptan önce, kayıtla birlikte).**
- Katılımcılar 18 yaşından küçük olduğu için veliden yazılı onay alınır.
- Onay formu, kurumun KVKK (6698 sayılı Kişisel Verilerin Korunması Kanunu) aydınlatma metniyle birlikte verilir.
- Her izin ayrı bir kutudur. Tek bir “her şeye izin veriyorum” kutusu kullanılmaz.

Basılı hâli: `gorseller/pdf/form-veli-onay.pdf`

> **FORM: Veli onay formu (izin kutuları)**
> ☐ Çocuğumun defterdeki cevapları, **adı olmadan**, atölyenin değerlendirme raporunda kullanılabilir.
> ☐ Çocuğumun yazdığı ya da söylediği cümleler, **adı olmadan** (örneğin “13 yaşında bir katılımcı”), atölyeyi tanıtan materyallerde alıntılanabilir.
> ☐ Çocuğumun cümleleri **adı ve yaşıyla** alıntılanabilir. (Çocuğun kendisi de kamp sırasında ayrıca onay verecektir.)
> ☐ Çocuğumun **yüzünün göründüğü** fotoğrafları atölyeyi tanıtan materyallerde kullanılabilir.
> ☐ Çocuğumun **yüzünün görünmediği** fotoğrafları (arkadan, eller, kartlar) kullanılabilir.

**2. Çocuğun kendi onayı.** Veli izin verse de çocuk, Varış sayfasındaki kutuyla ayrıca karar verir. İkisinden biri “hayır” derse kullanılmaz. **Ad kullanımı için hem velinin hem çocuğun açık onayı gerekir.**

**3. Yönlendirmeyen sorular.**
- Defterdeki sorular öğrenmeyi varsaymaz: “Ne öğrendin?” değil, “Ne değişti, neden?” diye sorulur ve “değişmedi” de geçerli bir cevaptır.
- Olumsuz görüşe yer açılır: “Değişmesini istediğim bir şey.”
- Örnek cevap okunmaz.

**4. Fotoğraf.** Fotoğraf onayı alıntı onayından ayrıdır. Yüz fotoğrafına izni olmayan öğrencilere kamp başında renkli bir yaka kartı verilir. Defter ve duvar fotoğraflarında el yazısından kimse tanınmasın, kamp adı görünmesin.

**5. Alıntı kullanırken.**
- Cümle düzeltilmez, kısaltılırsa “…” ile gösterilir.
- Hassas ya da kişisel bir içerik (aile, sağlık, zorbalık deneyimi) izin olsa bile tanıtımda kullanılmaz.
- Yalnızca olumlu cümleler seçilmez.

**6. Saklama.** Defterler ve formlar kilitli bir yerde saklanır, kurumun saklama süresi dolunca imha edilir. İsteyen çocuğa defterinin bir kopyası verilebilir. Defter onun yolculuğunun kaydıdır.

---

## Başarı ölçütleri

**Nasıl okunur?** Her hedefin bir **ana ölçütü** ve birkaç **destek ölçütü** var.
- **Doğrulandı:** Ana ölçüt ve destek ölçütlerinin en az yarısı karşılandı.
- **Kısmen doğrulandı:** Ana ölçüt karşılandı ama destek ölçütlerinin yarısından azı karşılandı. Ya da ana ölçüt kıl payı kaçtı ama destek ölçütlerinin çoğu karşılandı.
- **Doğrulanamadı:** Ana ölçüt karşılanmadı ve destek ölçütlerinin çoğu da karşılanmadı.

**Eşikler hakkında:** Bu eşikler ilk kamp için önerilen tahminlerdir, elimizde önceki bir kampın verisi yok. İlk kamptan sonra gerçek sonuçlara göre ayarlanmalı.

### (a) Sevdiler ve sıkılmadılar

| | Ölçüt | Eşik | Kaynak |
|---|---|---|---|
| **Ana** | Varış: “Kamp genel olarak” sorusuna 4 ya da 5 veren oran | **≥ %80** | Defter |
| Destek | Günlük “Bugün atölye” ortalaması | Her gün **≥ 3,8**, beş günün ortalaması **≥ 4,0** | Defter |
| Destek | Günlük puana 1 ya da 2 veren oran | Her gün **≤ %15** | Defter |
| Destek | Tartışmada en az bir kez konuşan öğrenci oranı | Her gün **≥ %70** | Gözlem |

### (b) Öğrendiler ve kendi sözleriyle anlatabiliyorlar

| | Ölçüt | Eşik | Kaynak |
|---|---|---|---|
| **Ana** | Varış'taki cevabı kodlama anahtarı 1'de **2 ya da 3** alanların oranı | **≥ %70**, ayrıca öğrencilerin **en az %50'si** Başlangıç'a göre en az bir düzey yükselmiş olmalı | Defter |
| Destek | “Ne değişti, neden?” sorusuna gerekçeli cevap (D+ ya da S+) | **≥ %70** | Defter |
| Destek | Tartışmada 3–4. düzey gerekçe oranı | 5. günde 1. güne göre **en az 15 puan artış** | Gözlem |
| Destek | B ve C maddeleri ortalaması | Başlangıç'a göre **en az 0,3 artış** | Defter |
| Destek | A maddesi (“tek bir doğru cevap vardır”) ortalaması | Başlangıç'a göre **en az 0,5 düşüş** | Defter |

### (c) Felsefenin hayatın her yerinde olduğunu fark ettiler

| | Ölçüt | Eşik | Kaynak |
|---|---|---|---|
| **Ana** | Varış'ta “Günlük hayatımda felsefe … karşıma çıkar” cümlesine **film dışı bir hayat alanı** yazan oran (2 ya da 3 puan) | **≥ %70**, ayrıca Başlangıç'a göre **en az 20 puan artış** | Defter |
| Destek | “Bu soru hayatımda nerede?” sorusuna 2 ya da 3 puanlık cevap | **≥ %60** | Defter |
| Destek | “Hayattan bir soru” satırını en az 3 gün dolduran öğrenci oranı | **≥ %60** | Defter |
| Destek | En az bir nitelikli hayat sorusu (rubrikte 3–4) yazan öğrenci oranı | **≥ %50** | Defter |
| Destek | D (ters) ve E maddeleri ortalaması | Başlangıç'a göre **en az 0,5 artış** | Defter |

### Sonuçları yorumlarken dikkat

- **Grup küçük.** 20–30 kişilik bir grupta 2–3 öğrencinin cevabı oranı 10 puan değiştirebilir. Oranların yanında sayıyı da yazın: “%75 (18/24)”.
- **Karşılaştırma grubu yok.** Başlangıç ile Varış arasındaki fark kampın etkisine işaret eder ama kanıtlamaz. Raporda “işaret” ve “eğilim” deyin, “kanıtladı” demeyin.
- **Sosyal beğenirlik.** Çocuklar moderatörü memnun etmek için cevabını “değiştirmiş” gösterebilir. “Değişmedi, çünkü …” cevabının da değerli olduğunu her gün açıkça söylemek bunu azaltır.
- **Kamp sonu yorgunluğu.** Varış uzun bir günün sonunda yapılıyor. Mümkünse 5. gün filmden önce ya da ertesi sabah yapın ve zamanı raporda belirtin.

---

## Kamp sonu rapor şablonu

*Genel Müdür'ün doldurup kurumla paylaşacağı 1–2 sayfalık rapor. Köşeli parantez içindeki yerler doldurulacak.*

> # Kaesu Akademi Felsefe Atölyesi · Kamp sonu raporu
>
> **Kamp:** [tarih aralığı] · [yer]
> **Katılımcı:** [sayı] öğrenci · Ortaokul [sayı], lise [sayı] · Yaş aralığı [__–__]
> **Model:** ☐ B (durdurmalı) ☐ C (görevli izleme) ☐ Karma: [açıklama]
> **Kamp sorusu:** İyi bir hayat nedir?
> **Hazırlayan:** [ad] · **Tarih:** [tarih]
>
> ## Özet
> [Üç cümle: Kamp ne amaçladı, ne oldu, üç hedeften hangileri doğrulandı.]
>
> | Hedef | Sonuç |
> |---|---|
> | (a) Sevdiler ve sıkılmadılar | ☐ Doğrulandı ☐ Kısmen ☐ Doğrulanamadı |
> | (b) Öğrendiler, kendi sözleriyle anlatabiliyorlar | ☐ Doğrulandı ☐ Kısmen ☐ Doğrulanamadı |
> | (c) Felsefenin hayatın her yerinde olduğunu fark ettiler | ☐ Doğrulandı ☐ Kısmen ☐ Doğrulanamadı |
>
> ## Yolculuk: Başlangıç ve Varış
>
> | Ölçüt | Başlangıç | Varış | Eşik | ✓ / ✗ |
> |---|---|---|---|---|
> | Kamp sorusu cevabı 2–3. düzey | [ ] | [ ] | Varış ≥ %70 | |
> | En az bir düzey yükselen öğrenci | — | [ ] | ≥ %50 | |
> | “Günlük hayatımda felsefe” film dışı alan | [ ] | [ ] | ≥ %70, +20 puan | |
> | A maddesi ort. (tek doğru cevap) | [ ] | [ ] | −0,5 | |
> | B ve C maddeleri ort. | [ ] | [ ] | +0,3 | |
> | D (ters) ve E maddeleri ort. | [ ] | [ ] | +0,5 | |
>
> **Tipik bir yolculuk (izinli, isimsiz):** Başlangıç: “[…]” · Varış: “[…]”
>
> ## Günden güne
>
> | | 1. gün | 2. gün | 3. gün | 4. gün | 5. gün |
> |---|---|---|---|---|---|
> | Bugün atölye ort. (1–5) | | | | | |
> | Bugün atölyeye 1–2 veren (%) | | | | | |
> | Konuşan öğrenci (%) | | | | | |
> | Gerekçe 3–4. düzey (%) | | | | | |
> | Öğrenci soruları 3–4. düzey (%) | | | | | |
> | Hayattan soru yazan öğrenci (%) | | | | | |
> | Duvardaki parça | | | | | |
> | Duvardaki açık soru | | | | | |
>
> ## Öğrencilerin büyük soruları
> [5–8 örnek, izinli ve isimsiz. Yanında “hayatımda nerede” cevabı.]
>
> ## Kolaylaştırıcı gözlemleri
> [En iyi işleyen an · Zorlanılan an · Takip gereken bir durum olduysa (isimsiz) nasıl ele alındığı]
>
> ## Öğrencilerin değiştirmek istedikleri
> [Varış'taki “Değişmesini istediğim bir şey” cevaplarından öne çıkan 3 konu]
>
> ## Sınırlılıklar
> Grup küçük ve karşılaştırma grubu yok. Sonuçlar kampın etkisine işaret eder ama kesin kanıt değildir. [Varış'ın yapıldığı zaman, eksik defter sayısı vb.]
>
> ## Bir sonraki kamp için öneriler
> 1. [ ]
> 2. [ ]
> 3. [ ]
>
> **Ekler:** Yolculuk Duvarı'nın fotoğrafı · Boş defter örneği
