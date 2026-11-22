"""Filmlerle Düşünme Rehberi (atölyeye katılamayan çocuklar için), Filya renkleriyle, logo yok.

Kaynak: ../katilamayanlar/filmlerle-dusunme-rehberi-kaynak.md (içerik Genel Müdür'ün; metin değiştirilmez,
yalnızca bileşenlere yerleştirilir). Bileşenler kitapçık ve defter yapraklarıyla aynıdır:
film motif bandı (build.motif), afiş ve IMDb künyesi, izleme görevi kartı, durak kartı, A/B ikilem kutuları.
"""
import pathlib
import re

import markdown

import build as kit

ROOT = pathlib.Path(__file__).resolve().parent
KAYNAK = ROOT.parent / "katilamayanlar" / "filmlerle-dusunme-rehberi-kaynak.md"

# film sırası -> (motif anahtarı, afiş, imdb, künye)
FILMLER = {
    1: ("truman", "gun1-truman.jpg", "tt0120382", "Peter Weir, 1998, 103 dk"),
    2: ("io2", "gun2-io2.jpg", "tt22022452", "Kelsey Mann, 2024, 96 dk"),
    3: ("wonder", "gun3-wonder.jpg", "tt2543472", "Stephen Chbosky, 2017, 113 dk"),
    4: ("zoo", "gun4-zoo.jpg", "tt2948356", "Byron Howard, Rich Moore, 2016, 108 dk"),
    5: ("soul", "gun5-soul.jpg", "tt2948372", "Pete Docter, Kemp Powers, 2020, 100 dk"),
}


def tirnak(t):
    """Düz çift tırnakları kıvrık tırnağa çevirir (yalnızca dizgi; metin aynı kalır)."""
    return re.sub(r'"', "”", re.sub(r'(^|[\s(\[])"', r"\1“", t))


def il(t):
    """Tek paragraflık markdown'ı satır içi HTML'e çevirir."""
    h = markdown.markdown(tirnak(t.strip()))
    return re.sub(r"^<p>(.*)</p>$", r"\1", h, flags=re.S)


def blok(t):
    t = re.sub(r"(?m)^((?![ \t]*(?:[-*]|\d+\.)[ \t]).+)\n([ \t]*(?:[-*]|\d+\.)[ \t])", r"\1\n\n\2", t.strip())
    return markdown.markdown(tirnak(t))


def lines(n):
    return '<div class="lines">' + "<div></div>" * n + "</div>"


def ust_serit(sag):
    return (f'<div class="ust-serit"><div class="label">KAESU FELSEFE ATÖLYESİ · FİLMLERLE DÜŞÜNME REHBERİ</div>'
            f'<div class="label" style="letter-spacing:0.04em">{sag}</div></div>')


def bolumler():
    md = KAYNAK.read_text(encoding="utf-8")
    bas, *secs = re.split(r"(?m)^## ", md)
    return bas, [(s.split("\n", 1)[0].strip(), s.split("\n", 1)[1]) for s in secs]


def kapak(bas, filmler):
    giris = re.search(r"\*\*Kampın büyük sorusu: İyi bir hayat nedir\?\*\* (.+)", bas).group(1).strip()
    satir = "".join(
        f'<div class="film"><div class="sw">{kit.motif(FILMLER[n][0])}</div>'
        f'<div><div class="ad">{ad}</div><div class="bs">{soru}</div></div></div>'
        for n, ad, soru in filmler)
    return (f'<section class="rh-kapak">'
            f'<div class="ust-label">KAESU FELSEFE ATÖLYESİ · FİLMLERLE DÜŞÜNME REHBERİ</div>'
            f'<div class="baslik">Filmlerle düşünme rehberi</div>'
            f'<div class="alt">Atölyeye katılamayan arkadaşlarımız için</div>'
            f'<div class="vurgu"></div>'
            f'<div class="blok-sari"><div class="label" style="color:var(--koyu)">KAMPIN BÜYÜK SORUSU</div>'
            f'<div class="title" style="font-size:30pt;margin-top:1mm">İyi bir hayat nedir?</div>'
            f'<div style="margin-top:2mm;font-size:12.5pt">{il(giris)}</div></div>'
            f'<div class="filmler">{satir}</div></section>')


def kullanim(govde):
    adimlar = re.findall(r"(?m)^(\d)\. (.+)$", govde)
    unutma = re.search(r"(?m)^Unutma: (.+)$", govde).group(1)
    out = "".join(f'<div class="adim"><div class="no">{n}</div><p>{il(t)}</p></div>' for n, t in adimlar)
    return (f'<section class="rh">{ust_serit("Başlarken")}<div class="sayfa-baslik">Bu rehber nasıl kullanılır?</div>'
            f'{out}<div class="unutma"><b>Unutma:</b> {il(unutma)}</div></section>')


def durak_karti(baslik, govde):
    m = re.match(r"(\d)\. durak · (.+?) · (.+)", baslik)
    no, zaman, ad = m.groups()
    ana = ""
    if ad.endswith("(ana sahne)"):
        ad, ana = ad[: -len("(ana sahne)")].strip(), '<span class="pill sari" style="font-size:9pt">ana sahne</span>'
    paragraflar = [p.strip() for p in govde.strip().split("\n\n") if p.strip()]
    ust, soru, daha = [], [], []
    for p in paragraflar:
        if p.startswith("**Soru:**") or p.startswith("**Isınma:**"):
            soru.append(p)
        elif p.startswith("*Biraz daha düşün:*"):
            daha.append(p)
        else:
            ust.append(p)
    soru_html = "".join(f"<p>{il(p)}</p>" for p in soru)
    daha_html = "".join(f'<p class="daha">{il(p.replace("*Biraz daha düşün:*", "**Biraz daha düşün:**", 1))}</p>' for p in daha)
    return (f'<div class="durak"><div class="ust"><span class="label">{no}. DURAK · {zaman.upper()}</span>{ana}</div>'
            f'<h4>{ad}</h4>{"".join(f"<p>{il(p)}</p>" for p in ust)}<div class="soru">{soru_html}</div>{daha_html}</div>')


def ikilem(govde):
    giris = re.search(r"\*\*Sen hangi taraftasın\?\*\* (.+)", govde).group(1).strip()
    a = re.search(r"(?m)^- \*\*A:\*\* (.+)$", govde).group(1)
    b = re.search(r"(?m)^- \*\*B:\*\* (.+)$", govde).group(1)
    son = govde.strip().split("\n")[-1].strip()

    def kutu(harf, t):
        bas, _, alt = t.partition(". ")
        alt = (" " + il(alt)) if alt else ""
        return f'<td><div class="harf">{harf}</div><b>{il(bas)}{"." if alt else ""}</b>{alt}</td>'
    return (f'<div class="taraf"><div class="ara">Sen hangi taraftasın?</div><p class="giris">{il(giris)}</p>'
            f'<table class="ikilem"><tr>{kutu("A", a)}{kutu("B", b)}</tr></table><p class="ortada">{il(son)}</p></div>')


def film_bolumu(baslik, govde):
    m = re.match(r"(\d)\. Film: (.+) \((\d{4}), (\d+) dk\)", baslik)
    n, ad = int(m.group(1)), m.group(2)
    key, afis, imdb, kunye = FILMLER[n]
    ust, *alt = re.split(r"(?m)^### ", govde)
    konu = re.search(r"\*\*Konu:\*\* (.+)", ust).group(1)
    buyuk = re.search(r"\*\*Filmin büyük sorusu:\*\* (.+)", ust).group(1)
    izleme = re.search(r"> \*\*İzleme görevi:\*\* (.+)", ust).group(1)
    izleme_on, _, izleme_soru = izleme.partition(": ")
    bilmen = re.search(r"\*\*Bilmen iyi olur:\*\* (.+)", ust)
    ilerleme = "".join('<i class="on"></i>' if i == n else "<i></i>" for i in range(1, 6))
    url = f"https://www.imdb.com/title/{imdb}/"
    afis_html = (f'<div class="afis"><img src="../afisler/{afis}" alt="{ad} afişi">'
                 f'<div class="k">{kunye}<br><a href="{url}">imdb.com/title/{imdb}</a><br>Afiş: İngilizce Vikipedi</div></div>')
    html = [f'<section class="rh">{ust_serit(f"{n}. film")}',
            f'<div class="gun-bant">{kit.motif(key)}<div class="ilerleme">Yolculuk {ilerleme}</div>'
            f'<div class="etiket"><span class="pill sari" style="font-size:12pt">{n}. film</span><span class="title">{ad}</span></div></div>',
            f'<table class="ust-blok"><tr><td><p><b>Konu:</b> {il(konu)}</p>'
            f'<div class="buyuk"><div class="label" style="color:var(--koyu)">FİLMİN BÜYÜK SORUSU</div>'
            f'<div class="title">{il(buyuk)}</div></div></td><td class="afis-hucre">{afis_html}</td></tr></table>',
            f'<div class="kaynak"><h4>İZLEME GÖREVİ · FİLMDEN ÖNCE OKU</h4><p>{il(izleme_on)}:</p>'
            f'<p class="yaprakta">{il(izleme_soru)}</p></div>']
    if bilmen:
        html.append(f'<div class="bilmen"><p><b>Bilmen iyi olur:</b> {il(bilmen.group(1))}</p></div>')
    html.append('<div class="ara">Üç durak</div>')
    for s in alt:
        sb, sg = s.split("\n", 1)
        sb = sb.strip()
        if sb.startswith("Film bitince"):
            liste, _, kalan = sg.partition("**Sen hangi taraftasın?**")
            html.append(f'<div class="son"><div class="ara">Film bitince</div><div class="bitince">{blok(liste)}</div>'
                        f'{ikilem("**Sen hangi taraftasın?**" + kalan)}</div>')
        else:
            html.append(durak_karti(sb, sg))
    html.append("</section>")
    return "".join(html)


def senin_cevabin(govde):
    giris = govde.strip().split("\n\n")[0]
    maddeler = dict((int(n), t) for n, t in re.findall(r"(?m)^(\d)\. (.+)$", govde))
    parcalar = ["gerçek", "kendin olmak", "iyilik", "adalet", "yaşamanın kendisi"]
    secim = '<div class="parcalar">' + "".join(f'<span><span class="dot"></span>{p}</span>' for p in parcalar) + "</div>"

    def alan(n, ek, satir):
        bas, _, alt = maddeler[n].partition("** ")
        return (f'<div class="alan"><div class="alan-etiket">{n}. {il(bas.strip("*"))}</div>'
                f'{f"<div class=alan-ipucu>{il(alt)}</div>" if alt else ""}{ek}{lines(satir)}</div>')
    return (f'<section class="rh">{ust_serit("Beş filmden sonra")}<div class="sayfa-baslik">Senin cevabın</div>'
            f'<p>{il(giris)}</p>{alan(1, secim, 3)}{alan(2, "", 4)}{alan(3, "", 4)}{alan(4, "", 4)}</section>')


def rehber():
    bas, secs = bolumler()
    filmler = []
    for b, g in secs:
        m = re.match(r"(\d)\. Film: (.+) \(", b)
        if m:
            filmler.append((int(m.group(1)), m.group(2), re.search(r"\*\*Filmin büyük sorusu:\*\* (.+)", g).group(1)))
    out = [kapak(bas, filmler)]
    for b, g in secs:
        if b.startswith("Bu rehber nasıl kullanılır"):
            out.append(kullanim(g))
        elif re.match(r"\d\. Film:", b):
            out.append(film_bolumu(b, g))
        elif b.startswith("Beş filmden sonra"):
            out.append(senin_cevabin(g))
    return "".join(out)
