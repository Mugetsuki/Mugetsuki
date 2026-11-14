"""Düşünce Yolculuğum yaprakları ve rozet yaprağı (Filya renkleri, logo yok).

Patika milimetre cinsinden çizilir (SVG viewBox birimi = 1 mm). Böylece rozet yaprağındaki
kesilecek parça, %100 baskıda patikadaki rozetin ve etiketinin üstüne birebir oturur.
Motifler build.py'deki özgün SVG motiflerdir; karakter, afiş ya da logo yoktur.
"""
import build as kit

# --------------------------------------------------------------------------------------
# Rozetler: anahtar -> (gün, kısa film adı)
# --------------------------------------------------------------------------------------
ROZETLER = {
    "truman": (1, "The Truman Show"),
    "io2": (2, "Ters Yüz 2"),
    "wonder": (3, "Mucize"),
    "zoo": (4, "Zootropolis"),
    "soul": (5, "Soul"),
    "robot": (2, "Vahşi Robot"),
    "adam": (4, "12 Kızgın Adam"),
}
ANA = ["truman", "io2", "wonder", "zoo", "soul"]

# Patika geometrisi (mm, patika kutusunun sol üst köşesine göre)
R_ROZET = 8.0          # rozetin dış yarıçapı (çap 16 mm)
R_ICI = 6.9            # motif dairesinin yarıçapı
ETIKET_G, ETIKET_Y = 26.0, 7.0   # rozet altındaki etiket kutusu
ETIKET_ARA = 1.5       # rozet ile etiket arası
TASMA = 1.0            # rozet parçasında kesim payı
PATIKA_G, PATIKA_Y = 176.0, 43.0
UST, ALT = 11.0, 23.0  # rozet merkezlerinin iki yüksekliği
DURAK_X = [8.0, 34.0, 61.0, 88.0, 115.0, 142.0, 168.0]   # başlangıç, 5 durak, varış
DURAK_Y = [ALT, UST, ALT, UST, ALT, UST, ALT]


def _mini(key, u):
    """Motifin rozet içi küçük hâli: merkez (0,0), yarıçap 25 birim."""
    r = 25
    g = []
    if key == "truman":
        g.append(f'<defs><linearGradient id="{u}g" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#0f3460"/><stop offset="1" stop-color="#9fd0f3"/></linearGradient></defs>')
        g.append(f'<rect x="-25" y="-25" width="50" height="50" fill="url(#{u}g)"/>')
        for k in (1, 2, 3):
            g.append(f'<ellipse cx="0" cy="{r + 6}" rx="{9 * k * 1.3}" ry="{9 * k}" fill="none" stroke="#fff" stroke-opacity=".45" stroke-width="1"/>')
        g.append('<polygon points="5,-8 9,-8 16,14 -2,14" fill="#f2b705" fill-opacity=".55"/><rect x="3" y="-14" width="8" height="7" rx="1.5" fill="#18202c"/>')
    elif key == "io2":
        g.append('<rect x="-25" y="-25" width="50" height="50" fill="#3b1e61"/>')
        for (dx, dy, rr, c) in [(-7, -4, 7, "#ffd23f"), (7, -7, 5, "#3a86ff"), (5, 8, 6, "#ef476f"), (-9, 10, 4, "#06d6a0")]:
            g.append(f'<circle cx="{dx}" cy="{dy}" r="{rr}" fill="{c}"/><circle cx="{dx - rr * .35:.1f}" cy="{dy - rr * .35:.1f}" r="{rr * .32:.1f}" fill="#fff" fill-opacity=".8"/>')
    elif key == "wonder":
        g.append('<rect x="-25" y="-25" width="50" height="50" fill="#101a3a"/>')
        g.append(kit._sparkle(-5, -4, 8, "#ffc845"))
        g.append('<circle cx="8" cy="7" r="5" fill="#8fa2e6"/><ellipse cx="8" cy="7" rx="9" ry="2.4" fill="none" stroke="#ffc845" stroke-width="1.3"/>')
        for (dx, dy) in [(9, -10), (-11, 9), (0, 12), (-12, -11)]:
            g.append(f'<circle cx="{dx}" cy="{dy}" r="1" fill="#fff"/>')
    elif key == "zoo":
        g.append('<rect x="-25" y="-25" width="50" height="50" fill="#5fb3a8"/><circle cx="8" cy="-8" r="5" fill="#e07a2e"/>')
        for (dx, w, h) in [(-16, 7, 16), (-8, 6, 24), (-1, 8, 13), (8, 6, 19), (14, 8, 11)]:
            g.append(f'<rect x="{dx}" y="{r - h}" width="{w}" height="{h}" fill="#0b3438"/>')
        g.append(f'<polygon points="-16,{r - 16} -12.5,{r - 22} -9,{r - 16}" fill="#d5eef9"/>')
    elif key == "soul":
        g.append('<rect x="-25" y="-25" width="50" height="50" fill="#4a1238"/>')
        kw = 2 * r / 6
        for i in range(6):
            g.append(f'<rect x="{-r + i * kw:.1f}" y="2" width="{kw:.1f}" height="{r}" fill="#f7f1e6" stroke="#1f0619" stroke-width=".8"/>')
        for i in (0, 1, 3, 4):
            g.append(f'<rect x="{-r + (i + 1) * kw - kw * .3:.1f}" y="2" width="{kw * .6:.1f}" height="9" fill="#160410"/>')
        g.append(kit._sparkle(0, -9, 7, "#3cc6e0"))
    elif key == "robot":
        g.append(kit.robot_mini())
    elif key == "adam":
        g.append(kit.adam_mini())
    return "".join(g)


def rozet(key, cx, cy, u):
    """Patikadaki ve rozet yaprağındaki rozetin aynısı (mm)."""
    s = R_ICI / 25
    return (f'<circle cx="{cx}" cy="{cy}" r="{R_ROZET}" fill="#FAF7E3" stroke="#202B23" stroke-width=".55"/>'
            f'<clipPath id="{u}c"><circle cx="{cx}" cy="{cy}" r="{R_ICI}"/></clipPath>'
            f'<g clip-path="url(#{u}c)"><g transform="translate({cx},{cy}) scale({s:.4f})">{_mini(key, u)}</g></g>')


def etiket_html(left, top, ust, alt, hiza="center"):
    return (f'<div class="rz-et" style="left:{left:.2f}mm;top:{top:.2f}mm;text-align:{hiza}">'
            f'<b>{ust}</b><span>{alt}</span></div>')


def patika(vurgu, anahtarlar=None):
    """vurgu: 'baslangic' ya da 'varis'. anahtarlar: 5 günün rozet anahtarları."""
    anahtarlar = anahtarlar or ANA
    u = f"pt{vurgu}"
    pts = list(zip(DURAK_X, DURAK_Y))
    d = f"M{pts[0][0]},{pts[0][1]}"
    for (x0, y0), (x1, y1) in zip(pts, pts[1:]):
        mx = (x0 + x1) / 2
        d += f" C{mx},{y0} {mx},{y1} {x1},{y1}"
    s = [f'<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 {PATIKA_G} {PATIKA_Y}" style="width:{PATIKA_G}mm;height:{PATIKA_Y}mm">']
    s.append(f'<path d="{d}" fill="none" stroke="#D9D2A6" stroke-width="2.6" stroke-linecap="round"/>')
    if vurgu == "varis":
        s.append(f'<path d="{d}" fill="none" stroke="#FFD85F" stroke-width="2.6" stroke-linecap="round"/>')
    s.append(f'<path d="{d}" fill="none" stroke="#6E7A6F" stroke-width=".6" stroke-dasharray=".6 2" stroke-linecap="round"/>')
    for i, key in enumerate(anahtarlar, 1):
        s.append(rozet(key, DURAK_X[i], DURAK_Y[i], f"{u}{i}"))
    bx, by = DURAK_X[0], DURAK_Y[0]
    ex, ey = DURAK_X[-1], DURAK_Y[-1]
    vurgulu = lambda x, y: f'<circle cx="{x}" cy="{y}" r="6.2" fill="#FFD85F" fill-opacity=".6"/><circle cx="{x}" cy="{y}" r="3.8" fill="#F47A3C" stroke="#202B23" stroke-width=".55"/>'
    sade = lambda x, y: f'<circle cx="{x}" cy="{y}" r="2.6" fill="#FAF7E3" stroke="#202B23" stroke-width=".55"/>'
    s.append(vurgulu(bx, by) if vurgu == "baslangic" else sade(bx, by))
    s.append(vurgulu(ex, ey) if vurgu == "varis" else sade(ex, ey))
    s.append("</svg>")
    et = []
    for i, key in enumerate(anahtarlar, 1):
        gun, film = ROZETLER[key]
        et.append(etiket_html(DURAK_X[i] - ETIKET_G / 2, DURAK_Y[i] + R_ROZET + ETIKET_ARA, f"{i}. gün", film))
    ust_b, alt_b = ("Buradasın", "1. gün, filmden önce") if vurgu == "baslangic" else ("Başlangıç", "1. gün")
    ust_v, alt_v = ("Buradasın", "Varış, 5. gün") if vurgu == "varis" else ("Varış", "5. gün")
    et.append(etiket_html(0, by + 5, ust_b, alt_b, "left").replace('class="rz-et"', 'class="rz-et uc"'))
    et.append(etiket_html(PATIKA_G - 30, ey + 5, ust_v, alt_v, "right").replace('class="rz-et"', 'class="rz-et uc"'))
    return f'<div class="patika">{"".join(s)}{"".join(et)}</div>'


# --------------------------------------------------------------------------------------
# Yapraklar
# --------------------------------------------------------------------------------------
IFADELER = [
    ("A", "Zor soruların tek bir doğru cevabı vardır."),
    ("B", "Fikrimi söylerken nedenini de söylerim."),
    ("C", "İyi bir gerekçe duyarsam fikrimi değiştiririm."),
    ("D", "Felsefe filozofların ve büyüklerin işidir."),
    ("E", "Yemek, oyun, kurallar gibi sıradan şeylerin altında da büyük sorular vardır."),
]

# Gün yaprakları: gün -> (film, kavram sorusu, hayattan soru ipucu, motif anahtarı)
GUN_YAPRAKLARI = {
    1: ("The Truman Show", "Bilmek nedir?",
        "Kampta “Bundan gerçekten emin miyim?” dediğin bir an. Yarın sabaha kadar yakalayıp buraya yaz.", "truman"),
    2: ("Ters Yüz 2", "“Kendin ol” ne demek, eğer “ben” sürekli değişiyorsa?",
        "Kampta “Burada başka biri gibi davranıyorum” dediğin bir an. Yarın sabaha kadar yakalayıp buraya yaz.", "io2"),
    3: ("Mucize", "İyi bir insan olmak ile iyi bir hayat yaşamak aynı şey mi?",
        "Kimsenin görmediği bir an: “Kimse görmeseydi yine böyle davranır mıydım?” Yarın sabaha kadar yakalayıp buraya yaz.", "wonder"),
    4: ("Zootropolis", "Adil olmak herkese aynı davranmak mı?",
        "Kampta “Bu kural herkes için aynı, ama herkes için adil mi?” dediğin bir an. Yarın sabaha kadar yakalayıp buraya yaz.", "zoo"),
    5: ("Soul", "Hayatı yaşamaya değer kılan bir amaca ulaşmak mı, yaşamanın kendisi mi?",
        "Kampta fark ettiğin sıradan ama değerli bir an: senin “okyanusun”.", "soul"),
}


def lines(n, cls="genis"):
    return f'<div class="lines {cls}">' + "<div></div>" * n + "</div>"


def cb():
    return '<span class="cb"></span>'


def dot():
    return '<span class="dot"></span>'


def ust_serit(sag_etiket):
    return (f'<div class="ust-serit"><div class="label">KAESU FELSEFE ATÖLYESİ · DÜŞÜNCE YOLCULUĞUM<br>'
            f'<span style="letter-spacing:0;font-weight:400">filmlerle düşünmek · {sag_etiket}</span></div>'
            f'<div class="ad"><span>Kamp adım:</span><span class="fill"></span></div></div>')


def olcek_tablo():
    rows = "".join(f'<tr><td><b>{k}.</b> {t}</td>' + "".join(f'<td class="c">{dot()}</td>' for _ in range(5)) + "</tr>" for k, t in IFADELER)
    return f'<table class="olcek"><tr><th style="width:56%"></th><th>1<br>hiç</th><th>2</th><th>3</th><th>4</th><th>5<br>tamamen</th></tr>{rows}</table>'


def yaprak_baslangic():
    on = f'''<section class="sheet yaprak">
  {ust_serit("Başlangıç yaprağı")}
  <div class="title" style="font-size:28pt;margin-top:5mm">Düşünce yolculuğum</div>
  {patika("baslangic")}
  <div class="blok-sari" style="margin-top:4mm">
    <div class="label" style="color:var(--koyu)">KAMP SORUSU</div>
    <div class="title" style="font-size:30pt;margin-top:1mm">İyi bir hayat nedir?</div>
  </div>
  <p style="margin-top:6mm;font-size:12.5pt">Daha hiçbir film izlemeden, şu an ne düşünüyorsun? Doğru cevap yok. Beşinci gün bu yaprağı yeniden okuyacaksın.</p>
  <div class="alan"><div class="alan-etiket">Bence iyi bir hayat …</div>{lines(4)}</div>
  <div class="alan"><div class="alan-etiket">… çünkü</div>{lines(4)}</div>
  <div class="alt-not">Arka yüzde devam ediyor.</div>
</section>'''
    arka = f'''<section class="sheet yaprak">
  {ust_serit("Başlangıç yaprağı · arka yüz")}
  <div class="kimlik" style="margin-top:8mm;font-size:12pt">
    <div class="row"><span>Yaşım:</span><span class="fill" style="width:18mm"></span><span style="margin-left:10mm">{cb()} Ortaokul</span><span style="margin-left:5mm">{cb()} Lise</span></div>
  </div>
  <div class="alan" style="margin-top:9mm">
    <div class="alan-etiket">Bu cümlelere ne kadar katılıyorsun?</div>
    <div class="alan-ipucu">Her satırda bir yuvarlak işaretle. 1 = hiç katılmıyorum, 5 = tamamen katılıyorum.</div>
    <div class="kart" style="margin-top:4mm;padding:4mm 6mm">{olcek_tablo()}</div>
  </div>
  <div class="alan" style="margin-top:12mm">
    <div class="alan-etiket">Günlük hayatımda felsefe … karşıma çıkar.</div>
    <div class="alan-ipucu">Bilmiyorsan “bilmiyorum” yaz. Bu da bir cevap.</div>{lines(4)}
  </div>
  <div class="alt-not">Bu yaprak senin yolculuğunun başlangıcı. Kamp boyunca her gün bir yaprak daha ekleyeceğiz.</div>
</section>'''
    return on + arka


def yaprak_gun(n, film, kavram, hayattan_ipucu, motif_anahtar, yedek=False):
    noktalar = "".join(f'<div class="sk">{dot()}{i}</div>' for i in range(1, 6))
    ilerleme = "".join('<i class="on"></i>' if i == n else "<i></i>" for i in range(1, 6))
    if n == 5:
        cevap = f'''<div class="alan"><div class="alan-etiket">Bugün kafamı en çok karıştıran soru</div>{lines(3)}</div>
  <div class="alan"><div class="alan-etiket">Hayattan bir soru</div><div class="alan-ipucu">{hayattan_ipucu}</div>{lines(3)}</div>
  <div class="alan"><div class="alan-etiket">Kamp sorusuna bugünkü cevabın</div><div class="alan-ipucu">Bugünkü cevabını birazdan Varış yaprağına yazacaksın.</div></div>'''
    else:
        cevap = f'''<div class="alan"><div class="alan-etiket">Kamp sorusuna bugünkü cevabım</div>
    <div class="alan-ipucu">“İyi bir hayat … çünkü …” Değişmediyse “Değişmedi, çünkü …” yaz. Bu da iyi bir cevap.</div>{lines(4)}</div>
  <div class="alan"><div class="alan-etiket">Bugün kafamı en çok karıştıran soru</div>{lines(2)}</div>
  <div class="alan"><div class="alan-etiket">Hayattan bir soru</div>
    <div class="alan-ipucu">{hayattan_ipucu}</div>{lines(2)}</div>'''
    alt_not = "Yaprağını moderatöre ver. Yarın sabah dosyana eklenmiş olarak geri gelecek." if n < 5 else "Yaprağını dosyana koy. Şimdi Varış yaprağına geçiyoruz."
    sag = f"{n}. gün yaprağı" + (" · yedek film" if yedek else "")
    return f'''<section class="sheet yaprak gun">
  {ust_serit(sag)}
  <div class="gun-bant">{kit.motif(motif_anahtar)}
    <div class="ilerleme">Yolculuk {ilerleme}</div>
    <div class="etiket"><span class="pill sari" style="font-size:12pt">{n}. gün</span><span class="title">{film}</span></div>
  </div>
  <div class="soru-kutu">
    <div class="label" style="color:var(--koyu)">BUGÜNÜN SORUSU</div>
    <div class="title" style="font-size:16pt;margin-top:1mm">{kavram}</div>
    <div style="margin-top:2mm;font-size:10.5pt">Kamp sorusu: İyi bir hayat nedir?</div>
  </div>
  {cevap}
  <div class="alan"><div class="alan-etiket">Bugünkü atölye nasıldı?</div>
    <div class="keyif-genis"><span class="ipucu">hiç sevmedim</span>{noktalar}<span class="ipucu">çok sevdim</span></div></div>
  <div class="alt-not">{alt_not}</div>
</section>'''


def yaprak_varis():
    on = f'''<section class="sheet yaprak">
  {ust_serit("Varış yaprağı")}
  <div class="title" style="font-size:28pt;margin-top:5mm">Varış</div>
  {patika("varis")}
  <p style="margin-top:3mm;font-size:12.5pt">Önce Başlangıç yaprağını aç ve ilk gün ne yazdığını oku. Sesli okumak yok.</p>
  <div class="alan" style="margin-top:6mm"><div class="alan-etiket">1. Şimdi: Bence iyi bir hayat … çünkü …</div>{lines(4)}</div>
  <div class="alan"><div class="alan-etiket">2. Ne değişti, neden?</div><div class="alan-ipucu">Değişmediyse neden değişmediğini yaz. Bu da iyi bir cevap.</div>{lines(4)}</div>
  <div class="alt-not">Arka yüzde devam ediyor.</div>
</section>'''
    arka = f'''<section class="sheet yaprak">
  {ust_serit("Varış yaprağı · arka yüz")}
  <div class="blok-sari" style="margin-top:7mm;padding:5mm 7mm 3mm">
    <div class="alan-etiket">3. Benim büyük sorum</div>
    <div class="alan-ipucu" style="color:var(--koyu)">Yapraklarındaki “kafamı karıştıran sorulardan” birini seçebilir ya da yenisini yazabilirsin.</div>{lines(2)}
    <div class="alan-etiket" style="margin-top:4mm">Şimdilik cevabım … çünkü …</div>{lines(3)}
  </div>
  <div class="alan"><div class="alan-etiket">4. Bu soru hayatımda nerede karşıma çıkıyor?</div><div class="alan-ipucu">Bir yer, bir an, bir kişi.</div>{lines(2)}</div>
  <div class="alan" style="margin-top:10mm"><div class="alan-etiket">5. Kamp genel olarak nasıldı?</div>
    <div class="keyif-genis"><span class="ipucu">hiç sevmedim</span>{"".join(f'<div class="sk">{dot()}{i}</div>' for i in range(1, 6))}<span class="ipucu">çok sevdim</span></div></div>
  <div class="alan"><div class="alan-etiket">6. Değişmesini istediğim bir şey</div>{lines(2)}</div>
  <div class="alt-not">Varış yaprağının ikinci sayfasına geç.</div>
</section>'''
    olcek = f'''<section class="sheet yaprak">
  {ust_serit("Varış yaprağı · 2. sayfa")}
  <div class="alan" style="margin-top:9mm">
    <div class="alan-etiket">7. Bu cümlelere şimdi ne kadar katılıyorsun?</div>
    <div class="alan-ipucu">İlk gün de aynı cümleleri işaretlemiştin. Şimdi ne düşünüyorsan onu işaretle.</div>
    <div class="kart" style="margin-top:4mm;padding:4mm 6mm">{olcek_tablo()}</div>
  </div>
  <div class="alan" style="margin-top:9mm"><div class="alan-etiket">Günlük hayatımda felsefe … karşıma çıkar.</div>{lines(2)}</div>
  <div class="kart" style="margin-top:7mm;padding:4mm 6mm;font-size:11pt">
    <b>8. Yazdıklarım kullanılabilir mi?</b> Birini işaretle. Hiçbirini işaretlemezsen kullanılmaz.<br>
    <div style="margin-top:2mm;line-height:2">{cb()} Hiçbir yerde kullanılmasın.<br>{cb()} Adım olmadan kullanılabilir.<br>{cb()} Adım ve yaşımla kullanılabilir. Adım: <span class="fill" style="width:50mm"></span></div>
  </div>
  <div class="alt-not">Teşekkürler. Beş gün boyunca birlikte düşündük. Büyük sorun seninle geliyor.</div>
</section>'''
    return on + arka + olcek


# --------------------------------------------------------------------------------------
# Rozet yaprağı
# --------------------------------------------------------------------------------------
PARCA_G = ETIKET_G + 2 * TASMA                                  # 28 mm
PARCA_Y = TASMA + 2 * R_ROZET + ETIKET_ARA + ETIKET_Y + TASMA   # 26.5 mm
ARA = 3.0


def rozet_parca(key, left, top, u):
    gun, film = ROZETLER[key]
    cx, cy = PARCA_G / 2, TASMA + R_ROZET
    svg = (f'<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 {PARCA_G} {PARCA_Y}" style="position:absolute;left:0;top:0;width:{PARCA_G}mm;height:{PARCA_Y}mm">'
           f'<rect x="0" y="0" width="{PARCA_G}" height="{PARCA_Y}" rx="3" fill="#F3EEC8"/>'
           f'{rozet(key, cx, cy, u)}'
           f'<rect x=".15" y=".15" width="{PARCA_G - .3}" height="{PARCA_Y - .3}" rx="3" fill="none" stroke="#6E7A6F" stroke-width=".3" stroke-dasharray="1.2 .9"/></svg>')
    et = etiket_html(TASMA, cy + R_ROZET + ETIKET_ARA, f"{gun}. gün", film)
    return f'<div class="rz-parca" style="left:{left:.2f}mm;top:{top:.2f}mm;width:{PARCA_G}mm;height:{PARCA_Y}mm">{svg}{et}</div>'


def rozet_yapragi(anahtarlar):
    """Bir A4'e sığan en fazla sayıda tam set (her rozetten 2: Başlangıç ve Varış)."""
    sol, ust_k, sag, alt_k = 12.0, 30.0, 12.0, 14.0
    sutun = int((210 - sol - sag + ARA) // (PARCA_G + ARA))
    satir = int((297 - ust_k - alt_k + ARA) // (PARCA_Y + ARA))
    kapasite = sutun * satir
    set_boyu = 2 * len(anahtarlar)
    set_sayisi = kapasite // set_boyu
    sira = [k for _ in range(set_sayisi) for k in anahtarlar for _ in range(2)]
    x0 = (210 - (sutun * PARCA_G + (sutun - 1) * ARA)) / 2
    parcalar = []
    for i, key in enumerate(sira):
        r, c = divmod(i, sutun)
        parcalar.append(rozet_parca(key, x0 + c * (PARCA_G + ARA), ust_k + r * (PARCA_Y + ARA), f"rz{i}"))
    bas = (f'<div style="position:absolute;left:{sol}mm;right:{sag}mm;top:11mm"><div class="label">KAESU FELSEFE ATÖLYESİ · ROZET YAPRAĞI</div>'
           f'<div style="font-size:9.5pt;color:var(--muted);margin-top:1mm">%100 ölçekte basın. Kesikli çizgiden kesin ve patikadaki rozetin üstüne, rozet rozete oturacak şekilde yapıştırın. '
           f'Bu sayfada {set_sayisi} set var: her set bir çocuğun Başlangıç ve Varış yaprakları içindir.</div></div>')
    return f'<section class="sheet" style="padding:0">{bas}{"".join(parcalar)}</section>', set_sayisi, kapasite
