#!/usr/bin/env python3
"""Kaesu Film Atölyesi görsel kitini üretir.

Ne yapar:
  1. html/  klasörüne her ürün için bir HTML kaynağı yazar (stil: ../kit.css).
  2. pdf/   klasörüne WeasyPrint ile yazdırmaya hazır PDF üretir (fontlar gömülü).
  3. onizleme/ klasörüne her sayfanın PNG önizlemesini yazar (pypdfium2, 72 dpi).

Kullanım:
  python build.py                      # film görselleri ve kamp afişlerini üret (formlar ve kitapçık: build_filya.py)
  python build.py --inceleme KLASÖR    # ayrıca 110 dpi inceleme PNG'leri yaz

Gerekenler: weasyprint, pypdfium2, markdown (README'ye bakın).
İçerik kaynağı: ../film-onerileri.md, ../senaryo-*.md, ../moderator-kagidi.md, ../olcme-degerlendirme.md
"""
import math
import pathlib
import random
import re
import sys

ROOT = pathlib.Path(__file__).resolve().parent
HTML_DIR, PDF_DIR, PNG_DIR = ROOT / "html", ROOT / "pdf", ROOT / "onizleme"

# --------------------------------------------------------------------------------------
# İçerik (film-onerileri.md, senaryolar ve moderator-kagidi.md ile aynı metinler)
# --------------------------------------------------------------------------------------
DAYS = [
    dict(n=1, key="truman", t="t-truman", film="The Truman Show", TEMA="BİLGİ", tema_alt="Gerçeklik ve bilgi",
         soru="Gerçek olduğunu nasıl bilebiliriz?",
         gorev="Truman neyin gerçek, neyin sahte olduğunu nasıl anlıyor? Sen izlerken hangi anda “bu sahte” dedin?",
         ikilem_q="Hangisini seçersin? Tarafını seç ve o duvara yürü.",
         A=("Güvenli ama tasarlanmış bir dünya", "Her şey senin için tasarlanmış. Güvenli ve mutlusun, ama hiçbir şeyin kesin gerçek olduğunu bilemiyorsun."),
         B=("Bilinmeyen ama gerçek bir dünya", "Neyle karşılaşacağını bilmiyorsun, ama her şey gerçek."),
         hero="Truman", kapi="Güvenli sahtelik yerine bilinmeyen gerçeği seçti.", kapi_alt=None),
    dict(n=2, key="io2", t="t-io2", film="Ters Yüz 2", TEMA="BENLİK", tema_alt="Zihin ve kimlik",
         soru="Beni “ben” yapan ne: anılarım mı, duygularım mı, seçimlerim mi?",
         gorev="Riley’nin “ben kimim” cümlesi film boyunca nasıl değişiyor? Hangi duygunun haklı olduğunu düşündüğün anları yaz.",
         ikilem_q="Kaygı’yı, filmde Neşe’ye yapıldığı gibi kavanoza kapatıp sırlar kasasına kilitleyebilirsin. Bir daha hiç kaygılanmazsın, ama geleceğe hazırlanmanı sağlayan ses de susar. Kilitler misin?",
         A=("Kaygı’yı kasaya kilitlerim", "Kaygısız bir hayat isterim, geleceği daha az düşünmek pahasına da olsa."),
         B=("Kaygı konsolda kalsın", "Kaygıyla birlikte yaşarım, geleceğe hazırlanma yeteneğimi korurum."),
         hero="Riley", kapi="Mükemmel bir “ben” yerine iyisi ve kötüsüyle karmaşık bir “ben”i kabul etti.", kapi_alt=None),
    dict(n=3, key="wonder", t="t-wonder", film="Wonder", TEMA="ÖTEKİ", tema_alt="Bireysel etik",
         soru="Haklı olmak ile iyi (nazik) olmak çatıştığında hangisini seçmeli?",
         gorev="Film birkaç kişinin gözünden anlatılıyor. Ekranda yeni bir isim çıkınca o kişinin derdi ne? Tek kelimeyle yaz.",
         ikilem_q="Bir insanın en yakın arkadaşı, o yokken onu başkalarına kötülemiş. Sonra içtenlikle özür diliyor. Ne yapmalı?",
         A=("Arkadaşlığa devam etmeli", "Özür içtense affetmeli ve arkadaşlığı eskisi gibi sürdürmeli."),
         B=("İlişkiyi bitirmeli", "Güven bir kez kırıldıysa bu arkadaşlığı bitirmeli."),
         hero="Auggie", kapi="Saklanmak yerine görünmeyi seçti.", kapi_alt="Ya da Jack: Haklı çıkmak yerine özür dilemeyi seçti."),
    dict(n=4, key="zoo", t="t-zoo", film="Zootropolis", TEMA="TOPLUM", tema_alt="Adalet ve önyargı",
         soru="Bir grup hakkında genelleme yapmak ne zaman adil, ne zaman haksızlık?",
         gorev="Filmde kim kimi önyargıyla yargılıyor? En az bir örnek yaz. Kahramanların da önyargılı olduğu anlara dikkat et.",
         ikilem_q="Bir grup hakkında doğru görünen bir genelleme var, ama karşındaki tek kişiye uygulamak ona haksızlık edebilir. Hangisi daha adil?",
         A=("Genellemeyle kendimi korurum", "Grubun genelinde doğru olanı dikkate alırım."),
         B=("Her kişiye sıfırdan bakarım", "Karşımdakini ait olduğu grupla değil, kendisiyle tanırım."),
         hero="Judy", kapi="Haklı olduğunu savunmak yerine yanıldığını kabul etti.", kapi_alt=None),
    dict(n=5, key="soul", t="t-soul", film="Soul", TEMA="ANLAM", tema_alt="İyi bir hayat",
         soru="Hayatı yaşamaya değer kılan ne?",
         gorev="Bu film sana önceki dört filmden hangi karakteri hatırlatıyor? “Bu an bana … hatırlattı, çünkü …” diye yaz.",
         ikilem_q="İkisi birden mümkün değilse hangisini seçersin?",
         A=("Tek bir büyük hedef", "Hayatını büyük bir hayale adayıp ona ulaşmak."),
         B=("Her günün küçük anları", "Büyük bir hedef olmadan her günün küçük şeylerinden keyif almak."),
         hero="Joe", kapi="Büyük hayalinin yanında sıradan anların değerini gördü.", kapi_alt=None),
]
BYKEY = {d["key"]: d for d in DAYS}

# Motif hizalaması: dar şeritlerde motifin en karakteristik kısmı görünsün
ALIGN = dict(truman="xMidYMid", io2="xMidYMid", wonder="xMidYMid", zoo="xMidYMax", soul="xMidYMax", camp="xMidYMid")


# --------------------------------------------------------------------------------------
# Soyut motifler (özgün SVG; film görseli, karakter ya da logo içermez)
# --------------------------------------------------------------------------------------
def _svg(inner, defs, align):
    d = f"<defs>{defs}</defs>" if defs else ""
    return (f'<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 400 200" '
            f'preserveAspectRatio="{align} slice">{d}{inner}</svg>')


def _shade(hexc, k):
    """k<0 koyulaştırır, k>0 açar."""
    r, g, b = (int(hexc[i:i + 2], 16) for i in (1, 3, 5))
    if k < 0:
        r, g, b = (int(c * (1 + k)) for c in (r, g, b))
    else:
        r, g, b = (int(c + (255 - c) * k) for c in (r, g, b))
    return f"#{r:02x}{g:02x}{b:02x}"


def _sparkle(x, y, r, fill, op=1.0):
    return (f'<path d="M{x},{y - r} Q{x},{y} {x + r},{y} Q{x},{y} {x},{y + r} Q{x},{y} {x - r},{y} Q{x},{y} {x},{y - r}Z" '
            f'fill="{fill}" fill-opacity="{op}"/>')


def _star5(x, y, r, fill):
    pts = []
    for i in range(10):
        a = math.radians(-90 + i * 36)
        rr = r if i % 2 == 0 else r * 0.45
        pts.append(f"{x + rr * math.cos(a):.1f},{y + rr * math.sin(a):.1f}")
    return f'<polygon points="{" ".join(pts)}" fill="{fill}"/>'


def m_truman(u):
    defs = (f'<linearGradient id="{u}g" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#0b2a52"/>'
            f'<stop offset="0.5" stop-color="#2f7bc4"/><stop offset="1" stop-color="#b3dbf7"/></linearGradient>')
    s = [f'<rect width="400" height="200" fill="url(#{u}g)"/>']
    cx, cy = 200, 380  # kubbe ızgarası: alttan yükselen eş merkezli yaylar ve meridyenler
    for i in range(1, 14):
        r = 62 * i
        s.append(f'<ellipse cx="{cx}" cy="{cy}" rx="{r * 1.3:.0f}" ry="{r}" fill="none" stroke="#ffffff" stroke-opacity="0.24" stroke-width="1.1"/>')
    for a in range(-84, 85, 12):
        x = cx + 1000 * math.sin(math.radians(a))
        y = cy - 1000 * math.cos(math.radians(a))
        s.append(f'<line x1="{cx}" y1="{cy}" x2="{x:.0f}" y2="{y:.0f}" stroke="#ffffff" stroke-opacity="0.18" stroke-width="1"/>')
    # boyalı gökyüzü bulutları (düz, stilize)
    for (x, y, k) in [(70, 120, 1.0), (175, 150, 0.8), (250, 105, 0.65), (360, 160, 0.9)]:
        s.append(f'<g fill="#ffffff" opacity="0.9"><ellipse cx="{x}" cy="{y}" rx="{30 * k:.0f}" ry="{9 * k:.0f}"/>'
                 f'<circle cx="{x - 10 * k:.0f}" cy="{y - 6 * k:.0f}" r="{11 * k:.0f}"/><circle cx="{x + 8 * k:.0f}" cy="{y - 10 * k:.0f}" r="{14 * k:.0f}"/></g>')
    # düşen spot ışığı
    s.append('<g transform="translate(322,70) rotate(-24)">'
             '<polygon points="-10,16 10,16 52,170 -52,170" fill="#f2b705" fill-opacity="0.32"/>'
             '<path d="M-17,-2 A17,17 0 0 1 17,-2" stroke="#eef3f8" stroke-width="2.6" fill="none"/>'
             '<rect x="-13" y="-4" width="26" height="22" rx="3" fill="#18202c" stroke="#eef3f8" stroke-width="1.8"/>'
             '<ellipse cx="0" cy="18" rx="12.5" ry="3.8" fill="#ffd84a"/></g>')
    s.append('<g stroke="#ffffff" stroke-width="2.2" stroke-linecap="round" opacity="0.85">'
             '<line x1="297" y1="8" x2="303" y2="30"/><line x1="311" y1="2" x2="317" y2="24"/><line x1="325" y1="10" x2="330" y2="32"/></g>')
    return _svg("".join(s), defs, ALIGN["truman"])


def m_io2(u):
    rnd = random.Random(2)
    defs = [f'<linearGradient id="{u}g" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#22103a"/><stop offset="1" stop-color="#4d2885"/></linearGradient>']
    cols = ["#ffd23f", "#3a86ff", "#ef476f", "#06d6a0", "#9b5de5", "#f77f00", "#00b4d8", "#f15bb5"]
    for i, c in enumerate(cols):
        defs.append(f'<radialGradient id="{u}c{i}" cx="0.36" cy="0.32" r="0.75"><stop offset="0" stop-color="#ffffff" stop-opacity="0.95"/>'
                    f'<stop offset="0.28" stop-color="{c}"/><stop offset="1" stop-color="{_shade(c, -0.45)}"/></radialGradient>')
    s = [f'<rect width="400" height="200" fill="url(#{u}g)"/>']
    # girdaplar: Arşimet spiralleri
    for (cx, cy, turns, k, c, w) in [(120, 110, 3.2, 6.0, "#f77f00", 3), (300, 90, 2.6, 7.5, "#3a86ff", 3),
                                     (210, 150, 2.2, 5.0, "#06d6a0", 2.4), (40, 40, 2.0, 5.5, "#f15bb5", 2.2), (370, 175, 2.0, 5.0, "#ffd23f", 2.2)]:
        pts = []
        t = 0.0
        while t < turns * 2 * math.pi:
            r = 4 + k * t
            pts.append(f"{cx + r * math.cos(t):.1f},{cy + r * math.sin(t):.1f}")
            t += 0.12
        s.append(f'<polyline points="{" ".join(pts)}" fill="none" stroke="{c}" stroke-opacity="0.5" stroke-width="{w}" stroke-linecap="round"/>')
    # anı küreleri
    spheres = [(70, 150, 22), (155, 60, 16), (205, 115, 30), (262, 160, 14), (290, 45, 20), (345, 120, 26), (30, 95, 12),
               (110, 30, 10), (380, 60, 11), (240, 25, 9), (180, 180, 10), (320, 185, 9)]
    for i, (x, y, r) in enumerate(spheres):
        ci = rnd.randrange(len(cols)) if i > 5 else i
        s.append(f'<circle cx="{x}" cy="{y}" r="{r + 4}" fill="none" stroke="#ffffff" stroke-opacity="0.22" stroke-width="1.2"/>'
                 f'<circle cx="{x}" cy="{y}" r="{r}" fill="url(#{u}c{ci})"/>')
    return _svg("".join(s), "".join(defs), ALIGN["io2"])


def m_wonder(u):
    rnd = random.Random(7)
    defs = (f'<linearGradient id="{u}g" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#070d24"/><stop offset="1" stop-color="#25336a"/></linearGradient>'
            f'<radialGradient id="{u}p" cx="0.35" cy="0.3" r="0.8"><stop offset="0" stop-color="#9fb0ec"/><stop offset="1" stop-color="#2c3c7c"/></radialGradient>')
    s = [f'<rect width="400" height="200" fill="url(#{u}g)"/>']
    s.append('<ellipse cx="200" cy="230" rx="330" ry="150" fill="none" stroke="#ffffff" stroke-opacity="0.25" stroke-dasharray="3 6" stroke-width="1.2"/>')
    s.append('<ellipse cx="200" cy="260" rx="420" ry="210" fill="none" stroke="#ffffff" stroke-opacity="0.15" stroke-dasharray="2 7" stroke-width="1"/>')
    for _ in range(140):
        x, y = rnd.uniform(0, 400), rnd.uniform(0, 200)
        s.append(f'<circle cx="{x:.1f}" cy="{y:.1f}" r="{rnd.uniform(0.4, 1.5):.2f}" fill="#ffffff" fill-opacity="{rnd.uniform(0.45, 1):.2f}"/>')
    # gezegen ve halkası
    s.append('<g transform="translate(318,112)">'
             '<ellipse cx="0" cy="0" rx="66" ry="15" transform="rotate(-16)" fill="none" stroke="#e9a800" stroke-opacity="0.55" stroke-width="5"/>'
             f'<circle cx="0" cy="0" r="38" fill="url(#{u}p)"/>'
             '<path d="M-63,12 A66,15 -16 0 0 63,-24" transform="rotate(0)" fill="none" stroke="#ffc845" stroke-width="5"/></g>')
    for (x, y, r) in [(60, 50, 9), (150, 125, 6), (230, 40, 11), (110, 170, 7), (390, 30, 7), (20, 140, 5)]:
        s.append(_sparkle(x, y, r, "#ffc845"))
    for (x, y, r) in [(185, 85, 7), (255, 165, 5), (35, 25, 4)]:
        s.append(_star5(x, y, r, "#ffe08a"))
    s.append('<line x1="40" y1="95" x2="110" y2="70" stroke="#ffffff" stroke-width="1.6" stroke-linecap="round" stroke-opacity="0.8"/>')
    return _svg("".join(s), defs, ALIGN["wonder"])


def m_zoo(u):
    rnd = random.Random(11)
    defs = f'<linearGradient id="{u}g" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#0a3a3e"/><stop offset="1" stop-color="#6cbcb0"/></linearGradient>'
    s = [f'<rect width="400" height="200" fill="url(#{u}g)"/>', '<circle cx="320" cy="62" r="26" fill="#e07a2e" fill-opacity="0.85"/>']
    # arka sıra
    x = 0
    while x < 400:
        w = rnd.uniform(14, 30)
        h = rnd.uniform(50, 120)
        s.append(f'<rect x="{x:.1f}" y="{200 - h:.1f}" width="{w:.1f}" height="{h:.1f}" fill="#2a8a84" fill-opacity="0.55"/>')
        x += w + rnd.uniform(1, 4)
    front = []
    # sol: buz bölgesi, sivri kuleler
    for (x0, w, h) in [(4, 24, 92), (30, 18, 120), (50, 26, 78), (78, 16, 104)]:
        front.append(f'<polygon points="{x0},200 {x0},{200 - h + 18} {x0 + w / 2},{200 - h} {x0 + w},{200 - h + 18} {x0 + w},200" fill="#d5eef9"/>')
    # orta: basamaklı kuleler
    for (x0, w, h) in [(100, 34, 86), (138, 22, 132), (164, 30, 100), (198, 40, 70)]:
        front.append(f'<rect x="{x0}" y="{200 - h}" width="{w}" height="{h}" fill="#0b3438"/>'
                     f'<rect x="{x0 + w * 0.2:.1f}" y="{200 - h - 14}" width="{w * 0.6:.1f}" height="14" fill="#0b3438"/>')
        for wy in range(int(200 - h + 8), 192, 10):
            for wx in range(int(x0 + 4), int(x0 + w - 5), 7):
                if rnd.random() < 0.7:
                    front.append(f'<rect x="{wx}" y="{wy}" width="3.4" height="5" fill="#f4d35e"/>')
    # çöl bölgesi: kubbeler
    for (x0, r) in [(258, 22), (292, 16), (318, 26)]:
        front.append(f'<path d="M{x0 - r},200 L{x0 - r},{200 - r * 1.6:.0f} A{r},{r} 0 0 1 {x0 + r},{200 - r * 1.6:.0f} L{x0 + r},200Z" fill="#e07a2e"/>')
    # yağmur ormanı: yuvarlak tepeli kuleler
    for (x0, w, h) in [(346, 20, 110), (368, 26, 84), (392, 16, 96)]:
        front.append(f'<rect x="{x0}" y="{200 - h}" width="{w}" height="{h}" rx="{w / 2}" fill="#3f7d4f"/>'
                     f'<circle cx="{x0 + w / 2}" cy="{200 - h}" r="{w * 0.62:.1f}" fill="#8cb369"/>')
    s.extend(front)
    s.append('<rect x="0" y="194" width="400" height="6" fill="#082a2d"/>')
    return _svg("".join(s), defs, ALIGN["zoo"])


def m_soul(u):
    rnd = random.Random(5)
    defs = f'<linearGradient id="{u}g" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#1f0619"/><stop offset="1" stop-color="#5e1846"/></linearGradient>'
    s = [f'<rect width="400" height="200" fill="url(#{u}g)"/>']
    for _ in range(90):
        x, y = rnd.uniform(0, 400), rnd.uniform(0, 140)
        s.append(f'<circle cx="{x:.1f}" cy="{y:.1f}" r="{rnd.uniform(0.4, 1.3):.2f}" fill="#ffffff" fill-opacity="{rnd.uniform(0.4, 1):.2f}"/>')
    # ses dalgaları (caz)
    for (amp, freq, ph, y0, c, w) in [(16, 0.035, 0, 92, "#e3a72f", 3.2), (11, 0.05, 1.3, 104, "#3cc6e0", 2.4), (7, 0.07, 2.2, 82, "#f2a3c7", 1.8)]:
        pts = " ".join(f"{x},{y0 + amp * math.sin(freq * x + ph):.1f}" for x in range(0, 401, 4))
        s.append(f'<polyline points="{pts}" fill="none" stroke="{c}" stroke-width="{w}" stroke-linecap="round"/>')
    # kozmik kıvılcımlar
    for (x, y, r, c) in [(60, 40, 10, "#3cc6e0"), (145, 30, 6, "#ffffff"), (250, 50, 12, "#e3a72f"), (335, 32, 7, "#3cc6e0"), (380, 75, 5, "#ffffff"), (20, 120, 6, "#e3a72f")]:
        s.append(_sparkle(x, y, r, c))
    # piyano tuşları
    kw = 400 / 22
    for i in range(22):
        s.append(f'<rect x="{i * kw:.2f}" y="140" width="{kw:.2f}" height="60" fill="#f7f1e6" stroke="#1f0619" stroke-width="1.2"/>')
    for i in range(22):
        if i % 7 in (0, 1, 3, 4, 5):
            s.append(f'<rect x="{(i + 1) * kw - kw * 0.3:.2f}" y="140" width="{kw * 0.6:.2f}" height="36" rx="1.5" fill="#160410"/>')
    return _svg("".join(s), defs, ALIGN["soul"])


MOTIFS = dict(truman=m_truman, io2=m_io2, wonder=m_wonder, zoo=m_zoo, soul=m_soul)
_uid = [0]


def motif(key):
    _uid[0] += 1
    return MOTIFS[key](f"{key}{_uid[0]}")


STRIPE = '<div class="stripe"><span class="c1"></span><span class="c2"></span><span class="c3"></span><span class="c4"></span><span class="c5"></span></div>'
SCISSOR = ('<svg class="scissor" style="{pos}" viewBox="0 0 24 24"><g transform="rotate({rot} 12 12)" fill="none" stroke="#4a4a55" stroke-width="1.6">'
           '<circle cx="6" cy="18" r="3.2"/><circle cx="18" cy="18" r="3.2"/><line x1="8" y1="15.5" x2="17" y2="2"/><line x1="16" y1="15.5" x2="7" y2="2"/></g></svg>')
CUTS4 = ('<div class="cut-v"></div><div class="cut-h"></div>'
         + SCISSOR.format(pos="left:102mm;top:3mm", rot=0) + SCISSOR.format(pos="left:3mm;top:145.5mm", rot=-90))
CUTS2 = '<div class="cut-h"></div>' + SCISSOR.format(pos="left:3mm;top:145.5mm", rot=-90)


def doc(title, body):
    return (f'<!doctype html>\n<html lang="tr"><head><meta charset="utf-8"><title>{title}</title>'
            f'<link rel="stylesheet" href="../kit.css"></head><body>\n{body}\n</body></html>\n')


def lines(n):
    return '<div class="lines">' + "<div></div>" * n + "</div>"


# --------------------------------------------------------------------------------------
# Günlük ürünler
# --------------------------------------------------------------------------------------
def soru_pt(q):
    return 64 if len(q) <= 40 else (54 if len(q) <= 62 else 50)


def poster(d):
    return f'''<section class="sheet poster {d["t"]}">
  <div class="band">{motif(d["key"])}
    <div class="scrim badge"><div class="kicker">KAESU FİLM ATÖLYESİ</div><div class="day">{d["n"]}. GÜN</div></div>
  </div>
  <div class="body">
    <div class="film">{d["film"]}</div>
    <div class="tema"><span class="chip">{d["TEMA"]}</span> {d["tema_alt"]}</div>
    <div class="rule"></div>
    <div class="label">GÜNÜN ANA SORUSU</div>
    <div class="soru" style="font-size:{soru_pt(d["soru"])}pt">{d["soru"]}</div>
  </div>
  <div class="footer">{STRIPE}Kaesu Akademi · Film Atölyesi · 5 günlük kamp</div>
</section>'''


def notecard(d):
    one = f'''<div class="card">
      <div class="head">{motif(d["key"])}<div class="scrim">{d["n"]}. GÜN · {d["film"]}</div></div>
      <div class="lbl">İZLEME GÖREVİ</div>
      <div class="task">{d["gorev"]}</div>
      <div class="box"><div class="lbl">AKLIMA TAKILAN AN</div>{lines(4)}</div>
      <div class="box"><div class="lbl">BENİM SORUM</div>{lines(3)}</div>
      <div class="note">Görevle ilgisi olmayan bir şey de yazabilirsin.</div>
    </div>'''
    cells = "".join(f'<div class="cell c{i}">{one}</div>' for i in range(4))
    return f'<section class="sheet {d["t"]}"><div class="grid4">{cells}</div>{CUTS4}</section>'


def dilemma(d, side):
    head, sub = d[side]
    tag = "İSTEĞE BAĞLI İKİLEM" if d["n"] == 5 else "KAPANIŞ İKİLEMİ"
    return f'''<section class="sheet land dilemma {d["t"]}">
  <div class="band">{motif(d["key"])}<div class="scrim">{d["n"]}. GÜN · {tag} · {d["film"]}</div></div>
  <div class="letter">{side}</div>
  <div class="side"><div class="kicker">{side} DUVARI</div><div class="head">{head}</div><div class="sub">{sub}</div></div>
  <div class="q">{d["ikilem_q"]}</div>
</section>'''


# --------------------------------------------------------------------------------------
# Kamp geneli
# --------------------------------------------------------------------------------------
def legend():
    items = "".join(f'<div class="item {d["t"]}"><div class="sw">{motif(d["key"])}</div>'
                    f'<div class="tx">{d["n"]}. GÜN · {d["TEMA"]}<span>{d["film"]}</span></div></div>' for d in DAYS)
    return f'<div class="legend">{items}</div>'


def wall_pages():
    soru = f'''<section class="sheet land t-camp"><div style="position:absolute;left:14mm;right:14mm;top:12mm">
  {STRIPE}
  <div class="kicker" style="margin-top:9mm;color:var(--camp-dark)">KAESU FİLM ATÖLYESİ</div>
  <div class="camp-title" style="font-size:104pt;margin-top:3mm">SORU DUVARI</div>
  <p style="font-size:21pt;font-weight:700;margin-top:6mm;line-height:1.25">Her günün seçilen sorusu burada birikir. Altına, kapanış ikileminde çoğunluğun hangi duvarı seçtiğini yazarız.</p>
</div>
<div style="position:absolute;left:14mm;right:14mm;bottom:14mm">{legend()}</div></section>'''
    alanlar = "".join(f'<span class="pill">{a}</span>' for a in ["Yemekte", "Oyunda", "Odada", "Bir konuşmada", "Kurallarda", "Arkadaşlıkta"])
    hayat = f'''<section class="sheet land t-camp"><div style="position:absolute;left:14mm;right:14mm;top:12mm">
  {STRIPE}
  <div class="kicker" style="margin-top:9mm;color:var(--camp-dark)">KAESU FİLM ATÖLYESİ · FELSEFE HER YERDE</div>
  <div class="camp-title" style="font-size:92pt;margin-top:3mm">HAYATTAN SORULAR</div>
  <p style="font-size:21pt;font-weight:700;margin-top:6mm;line-height:1.25">Filmlerin dışında, kamp hayatında yakaladığımız felsefi sorular. Kartını buraya as.</p>
  <div style="margin-top:9mm">{alanlar}</div>
</div>
<div style="position:absolute;left:14mm;right:14mm;bottom:12mm">{STRIPE}</div></section>'''
    return soru + hayat


def door_pages():
    out = []
    for d in DAYS:
        alt = f'<div class="alt">{d["kapi_alt"]}</div>' if d["kapi_alt"] else ""
        out.append(f'''<section class="sheet door {d["t"]}">
  <div class="band">{motif(d["key"])}<div class="scrim"><div class="kicker">BEŞ KAPI</div><div class="n">{d["n"]}. KAPI</div></div></div>
  <div class="body">
    <div class="hero">{d["hero"]}</div>
    <div class="film">{d["film"]} · {d["n"]}. gün · {d["tema_alt"]}</div>
    <div class="choice">{d["kapi"]}</div>{alt}
  </div>
  <div class="q">Bu beş seçimden hangisi, iyi bir hayat için en önemlisi?</div>
  <div class="footer">{STRIPE}</div>
</section>''')
    return "".join(out)


def rules_pages():
    rules = [("Aynı anda tek kişi konuşur.", "Konuşma nesnesi kimdeyse söz onda."),
             ("Kişiye değil, fikre itiraz ederiz.", "Fikirler tartışılır, insanlar değil."),
             ("“Çünkü” en önemli kelimemiz.", "“Katılıyorum, çünkü…” · “Katılmıyorum, çünkü…”"),
             ("Pas geçmek serbest.", "Kimse konuşmak zorunda değil.")]
    rows = "".join(f'<div class="rule-row"><div class="num">{i}</div><div><div class="t">{t}</div><div class="s">{s}</div></div></div>'
                   for i, (t, s) in enumerate(rules, 1))
    p1 = f'''<section class="sheet t-camp"><div style="position:absolute;left:14mm;right:14mm;top:14mm">{STRIPE}
  <div class="kicker" style="margin-top:9mm;color:var(--camp-dark)">KAESU FİLM ATÖLYESİ</div>
  <div class="camp-title" style="font-size:50pt;margin-top:3mm;margin-bottom:14mm">Topluluk<br>Kurallarımız</div>
  {rows}</div><div class="footer">{STRIPE}Burada birini yenmeye değil, birlikte daha iyi düşünmeye çalışıyoruz.</div></section>'''
    bubbles = "".join(f'<div class="bubble">{b}</div>' for b in
                      ["Katılıyorum, çünkü…", "Katılmıyorum, çünkü…", "…’nın söylediğine eklemek istiyorum…", "Fikrimi değiştirdim, çünkü…", "Bir örnek vereyim…"])
    p2 = f'''<section class="sheet t-camp"><div style="position:absolute;left:14mm;right:14mm;top:14mm">{STRIPE}
  <div class="kicker" style="margin-top:9mm;color:var(--camp-dark)">KAESU FİLM ATÖLYESİ</div>
  <div class="camp-title" style="font-size:50pt;margin-top:3mm;margin-bottom:12mm">Konuşma<br>Kalıpları</div>
  {bubbles}</div><div class="footer">{STRIPE}</div></section>'''
    p3 = f'''<section class="sheet t-camp"><div style="position:absolute;left:14mm;right:14mm;top:14mm">{STRIPE}
  <div class="kicker" style="margin-top:9mm;color:var(--camp-dark)">KAESU FİLM ATÖLYESİ</div>
  <div class="camp-title" style="font-size:50pt;margin-top:3mm;margin-bottom:12mm">Felsefi soru<br>nedir?</div>
  <p class="serif" style="font-size:38pt;font-weight:800;line-height:1.12">Cevabı filmde olmayan, akıllı insanların bile farklı cevaplar verebileceği soru.</p>
  <p style="font-size:24pt;font-weight:700;margin-top:14mm">Genelde şöyle başlar:</p>
  <div style="margin-top:4mm"><span class="pill" style="font-size:32pt">Neden…?</span><span class="pill" style="font-size:32pt">…olmalı mı?</span><span class="pill" style="font-size:32pt">…ne demek?</span></div>
  <table style="width:100%;margin-top:16mm;border-collapse:collapse;font-size:21pt;table-layout:fixed">
    <tr><td style="width:50%;padding:4mm;border:0.6mm solid var(--line);vertical-align:top"><b>Film sorusu</b><br>Cevabı filmde var.<br><i>“Köpeğin kaç bacağı vardı?”</i></td>
        <td style="padding:4mm;border:0.6mm solid var(--camp-dark);vertical-align:top"><b>Felsefi soru</b><br>Film bitince de tartışılır.<br><i>“Başkasında beğenmediğimiz şey bazen kendimizde de olabilir mi?”</i></td></tr>
  </table></div><div class="footer">{STRIPE}</div></section>'''
    return p1 + p2 + p3


# --------------------------------------------------------------------------------------
# Ürün listesi: dosya adı -> (başlık, gövde, beklenen sayfa sayısı)
# --------------------------------------------------------------------------------------
def products():
    p = {}
    for d in DAYS:
        g = f"gun{d['n']}-{d['key']}"
        p[f"{g}-afis"] = (f"{d['n']}. Gün afişi · {d['film']}", poster(d), 1)
        p[f"{g}-not-karti"] = (f"{d['n']}. Gün not kartı · {d['film']}", notecard(d), 1)
        p[f"{g}-ikilem"] = (f"{d['n']}. Gün ikilem tabelaları · {d['film']}", dilemma(d, "A") + dilemma(d, "B"), 2)
    p["kamp-soru-duvari-basliklari"] = ("Soru Duvarı ve Hayattan Sorular", wall_pages(), 2)
    p["kamp-bes-kapi"] = ("Beş Kapı köşe kartları", door_pages(), 5)
    p["kamp-topluluk-kurallari"] = ("Topluluk Kuralları", rules_pages(), 3)
    return p


def main():
    import weasyprint
    import pypdfium2 as pdfium
    review = None
    if "--inceleme" in sys.argv:
        review = pathlib.Path(sys.argv[sys.argv.index("--inceleme") + 1])
        review.mkdir(parents=True, exist_ok=True)
    only = [a for a in sys.argv[1:] if not a.startswith("--") and (review is None or a != str(review))]
    for d in (HTML_DIR, PDF_DIR, PNG_DIR):
        d.mkdir(exist_ok=True)
    problems = []
    for name, (title, body, expected) in products().items():
        if only and not any(o in name for o in only):
            continue
        hp = HTML_DIR / f"{name}.html"
        hp.write_text(doc(title, body), encoding="utf-8")
        pp = PDF_DIR / f"{name}.pdf"
        weasyprint.HTML(filename=str(hp)).write_pdf(str(pp))
        pdf = pdfium.PdfDocument(str(pp))
        n = len(pdf)
        for old in PNG_DIR.glob(f"{name}-s*.png"):
            old.unlink()
        for i, page in enumerate(pdf, 1):
            page.render(scale=1).to_pil().save(PNG_DIR / f"{name}-s{i}.png", optimize=True)
            if review:
                page.render(scale=110 / 72).to_pil().save(review / f"{name}-s{i}.png")
        pdf.close()
        flag = "" if n == expected else f"  <-- beklenen {expected} sayfa"
        if flag:
            problems.append(name)
        print(f"{name:34s} {n:2d} sayfa{flag}")
    if problems:
        print("SAYFA SAYISI UYUMSUZ:", ", ".join(problems))
        sys.exit(1)


if __name__ == "__main__":
    main()
