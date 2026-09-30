<?php
/**
 * Site pages created by Setup. Mini markup (see Setup::blocks()):
 *   "## Title {#anchor}" → H2, "### " → H3, "- " → list item,
 *   "[[skyra/block {json}]]" → dynamic block, other lines → paragraph.
 *
 * Legal texts describe what this code actually does with data. Bracketed
 * fields must be completed and the texts reviewed by counsel before launch.
 *
 * @package Skyra\Core
 */

defined( 'ABSPATH' ) || exit;

return array(
	array(
		'slug'    => 'astroloji-araclari',
		'title'   => 'Astroloji Araçları',
		'excerpt' => 'Doğum haritası, yükselen burç, Ay burcu, burç uyumu, transitler, retro ve astroloji takvimi: hepsi gerçek gökyüzü hesabıyla.',
		'body'    => <<<'TXT'
Skyra’nın araçları gerçek gökyüzü hesabıyla çalışır: gezegen konumları, doğum yerinin tarihsel saat dilimi ve yaz saati uygulaması hesaba katılır. Sonuçlar, kendini tanımak için bir başlangıç noktasıdır.
[[skyra/tools-grid {"align":"wide","eyebrow":"","heading":"Tüm araçlar","intro":""}]]
TXT,
	),
	array(
		'slug'    => 'dogum-haritasi',
		'title'   => 'Doğum Haritası Hesaplama',
		'excerpt' => 'Doğum tarihin, saatin ve yerinle ücretsiz doğum haritanı oluştur: gezegenler, evler, açılar, yükselen ve element dengesi.',
		'body'    => <<<'TXT'
Doğum haritası, doğduğun an ve yerde gökyüzünün bir fotoğrafıdır. Tarihini, saatini ve doğum yerini gir; gezegenlerin burçlarını, evlerini ve aralarındaki açıları birlikte görelim.
[[skyra/birth-chart {"align":"wide","variant":"full"}]]
## Harita nasıl hesaplanıyor? {#yontem}
Doğum yerinin koordinatları ve o tarihte geçerli olan saat dilimi (yaz saati dahil) ile yerel saat evrensel zamana çevrilir. Gezegen konumları Skyra efemeris motoruyla geosentrik ve tropikal zodyağa göre hesaplanır; evler Placidus sistemiyle bulunur. Kutup dairesine yakın enlemlerde Porphyry ev sistemi kullanılır.
Doğum bilgilerin yalnızca bu hesaplama için kullanılır; sunucuda saklanmaz, loglanmaz ve adres çubuğuna yazılmaz. Sayfa yenilendiğinde sonuç kaybolur.
TXT,
	),
	array(
		'slug'    => 'yukselen-burc-hesaplama',
		'title'   => 'Yükselen Burç Hesaplama',
		'excerpt' => 'Doğum tarihi, saati ve yeriyle yükselen burcunu ve derecesini hesapla; sınırdaysan uyaralım.',
		'body'    => <<<'TXT'
Yükselen burç, doğduğun anda doğu ufkunda yükselen burçtur. Doğum saatine ve yerine göre değişir; bu yüzden üç bilgiye de ihtiyaç var.
[[skyra/rising-sign {"align":"wide"}]]
## Yükselen neden bu kadar hassas?
Zodyağın 12 burcu yaklaşık 24 saatte bir ufuktan yükselir; her burç ortalama iki saat sürer. Yükselenin derecesi burç sınırına yakınsa, birkaç dakikalık saat farkı onu komşu burca taşıyabilir. Böyle bir durumda sonucun altında bir not gösteririz.
TXT,
	),
	array(
		'slug'    => 'ay-burcu-hesaplama',
		'title'   => 'Ay Burcu Hesaplama',
		'excerpt' => 'Ay burcunu doğum tarihin ve yerinle bul. Saatini bilmiyorsan ve Ay o gün burç değiştirdiyse iki olasılığı saatiyle gösteririz.',
		'body'    => <<<'TXT'
Ay burcu, duygusal ihtiyaçlarını ve güvende hissetme biçimini anlatır. Ay yaklaşık iki buçuk günde bir burç değiştirir; çoğu zaman doğum saatin olmadan da bulunabilir.
[[skyra/moon-sign {"align":"wide"}]]
TXT,
	),
	array(
		'slug'    => 'burc-uyumu',
		'title'   => 'Burç Uyumu',
		'excerpt' => 'İki burç arasındaki açıyı, element ve nitelik ilişkisini oku. Yargı değil, konuşmaya açılan bir kapı.',
		'body'    => <<<'TXT'
İki Güneş burcu arasındaki açı ve elementlerin ilişkisi, bir bağın genel dinamiği hakkında fikir verir. Daha kişisel bir karşılaştırma için iki doğum haritasına birlikte bakmak gerekir.
[[skyra/compatibility {"align":"wide"}]]
TXT,
	),
	array(
		'slug'    => 'transitler',
		'title'   => 'Transitler',
		'excerpt' => 'Gezegenlerin şu anki konumları, retro durumları ve aralarındaki açılar. İstediğin tarih ve saate göre de bakabilirsin.',
		'body'    => <<<'TXT'
Transitler, gezegenlerin şu anki hareketleridir. Aşağıda gökyüzünün bu anki haritasını, gezegenlerin günlük hareketini ve aralarındaki sıkı açıları görebilirsin.
[[skyra/transits {"align":"wide"}]]
TXT,
	),
	array(
		'slug'    => 'retro-takvimi',
		'title'   => 'Retro Takvimi',
		'excerpt' => 'Merkür, Venüs, Mars ve dış gezegenlerin yıl içindeki retro dönemleri; başlangıç ve bitiş dereceleriyle.',
		'body'    => <<<'TXT'
Retro, bir gezegenin Dünya’dan bakıldığında geri gidiyormuş gibi göründüğü dönemdir. Aşağıdaki takvim, her gezegenin yıl içindeki retro dönemlerini başlangıç ve bitiş dereceleriyle gösterir.
[[skyra/retro-calendar {"align":"wide"}]]
TXT,
	),
	array(
		'slug'    => 'astroloji-takvimi',
		'title'   => 'Astroloji Takvimi',
		'excerpt' => 'Yeni aylar, dolunaylar, tutulmalar, retro başlangıç ve bitişleri ve burç geçişleri: yılın gökyüzü takvimi.',
		'body'    => <<<'TXT'
Yılın gökyüzü olayları, ay ay. Yeni Ay ve Dolunaylar, tutulmalar, retro başlangıç ve bitişleri ile gezegenlerin burç geçişleri Türkiye saatiyle listelenir.
[[skyra/astro-calendar {"align":"wide"}]]
TXT,
	),
	array(
		'slug'    => 'gunluk-burc-yorumlari',
		'title'   => 'Günlük Burç Yorumları',
		'excerpt' => '12 burç için bugünün gökyüzü okuması: Ay’ın burcuna göre evi, günün açısı ve Güneş’in aydınlattığı alan.',
		'body'    => <<<'TXT'
Her gün, Ay’ın senin burcuna göre bulunduğu ev ve günün en belirgin gezegen açısı üzerinden kısa bir okuma. Burcunu seç, ayrıntısına geç.
[[skyra/daily-horoscopes {"align":"wide","layout":"grid","eyebrow":"","heading":"Bugünün yorumları","intro":""}]]
TXT,
	),
	array(
		'slug'    => 'hakkimizda',
		'title'   => 'Hakkımızda',
		'excerpt' => 'Skyra Astro, astrolojiyi kişisel farkındalık, editoryal içerik ve kolay kullanılan araçlarla birleştiren dijital bir keşif alanı.',
		'body'    => <<<'TXT'
Skyra Astro, Türkiye’den doğan; astrolojiyi kişisel farkındalık, editoryal içerik ve kolay kullanılan araçlarla birleştiren dijital bir keşif alanı. Gökyüzünü anlamak karmaşık olabilir; biz onu kişisel, anlaşılır ve güzel bir deneyime dönüştürmeye çalışıyoruz.
## Neye inanıyoruz?
- Açıklık: veriyi, yorumu ve öneriyi birbirinden ayırırız.
- Kullanıcı iradesi: kehanet ya da korku yerine seçenek ve düşünme alanı sunarız.
- Mahremiyet: doğum bilgilerini saklamayız.
- Özen: her tarih, her etiket, her hata mesajı işe yarasın isteriz.
## Yöntem {#yontem}
Gezegen konumları, sitenin kendi efemeris motoruyla sunucuda hesaplanır. Yörünge elemanları ve düzeltme terimleri, astronomide yaygın kullanılan yayımlanmış yöntemlere (Paul Schlyter’in gezegen konumu yöntemi, Jean Meeus’ün “Astronomical Algorithms” eseri) dayanır. Konumlar bağımsız bir referans kütüphanesiyle karşılaştırılarak test edilir; 1900–2100 arasında sapma birkaç yay dakikasıdır. Bu, burç, derece ve açı hesapları için fazlasıyla yeterli bir hassasiyettir.
Zodyak tropikaldir; konumlar geosentriktir (Dünya’nın merkezinden). Evler Placidus sistemiyle hesaplanır. Doğum yerinin koordinatları GeoNames veri tabanından, tarihsel saat dilimleri IANA saat dilimi veritabanından gelir.
Günlük burç yorumları, editör tarafından yazılmamış günlerde gökyüzü verisinden türetilir: Ay’ın her burca göre bulunduğu ev, Ay’ın burç değiştirme saati ve günün en sıkı gezegen açısı. Bu okumalar sayfada “Gökyüzü okuması” olarak, editör yorumları ise “Editör yorumu” olarak etiketlenir.
Astroloji, bilimsel olarak kanıtlanmış bir öngörü yöntemi değildir. Skyra’daki yorumlar sembolik okumalardır; sağlık, hukuk ya da finans kararları için profesyonel desteğin yerini tutmaz.
TXT,
	),
	array(
		'slug'    => 'iletisim',
		'title'   => 'İletişim',
		'excerpt' => 'Soruların, önerilerin ve iş birliği fikirlerin için bize yaz.',
		'body'    => <<<'TXT'
Soruların, içerik önerilerin ya da iş birliği fikirlerin için aşağıdaki formu kullanabilirsin. Mesajların bize ulaşır ve e-posta ile yanıt veririz. Danışmanlık başvuruları için Instagram hesabımızdan da yazabilirsin.
[[skyra/contact-form {}]]
TXT,
	),
	array(
		'slug'    => 'kvkk',
		'title'   => 'KVKK Aydınlatma Metni',
		'excerpt' => 'Kişisel verilerinin hangi amaçla işlendiği ve haklarına ilişkin aydınlatma metni.',
		'body'    => <<<'TXT'
Bu metin, 6698 sayılı Kişisel Verilerin Korunması Kanunu’nun 10. maddesi uyarınca, Skyra Astro web sitesinde işlenen kişisel verilere ilişkin olarak hazırlanmıştır.
## Veri sorumlusu
[Veri sorumlusunun unvanı, adresi ve iletişim e-postası yayından önce eklenecektir.]
## Hangi verileri, hangi amaçla işliyoruz?
- Bülten: e-posta adresin ve onay zamanı, yalnızca haftalık bülteni gönderebilmek için. Kayıt, e-postandaki bağlantıyı onaylaman halinde tamamlanır (çift onay).
- İletişim formu: adın, e-posta adresin, seçtiğin konu ve mesajın, yalnızca sana yanıt verebilmek için.
- Doğum haritası ve hesaplama araçları: doğum tarihin, saatin ve yerin yalnızca hesaplama anında kullanılır; saklanmaz, loglanmaz, üçüncü kişilerle paylaşılmaz.
## Hukuki sebep
Bülten için açık rızan; iletişim formu için bir sözleşmenin kurulması veya ifası ile doğrudan ilgili olması ve meşru menfaat; hesaplama araçları için ise talebinin yerine getirilmesi hukuki sebeplerine dayanırız.
## Saklama süresi
Bülten kaydın, bültenden ayrılana kadar saklanır; her e-postada ayrılma bağlantısı bulunur. Onaylanmayan kayıtlar gönderim listesine eklenmez. İletişim mesajları yönetim panelinde tutulur ve [saklama süresi yayından önce belirlenecektir].
## Aktarım
Verilerin, sitenin barındırıldığı sunucuda tutulur [barındırma sağlayıcısı ve sunucu konumu yayından önce eklenecektir]. Bülten gönderimi için bir e-posta servis sağlayıcısı kullanılması halinde bu metin güncellenecektir.
## Hakların
KVKK’nın 11. maddesi kapsamında verilerinin işlenip işlenmediğini öğrenme, bilgi talep etme, düzeltilmesini veya silinmesini isteme ve itiraz etme haklarına sahipsin. Taleplerini İletişim sayfasındaki form aracılığıyla iletebilirsin.
TXT,
	),
	array(
		'slug'    => 'gizlilik-politikasi',
		'title'   => 'Gizlilik Politikası',
		'excerpt' => 'Skyra Astro’da hangi bilgilerin toplandığı, nasıl kullanıldığı ve nasıl korunduğu.',
		'body'    => <<<'TXT'
Skyra Astro’yu kullanırken mahremiyetine saygı duyarız. Bu sayfa, sitenin gerçekte neyi topladığını ve neyi toplamadığını sade bir dille anlatır.
## Doğum bilgilerin
Doğum haritası, yükselen burç ve Ay burcu araçlarına girdiğin tarih, saat ve yer yalnızca hesaplama için sunucuya gönderilir. Yanıt “önbelleğe alınmaz” olarak işaretlenir, bilgi veritabanına yazılmaz ve sayfa adresine eklenmez. Ana sayfadaki özetten tam haritaya geçerken bilgiler yalnızca tarayıcı sekmende (sessionStorage) geçici olarak tutulur ve sekme kapandığında silinir.
## Bülten ve iletişim
Bülten için e-posta adresini, iletişim formu için adını, e-postanı ve mesajını alırız. Ayrıntılar KVKK Aydınlatma Metni’nde yer alır.
## Analitik ve reklam
Site, bu sürümde analitik ya da reklam takibi yapan üçüncü taraf bir betik içermez. Fontlar ve görseller sitenin kendi sunucusundan yüklenir.
## Tercihlerin
Açık/koyu tema tercihin ve seçtiğin burç, yalnızca kendi tarayıcında (localStorage) saklanır ve sunucuya gönderilmez.
## İletişim
Sorularını İletişim sayfasından iletebilirsin.
TXT,
	),
	array(
		'slug'    => 'cerez-politikasi',
		'title'   => 'Çerez Politikası',
		'excerpt' => 'Skyra Astro’nun kullandığı çerezler ve tarayıcı depolaması.',
		'body'    => <<<'TXT'
Skyra Astro, ziyaretçiler için takip veya reklam çerezi kullanmaz.
## Kullanılan depolama
- Tema tercihi (skyra-theme): açık ya da koyu görünümü hatırlamak için tarayıcındaki localStorage’da tutulur.
- Burç tercihi (skyra-sign): günlük yorumlarda burcunu öne çıkarmak için localStorage’da tutulur.
- Harita aktarımı (skyra-birth): ana sayfadaki özetten tam harita sayfasına geçerken sessionStorage’da geçici olarak tutulur; sekme kapandığında silinir.
## Zorunlu çerezler
Yalnızca site yöneticileri ve editörler için, WordPress oturumunu yöneten zorunlu çerezler kullanılır.
## Tercihlerini silmek
Tarayıcının site verilerini temizleme seçeneğiyle bu kayıtları istediğin zaman silebilirsin.
TXT,
	),
);
