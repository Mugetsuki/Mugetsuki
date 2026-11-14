Filya, çocuklar için felsefe (P4C) atölyeleri ve aile danışmanlığı sunan bir uygulamanın markasıdır. Kitlesi iki katmanlıdır: çocuklarına düşünmeyi sevdirmek isteyen veliler ve okullar; ilişkilerinde destek arayan çiftler ve aileler. Marka sıcak, sakin ve meraklıdır; asla vaatkâr değildir.

## İsmin hikâyesi

*Philia* (φιλία) Yunancada sevgi ve dostluk demektir. "Felsefe" kelimesinin ilk yarısı da odur: *philo-sophia*, bilgelik sevgisi. Aristoteles *Nikomakhos'a Etik*'te *philia*'yı anlatırken anne-baba ile çocuk ve eşler arasındaki bağı da sayar. Filya bu iki anlamı birleştirir: düşünmeyi sevmek ve birbirini sevmek. İki kelime de aynı harfle başlar: φ. Logonun işareti bu harftir.

Hikâyeyi sitede "Hakkımda" ya da ana sayfanın kapanışında en fazla üç cümleyle anlat. Yunanca harfleri yalnızca bu anlatıda kullan; logoda veya başlıklarda kullanma.

Çocuklar isimde "fil" duyacaktır. Bunu düzeltmeye çalışma. Fil sürüleri en yaşlı dişinin önderliğinde yaşayan aile topluluklarıdır; P4C materyallerinde (soru kartlarının arkası, çocuk etkinlik sayfaları) bu çağrışımı oyunlu bir ayrıntı olarak kullanabilirsin. Danışmanlık sayfalarında kullanma.

## İçerik ve ses

- Okura "siz" diye hitap et. Cümleler kısa, sözcükler gündelik olsun.
- Söz verme, çerçeve çiz: "birlikte bakacağız", "ilk görüşmede konuşuruz". "Garanti", "en iyi", "kesin çözüm", "mutlu aile formülü" yazma.
- Terapi klişelerinden kaçın ("iç yolculuk", "en iyi versiyonun"). Somut ol: kaç dakika, kaç kişi, hangi yaş.
- P4C metinlerinde ton daha oyunlu olabilir ve soru sormaktan çekinme. Danışmanlık metinlerinde ton sakin ve düz kalsın.
- Danışan yorumu, uydurma istatistik, "500+ aile" gibi sayılar ve kurum logoları kullanma.
- Emoji kullanma. Başlıklarda yalnızca ilk kelimenin baş harfi büyük olsun (özel adlar hariç).
- Tırnak için “ ” ve ‘ ’ kullan; düz " işaretini metinde kullanma.
- Aile danışmanlığı sayfalarında görünür bir yerde şu satır bulunsun: "Acil bir durumdaysanız 112'yi arayın."

| Böyle yaz | Böyle yazma |
|---|---|
| İlk görüşmede neye ihtiyaç duyduğunuzu birlikte konuşuruz. | Tüm sorunlarınıza kalıcı çözüm! |
| Çocuklar bir hikâyeden yola çıkıp kendi sorularını sorar. | Çocuğunuzun zekâsını %40 artırın. |
| 6–8 çocukla, 60 dakika, haftada bir. | Eşsiz ve dünyaca ünlü yöntem. |

Sloganlar: **"Sorunu soruya çevirmek."** (ana slogan), "Birlikte düşünen aileler." (ikincil).

## Logo

Filya işareti Yunanca φ (fi) harfidir: hem *φιλοσοφία* (felsefe) hem *φιλία* (sevgi, dostluk) bu harfle başlar. Halkanın sol yarısı ve dikey çizgi koyu, sağ yarısı turuncudur: düşünce ile sevgi aynı harfte buluşur. Yazı Literata 600 ile yazılmış "Filya"dır; sitenin başlık yazı tipiyle aynı ailedendir.

- Logo dosyalarını `assets/Logolar/` altından al; yeniden çizme, yazı tipiyle "Filya" yazarak logo taklidi yapma.
- Açık zeminde (`surface`, `highlight-soft`) `filya-yatay.svg`, koyu zeminde `filya-yatay-koyu-zemin.svg` kullan. Tek renk baskı ve kaşe için `filya-tek-renk.svg`.
- Favicon, uygulama ikonu ve profil fotoğrafı için `filya-uygulama-ikonu.svg` kullan.
- Satır içi SVG gerekiyorsa `Logo` bileşenini kullan: mürekkep `ink`, yarım halka `logo-accent` rengini alır, böylece iki temada da doğru görünür.
- Boşluk: logonun çevresinde en az φ halkasının yüksekliğinin yarısı kadar boş alan bırak.
- En küçük boyut: φ tek başına 16px; yatay logo 120px genişlik.
- Turuncu yarıyı sola alma, halkayı tamamen turuncu yapma, dikey çizgiyi kısaltma, logoyu döndürme, gölge veya gradyan ekleme, fotoğraf üstüne düz bir zemin olmadan koyma.
- φ'yi metin içinde Yunan harfi olarak yazarken (`φ`) Literata kullan; logo yerine geçmez.

## Renk

Dört marka rengi vardır: `koyu`, `krem`, `sari`, `turuncu`. Bileşenlerde bunları doğrudan değil, anlamsal token'lar üzerinden kullan.

- Oran: zemin (`surface`) yaklaşık %60, `ink` ve koyu yüzeyler %25, `highlight` %10, `accent` %5'ten az.
- Gövde metni her zaman `ink`, ikincil metin `ink-muted`.
- `accent` (turuncu) ekran başına bir kez kullanılır: birincil buton ya da tek bir vurgu. Üstündeki metin `on-accent`, asla beyaz.
- `highlight` (sarı) yalnızca dolgudur. Açık temada sarı veya turuncu metin kullanma.
- İki hizmet aynı sistemi paylaşır ama farklı hissettirir: Çocuklar için Felsefe sayfasında `highlight` ve `highlight-soft` baskın olsun; Aile Danışmanlığı sayfasında `surface`, `surface-sunk` ve `ink` baskın, sarı yalnızca küçük ayrıntılarda.
- Krem ve koyu yeşil bölümler, kaydırdıkça değişen sahneler gibi dönüşümlü kullanılabilir (`koyu` zeminde `krem` metin ve `sari` dolgular). Geçişler sert bir kesme olmasın; zemin rengi akarak değişsin ya da yeni sahne bir maske (`clip-path`) ile açılsın.
- Ara ton gerekiyorsa yalnızca bu dört renkten türet; yeni bir ana renk ekleme.
- Krem zemin, serif başlık ve turuncu vurgu birlikte çok görülen bir görünüm oluşturur. Filya'yı ayırt edici kılan `koyu` ve `sari` ikilisidir: koyu yeşil bölümlerde sarı dolgular (10.7:1) ve P4C alanlarında geniş sarı bloklar kullan; turuncuyu logo ve birincil butonla sınırla.
- Turuncu ile sarıyı yan yana küçük öğeler olarak kullanma (1.98:1); birbirine karışır.

## Tipografi

- Başlıklarda `display` ailesini (Literata), metinde ve arayüzde `body` ailesini (Albert Sans) kullan. İkisini de Google Fonts'tan latin-ext alt kümesiyle yükle; Next.js'te `next/font/google` ile kendi sunucundan sun.
- Literata optik boyutludur (`opsz` 7–72): tarayıcının otomatik optik boyutunu açık bırak (`font-optical-sizing: auto`); büyük başlıklar daha keskin, küçükler daha açık görünür.
- Başlıklar 600 kalınlıkta. Uzun okuma metinlerinde (yazılar, hikâyeler) gövdeyi Literata 400 ile de dizebilirsin; buton, etiket, form ve meta bilgide her zaman Albert Sans.
- Ölçek: `display-xl`, `display`, `heading`, `subheading` başlıklar için; `body-lg`, `body`, `caption`, `label` metin için. Ara boyut uydurma.
- Sayfanın kök öğesinde `lang="tr"` olsun. Büyük harfe çevirme (`label`) yalnızca böylece doğru çalışır: "i" → "İ", "ı" → "I".
- Gövde metnini büyük harfle yazma. Satır uzunluğu en fazla 65 karakter. Başlıklarda `text-wrap: balance` kullan.

## Boşluk, köşe, yüzey

- Boşlukları `space-*` ölçeğinden seç. Kenar boşluğu mobilde `space-4`, masaüstünde `space-8`; bölümler arası mobilde `space-16`, masaüstünde `space-24`.
- Köşeler cömert: kartlarda `radius-md`, soru kartları ve büyük görsellerde `radius-lg`, buton ve etiketlerde `radius-full`.
- Yüzeyleri önce renk tonuyla (`surface-raised`, `surface-sunk`) ayır; gölgeyi (`shadow-sm`) yalnızca krem kartlarda, `shadow-lg`'yi yalnızca yüzen öğelerde kullan.
- Her odaklanabilir öğe klavye odağında `focus-ring` gösterir.

## Görsel dil

- Fotoğraf: doğal ışık, gerçek mekânlar, eller, kitaplar, çember halinde oturan gruplar. Yüzü tanınabilir çocuk fotoğrafı ve yapay zekâyla üretilmiş insan yüzü kullanma.
- Danışmanın portresi gerçek olmalı; sıcak, sade bir arka planda.
- İllüstrasyon gerekiyorsa yalnızca logonun dilini kullan: yarım halkalar, tam halkalar ve dikey çizgiler; bir yarısı koyu, bir yarısı turuncu ya da sarı. Karakter çizimi, maskot ya da stok illüstrasyon kullanma.

## Hareket

Filya canlı bir sitedir: kullanıcı kaydırdıkça sahneler kurulur. Her bölümün belirgin bir giriş hareketi, önemli bölümlerin kaydırmaya bağlı (scroll-linked) bir sahnesi vardır.

- **Giriş hareketleri belirgin olsun:** Başlıklar satır satır maskenin altından yükselir (40–80px). Görseller φ halkasını andıran bir daire ya da yarım kapsül maskesiyle açılır. Kartlar hafif bir dönüşle sırayla gelir. Süre 500–900ms, yumuşak ease-out ya da yay (spring).
- **Kaydırmaya bağlı sahneler:**
  - φ'nin parçaları kaydırdıkça çizilir ve birleşir.
  - Büyük görseller kaydırmayla ölçeklenir ve paralaks yapar.
  - Adımlar ekrana sabitlenip üst üste dizilir.
  - Uzun cümlelerde kelimeler kaydırdıkça koyulaşır.
  - Bölümler arasında zemin rengi akarak değişir.
- **Yoğunluk sayfaya göre değişir:** Çocuklar için Felsefe en hareketli sayfadır, ana sayfa hemen ardından gelir. Aile danışmanlığı daha yumuşak ama asla durağan değildir.
- **Hareketin dili logodan gelir:** halkalar, yarım halkalar, dikey çizgiler ve sarı dolgular. Rastgele zıplama, sallanma ve dönen ikonlar kullanma.
- **Açılış:** İlk ziyarette kısa bir açılış anı olabilir: φ çizilir ve sayfaya açılır (en fazla 1.5 saniye, tıklayınca atlanır, sonraki ziyaretlerde gösterilmez).
- **Logo:** Üst çubuktaki logo açılışta bir kez çizilir; φ'yi sürekli döndürme ya da zıplatma. Sayfa içindeki büyük φ çizimleri ise (3D dahil) kaydırmaya bağlı olarak hareket edebilir.
- **Sahne yapısı:** Bölümler birer sahne gibi düşünülür. Çocuklar için Felsefe sayfasında hikâye anlatan bölümler, çizgi roman kareleri gibi kaydırdıkça birbirine geçebilir.
- **Teknik sınırlar:** Yalnızca `transform`, `opacity`, `clip-path` ve SVG çizgi uzunluğu (`stroke-dashoffset`) canlandır; 60 fps hedefle.
- **Erişilebilirlik:** `prefers-reduced-motion: reduce` açıkken tüm sahneler son hâllerine anında geçer; içerik eksiksiz görünür.

## İkonlar

- Lucide ikonlarını kullan: 24px, çizgi kalınlığı 1.75, yuvarlak uç ve birleşim. Bu ayarlar logodaki yuvarlak uçlu çizgiyle uyumludur.
- İkon her zaman `ink` ya da `ink-muted` rengindedir; tek başına anlam taşıyorsa yanına metin ya da `aria-label` ekle.
- Emoji ve dolgulu ikon kullanma.
