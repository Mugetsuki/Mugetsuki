#!/usr/bin/env python3
"""Felsefe Makinesi biçimindeki bir modülden moderatör kitapçığının o güne ait sayfalarını üretir
ve ../moderator-kagidi.md içindeki ilgili `## …` bölümünün yerine yazar.

Kitapçık sayfaları modülün kısaltılmış hâlidir: denetim tablosu ve “soruların altındaki soru” tablosu
kitapçığa girmez, merdiven tabloları madde listesine dönüşür, ikilem iki kartlı tabloya dönüşür.
Sayfa bölmeleri sabittir: gün ve akış · 1. ve 2. durak · 3. durak ve tartışmanın başı ·
merdivenin 1–2. basamağı · 3–4. basamağı · ikilem (ya da Beş Kapı) ve kapanış. 1. ve 2. günün kitapçık sayfaları elle yazıldı.

Kullanım: python3 kitapcik_uret.py gun3-mucize.md "## 3. GÜN · Mucize (Wonder) · Öteki"
"""
import pathlib
import re
import sys

HERE = pathlib.Path(__file__).resolve().parent
KITAP = HERE.parent / "moderator-kagidi.md"


def bolumler(md):
    parcalar = re.split(r"(?m)^## ", md)
    return parcalar[0], {p.split("\n", 1)[0].strip(): p.split("\n", 1)[1] for p in parcalar[1:]}


def bul(secs, bas):
    return next((v for k, v in secs.items() if k.startswith(bas)), "")


def madde(govde, etiket):
    m = re.search(rf"(?m)^- \*\*{re.escape(etiket)}[^*]*:\*\* (.+)$", govde)
    return m.group(1).strip() if m else ""


def tablo_maddeye(metin):
    """| Çocuk şunu derse | Gruba şunu sor | Araç | tablolarını madde listesine çevirir."""
    out = []
    for satir in metin.split("\n"):
        if satir.startswith("| Çocuk şunu derse") or satir.startswith("| Durum |") or re.match(r"^\|[-| ]+\|$", satir):
            continue
        if satir.startswith("| "):
            h = [c.strip() for c in satir.strip().strip("|").split(" | ")]
            if len(h) >= 2:
                a, b = h[0], h[1]
                arac = h[2] if len(h) > 2 else ""
                a = a if a.startswith("“") else a
                bas = f"*{a}*" if a.startswith("“") else f"*{a}:*"
                out.append(f"- {bas} {b} {arac}".rstrip())
            continue
        out.append(satir)
    return "\n".join(out)


def durak_karti(blok):
    baslik, govde = blok.split("\n", 1)
    govde = govde.strip()
    # Koşul satırını sahnenin sonuna ekle, tartışmada geri çağır satırını kısalt
    kosul = re.search(r"(?m)^\*\*Koşul:\*\* (.+)$", govde)
    if kosul:
        govde = govde.replace(kosul.group(0) + "\n\n", "")
        govde = re.sub(r"(?m)^(\*\*Sahne:\*\* .+)$", lambda m: m.group(1) + f" **Koşul:** {kosul.group(1)}", govde, count=1)
    govde = govde.replace("**Tartışmada geri çağır:**", "**Geri çağır:**")
    return f"::: durak\n#### {baslik.strip()}\n\n{govde}\n:::\n"


def ikilem_tablosu(metin):
    a = re.search(r"\*\*A duvarı:\*\* (.+)", metin)
    b = re.search(r"\*\*B duvarı:\*\* (.+)", metin)
    ta = re.search(r"(?m)^- \*\*A tarafına:\*\* (.+)$", metin)
    tb = re.search(r"(?m)^- \*\*B tarafına:\*\* (.+)$", metin)
    if not (a and b):
        return None

    def kart(harf, m, t):
        bas, _, alt = m.group(1).strip().rstrip("”").partition(". ")
        bas = bas.rstrip(".")
        alt = (" " + alt) if alt else ""
        ek = "dakilere" if harf == "A" else "dekilere"
        ters = f'<div class="ters">{harf}\'{ek}: {t.group(1)}</div>' if t else ""
        return f'<td><div class="harf">{harf}</div><b>{bas}.</b>{alt}{ters}</td>'
    return f'<table class="ikilem"><tr>{kart("A", a, ta)}{kart("B", b, tb)}</tr></table>'


def uret(dosya, kitap_baslik):
    md = (HERE / dosya).read_text(encoding="utf-8")
    head, secs = bolumler(md)
    imdb = re.search(r"IMDb: (\S+)", head).group(1)
    yer = bul(secs, "Günün yolculuktaki yeri")
    devr = madde(yer, "Dünden devralınan soru")
    ilk = madde(yer, "Sınanan ilk cevap")
    acik = madde(yer, "Günün Açık Sorusu")
    kopru = madde(yer, "Kamp sorusuna bağlanan soru") or madde(yer, "Kamp sorusuna bağlantı")
    neden = madde(yer, "Neden bu film")

    # Akış tablosu (Saat | Süre | Bölüm) -> (Saat | Ne yapıyoruz | Süre)
    akis = bul(secs, "Günün akışı")
    satirlar, toplam = [], ""
    for s in akis.split("\n"):
        h = [c.strip() for c in s.strip().strip("|").split("|")] if s.startswith("| ") else None
        if not h or h[0] in ("Saat",) or set(h[0]) <= set("-"):
            continue
        if h[0].startswith("**Toplam"):
            toplam = re.sub(r"\*", "", h[2]); continue
        saat = h[0].split("–")[0].lstrip("0").replace(":", ":", 1)
        saat = saat if not saat.startswith(":") else "0" + saat
        bolum = re.sub(r"\*\*|:\*\*.*", "", h[2]).split(":")[0] if h[2].startswith("**") else h[2]
        satirlar.append(f"| {saat} | {bolum} | {h[1]} |")
    acilis = bul(secs, "Açılış")
    hassas_not = re.search(r"\*\*Hassas içerik notu[^*]*\*\* (.+)", acilis)
    izleme = re.search(r"(?m)^\*\*(“.+”)\*\*$", bul(secs, "İzleme görevi"))

    p1 = [f"### SAYFA · GÜN VE AKIŞ\n\n**IMDb:** <{imdb}>\n"]
    if devr:
        p1.append(f"**Bugün ne oluyor?** Dünden devralınan soru: {devr}")
    p1.append(f"- **Sınadığımız ilk cevap:** {ilk}")
    p1.append(f"- **Günün Açık Sorusu (tahtada tek başına):** {acik}")
    if kopru:
        p1.append(f"- **Kamp sorusuna köprü:** {kopru}")
    p1.append("\n[[ÜST BLOK SONU]]\n")
    p1.append(f"**Günün akışı** ({toplam})\n\n| Saat | Ne yapıyoruz | Süre |\n|---|---|---|\n" + "\n".join(satirlar) + "\n")
    if hassas_not:
        p1.append(f"**Hassas içerik notu (filmden önce):** {hassas_not.group(1)}\n")
    if izleme:
        p1.append("::: kaynak\n#### İZLEME GÖREVİ (FİLMDEN ÖNCE, TAHTAYA YAZILIR)\n\n"
                  f'<p class="yaprakta">{izleme.group(1)}</p>\n\n'
                  '<span class="et">Demir atmak · s. 48</span> <span class="et">Sandalye\'deki açılış · s. 65</span> Soru film boyunca tahtada kalır, her durakta ona dönülür.\n:::\n')
    ek = " Son yarım saatte durak yok." if "Son yarım saatte durak yok" in bul(secs, "Duraklar") else ""
    p1.append('<div class="soru-kutu">Duraklarda nasıl? Açık Soruyu tek başına sor ve tahtaya yaz. Yarım dakika sessizlik, bir dakika yanındakiyle konuşma, sonra 3–4 cevap. '
              f'Cevapları onaylama, birbirine bağla. Bir durak 6 dakikayı geçmesin.{ek} <span class="et">Düşünme için zaman · s. 37</span> <span class="et">Konuşma Zamanı · s. 34</span></div>\n')

    durak_metni = bul(secs, "Duraklar")
    bloklar = re.split(r"(?m)^### ", durak_metni)[1:]
    kartlar = [durak_karti(b.split("\n*Dakikalar yaklaşıktır")[0]) for b in bloklar]
    p2 = ["### SAYFA · 1. VE 2. DURAK\n"] + kartlar[:2]
    p3 = [f"### SAYFA · {'3. DURAK VE ' if len(kartlar) > 2 else ''}TARTIŞMANIN BAŞI\n"] + kartlar[2:]
    p3.append("*Dakikalar yaklaşıktır. Kamptan önce filmi bir kez izle ve duraklarını kendi oynatıcında işaretle.*\n")
    p3.append('<p class="ara">Film bitince: 60 dakika</p>\n')
    p3.append('Hamleleri cevap veren çocuğa değil, gruba sor: “Buna ne diyeceksiniz?” <span class="et">Ağ dinamiği · s. 44</span>\n')

    tart = bul(secs, "Tartışma (60")
    alt = re.split(r"(?m)^### ", tart)[1:]
    p4, p4b, p5, hedef = [], [], [], p3
    for blok in alt:
        bas, govde = blok.split("\n", 1)
        bas = bas.strip()
        govde = govde.strip()
        if bas.startswith("dk 5–12") or bas.startswith("dk 5–10"):
            govde = re.sub(r"(?ms)^\|.*?\n(?=\n|Sonra)", "", govde + "\n")
            govde = govde.replace("Çocukların film sorularını insan sorusuna çevir:\n", "Film sorularını insan sorusuna çevir.").strip()
            govde = re.sub(r"\s*\n\n\s*", " ", govde)
            hedef.append(f"**{bas}.** {govde}\n")
        elif "Soru merdiveni" in bas:
            basamaklar = re.split(r"(?m)^(?=\*\*\d\. basamak)", govde)
            for i, bs in enumerate(b for b in basamaklar if b.strip().startswith("**")):
                bs = re.sub(r" \((yaklaşık \d+ dk)\)\*\*", "**", bs)
                bs = tablo_maddeye(bs.strip())
                bs = re.sub(r"\n\n- ", "\n- ", bs)
                if i == 0:
                    hedef = p4
                if i == 2:
                    hedef = p4b
                hedef.append(bs + "\n")
        elif "ikilem" in bas.lower() or "Beş Kapı" in bas:
            hedef = p5
            tablo = ikilem_tablosu(govde)
            if tablo:
                giris = govde.split("“Ayağa kalkıyoruz")[0].strip()
                kurgu = re.search(r"Ayağa kalkıyoruz\.(.*?)\*\*A duvarı", govde, re.S)
                kurgu = kurgu.group(1).strip() if kurgu else ""
                giris = (giris + " " + (f"“{kurgu}”" if kurgu else "")).strip()
                kalan = govde.split("Seçin ve yürüyün.”")[-1] if "Seçin ve yürüyün" in govde else govde.split("Ortada durmak serbest.”", 1)[-1]
                kalan = re.sub(r"(?m)^- \*\*[AB] tarafına:\*\*.*\n?", "", kalan).replace("Her taraftan 2–3 gerekçe al. Sonra iki tarafa da, onlara katılmayan birinin sesiyle ters köşe sorusu sor:",
                                                                                     "Ters köşe soruları iki tarafa da onlara katılmayan birinin sesiyle sorulur.")
                kalan = re.sub(r"\s*\n+\s*", " ", kalan.strip())
                hedef.append(f"**{bas}.** Herkes ayağa kalkar ve bir duvar seçer. {giris}\n\n{tablo}\n\nOrtada durmak serbest. {kalan}\n")
            else:
                hedef.append(f"**{bas}.** {govde}\n")
        else:
            if bas.startswith("dk 55") or bas.startswith("dk 56"):
                govde = "Sessiz beş dakika." if "Varış" not in govde else govde
            if bas.startswith("dk 50") or bas.startswith("dk 45"):
                govde = re.sub(r"(?ms)Grubun varabileceği yerler.*?(?=\n\n|\Z)", "", govde).strip()
                govde = re.sub(r"(?m)^- “.*$\n?", "", govde).strip()
            if hedef is p3 and not bas.startswith("dk 0") and not bas.startswith("dk 5"):
                hedef = p5
            hedef.append(f"**{bas}.** {govde}\n")

    kapanis = bul(secs, "Kapanış")
    kp = re.search(r"(?m)^(“.+”)$", kapanis)
    hg = re.search(r"\*\*Hayattan soru görevi:\*\* (“[^”]+”)", kapanis)
    if kp:
        p5.append(f"**Köprü (3 dk).** {kp.group(1)}" + (f" **Hayattan soru görevi:** {hg.group(1)}" if hg else "") + "\n")
    tak = bul(secs, "Takılırsan")
    if tak:
        maddeler = []
        for satir in tak.split("\n"):
            if satir.startswith("| ") and not satir.startswith("| Durum") and not re.match(r"^\|[-| ]+\|$", satir):
                h = [c.strip() for c in satir.strip().strip("|").split(" | ")]
                maddeler.append(f"- **{h[0]}:** {h[1]} {h[2] if len(h) > 2 else ''}".rstrip())
        (p4b if p4b else p5).append("**Takılırsan.**\n" + "\n".join(maddeler) + "\n")
    has = bul(secs, "Hassas içerik")
    if has:
        p5.append("**Hassas anlar.**\n" + "\n".join(l for l in has.strip().split("\n") if l.strip()) + "\n")
    fk = re.search(r"Söylenebilecek tek cümle[^:]*: (.+)", bul(secs, "Moderatör için felsefe köşesi"))
    if fk:
        p5.append(f'**Felsefe köşesi** (en sonda, tek cümle) <span class="et">s. 65</span> {fk.group(1).strip()}\n')

    p6 = []
    varis = bul(secs, "Varış")
    if varis:
        p6.append("### SAYFA · VARIŞ (25 DK)\n")
        p6.append("Çocuklar Başlangıç yaprağını ve Varış yaprağını önlerine koyar. Her adımda tek bir cümle söylersin, sonra sessizlik.\n")
        for satir in varis.split("\n"):
            if satir.startswith("| ") and not satir.startswith("| Süre") and not set(satir) <= set("|- "):
                h = [c.strip() for c in satir.strip().strip("|").split("|")]
                p6.append(f"- {h[1]} ({h[0]}): {h[2]}")
        son = re.search(r"\*\*Kampın son sözü:\*\* (.+)", varis)
        if son:
            p6.append(f"\n**Kampın son sözü.** {son.group(1)}\n")
        p6.append("Dosyalar toplanır. Kamp sonunda dosya çocuğa verilir.\n")
    p4.insert(0, "### SAYFA · MERDİVEN: 1. VE 2. BASAMAK\n")
    if p4b:
        p4b.insert(0, "### SAYFA · MERDİVEN: 3. VE 4. BASAMAK, TAKILIRSAN\n")
    p5.insert(0, "### SAYFA · " + ("BEŞ KAPI, VARIŞ VE KAPANIŞ" if any("Beş Kapı" in x for x in p5) else "İKİLEM VE KAPANIŞ") + "\n")
    govde = "\n".join("\n".join(p) for p in (p1, p2, p3, p4, p4b, p5, p6) if p)
    govde = re.sub(r"\n{3,}", "\n\n", govde)
    return f"{kitap_baslik}\n\n{govde}\n---\n\n"


def main():
    dosya, baslik = sys.argv[1], sys.argv[2]
    yeni = uret(dosya, baslik)
    s = KITAP.read_text(encoding="utf-8")
    a = s.index(baslik + "\n")
    b = s.index("\n## ", a + len(baslik)) + 1
    KITAP.write_text(s[:a] + yeni + s[b:], encoding="utf-8")
    print(f"{baslik}: {len(yeni.splitlines())} satır yazıldı")


if __name__ == "__main__":
    main()
