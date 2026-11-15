"""Altın Parmak oturumu: plan + simülasyon PDF'i ve tek sayfalık kopya kâğıdı.

Kullanım: ~/.local/share/kaesu-gorsel-venv/bin/python build.py
Renkler ve fontlar Kaesu atölyesinin Filya stilinden gelir (logo yok, sarı yalnızca dolgu).
"""
import re
import sys
from pathlib import Path

import markdown
from weasyprint import HTML

ROOT = Path(__file__).resolve().parent
CSS = "../kaesu-film-atolyesi/ornekler/filya.css"

EK_CSS = """
.kb .bant .t { font-size: 19pt; }
.kb h4 { margin: 0 0 1.5mm; }
.kb .sahne { background: #fff; border: 0.35mm solid var(--line); border-radius: 4mm; padding: 3.5mm 5mm 1.5mm; margin: 0 0 3mm; }
.kb .sahne p { margin: 0 0 1.6mm; }
.kb .not { background: var(--sunk); border-left: 1.4mm solid var(--koyu); border-radius: 2mm; padding: 3mm 4.5mm 1.5mm; margin: 0 0 4mm; font-size: 10pt; break-inside: avoid; }
.kb .tk, .kb .te { display: inline-block; border-radius: 99mm; padding: 0 1.8mm; font-size: 7.6pt; font-weight: 700; line-height: 1.55; vertical-align: 1px; white-space: nowrap; }
.kb .tk { background: var(--sari); color: var(--koyu); }
.kb .te { border: 0.3mm dashed var(--line-strong); color: var(--ink); }
.kb .giris { font-size: 10pt; color: var(--muted); margin-bottom: 4mm; }
.kb .giris p { margin-bottom: 1.5mm; }
/* kapanış metni */
.kb .oku { background: var(--raised); border: 0.35mm solid var(--line); border-radius: 4mm; padding: 4mm 6mm 2mm; margin: 0 0 4mm; }
.kb .oku h4 { font-weight: 700; font-size: 9.5pt; letter-spacing: 0.08em; color: var(--muted); margin: 0 0 2mm; }
.kb .oku p { font-size: 12.6pt; line-height: 1.45; margin: 0 0 2.2mm; }
/* kopya kâğıdı */
.kopya { font-size: 10.4pt; line-height: 1.36; }
.kopya .bant { padding: 3mm 5mm; margin-bottom: 3mm; }
.kopya .bant .t { font-size: 16pt; }
.kopya .ikili { display: flex; gap: 5mm; }
.kopya .sol { width: 50%; } .kopya .sag { width: 50%; }
.kopya h4 { font-weight: 700; font-size: 9pt; letter-spacing: 0.08em; color: var(--muted); text-transform: uppercase; margin: 2.6mm 0 1.2mm; }
.kopya h4:first-child { margin-top: 0; }
.kopya p { margin: 0 0 1.6mm; }
.kopya ul { margin: 0; padding-left: 4mm; } .kopya li { margin-bottom: 0.6mm; }
.kopya table.akis { font-size: 10pt; margin: 0 0 1mm; }
.kopya table.akis th:empty { display: none; }
.kopya table.akis td { padding: 0.7mm 1.2mm; vertical-align: top; }
.kopya table.akis td:first-child { white-space: nowrap; font-weight: 700; }
.kopya .soru-kutu { padding: 2.4mm 3.5mm; margin: 0 0 2.2mm; font-family: "Literata"; font-size: 11.5pt; line-height: 1.3; }
.kopya .et { font-size: 7.6pt; }
.kopya .dip { font-size: 8pt; color: var(--muted); margin-top: 3mm; }
"""


def satir_ici(html):
    html = html.replace("{K}", '<span class="tk">Kitapta</span>').replace("{E}", '<span class="te">Bizim ek</span>')
    html = html.replace("{Ö}", '<span class="tk">Önerim</span>')
    return re.sub(r"\[\[(.+?)\]\]", r'<span class="et">\1</span>', html)


def conv(text):
    text = re.sub(r"(?m)^((?![ \t]*(?:[-*]|\d+\.)[ \t]).+)\n([ \t]*(?:[-*]|\d+\.)[ \t])", r"\1\n\n\2", text)
    html = markdown.markdown(text, extensions=["tables", "md_in_html"])
    return satir_ici(html.replace("<table>", '<table class="akis">'))


def bloklar(text):
    parcalar = re.split(r"(?ms)^::: (\w+)\n(.*?)^:::\n", text)
    out = [conv(parcalar[0])]
    for i in range(1, len(parcalar), 3):
        out.append(f'<div class="{parcalar[i]}">{conv(parcalar[i + 1])}</div>{conv(parcalar[i + 2])}')
    return "".join(out)


def bant(ust, baslik, alt, sag):
    return (f'<div class="bant"><table><tr><td><div class="label" style="color:var(--krem)">{ust}</div>'
            f'<div class="t">{baslik}</div><div class="s">{alt}</div></td>'
            f'<td style="text-align:right"><span class="pill sari">{sag}</span></td></tr></table></div>')


def doc(body):
    return (f'<!doctype html><html lang="tr"><head><meta charset="utf-8"><link rel="stylesheet" href="{CSS}">'
            f'<style>{EK_CSS}</style></head><body>{body}</body></html>')


def sayfali(dosya, ust, alt):
    md = (ROOT / dosya).read_text(encoding="utf-8")
    bas, govde = md.split("\n### SAYFA · ", 1)
    giris = "\n".join(bas.splitlines()[1:])
    sayfalar = re.split(r"(?m)^### SAYFA · (.+)\n", "### SAYFA · " + govde)[1:]
    toplam = len(sayfalar) // 2
    out = []
    for i in range(toplam):
        ad, metin = sayfalar[2 * i].strip(), sayfalar[2 * i + 1]
        ek = f'<div class="giris">{conv(giris)}</div>' if i == 0 else ""
        out.append(f'<section class="kb">{bant(ust, "Altın Parmak", alt, f"{i + 1}/{toplam} · {ad}")}{ek}{bloklar(metin)}</section>')
    return doc("".join(out))


def oturum():
    return sayfali("altin-parmak-oturum.md", "FELSEFE MAKİNESİ · TEK OTURUM", "Kral Midas’ın dileği · dil, anlam, açıklık ve kesinlik")


def kapanis():
    return sayfali("altin-parmak-kapanis.md", "KAPANIŞ METNİ · SESLİ OKUNUR", "Hikâyenin sonundan sonra · felsefe en sonda")


def yaratici():
    return sayfali("altin-parmak-yaratici.md", "SENİN KATABİLECEKLERİN · BİZİM EK", "Oyun, nesne, ritüel, beden, kapanış hediyesi")


KARTLAR = ["üzüm", "su", "kızı", "gül", "kendi eli", "kıyafeti", "sandalye", "hava",
           "gölgesi", "bir şarkı", "kedi", "bastığı yer", "saçı", "gözyaşı", "bir anı", "altın para"]

MALZEME_CSS = """
.mz { page: kitap; break-after: page; }
.mz:last-child { break-after: auto; }
.mz .bant { padding: 3mm 5mm; margin-bottom: 3mm; }
.mz .bant .t { font-size: 16pt; }
.mz .yon { font-size: 9.5pt; color: var(--muted); margin: 0 0 3mm; }
.mz table.izgara { width: 100%; border-collapse: collapse; table-layout: fixed; }
.mz table.izgara td { border: 0.35mm dashed var(--line-strong); text-align: center; vertical-align: middle; padding: 0; }
.mz .kart { height: 44mm; border: 0; border-radius: 0; background: none; padding: 3mm; }
.mz .kart .k { font-size: 7.5pt; letter-spacing: 0.08em; color: var(--muted); font-weight: 700; }
.mz .kart .w { font-family: "Literata"; font-weight: 600; font-size: 19pt; margin-top: 7mm; }
.mz .bolge td { height: 30mm; font-family: "Literata"; font-weight: 600; font-size: 17pt; }
.mz .bolge td.s { background: var(--sari); } .mz .bolge td.r { background: var(--raised); } .mz .bolge td.u { background: var(--sunk); }
.mz .dk { height: 76mm; text-align: left !important; vertical-align: top !important; padding: 5mm 6mm !important; background: var(--raised); }
.mz .dk .bas { display: flex; justify-content: space-between; align-items: center; }
.mz .dk .t { font-family: "Literata"; font-weight: 600; font-size: 15pt; }
.mz .dk .ad { font-size: 8.5pt; color: var(--muted); }
.mz .dk .s { font-weight: 700; font-size: 9.5pt; margin-top: 3mm; }
.mz .dk .c { height: 6.4mm; border-bottom: 0.3mm solid var(--line-strong); }
.mz .dk .sec { font-size: 9.5pt; margin-top: 2mm; }
.mz .dk .dot { width: 3.6mm; height: 3.6mm; vertical-align: -0.6mm; margin: 0 1mm 0 2.5mm; }
.mz .dk .dot:first-child { margin-left: 0; }
"""


def malzeme():
    satirlar = []
    for r in range(4):
        hucre = "".join(f'<td><div class="kart"><div class="k">ALTINA DÖNÜŞÜR MÜ?</div><div class="w">{KARTLAR[4 * r + c]}</div></div></td>' for c in range(4))
        satirlar.append(f"<tr>{hucre}</tr>")
    s1 = (f'<section class="kb mz">{bant("BASILACAK MALZEME · 1/2", "Altına dönüşür mü? kartları", "Tek kopya bas, kesik çizgilerden kes · 5–9 yaşta 8 kartla oyna", "A4")}'
          f'<p class="yon">Tahtadaki son dileğe göre her kart bir bölgeye konur, nedeni söylenir. Hiçbir kartın yerini sen onaylama.</p>'
          f'<table class="izgara">{"".join(satirlar)}</table>'
          f'<table class="izgara bolge" style="margin-top:5mm"><tr><td class="s">Altına döner</td><td class="r">Dönmez</td><td class="u">Emin değilim</td></tr></table></section>')
    dk = ('<div class="bas"><span class="t">Dilek kartım</span><span class="ad">Adım: ..................</span></div>'
          '<div class="s">Dilerim ki</div><div class="c"></div><div class="c"></div><div class="c"></div>'
          '<div class="s">Bir cin bu dileği nasıl bozabilir?</div><div class="c"></div><div class="c"></div>'
          '<div class="s">Bu dilek gerçekleşse mutlu olur muydum?</div>'
          '<div class="sec"><span class="dot"></span>Evet <span class="dot"></span>Hayır <span class="dot"></span>Bilmiyorum</div>')
    s2 = (f'<section class="kb mz">{bant("BASILACAK MALZEME · 2/2", "Dilek kartım", "Bir A4’te 6 kart · 12 çocuk için 2 kopya · kartlar okunmaz, eve gider", "A4")}'
          f'<table class="izgara">' + "".join(f'<tr><td class="dk">{dk}</td><td class="dk">{dk}</td></tr>' for _ in range(3)) + '</table></section>')
    return doc(f"<style>{MALZEME_CSS}</style>{s1}{s2}")


def kopya():
    md = (ROOT / "altin-parmak-kopya.md").read_text(encoding="utf-8")
    parcalar = dict(re.findall(r"(?ms)^::: (\w+)\n(.*?)^:::\n", md))
    govde = (f'{bant("KOPYA KÂĞIDI · TABLET", "Altın Parmak", "Felsefe Makinesi, s. 112–117 · “Sizce Midas nerede yanlış yapmıştır?”", "tek sayfa")}'
             f'<div class="ikili"><div class="sol">{conv(parcalar["sol"])}</div><div class="sag">{conv(parcalar["sag"])}</div></div>')
    return doc(f'<section class="kb kopya">{govde}</section>')


def yaz(ad, html, beklenen=None):
    pdf = ROOT / f"{ad}.pdf"
    belge = HTML(string=html, base_url=str(ROOT) + "/").render()
    n = len(belge.pages)
    belge.write_pdf(pdf)
    durum = "OK" if beklenen is None or n == beklenen else f"BEKLENEN {beklenen}"
    print(f"{pdf.name}: {n} sayfa {durum}")
    return beklenen is None or n == beklenen


if __name__ == "__main__":
    ok = yaz("altin-parmak-oturum", oturum(), 13) & yaz("altin-parmak-kopya", kopya(), 1)
    ok &= yaz("altin-parmak-kapanis", kapanis(), 1)
    ok &= yaz("altin-parmak-yaratici", yaratici(), 5) & yaz("altin-parmak-malzeme", malzeme(), 2)
    sys.exit(0 if ok else 1)
