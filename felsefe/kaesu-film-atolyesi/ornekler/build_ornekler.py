#!/usr/bin/env python3
"""Onay için iki görsel örnek üretir (Filya renkleri, logo yok):

  dusunce-yolculugu-ornek.pdf  Düşünce Yolculuğum defterinin ilk yaprağı (ön: Başlangıç, arka: 1–3. gün)
  moderator-gun1-ornek.pdf     Moderatör kitapçığının 1. gün yaprağı (ön ve arka yüz, küçük afiş ve IMDb)

Her sayfanın PNG önizlemesi aynı klasöre yazılır. HTML kaynakları html/ altında.
Kullanım: ~/.local/share/kaesu-gorsel-venv/bin/python build_ornekler.py [--inceleme KLASÖR]
"""
import pathlib
import re
import sys

ROOT = pathlib.Path(__file__).resolve().parent
HTML = ROOT / "html"


def doc(title, body):
    return (f'<!doctype html>\n<html lang="tr"><head><meta charset="utf-8"><title>{title}</title>'
            f'<link rel="stylesheet" href="../filya.css"></head><body>\n{body}\n</body></html>\n')


def lines(n, cls=""):
    return f'<div class="lines {cls}">' + "<div></div>" * n + "</div>"


def cb():
    return '<span class="cb"></span>'


def dot():
    return '<span class="dot"></span>'


# --------------------------------------------------------------------------------------
# Düşünce Yolculuğum: ilk yaprak
# --------------------------------------------------------------------------------------
IFADELER = [
    ("A", "Zor soruların tek bir doğru cevabı vardır."),
    ("B", "Fikrimi söylerken nedenini de söylerim."),
    ("C", "İyi bir gerekçe duyarsam fikrimi değiştiririm."),
    ("D", "Felsefe filozofların ve büyüklerin işidir."),
    ("E", "Yemek, oyun, kurallar gibi sıradan şeylerin altında da büyük sorular vardır."),
]
GUNLER = [(1, "The Truman Show"), (2, "Ters Yüz 2"), (3, "Wonder")]


def defter():
    rows = "".join(f'<tr><td style="width:60%"><b>{k}.</b> {t}</td>' + "".join(f'<td class="c">{dot()}</td>' for _ in range(5)) + "</tr>"
                   for k, t in IFADELER)
    on = f'''<section class="sheet">
  <div class="ust">
    <div><div class="label">KAESU FİLM ATÖLYESİ</div><div class="title" style="font-size:30pt;margin-top:1mm">Düşünce yolculuğum</div></div>
  </div>
  <div class="kimlik">
    <div class="row"><span>Kamp adım:</span><span class="fill" style="flex:1"></span></div>
    <div class="row"><span>Yaşım:</span><span class="fill" style="width:16mm"></span><span style="margin-left:8mm">{cb()} Ortaokul</span><span style="margin-left:4mm">{cb()} Lise</span></div>
  </div>
  <div class="blok-sari" style="margin-top:4mm">
    <div class="label" style="color:var(--koyu)">KAMP SORUSU</div>
    <div class="title" style="font-size:34pt;margin-top:1mm">İyi bir hayat nedir?</div>
  </div>
  <div class="alan-baslik"><div class="title" style="font-size:18pt">Başlangıç</div><span class="pill turuncu">1. gün · filmden önce</span></div>
  <p style="margin-top:2mm">Daha hiçbir film izlemeden, şu an ne düşünüyorsun? Doğru cevap yok. Beşinci gün bu sayfaya geri döneceksin.</p>
  <div class="soru-etiketi">Bence iyi bir hayat</div>{lines(3)}
  <div class="soru-etiketi">çünkü</div>{lines(3)}
  <div class="kart" style="margin-top:6mm">
    <div style="font-weight:700;margin-bottom:2mm">Bu cümlelere ne kadar katılıyorsun?</div>
    <table class="olcek"><tr><th style="width:58%"></th><th>1<br>hiç</th><th>2</th><th>3</th><th>4</th><th>5<br>tamamen</th></tr>{rows}</table>
  </div>
  <div class="soru-etiketi" style="margin-top:6mm">Günlük hayatımda felsefe … karşıma çıkar.</div>
  <div class="ipucu">Bilmiyorsan “bilmiyorum” yaz. Bu da bir cevap.</div>{lines(2)}
</section>'''
    bloklar = ""
    for n, film in GUNLER:
        bloklar += f'''<div class="kart gun">
    <div class="bas"><span class="pill sari">{n}. gün</span><span class="film">{film}</span></div>
    <div class="soru-etiketi" style="margin-top:1mm">Kamp sorusuna bugünkü cevabım</div>
    <div class="ipucu">İyi bir hayat … çünkü … Değişmediyse “Değişmedi, çünkü …” yaz. Bu da iyi bir cevap.</div>{lines(2, "tight")}
    <div class="soru-etiketi">Bugün kafamı en çok karıştıran soru</div>{lines(1, "tight")}
    <div class="soru-etiketi">Hayattan bir soru <span class="ipucu" style="font-weight:400">(kampta yakaladığım)</span></div>{lines(1, "tight")}
    <div class="keyif">Bugün atölye: <span class="n">hiç sevmedim</span> {dot()} {dot()} {dot()} {dot()} {dot()} <span class="n">çok sevdim</span></div>
  </div>'''
    arka = f'''<section class="sheet">
  <div class="label">KAESU FİLM ATÖLYESİ · DÜŞÜNCE YOLCULUĞUM</div>
  <div class="title" style="font-size:22pt;margin-top:1mm">Her günün sonunda</div>
  <p class="muted" style="margin-top:1mm">Beş dakika, sessiz. Kimse not vermeyecek. Kamp adım: <span class="fill" style="width:55mm"></span></p>
  {bloklar}
</section>'''
    return on + arka


# --------------------------------------------------------------------------------------
# Moderatör kitapçığı: 1. gün yaprağı (moderator-gun1-ornek.md'den)
# --------------------------------------------------------------------------------------
def kitap():
    import markdown
    md = (ROOT / "moderator-gun1-ornek.md").read_text(encoding="utf-8")
    md = re.sub(r"<!--.*?-->", "", md, flags=re.S)

    def conv(text):
        text = re.sub(r"(?m)^((?![ \t]*(?:[-*]|\d+\.)[ \t]).+)\n([ \t]*(?:[-*]|\d+\.)[ \t])", r"\1\n\n\2", text)
        return markdown.markdown(text, extensions=["tables"])

    on = md.split("### ÖN YÜZ", 1)[1].split("### ARKA YÜZ", 1)[0]
    arka = md.split("### ARKA YÜZ", 1)[1]
    imdb = "https://www.imdb.com/title/tt0120382/"
    on = re.sub(r"\*\*IMDb:\*\*[^\n]*\n", "", on)  # IMDb, afişin altında gösteriliyor

    def bant(yuz):
        return (f'<div class="bant"><div><div class="label" style="color:var(--krem)">MODERATÖR KİTAPÇIĞI · 1. GÜN</div>'
                f'<div class="t">The Truman Show</div><div class="s">Bilgi · Kamp sorusu: İyi bir hayat nedir?</div></div>'
                f'<span class="pill sari">{yuz}</span></div>')

    afis = (f'<div class="afis"><img src="../afis/gun1-truman.jpg" alt="The Truman Show afişi">'
            f'<div class="k">Peter Weir, 1998, 103 dk<br><a href="{imdb}">imdb.com/title/tt0120382</a><br>Afiş: İngilizce Vikipedi</div></div>')
    ust, alt = on.split("[[ÜST BLOK SONU]]", 1)
    ust_blok = f'<table class="ust-blok"><tr><td>{conv(ust)}</td><td class="afis-hucre">{afis}</td></tr></table>'
    return (f'<section class="kitap">{bant("ÖN YÜZ")}{ust_blok}{conv(alt)}</section>'
            f'<section class="kitap">{bant("ARKA YÜZ")}{conv(arka)}</section>')


URUNLER = {
    "dusunce-yolculugu-ornek": ("Düşünce yolculuğum · örnek", defter, 2),
    "moderator-gun1-ornek": ("Moderatör kitapçığı · 1. gün · örnek", kitap, 2),
}


def main():
    import weasyprint
    import pypdfium2 as pdfium
    inceleme = None
    if "--inceleme" in sys.argv:
        inceleme = pathlib.Path(sys.argv[sys.argv.index("--inceleme") + 1])
        inceleme.mkdir(parents=True, exist_ok=True)
    HTML.mkdir(exist_ok=True)
    hata = False
    for ad, (baslik, fn, beklenen) in URUNLER.items():
        hp = HTML / f"{ad}.html"
        hp.write_text(doc(baslik, fn()), encoding="utf-8")
        pp = ROOT / f"{ad}.pdf"
        weasyprint.HTML(filename=str(hp)).write_pdf(str(pp))
        pdf = pdfium.PdfDocument(str(pp))
        for i, sayfa in enumerate(pdf, 1):
            sayfa.render(scale=100 / 72).to_pil().save(ROOT / f"{ad}-s{i}.png", optimize=True)
            if inceleme:
                sayfa.render(scale=130 / 72).to_pil().save(inceleme / f"{ad}-s{i}.png")
        n = len(pdf)
        pdf.close()
        uyari = "" if n == beklenen else f"  <-- beklenen {beklenen} sayfa"
        hata |= bool(uyari)
        print(f"{ad:28s} {n} sayfa{uyari}")
    sys.exit(1 if hata else 0)


if __name__ == "__main__":
    main()
