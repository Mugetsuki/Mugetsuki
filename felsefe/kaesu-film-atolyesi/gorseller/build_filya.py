#!/usr/bin/env python3
"""Filya renkleriyle basılan kamp materyallerini üretir (logo yok):

  pdf/defter-dusunce-yolculugum.pdf   Düşünce Yolculuğum defteri (4 sayfa: Başlangıç, 1–3. gün, 4–5. gün, Varış)
  pdf/moderator-kitapcigi.pdf         Moderatör kitapçığı (altın kurallar, kamp omurgası, 5 gün ön/arka); kaynak: ../moderator-kagidi.md
  pdf/form-gozlem.pdf                 Gözlem formu (ön) ve rubrik (arka)
  pdf/form-veli-onay.pdf              Veli onay formu

Stil: filya.css. HTML kaynakları html/ altında, PNG önizlemeler onizleme/ altında.
Film görselleri (afiş, not kartı, ikilem, Beş Kapı) build.py ile üretilir.
Kullanım: ~/.local/share/kaesu-gorsel-venv/bin/python build_filya.py [--inceleme KLASÖR]
"""
import pathlib
import re
import sys

ROOT = pathlib.Path(__file__).resolve().parent
HTML_DIR, PDF_DIR, PNG_DIR = ROOT / "html", ROOT / "pdf", ROOT / "onizleme"

GUNLER = {
    1: dict(film="The Truman Show", tema="Bilgi", afis="gun1-truman.jpg", imdb="tt0120382", kunye="Peter Weir, 1998, 103 dk"),
    2: dict(film="Ters Yüz 2", tema="Benlik", afis="gun2-io2.jpg", imdb="tt22022452", kunye="Kelsey Mann, 2024, 96 dk"),
    3: dict(film="Mucize (Wonder)", tema="Öteki", afis="gun3-wonder.jpg", imdb="tt2543472", kunye="Stephen Chbosky, 2017, 113 dk"),
    4: dict(film="Zootropolis", tema="Toplum", afis="gun4-zoo.jpg", imdb="tt2948356", kunye="Byron Howard, Rich Moore, 2016, 108 dk"),
    5: dict(film="Soul", tema="Anlam", afis="gun5-soul.jpg", imdb="tt2948372", kunye="Pete Docter, Kemp Powers, 2020, 100 dk"),
}


def doc(title, body):
    return (f'<!doctype html>\n<html lang="tr"><head><meta charset="utf-8"><title>{title}</title>'
            f'<link rel="stylesheet" href="../filya.css"></head><body>\n{body}\n</body></html>\n')


def lines(n, cls=""):
    return f'<div class="lines {cls}">' + "<div></div>" * n + "</div>"


def cb():
    return '<span class="cb"></span>'


def dot():
    return '<span class="dot"></span>'


def fill(w=None):
    return f'<span class="fill" style="{"width:%s" % w if w else "flex:1"}"></span>'


# --------------------------------------------------------------------------------------
# Düşünce Yolculuğum defteri
# --------------------------------------------------------------------------------------
IFADELER = [
    ("A", "Zor soruların tek bir doğru cevabı vardır."),
    ("B", "Fikrimi söylerken nedenini de söylerim."),
    ("C", "İyi bir gerekçe duyarsam fikrimi değiştiririm."),
    ("D", "Felsefe filozofların ve büyüklerin işidir."),
    ("E", "Yemek, oyun, kurallar gibi sıradan şeylerin altında da büyük sorular vardır."),
]


def olcek():
    rows = "".join(f'<tr><td><b>{k}.</b> {t}</td>' + "".join(f'<td class="c">{dot()}</td>' for _ in range(5)) + "</tr>" for k, t in IFADELER)
    return (f'<table class="olcek"><tr><th style="width:58%"></th><th>1<br>hiç</th><th>2</th><th>3</th><th>4</th><th>5<br>tamamen</th></tr>{rows}</table>')


def gun_blok(n):
    g = GUNLER[n]
    return f'''<div class="kart gun">
    <div class="bas"><span class="pill sari">{n}. gün</span><span class="film">{g["film"]}</span></div>
    <div class="soru-etiketi" style="margin-top:1mm">Kamp sorusuna bugünkü cevabım</div>
    <div class="ipucu">İyi bir hayat … çünkü … Değişmediyse “Değişmedi, çünkü …” yaz. Bu da iyi bir cevap.</div>{lines(2, "tight")}
    <div class="soru-etiketi">Bugün kafamı en çok karıştıran soru</div>{lines(1, "tight")}
    <div class="soru-etiketi">Hayattan bir soru <span class="ipucu" style="font-weight:400">(kampta yakaladığım)</span></div>{lines(1, "tight")}
    <div class="keyif">Bugün atölye: <span class="n">hiç sevmedim</span> {dot()} {dot()} {dot()} {dot()} {dot()} <span class="n">çok sevdim</span></div>
  </div>'''


def defter():
    s1 = f'''<section class="sheet">
  <div class="label">KAESU FİLM ATÖLYESİ</div>
  <div class="title" style="font-size:30pt;margin-top:1mm">Düşünce yolculuğum</div>
  <div class="kimlik">
    <div class="row"><span>Kamp adım:</span>{fill()}</div>
    <div class="row"><span>Yaşım:</span>{fill("16mm")}<span style="margin-left:8mm">{cb()} Ortaokul</span><span style="margin-left:4mm">{cb()} Lise</span></div>
  </div>
  <div class="blok-sari" style="margin-top:4mm">
    <div class="label" style="color:var(--koyu)">KAMP SORUSU</div>
    <div class="title" style="font-size:34pt;margin-top:1mm">İyi bir hayat nedir?</div>
  </div>
  <div class="alan-baslik"><div class="title" style="font-size:18pt">Başlangıç</div><span class="pill turuncu">1. gün · filmden önce</span></div>
  <p style="margin-top:2mm">Daha hiçbir film izlemeden, şu an ne düşünüyorsun? Doğru cevap yok. Beşinci gün bu sayfaya geri döneceksin.</p>
  <div class="soru-etiketi">Bence iyi bir hayat</div>{lines(3)}
  <div class="soru-etiketi">çünkü</div>{lines(3)}
  <div class="kart" style="margin-top:6mm"><div style="font-weight:700;margin-bottom:2mm">Bu cümlelere ne kadar katılıyorsun?</div>{olcek()}</div>
  <div class="soru-etiketi" style="margin-top:6mm">Günlük hayatımda felsefe … karşıma çıkar.</div>
  <div class="ipucu">Bilmiyorsan “bilmiyorum” yaz. Bu da bir cevap.</div>{lines(2)}
</section>'''
    s2 = f'''<section class="sheet">
  <div class="label">KAESU FİLM ATÖLYESİ · DÜŞÜNCE YOLCULUĞUM</div>
  <div class="title" style="font-size:22pt;margin-top:1mm">Her günün sonunda</div>
  <p class="muted" style="margin-top:1mm">Beş dakika, sessiz. Kimse not vermeyecek. Kamp adım: {fill("55mm")}</p>
  {gun_blok(1)}{gun_blok(2)}{gun_blok(3)}
</section>'''
    s3 = f'''<section class="sheet">
  <div class="label">KAESU FİLM ATÖLYESİ · DÜŞÜNCE YOLCULUĞUM</div>
  <div class="title" style="font-size:22pt;margin-top:1mm">Her günün sonunda</div>
  <p class="muted" style="margin-top:1mm">Beş dakika, sessiz. Kamp adım: {fill("55mm")}</p>
  {gun_blok(4)}{gun_blok(5)}
  <div class="kart gun">
    <div class="soru-etiketi" style="margin-top:0">Kampta yakaladığım başka sorular</div>
    <div class="ipucu">Yemekte, oyunda, odada, bir konuşmada… Aklına geldikçe yaz.</div>{lines(3, "tight")}
  </div>
</section>'''
    s4 = f'''<section class="sheet">
  <div class="label">KAESU FİLM ATÖLYESİ · DÜŞÜNCE YOLCULUĞUM</div>
  <div class="alan-baslik" style="margin-top:1mm"><div class="title" style="font-size:24pt">Varış</div><span class="pill turuncu">5. gün · son</span></div>
  <p style="margin-top:1.5mm">Önce ilk sayfayı aç ve ilk gün ne yazdığını oku. Sesli okumak yok.</p>
  <div class="soru-etiketi">1. Şimdi: Bence iyi bir hayat … çünkü …</div>{lines(2, "tight")}
  <div class="soru-etiketi">2. Ne değişti, neden? <span class="ipucu" style="font-weight:400">Değişmediyse neden değişmediğini yaz.</span></div>{lines(2, "tight")}
  <div class="blok-sari" style="margin-top:3mm;padding:4mm 5mm">
    <div class="soru-etiketi" style="margin-top:0">3. Benim büyük sorum <span style="font-weight:400">(defterindeki “kafamı karıştıran sorulardan” birini seçebilirsin)</span></div>{lines(1, "tight")}
    <div class="soru-etiketi">Şimdilik cevabım … çünkü …</div>{lines(2, "tight")}
  </div>
  <div class="soru-etiketi">4. Bu soru hayatımda nerede karşıma çıkıyor? <span class="ipucu" style="font-weight:400">Bir yer, bir an, bir kişi.</span></div>{lines(1, "tight")}
  <div class="kart" style="margin-top:3mm;padding:3mm 4mm">
    <div style="font-weight:700;margin-bottom:1mm">5. Bu cümlelere şimdi ne kadar katılıyorsun?</div>{olcek()}
    <div class="soru-etiketi" style="margin-top:2mm">Günlük hayatımda felsefe … karşıma çıkar.</div>{lines(1, "tight")}
  </div>
  <div class="keyif" style="margin-top:3mm">6. Kamp genel olarak: <span class="n">hiç sevmedim</span> {dot()} {dot()} {dot()} {dot()} {dot()} <span class="n">çok sevdim</span></div>
  <div class="soru-etiketi" style="margin-top:2mm">Değişmesini istediğim bir şey</div>{lines(1, "tight")}
  <div class="kart" style="margin-top:3mm;padding:2.5mm 4mm;font-size:10pt">
    <b>7. Yazdıklarım kullanılabilir mi?</b> Birini işaretle. Hiçbirini işaretlemezsen kullanılmaz.<br>
    {cb()} Hiçbir yerde kullanılmasın. &nbsp; {cb()} Adım olmadan kullanılabilir. &nbsp; {cb()} Adım ve yaşımla kullanılabilir. Adım: {fill("30mm")}
  </div>
</section>'''
    return s1 + s2 + s3 + s4


# --------------------------------------------------------------------------------------
# Moderatör kitapçığı (../moderator-kagidi.md)
# --------------------------------------------------------------------------------------
def md2html(text):
    import markdown
    text = re.sub(r"(?m)^((?![ \t]*(?:[-*]|\d+\.)[ \t]).+)\n([ \t]*(?:[-*]|\d+\.)[ \t])", r"\1\n\n\2", text)
    return markdown.markdown(text, extensions=["tables"])


def bant(ust, baslik, alt, etiket):
    return (f'<div class="bant"><div><div class="label" style="color:var(--krem)">{ust}</div>'
            f'<div class="t">{baslik}</div><div class="s">{alt}</div></div><span class="pill sari">{etiket}</span></div>')


def kitapcik():
    md = (ROOT.parent / "moderator-kagidi.md").read_text(encoding="utf-8")
    secs = re.split(r"(?m)^## ", md)[1:]
    out = []
    for sec in secs:
        baslik, govde = sec.split("\n", 1)
        govde = govde.rsplit("\n---", 1)[0] if govde.rstrip().endswith("---") else govde
        govde = re.sub(r"(?m)^---\s*$", "", govde)
        if baslik.startswith("MODERATÖRÜN"):
            govde = re.sub(r"(?m)^\*\(Kamp boyunca[^\n]*\n", "", govde)
            out.append(f'<section class="kitap genel">{bant("MODERATÖR KİTAPÇIĞI", "Moderatörün altın kuralları", "Kamp boyunca geçerli", "KURALLAR")}{md2html(govde)}</section>')
            continue
        if baslik.startswith("KAMP OMURGASI"):
            out.append(f'<section class="kitap genel">{bant("MODERATÖR KİTAPÇIĞI", "Kamp omurgası", "Kamp sorusu: İyi bir hayat nedir?", "OMURGA")}{md2html(govde)}</section>')
            continue
        m = re.match(r"(\d)\. GÜN", baslik)
        if not m:
            continue
        n = int(m.group(1)); g = GUNLER[n]
        on = govde.split("### ÖN YÜZ", 1)[1].split("### ARKA YÜZ", 1)[0]
        arka = govde.split("### ARKA YÜZ", 1)[1]
        on = re.sub(r"\*\*IMDb:\*\*[^\n]*\n", "", on)
        url = f"https://www.imdb.com/title/{g['imdb']}/"
        afis = (f'<div class="afis"><img src="../afisler/{g["afis"]}" alt="{g["film"]} afişi">'
                f'<div class="k">{g["kunye"]}<br><a href="{url}">imdb.com/title/{g["imdb"]}</a><br>Afiş: İngilizce Vikipedi</div></div>')
        ust, alt = on.split("[[ÜST BLOK SONU]]", 1)
        ust_blok = f'<table class="ust-blok"><tr><td>{md2html(ust)}</td><td class="afis-hucre">{afis}</td></tr></table>'
        alt_baslik = f'{g["tema"]} · Kamp sorusu: İyi bir hayat nedir?'
        out.append(f'<section class="kitap">{bant(f"MODERATÖR KİTAPÇIĞI · {n}. GÜN", g["film"], alt_baslik, "ÖN YÜZ")}{ust_blok}{md2html(alt)}</section>')
        out.append(f'<section class="kitap">{bant(f"MODERATÖR KİTAPÇIĞI · {n}. GÜN", g["film"], alt_baslik, "ARKA YÜZ")}{md2html(arka)}</section>')
    return "".join(out)


# --------------------------------------------------------------------------------------
# Gözlem formu ve veli onayı
# --------------------------------------------------------------------------------------
def gozlem():
    cent = "".join(cb() + (" " if (i + 1) % 5 == 0 else "") for i in range(20))
    duzey = [("1 · Yalnızca iddia", "“Bence yanlış.”"), ("2 · Tek gerekçe", "“…çünkü…”"), ("3 · Gerekçe ve destek", "örnek, filmden an"), ("4 · Gerekçe ve karşı görüş", "başkasına cevap, koşul")]
    r1 = "".join(f"<tr><td><b>{a}</b><br><span class='muted'>{b}</span></td><td>{cent}</td><td></td></tr>" for a, b in duzey)
    r2 = "".join(f"<tr><td>{i}</td><td style='height:7mm'></td><td></td></tr>" for i in range(1, 6))
    gost = ["Başka bir öğrencinin fikrine atıf yaptı", "Fikrini değiştirip nedenini söyledi", "Filmden günlük hayata bağlantı kurdu", "Moderatöre “Doğrusu ne?” diye sordu (azalması beklenir)"]
    r3 = "".join(f"<tr><td>{x}</td><td>{cent[: len(cent) // 2]}</td></tr>" for x in gost)
    gun = '<span class="gun-kutu">' + "".join(f"<i>{i}</i>" for i in range(1, 6)) + "</span>"
    on = f'''<section class="sheet">
  <div class="form-bas"><div class="label">KAESU FİLM ATÖLYESİ · GÖZLEMCİ İÇİN</div><div class="title">Gözlem formu</div></div>
  <div class="form-satir"><span>Gün:</span>{gun}<span style="margin-left:6mm">Model:</span>{cb()} B {cb()} C<span style="margin-left:6mm">Gözlemci:</span>{fill()}</div>
  <div class="form-satir"><span>Katılımcı sayısı:</span>{fill("18mm")}<span style="margin-left:6mm">Gözlenen dilim: dk</span>{fill("12mm")}<span>– dk</span>{fill("12mm")}</div>
  <div class="bolum">1. Gerekçe düzeyi · Soru merdiveninde her konuşmaya bir çentik</div>
  <table class="form"><tr><th style="width:52mm">Düzey</th><th>Çentik</th><th style="width:18mm">Toplam</th></tr>{r1}
    <tr><td colspan="2" style="text-align:right"><b>3 ve 4'ün oranı</b></td><td><b>%</b></td></tr></table>
  <div class="bolum">2. Soruların altındaki soru · Okunan öğrenci soruları ve düzeyleri (arka yüzdeki rubrik)</div>
  <table class="form"><tr><th style="width:12mm">#</th><th>Soru (kısaca)</th><th style="width:22mm">Düzey 1–4</th></tr>{r2}</table>
  <div class="bolum">3. Davranış göstergeleri</div>
  <table class="form"><tr><th style="width:95mm">Gösterge</th><th>Çentik</th></tr>{r3}</table>
  <div class="bolum">4. Katılım ve ikilem</div>
  <div class="form-satir"><span>En az bir kez konuşan:</span>{fill("14mm")}<span>/</span>{fill("14mm")}<span style="margin-left:4mm">Ortaokullu</span>{fill("12mm")}<span>Liseli</span>{fill("12mm")}</div>
  <div class="form-satir"><span>İkilem: A</span>{fill("12mm")}<span>B</span>{fill("12mm")}<span>Ortada</span>{fill("12mm")}<span>Gerekçeyle yer değiştiren</span>{fill("12mm")}</div>
  <div class="bolum">5. Duvara yazılanlar</div>
  <div class="form-satir"><span>Bugün eklediğimiz parça:</span>{fill()}</div>
  <div class="form-satir"><span>Açık kalan soru:</span>{fill()}</div>
  <div class="bolum">6. Not · Dikkat çeken bir cümle (isimsiz), enerji, değişmesi gereken</div>{lines(1, "tight")}
</section>'''
    sq = [("1", "Film sorusu", "Cevabı filmde var. Bir olayı ya da bilgiyi soruyor.", "Truman neden tekneye bindi?"),
          ("2", "Görüş sorusu", "Kişisel tercih ya da tahmin soruyor, bir kavramı tartışmaya açmıyor.", "Sen Truman olsan ne yapardın?"),
          ("3", "Felsefi soru", "Genel ve tartışmaya açık. Bir kavram ya da değer içeriyor (adil, gerçek, doğru, iyi).", "Birini korumak için ona yalan söylemek doğru mu?"),
          ("4", "Derin felsefi soru", "Bir kavramın ne olduğunu sorguluyor, iki kavramı karşılaştırıyor ya da bir varsayımı açığa çıkarıyor.", "Bir şeyin gerçek olduğunu bilmek için ne gerekir?")]
    gq = [("1", "Yalnızca iddia", "Bir fikir söylüyor, nedenini söylemiyor.", "Bence yanlış."),
          ("2", "Tek gerekçe", "“Çünkü” ile bir neden veriyor.", "Yanlış, çünkü yalan söylemek kötü."),
          ("3", "Gerekçe ve destek", "Nedene bir örnek, filmden bir an ya da hayattan bir durum ekliyor.", "…çünkü Christof ona yalan söyledi ve Truman otuz yıl kendi hayatını yaşayamadı."),
          ("4", "Gerekçe ve karşı görüş", "Başka birinin fikrine cevap veriyor, karşı örnek veriyor ya da fikrinin geçerli olduğu koşulu belirtiyor.", "Elif korumak için yalan söylenebilir dedi. Ama bence koruma da yalanı haklı çıkarmaz, çünkü…")]
    rub = lambda d: ("<table class='form'><tr><th style='width:8mm'>#</th><th style='width:34mm'>Adı</th><th style='width:72mm'>Gözlemlenebilir tanım</th><th>Örnek</th></tr>"
                     + "".join(f"<tr><td><b>{a}</b></td><td><b>{b}</b></td><td>{c}</td><td>“{e}”</td></tr>" for a, b, c, e in d) + "</table>")
    arka = f'''<section class="sheet">
  <div class="form-bas"><div class="label">GÖZLEM FORMUNUN ARKA YÜZÜ</div><div class="title">Gözlem rubriği</div></div>
  <div class="bolum">Rubrik 1 · Soru kalitesi</div>
  <p class="ipucu" style="margin-bottom:2mm">Soruların altındaki soru adımındaki öğrenci soruları ve defterdeki soru satırları bu rubrikle puanlanır.</p>{rub(sq)}
  <div class="bolum" style="margin-top:7mm">Rubrik 2 · Gerekçe düzeyi</div>
  <p class="ipucu" style="margin-bottom:2mm">Soru merdivenindeki her konuşma bir puan alır.</p>{rub(gq)}
  <div class="bolum" style="margin-top:7mm">Gözlemci yoksa</div>
  <p>Kolaylaştırıcı yalnızca soru merdiveninin 10 dakikalık bir diliminde puanlama yapar ve her gün aynı dilimi seçer.</p>
</section>'''
    return on + arka


def veli():
    izinler = ["Çocuğumun defterdeki cevapları, <b>adı olmadan</b>, atölyenin değerlendirme raporunda kullanılabilir.",
               "Çocuğumun yazdığı ya da söylediği cümleler, <b>adı olmadan</b> (örneğin “13 yaşında bir katılımcı”), atölyeyi tanıtan materyallerde alıntılanabilir.",
               "Çocuğumun cümleleri <b>adı ve yaşıyla</b> alıntılanabilir. (Çocuğun kendisi de kamp sonunda defterinde ayrıca karar verecektir.)",
               "Çocuğumun <b>yüzünün göründüğü</b> fotoğrafları atölyeyi tanıtan materyallerde kullanılabilir.",
               "Çocuğumun <b>yüzünün görünmediği</b> fotoğrafları (arkadan, eller, defterler) kullanılabilir."]
    rows = "".join(f'<div class="izin"><div>{cb()}</div><div>{t}</div></div>' for t in izinler)
    return f'''<section class="sheet" style="font-size:11.5pt;line-height:1.42">
  <div class="form-bas"><div class="label">KAESU FİLM ATÖLYESİ</div><div class="title">Veli onay formu</div></div>
  <p>Kaesu Akademi Film Atölyesi'nde çocuğunuzun atölyeyle ilgili görüşleri ve kamp sırasındaki fotoğrafları, atölyeyi değerlendirmek ve tanıtmak için kullanılabilir. Lütfen izin verdiğiniz her madde için kutuyu <b>ayrı ayrı</b> işaretleyin. İşaretlemediğiniz maddeler için izin vermemiş sayılırsınız.</p>
  <div style="margin-top:7mm">
    <div class="form-satir"><span>Çocuğun adı soyadı:</span>{fill()}</div>
    <div class="form-satir"><span>Veli adı soyadı:</span>{fill()}</div>
  </div>
  <div class="kart" style="margin-top:6mm;padding:5mm 6mm">{rows}</div>
  <p style="margin-top:5mm">Veli izin verse de çocuk, kamp sonunda kendi defterinde ayrıca karar verir. İkisinden biri “hayır” derse kullanılmaz. Ad kullanımı için hem velinin hem çocuğun açık onayı gerekir.</p>
  <p style="margin-top:3mm">Bu form, kurumun KVKK (6698 sayılı Kişisel Verilerin Korunması Kanunu) aydınlatma metniyle birlikte verilir. İzninizi istediğiniz zaman geri alabilirsiniz.</p>
  <div style="margin-top:8mm">
    <div class="form-satir"><span>Geri alma talebi için:</span>{fill()}</div>
    <div class="form-satir"><span>Tarih:</span>{fill("45mm")}<span style="margin-left:10mm">İmza:</span>{fill()}</div>
  </div>
</section>'''


URUNLER = {
    "defter-dusunce-yolculugum": ("Düşünce yolculuğum", defter, 4),
    "moderator-kitapcigi": ("Moderatör kitapçığı", kitapcik, 12),
    "form-gozlem": ("Gözlem formu", gozlem, 2),
    "form-veli-onay": ("Veli onay formu", veli, 1),
}


def main():
    import weasyprint
    import pypdfium2 as pdfium
    inceleme = None
    if "--inceleme" in sys.argv:
        inceleme = pathlib.Path(sys.argv[sys.argv.index("--inceleme") + 1])
        inceleme.mkdir(parents=True, exist_ok=True)
    for d in (HTML_DIR, PDF_DIR, PNG_DIR):
        d.mkdir(exist_ok=True)
    hata = False
    for ad, (baslik, fn, beklenen) in URUNLER.items():
        hp = HTML_DIR / f"{ad}.html"
        hp.write_text(doc(baslik, fn()), encoding="utf-8")
        pp = PDF_DIR / f"{ad}.pdf"
        weasyprint.HTML(filename=str(hp)).write_pdf(str(pp))
        pdf = pdfium.PdfDocument(str(pp))
        for old in PNG_DIR.glob(f"{ad}-s*.png"):
            old.unlink()
        for i, sayfa in enumerate(pdf, 1):
            sayfa.render(scale=1).to_pil().save(PNG_DIR / f"{ad}-s{i}.png", optimize=True)
            if inceleme:
                sayfa.render(scale=110 / 72).to_pil().save(inceleme / f"{ad}-s{i}.png")
        n = len(pdf)
        pdf.close()
        uyari = "" if n == beklenen else f"  <-- beklenen {beklenen} sayfa"
        hata |= bool(uyari)
        print(f"{ad:28s} {n:2d} sayfa{uyari}")
    sys.exit(1 if hata else 0)


if __name__ == "__main__":
    main()
