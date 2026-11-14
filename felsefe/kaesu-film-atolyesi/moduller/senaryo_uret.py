#!/usr/bin/env python3
"""Günlük modüllerden (gunN-*.md) iki uygulama senaryosunu üretir:

  ../senaryo-C-gorevli-izleme.md   Model C: not kartı, 0–2 yazma durağı
  ../senaryo-B-durdurmali.md       Model B: 2–3 konuşmalı durak, sözlü izleme görevi

İçeriğin kaynağı modüllerdir. Senaryolar elle düzenlenmez; modül değişince bu betik yeniden çalıştırılır.
Kullanım: python3 senaryo_uret.py
"""
import pathlib
import re

HERE = pathlib.Path(__file__).resolve().parent
ROOT = HERE.parent
MODULLER = ["gun1-truman.md", "gun2-ters-yuz-2.md", "gun3-mucize.md", "gun4-zootropolis.md", "gun5-soul.md"]

# Günlerin zaman dilimleri: (ad, dakika). "FILM" yer tutucusu duraklarla birlikte filmi temsil eder.
GUNLER = {
    1: dict(film=103, oyun="“Christof der ki”", acilis=[("Hoş geldiniz, topluluk kuralları", 5), ("Başlangıç sayfası: kamp sorusu ve ilk cevap", 12),
                                                        ("*The Present* ve “soruların altındaki soru”", 7), ("İzleme görevi", 3)], son=[("Köprü ve hayattan soru görevi", 3)]),
    2: dict(film=96, oyun="“Duygu heykelleri”", acilis=[("Dünkü gün kartı", 4), ("Hayattan sorular", 5), ("Hassas içerik uyarısı", 2), ("İzleme görevi", 4)],
            son=[("Köprü ve hayattan soru görevi", 3)]),
    3: dict(film=113, oyun="“Ayna”", acilis=[("Dünkü gün kartı", 4), ("Hayattan sorular", 5), ("Hassas içerik uyarısı", 2), ("İzleme görevi", 4)],
            son=[("Köprü ve hayattan soru görevi", 3)]),
    4: dict(film=108, oyun="“Gruplan!”", acilis=[("Dünkü gün kartı", 4), ("Hayattan sorular", 5), ("İzleme görevi ve “önyargı” kavramı", 6)],
            son=[("Köprü ve hayattan soru görevi", 3)]),
    5: dict(film=100, oyun="“Kıvılcım”", acilis=[("Dünkü gün kartı", 4), ("Hayattan sorular", 4), ("Yolculuk Duvarı yürüyüşü", 4), ("Hassas içerik notu", 1), ("İzleme görevi", 2)],
            son=[("Varış ve paylaşım çemberi", 25)]),
}
DURAK = {"C": {1: 2, 2: 1, 3: 1, 4: 1, 5: 0}, "B": {1: 3, 2: 2, 3: 3, 4: 3, 5: 2}}
DURAK_DK = {"C": 5, "B": 6}

OYUNLAR = {
    1: "“Herkes çemberde ayağa kalksın. Ben ‘Christof der ki’ diye başlarsam yaparsınız, başlamazsam yapmazsınız. Yapan oturur. Christof der ki elinizi kaldırın. Christof der ki tek ayak üstünde durun. Zıplayın!” *Bitirirken:* “Bir dakikalığına hepiniz Christof'un (Truman'ın hayatını yöneten yapımcı) dediğini yaptınız. Truman da otuz yıl öyle yaptı.”",
    2: "“Herkes ayağa! Bir duygunun adını söyleyeceğim, o duygunun heykeli olup donacaksınız. Neşe! … Can Sıkıntısı! … Kaygı! … Şimdi zor olanı: Neşe ve Kaygı aynı anda!” *Bitirirken:* “Aynı anda iki duygu zor ama mümkünmüş. Riley (filmin 13 yaşındaki kahramanı) de öyleydi.”",
    3: "“İkili olun, karşı karşıya durun. Biriniz yavaşça hareket ediyor, diğeri onun aynası oluyor. Konuşmak yok. … Şimdi değişin.” *Bitirirken:* “Başkasının hareketini taklit etmek için ona çok dikkatli bakmanız gerekti. Bugünkü film de herkesi başka birinin gözünden anlatıyordu.”",
    4: "“Herkes ayağa. Söylediğim şeye göre hızla gruplanın: Doğduğunuz mevsim! … Sabah mı akşam mı insanısınız? … Tatlı mı tuzlu mu?” *Hassas kategoriler (din, köken, cinsiyet, beden) kullanmayın. Bitirirken:* “Her seferinde başka bir grupta yer aldınız. Kimse tek bir gruptan ibaret değil.”",
    5: "“Çember olun. Yanımdakine el çırparak bir ‘kıvılcım’ gönderiyorum, o da yanındakine. … Daha hızlı! … Biri ‘Dön!’ derse yön değişiyor.” *Bitirirken:* “Filmde ruhlar Dünya'ya gitmek için bir kıvılcım (bir ruhun Dünya'ya gidebilmesi için bulması gereken şey) arıyordu. Bakalım kıvılcım neymiş.”",
}

KURALLAR_GUN1 = """## Hoş geldiniz ve topluluk kuralları (5 dk)

“Hoş geldiniz! Bu kampta beş gün boyunca beş film izleyeceğiz. Ama sadece film izlemeyeceğiz. Filmlerin aklımıza getirdiği büyük soruları birlikte düşüneceğiz. Benim size vereceğim hazır bir cevap yok. Ben soru soracağım, siz düşüneceksiniz.”

“Dört kuralımız var:
1. Aynı anda tek kişi konuşur. Bu top kimdeyse o konuşur.
2. Kişiye değil, fikre itiraz ederiz.
3. Konuşurken ‘Katılıyorum, çünkü…’ ya da ‘Katılmıyorum, çünkü…’ diye başlarız. ‘Çünkü’ en önemli kelimemiz.
4. Pas geçmek serbest. Kimse konuşmak zorunda değil.”

*Defterleri dağıtın. Herkes kapağa kamp adını yazsın:* “Bu defter beş gün boyunca sizinle. Adı **Düşünce Yolculuğum.** Kimse buna not vermeyecek, yanlış diye bir şey yok. Her akşam ben toplayacağım, sabah geri vereceğim.”

"""

GENEL = {
    "C": dict(
        baslik="# Uygulama senaryosu C: Görevli izleme",
        oz="""## Model C'nin özü

Filmden önce öğrencilere bir **izleme görevi** verilir. Her öğrencinin elinde günün **not kartı** vardır: üstte görev, altta “Aklıma takılan an” ve “Benim sorum”. Film her gün en fazla bir kez kısa süre durur (1. gün iki kez, 5. gün hiç). Durakta konuşulmaz: iki dakika sessiz yazılır, tek bir kısa soru sorulur, iki üç kişi tek cümleyle cevap verir. Film bitince tartışma, kartlardaki sorulardan “soruların altındaki soru”ya ve oradan soru merdivenine geçer.""",
        malzeme_ek="| Günün not kartı (öğrenci başına 1 + yedek) | Üstte izleme görevi, altta “Aklıma takılan an” ve “Benim sorum”. Görseller: `gorseller/pdf/gunN-…-not-karti.pdf` |\n| Kalem ve sert altlık | Karanlıkta yazmak için. Oda tam karartılmasın. |\n",
        alt_soru="Kartlardan 4–5 soru okunur.",
    ),
    "B": dict(
        baslik="# Uygulama senaryosu B: Durdurmalı izleme",
        oz="""## Model B'nin özü

Film her gün önceden belirlenmiş **2 ya da 3 noktada** durdurulur. Her durakta siz bir kısa soru sorarsınız, 3–4 öğrenci cevap verir, cevaplar tahtaya tek satırla yazılır ve film devam eder. Her durak yaklaşık **6 dakika** sürer. Öğrencilerin elinde not kartı yoktur, izleme görevi sözlü verilir. Film bitince tartışma, tahtadaki durak cevaplarından “soruların altındaki soru”ya ve oradan soru merdivenine geçer.

**Duraklar için altın kural:** Durak tartışma değildir. Bir soru, 3–4 cevap, tahtaya not, devam. Durak 7 dakikayı geçerse grup filmden kopar. Öğrenci uzatırsa: “Çok güzel bir nokta. Tahtaya yazıyorum, film bitince ilk buna döneceğiz.”""",
        malzeme_ek="| Yardımcı bir yetişkin (mümkünse) | Duraklarda cevapları tahtaya yazar, ışıkları açıp kapatır. |\n",
        alt_soru="Tahtadaki durak cevaplarına bakılır: “Film bitti, hâlâ öyle mi düşünüyorsunuz?”",
    ),
}

GIRIS = """Kaesu Akademi Felsefe Atölyesi · 5 günlük kamp · 11–17 yaş, karışık grup
Hazırlayan: Felsefe Departmanı (desk-8) · 2026-11-14 · Bu dosya `moduller/senaryo_uret.py` ile günlük modüllerden üretilir, elle düzenlenmez.
İlgili dosyalar: **[kamp-omurgasi.md](kamp-omurgasi.md) (kampın kalbi)** · [moduller/](moduller/) (günlük modüller) · [{diger}]({diger}) (diğer senaryo) · [moderator-kagidi.md](moderator-kagidi.md) (moderatör kitapçığı) · [olcme-degerlendirme.md](olcme-degerlendirme.md) (defter ve ölçme)

---

## Bu senaryo nasıl okunur?

- Metni elinizde tutup sırayla okuyarak atölyeyi yönetebilirsiniz.
- **Tırnak içindeki metinler** öğrencilere söyleyeceğiniz cümlelerdir. *Eğik yazılar* sizin için nottur.
- Filmden geçen yer, kişi ve kavramların yanında kısa bir hatırlatma parantezi vardır. Filmi izlememiş biri de soruyu anlasın diye. Öğrencilere okurken parantezi atlayabilirsiniz.
- Saatler günün başından itibaren sayılır. Film süreleri jenerik dahildir.
- **Durma noktaları dakika olarak değil, sahne tarifiyle verildi.** Kamptan önce her filmi bir kez izleyip durak yerlerini oynatıcınızda işaretleyin.

**Kamp sorusu:** İyi bir hayat nedir? Her gün, çocukların ilk gün sık verdiği bir cevabı sınar: mutlu olmak, kendin olmak, iyi biri olmak, herkesin eşit olması, hayallerine ulaşmak.

{oz}

## Hazırlık ve malzeme (her gün)

| Malzeme | Not |
|---|---|
| **Düşünce Yolculuğum** defteri (öğrenci başına 1) | Kapağında yalnızca kamp adı. Her akşam toplanır, sabah dağıtılır. Not vermek ya da düzeltmek için okunmaz. `gorseller/pdf/defter-dusunce-yolculugum.pdf` |
{malzeme_ek}| Konuşma nesnesi | Küçük top ya da peluş. Elinde olan konuşur. |
| İki duvar tabelası: **A** ve **B** | Kapanış ikilemi için. 5. gün yerine beş köşe kartı. |
| **Yolculuk Duvarı** | Ortada kamp sorusu, etrafında beş gün kartı. Her kartta üç satır: “Bugün eklediğimiz parça”, “Açık kalan soru”, “Hayattan yakaladığımız soru”. Kalın kalem. |
| Tahta ya da büyük kâğıt | Soruları yazmak için. Günün kavram sorusu en üstte kalır. |
| Moderatör kitapçığı ve gözlem formu | `gorseller/pdf/moderator-kitapcigi.pdf` · `gorseller/pdf/form-gozlem.pdf` |

**Oturma düzeni:** Film için sinema düzeni, tartışma için çember. Çemberde ortaokullu ve liseli karışık otursun.

**Her akşam (10 dk):** Defterleri okuyun, ertesi sabah okunacak 2–3 “hayattan soru” seçin, gözlem formunu tamamlayın.

## Her gün geçerli: hassas paylaşım protokolü

1. Paylaşan öğrenciye teşekkür edin: “Bunu bizimle paylaştığın için teşekkür ederim.”
2. Soruyu kişiden alıp genele çevirin: “Bir insan böyle bir şey yaşadığında ne hisseder sizce?”
3. Kimse kişisel bir şey anlatmaya zorlanmaz. Grubun başkasının paylaşımına gülmesine izin vermeyin.
4. Oturumdan sonra o öğrenciyle birebir, kısa konuşun: “Biraz önce anlattıkların aklımda kaldı. Nasılsın?”
5. **Öğrenci kendine ya da başkasına zarar verme, istismar ya da ciddi bir tehlike anlatırsa bunu gizli tutmazsınız.** Aynı gün kampın sorumlu yetkilisine ve varsa rehber öğretmene ya da psikolojik danışmana bildirin. Acil durumda 112. Öğrenciye bunu dürüstçe söyleyin: “Senin güvende olman benim için önemli, bu yüzden bunu yardım edebilecek biriyle paylaşmam gerekiyor.”

## 60 dakikalık tartışmanın akışı (1–4. gün)

| Dakika | Adım | Özet |
|---|---|---|
| 0–5 | İlk tepki | Tek kelime ya da cümle. Pas serbest. |
| 5–12 | Soruların altındaki soru | {alt_soru} Film soruları insan sorusuna çevrilir ve altlarındaki ortak soru, yani günün kavram sorusu gösterilir. |
| 12–42 | Soru merdiveni | Dört basamak. En az bir düşünce deneyi. Öğrenci şunu derse, siz şunu sorarsınız. |
| 42–50 | Kapanış ikilemi (hareketli) | İki duvar. Her iki tarafa da bir “ters köşe” sorusu. |
| 50–55 | Kamp sorusuna dönüş | Grubun bugün eklediği parça ve açık kalan soru gün kartına yazılır. |
| 55–60 | Defter | Sessiz, günlük blok. |

Ardından **3 dk kapanış:** köprü cümlesi ve hayattan soru görevi. 5. gün farklıdır: merdivenden sonra Beş Kapı ve Varış.

---
"""

BC_NOTU = """# B mi, C mi? Hangisini ne zaman seçmeli?

*Bu not iki senaryo dosyasında da aynıdır.*

**Temel fark:** İki modelde de film sonrası tartışma aynıdır: soruların altındaki soru, soru merdiveni, ikilem, kamp sorusuna dönüş ve defter. Fark film sırasındadır.
- **B'de** film 2–3 kez durur. Her durakta siz bir soru sorarsınız ve kısa bir sohbet olur.
- **C'de** öğrencinin elinde bir not kartı vardır. Film en fazla bir kez durur, durakta konuşulmaz, yazılır. Tartışma öğrencilerin kendi kart sorularından başlar.

| Durum | Seçim |
|---|---|
| Grup P4C'yi ilk kez deneyecek, siz de kolaylaştırıcılıkta yenisiniz | **B** ile başlayın. Duraklarda grubu tanırsınız. |
| Grupta 11–12 yaş ağırlıkta, dikkat süresi kısa | **B**. Sık duraklar dikkati tazeler. |
| Amaç öğrencilerin kendi sorularını sormayı öğrenmesi | **C**. Soru sorma becerisi ancak öğrenci soru yazdığında gelişir. |
| Grup lise ağırlıklı | **C**. Liseliler sık durdurulmayı “okul dersi” gibi bulur. |
| Film duygusal ya da gerilimli (Ters Yüz 2, Soul) | **C**. Duraklar filmin etkisini bozar. |
| Gün uzun, zaman dar | **C**. B güne 7–13 dk ekler. |

**Karma yol:** İlk iki günü B ile, son üç günü C ile yapabilirsiniz. Geçiş günü not kartını ve izleme görevini 3 dakikayla tanıtın.

**Önerim:** **C**, çünkü kampın B noktası (“sıradan şeylerin altında da soru var”) çocuğun kendi sorusunu yazmasına dayanıyor. B'yi, kolaylaştırıcı yeniyse ya da grup çok küçük yaş ağırlıklıysa seçin.
"""


def sections(md):
    """'## ' başlıklarına göre böler. [(başlık, gövde)]"""
    parts = re.split(r"(?m)^## ", md)
    head = parts[0]
    return head, [(p.split("\n", 1)[0].strip(), p.split("\n", 1)[1] if "\n" in p else "") for p in parts[1:]]


def saat(dk):
    return f"{dk // 60:02d}:{dk % 60:02d}"


def akis(n, model):
    g = GUNLER[n]
    rows, t = [], 0
    for ad, d in g["acilis"]:
        rows.append((f"{saat(t)}–{saat(t + d)}", f"{d} dk", ad)); t += d
    k = DURAK[model][n]; dd = DURAK_DK[model]
    film = g["film"] + k * dd
    durak_ad = "durak yok" if k == 0 else (f"{k} yazma durağı, her biri {dd} dk" if model == "C" else f"{k} konuşmalı durak, her biri {dd} dk")
    rows.append((f"{saat(t)}–{saat(t + film)}", f"{g['film']} + {k * dd} dk" if k else f"{g['film']} dk", f"Film ({durak_ad})")); t += film
    for ad, d in [(f"Ara (son 3 dakikası enerji oyunu {g['oyun']})", 10), ("Tartışma", 60)] + g["son"]:
        rows.append((f"{saat(t)}–{saat(t + d)}", f"{d} dk", ad)); t += d
    tablo = "| Saat | Süre | Bölüm |\n|---|---|---|\n" + "".join(f"| {a} | {b} | {c} |\n" for a, b, c in rows)
    return tablo, t


def model_filtre(text, model):
    other = "B" if model == "C" else "C"
    text = re.sub(rf"(?m)^- \*\*{other}( \([^)]*\))?:\*\*[^\n]*\n", "", text)
    text = re.sub(rf"(?m)^- \*\*{model}( \([^)]*\))?:\*\* ", "- ", text)
    for x, y in (("- Not kartının üstünde yazar.", "*Görev not kartının üstünde yazar. Kartları dağıtın.*"),
                 ("- Görev not kartının üstünde yazar.", "*Görev not kartının üstünde yazar. Kartları dağıtın.*"),
                 ("- Sözlü verilir.", "*Görev sözlü verilir, not kartı yok.*"),
                 ("- Görev sözlü verilir.", "*Görev sözlü verilir, not kartı yok.*")):
        text = text.replace(x, y)
    # İki modeli birlikte anlatan cümleler
    if model == "C":
        text = text.replace("C'de kartlardan, B'de tahtadaki durak cevaplarından 4–5 soru okunur.", "Kartlardan 4–5 soru okunur.")
        text = text.replace("C'de 4–5 öğrenci kartından birer soru okur. B'de tahtadaki durak cevaplarına bakılır.", "4–5 öğrenci kartından birer soru okur.")
        text = text.replace(" C: not kartı, 0–2 kısa yazma durağı. B: 2–3 konuşmalı durak.", "")
    else:
        text = text.replace("C'de kartlardan, B'de tahtadaki durak cevaplarından 4–5 soru okunur.", "Tahtadaki durak cevaplarına bakılır: “Film bitti, hâlâ öyle mi düşünüyorsunuz?” Sonra 4–5 soru seçilir.")
        text = text.replace("C'de 4–5 öğrenci kartından birer soru okur. B'de tahtadaki durak cevaplarına bakılır.", "Tahtadaki durak cevaplarına bakılır: “Film bitti, hâlâ öyle mi düşünüyorsunuz?”")
    return text


def duraklar(govde, model):
    """Modüldeki B/C durak tablosunu modele özgü alt başlıklara çevirir."""
    lines = govde.split("\n")
    rows = [l for l in lines if l.startswith("| **")]
    rest = [l for l in lines if not l.startswith("|")]
    out, k = [], 0
    for r in rows:
        cells = [c.strip() for c in r.strip().strip("|").split(" | ")]
        sahne, c_cell, b_cell = cells[0], cells[1], cells[2]
        cell = c_cell if model == "C" else b_cell
        if cell.startswith("Durak yok") or cell.startswith("Film kesintisiz"):
            continue
        if cell.startswith("Aynı"):
            src = b_cell if model == "C" else c_cell
            if model == "B":
                m = re.search(r"\*\*Sor[^*]*:\*\* (“.*”)", src)
                pre = src.split("**Yaz:**")[0].strip()
                cell = ((pre + " ") if pre and not pre.startswith("**Yaz") else "") + (m.group(1) if m else src)
            else:
                cell = src
        if cell.startswith("Grup çok yorgunsa"):
            continue
        k += 1
        sure = f"{DURAK_DK[model]} dk"
        nasil = ("*Önce 2 dakika sessiz yazma, sonra 2–3 kısa cevap, 1 dakika su ve tuvalet. Tartışma açılmaz. Uzatan olursa:* “Kartına yaz, tartışmada buna döneceğiz.”"
                 if model == "C" else "*Önce yarım dakika düşünme, sonra 3–4 cevap. Yardımcı her cevabı tahtaya tek satırla yazar. Durak 7 dakikayı geçmesin.*")
        out.append(f"### Durak {k}: {sahne.replace('**', '')}\n\n**Süre:** {sure}\n\n{cell}\n\n{nasil}\n")
    if not out:
        out.append("Bugün film **kesintisiz** izlenir. Grup çok yorgunsa isteğe bağlı tek durak: B senaryosundaki 2. durak.\n")
    ek = "\n".join(l for l in rest if l.strip())
    return "\n".join(out) + ("\n" + ek + "\n" if ek else "")


def gun_bolumu(dosya, model):
    md = (HERE / dosya).read_text(encoding="utf-8")
    n = int(re.match(r"gun(\d)", dosya).group(1))
    head, secs = sections(md)
    title = head.split("\n", 1)[0].lstrip("# ").strip()
    kunye = re.search(r"\*\*Künye:\*\*[^\n]*", head).group(0)
    tablo, toplam = akis(n, model)
    out = [f"# {title.replace('. gün', '. GÜN')}\n\n{kunye}\n\nTam modül: [moduller/{dosya}](moduller/{dosya}) · Toplam süre: yaklaşık **{toplam // 60} sa {toplam % 60} dk** ({toplam} dk)\n"]
    for ad, govde in secs:
        if ad.startswith("Günün akışı"):
            ekler = "\n".join(l for l in govde.split("\n") if l.startswith("İsteğe bağlı") or l.startswith("En uzun"))
            out.append(f"## Günün akışı\n\n{tablo}\n{ekler}\n")
            if n == 1:
                out.append(KURALLAR_GUN1)
            continue
        if ad.startswith("Duraklar"):
            out.append(f"## Film ve duraklar\n\n{duraklar(govde, model)}")
            out.append(f"## Ara (10 dk)\n\n*İlk 7 dakika serbest: su, atıştırmalık, hareket. Bu sırada sandalyeleri çember yapın.*\n\n**Enerji oyunu {GUNLER[n]['oyun']} (3 dk, hareketli):** {OYUNLAR[n]}\n\n")
            continue
        out.append(f"## {ad}\n{model_filtre(govde, model)}")
    return "\n".join(out).rstrip() + "\n\n---\n\n"


def main():
    for model, dosya, diger in (("C", "senaryo-C-gorevli-izleme.md", "senaryo-B-durdurmali.md"), ("B", "senaryo-B-durdurmali.md", "senaryo-C-gorevli-izleme.md")):
        g = GENEL[model]
        metin = g["baslik"] + "\n\n" + GIRIS.format(diger=diger, oz=g["oz"], malzeme_ek=g["malzeme_ek"], alt_soru=g["alt_soru"])
        metin += "\n" + "".join(gun_bolumu(m, model) for m in MODULLER) + BC_NOTU
        metin = re.sub(r"\n{3,}", "\n\n", metin)
        (ROOT / dosya).write_text(metin, encoding="utf-8")
        print(dosya, len(metin.splitlines()), "satır")


if __name__ == "__main__":
    main()
