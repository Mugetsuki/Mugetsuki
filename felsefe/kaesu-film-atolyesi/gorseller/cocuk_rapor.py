#!/usr/bin/env python3
"""Çocuk başına kamp raporu: <Ad>-rapor.md dosyasından Filya renkleriyle PDF üretir (logo yok).

Çocuk verisi bu klasörde DURMAZ ve git'e girmez. Rapor metni (.md), PDF ve önizlemeler
yalnızca E: altındaki Cocuk-Raporlari klasöründe tutulur. Burada yalnızca üretici var.
Şablon ve doldurma yöntemi: ../cocuk-raporu-sablonu.md

Kullanım:
  ~/.local/share/kaesu-gorsel-venv/bin/python cocuk_rapor.py /mnt/e/.../Cocuk-Raporlari/<Ad>-rapor.md [--onizleme KLASÖR]
PDF, .md ile aynı klasöre aynı adla yazılır. --onizleme verilirse sayfa PNG'leri o klasöre gider
(çocuk verisi içerdikleri için git dışında bir klasör verin).
"""
import html as _html
import pathlib
import re
import sys

import markdown

ROOT = pathlib.Path(__file__).resolve().parent
sys.path.insert(0, str(ROOT))
import build as kit  # noqa: E402  film motifleri

GUNLER = {
    1: ("truman", "The Truman Show"),
    2: ("io2", "Ters Yüz 2"),
    3: ("wonder", "Mucize"),
    4: ("zoo", "Zootropolis"),
    5: ("soul", "Soul"),
}
CUMLELER = {
    "A": "Zor soruların tek bir doğru cevabı vardır.",
    "B": "Fikrimi söylerken nedenini de söylerim.",
    "C": "İyi bir gerekçe duyarsam fikrimi değiştiririm.",
    "D": "Felsefe filozofların ve büyüklerin işidir.",
    "E": "Sıradan şeylerin altında da büyük sorular vardır.",
}


def oku(md_yolu):
    """Ön bilgi (--- arası anahtar: değer) ve '## ' bölümlerini döndürür."""
    metin = pathlib.Path(md_yolu).read_text(encoding="utf-8")
    m = re.match(r"---\n(.*?)\n---\n(.*)", metin, re.S)
    if not m:
        sys.exit("Rapor dosyası '---' ile başlayan ön bilgi bloğu içermeli.")
    bilgi = dict(re.findall(r"(?m)^(\w+):\s*(.*?)\s*$", m.group(1)))
    bolumler = {}
    for parca in re.split(r"(?m)^## ", m.group(2))[1:]:
        baslik, _, govde = parca.partition("\n")
        bolumler[baslik.strip()] = govde.strip()
    return bilgi, bolumler


def alt(govde):
    """'### ' alt bölümlerini sıralı (başlık, gövde) listesi olarak döndürür."""
    return [(p.partition("\n")[0].strip(), p.partition("\n")[2].strip())
            for p in re.split(r"(?m)^### ", govde)[1:]]


def md(t):
    return markdown.markdown(t.strip())


def il(t):
    return re.sub(r"^<p>(.*)</p>$", r"\1", md(t), flags=re.S)


def olcek(s):
    return {k: int(v) for k, v in re.findall(r"([A-E])\s*([1-5])", s)}


def olcek_svg(bas, var):
    """Beş satır: başlangıç (içi boş) ve varış (sarı dolgu) noktası, aradaki ok."""
    w, sol, sag, satir = 182, 92, 172, 9.5
    xs = {i: sol + (sag - sol) * (i - 1) / 4 for i in range(1, 6)}
    h = 8 + satir * 5
    p = [f'<svg viewBox="0 0 {w} {h}" xmlns="http://www.w3.org/2000/svg" font-family="Albert Sans">',
         '<defs><marker id="ok" viewBox="0 0 10 10" refX="9" refY="5" markerWidth="5" markerHeight="5" orient="auto">'
         '<path d="M0,1 L9,5 L0,9 z" fill="#202B23"/></marker></defs>']
    for i, x in xs.items():
        etiket = {1: "1 hiç", 5: "5 tamamen"}.get(i, str(i))
        p.append(f'<text x="{x}" y="4" font-size="2.9" font-weight="700" fill="#4C584F" text-anchor="middle">{etiket}</text>')
    for n, k in enumerate("ABCDE"):
        y = 8 + satir * n + satir / 2
        p.append(f'<line x1="2" y1="{y + satir / 2}" x2="{w - 2}" y2="{y + satir / 2}" stroke="#D9D2A6" stroke-width="0.3"/>')
        p.append(f'<text x="2" y="{y + 1.1}" font-size="3.3" fill="#202B23"><tspan font-weight="700">{k}.</tspan> '
                 f'{_html.escape(CUMLELER[k])}</text>')
        p.append(f'<line x1="{sol}" y1="{y}" x2="{sag}" y2="{y}" stroke="#6E7A6F" stroke-width="0.35"/>')
        for x in xs.values():
            p.append(f'<circle cx="{x}" cy="{y}" r="0.7" fill="#6E7A6F"/>')
        b, v = bas.get(k), var.get(k)
        if b and v and b != v:
            yon = 1 if v > b else -1
            p.append(f'<line x1="{xs[b] + 2.6 * yon}" y1="{y}" x2="{xs[v] - 2.9 * yon}" y2="{y}" stroke="#202B23" '
                     f'stroke-width="0.6" marker-end="url(#ok)"/>')
        if b:
            p.append(f'<circle cx="{xs[b]}" cy="{y}" r="2.2" fill="#FAF7E3" stroke="#202B23" stroke-width="0.55"/>')
        if v:
            r = 2.2 if v != b else 1.35
            p.append(f'<circle cx="{xs[v]}" cy="{y}" r="{r}" fill="#FFD85F" stroke="#202B23" stroke-width="0.55"/>')
    p.append("</svg>")
    return "".join(p)


def kimlik(b):
    gun = b.get("tamamlanan_gun", "5")
    return (f'<div class="cr-kimlik"><div class="label">KAESU FELSEFE ATÖLYESİ · ÇOCUK RAPORU</div>'
            f'<div class="satir"><div class="ad">{_html.escape(b["kamp_adi"])}</div>'
            f'<div class="cipler"><span class="pill sari">{b["yas"]} yaş</span>'
            f'<span class="pill sari">{_html.escape(b["okul"])}</span>'
            f'<span class="pill sari">{gun}/5 gün tamamlandı</span>'
            + (f'<span class="pill sari">{_html.escape(b["etiket"])}</span>' if b.get("etiket") else "")
            + '</div></div>'
            f'<div class="vurgu"></div></div>')


def bolum(baslik, ic, cls=""):
    return f'<section class="cr-bolum {cls}"><h2>{baslik}</h2>{ic}</section>'


def basla_varis(govde):
    a = dict(alt(govde))
    return (f'<table class="cr-ikili"><tr>'
            f'<td><div class="kart bas"><div class="label">BAŞLANGIÇ · 1. GÜN, FİLMDEN ÖNCE</div>{md(a["Başlangıç"])}</div></td>'
            f'<td class="ok">→</td>'
            f'<td><div class="kart var"><div class="label">VARIŞ · 5. GÜN</div>{md(a["Varış"])}</div></td>'
            f'</tr></table><div class="cr-metin">{md(a["Ne değişti"])}</div>')


def olcek_bolumu(b, govde):
    return (f'<div class="cr-olcek">{olcek_svg(olcek(b["olcek_baslangic"]), olcek(b["olcek_varis"]))}'
            f'<div class="lejant"><span class="n bos"></span> başlangıç <span class="n dolu"></span> varış '
            f'<span class="ince">(aynıysa iç içe)</span></div></div><div class="cr-metin">{md(govde)}</div>')


COK_BEGENDI = "çok beğendi"


def puan_yaz(puan):
    """Atölye puanını yazar. 1–5: '4' -> '4/5'.
    5'in üstündeki işaret (6, ∞, ∞+∞ …) ölçek hatası değil, "çok beğendi" demektir (Genel Müdür kararı):
    yazılan olduğu gibi gösterilir, yanına "çok beğendi" eklenir.
      '6' -> '6 (çok beğendi)' · '∞' -> '∞ (çok beğendi)' · '5 (yanına ∞)' -> '5 (yanına ∞: çok beğendi)'"""
    puan = puan.strip()
    if puan.isdigit() and 1 <= int(puan) <= 5:
        return f"{puan}/5"
    m = re.fullmatch(r"(\d)\s*\(yanına\s*(.+?)\)", puan)
    if m:
        return _html.escape(f"{m.group(1)} (yanına {m.group(2)}: {COK_BEGENDI})")
    if "(" in puan:
        sys.exit(f"Atölye puanı anlaşılmadı: '{puan}'. 5'in üstündeki işareti olduğu gibi yazın (ör. '∞', '6', '5 (yanına ∞)').")
    return _html.escape(f"{puan} ({COK_BEGENDI})")


def gun_gun(govde):
    out = []
    for bas, ic in alt(govde):
        n, _, puan = bas.partition("·")
        n = int(n.strip())
        key, film = GUNLER[n]
        alanlar = dict(re.findall(r"(?m)^- (Cevap|Karıştıran|Gözlem):\s*(.+)$", ic))
        out.append(
            f'<table class="cr-gun"><tr><td class="sol"><div class="sw">{kit.motif(key)}</div>'
            f'<div class="gn">{n}. gün</div><div class="fa">{film}</div>'
            f'<div class="pu">Atölye puanı: {puan_yaz(puan)}</div></td><td class="sag">'
            f'<p><span class="et">Kamp sorusuna cevabı</span> {il(alanlar.get("Cevap", "(boş)"))}</p>'
            f'<p><span class="et">Kafasını karıştıran soru</span> {il(alanlar.get("Karıştıran", "(boş)"))}</p>'
            f'<p class="gz"><span class="et">Gözlem</span> {il(alanlar.get("Gözlem", ""))}</p></td></tr></table>')
    return "".join(out)


def maddeler(govde, en_az, en_cok, ad):
    n = len(re.findall(r"(?m)^- ", govde))
    if not en_az <= n <= en_cok:
        sys.exit(f"{ad}: {en_az}–{en_cok} madde olmalı, {n} madde var.")
    return md(govde)


ZORUNLU = ["Başlangıç ve varış", "Ölçek", "Gün gün", "Düşünme izleri", "Benim büyük sorum", "Güçlü yanlar"]


def rapor_html(md_yolu):
    b, s = oku(md_yolu)
    eksik = [z for z in ZORUNLU if z not in s]
    if eksik:
        sys.exit("Eksik bölüm: " + ", ".join(eksik))
    govde = "".join([
        f'<div class="cr">{kimlik(b)}',
        bolum("Kamp sorusu: başlangıç ve varış", basla_varis(s["Başlangıç ve varış"])),
        bolum("Beş cümle: başlangıçtan varışa", olcek_bolumu(b, s["Ölçek"]), "kirilmaz"),
        bolum("Gün gün", gun_gun(s["Gün gün"])),
        bolum("Düşünme izleri", f'<div class="cr-izler">{maddeler(s["Düşünme izleri"], 3, 4, "Düşünme izleri")}</div>',
              "kirilmaz"),
        '<table class="cr-ikiz"><tr><td>',
        bolum("Benim büyük sorum", f'<div class="cr-buyuk">{md(s["Benim büyük sorum"])}</div>'),
        '</td><td>',
        bolum("Güçlü yanlar", maddeler(s["Güçlü yanlar"], 1, 3, "Güçlü yanlar")),
        '</td></tr></table>',
        "</div>"])
    return (f'<!doctype html><html lang="tr"><head><meta charset="utf-8"><title>Çocuk raporu · {b["kamp_adi"]}</title>'
            f'<link rel="stylesheet" href="filya.css"></head><body>{govde}</body></html>')


def main():
    import weasyprint
    import pypdfium2 as pdfium
    args = [a for a in sys.argv[1:]]
    onizleme = None
    if "--onizleme" in args:
        i = args.index("--onizleme")
        onizleme = pathlib.Path(args[i + 1])
        del args[i:i + 2]
    if not args:
        sys.exit(__doc__)
    for yol in map(pathlib.Path, args):
        pdf_yolu = yol.with_suffix(".pdf")
        weasyprint.HTML(string=rapor_html(yol), base_url=str(ROOT) + "/").write_pdf(str(pdf_yolu))
        pdf = pdfium.PdfDocument(str(pdf_yolu))
        if onizleme:
            onizleme.mkdir(parents=True, exist_ok=True)
            for i, sayfa in enumerate(pdf, 1):
                sayfa.render(scale=110 / 72).to_pil().save(onizleme / f"{yol.stem}-s{i}.png")
        print(f"{pdf_yolu}: {len(pdf)} sayfa")
        pdf.close()


if __name__ == "__main__":
    main()
