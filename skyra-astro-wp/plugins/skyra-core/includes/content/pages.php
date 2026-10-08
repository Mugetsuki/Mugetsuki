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
Soruların, içerik önerilerin ya da iş birliği fikirlerin için aşağıdaki formu kullanabilirsin. Mesajların bize ulaşır ve e-posta ile yanıt veririz.
[[skyra/contact-form {}]]
TXT,
	),
	array(
		'slug'    => 'kvkk',
		'title'   => 'KVKK Aydınlatma Metni',
		'excerpt' => 'Kişisel verilerinin hangi amaçla, hangi hukuki sebeple işlendiği ve hakların.',
		'body'    => <<<'TXT'
Bu metin, 6698 sayılı Kişisel Verilerin Korunması Kanunu’nun (KVKK) 10. maddesi ve Aydınlatma Yükümlülüğünün Yerine Getirilmesinde Uyulacak Usul ve Esaslar Hakkında Tebliğ uyarınca, Skyra Astro web sitesinde (skyraastro.com) işlenen kişisel verilerin hakkında seni bilgilendirmek için hazırlandı.
## Veri sorumlusu
Skyra Astro, Şuara Güncü tarafından işletilir. KVKK kapsamındaki veri sorumlusu Şuara Güncü’dür. İletişim ve başvuru adresi: <a href="mailto:skyra.astro@gmail.com">skyra.astro@gmail.com</a>
## Hangi verileri, hangi amaçla ve hangi hukuki sebeple işliyoruz?
### E-posta ile iletişim
Bize skyra.astro@gmail.com adresinden yazdığında adını, e-posta adresini ve mesajında paylaştığın bilgileri alırız. Bu bilgileri yalnızca mesajını okuyup sana yanıt vermek için kullanırız. Hukuki sebebimiz, genel sorularda veri sorumlusunun meşru menfaatidir (KVKK m.5/2-f); yazışma bir sözleşmenin kurulması veya ifasıyla doğrudan ilgiliyse KVKK m.5/2-c’dir. Mesajına konuyla ilgisi olmayan sağlık, din, kimlik belgesi gibi bilgileri ya da başka kişilerin özel bilgilerini eklememeni rica ederiz.
### Doğum haritası ve hesaplama araçları
Doğum haritası, yükselen burç ve Ay burcu araçlarında girdiğin doğum tarihi, saati ve doğum yeri, istediğin hesaplamanın yapılabilmesi için site sunucusuna gönderilir. Sitenin yazılımı bu bilgileri veritabanına kaydetmez ve hesaplama bittikten sonra saklamaz. Yer aramasına yazdığın ifade, aramanın yapılabilmesi için sunucuya iletilir; bu sorgu sayfa adresinin bir parçası olarak gittiği için barındırma sağlayıcısının teknik erişim kayıtlarında yer alabilir. Hukuki sebep: talep ettiğin hesaplama hizmetinin sunulması (KVKK m.5/2-c).
### Sitenin barındırılması ve teknik kayıtlar
Siteyi ziyaret ettiğinde tarayıcın, sayfanın sana gönderilebilmesi için IP adresini, tarayıcı ve cihaz bilgisini, istenen sayfa adresini ve zaman bilgisini barındırma sağlayıcısına iletir. Görseller, site ikonu ve yazı tipleri de aynı sağlayıcının içerik dağıtım altyapısından yüklenir. Bu kayıtlar sitenin çalışması, güvenliği ve ilgili mevzuattan doğan yükümlülükler için tutulur. Hukuki sebep: meşru menfaat (KVKK m.5/2-f) ve kanunlarda öngörülme ile hukuki yükümlülük (KVKK m.5/2-a ve ç). Bu kayıtların içeriği ve saklama süresi barındırma sağlayıcısının politikalarına göre belirlenir.
### Ziyaret istatistikleri ve performans ölçümü (yalnızca iznin varsa)
Çerez tercihlerinden izin verirsen Jetpack İstatistik ve WordPress.com performans ölçümü çalışır. Bu araçlar IP adresini, görüntülediğin sayfayı, seni yönlendiren adresi, tarayıcı ve cihaz bilgisini ve zaman bilgisini işler. Amaç, hangi sayfaların ne kadar ziyaret edildiğini ve sitenin ne kadar hızlı açıldığını görmektir. Hukuki sebep: açık rızan (KVKK m.5/1). İzin vermezsen bu araçlar hiç çalışmaz ve site aynı şekilde kullanılabilir. İznini istediğin zaman <a href="#cerez-tercihleri">çerez tercihlerinden</a> geri alabilirsin.
### Tarot sayfası
Tarot sayfasında soru yazma alanı yoktur. Kart seçimlerin ve açılımın yalnızca tarayıcında oluşturulur; sunucuya gönderilmez ve kaydedilmez. Sayfanın açılmasına ilişkin teknik erişim kayıtları, diğer sayfalarda olduğu gibi barındırma sağlayıcısında tutulabilir.
### Tarayıcında tutulan tercihler
Tema tercihin, seçtiğin burç ve çerez tercihin yalnızca kendi tarayıcında saklanır ve bize gönderilmez. Ayrıntılar <a href="/cerez-politikasi/">Çerez Politikası</a>’nda.
### Daha önceki bülten kayıtları
Bülten hizmeti şu anda sunulmuyor ve yeni kayıt alınmıyor. Daha önce bültene kaydolduysan e-posta adresin ve onay kaydın sitenin veritabanında durmaktadır; bu adreslere bülten gönderilmemektedir. Bu kayıtlar en geç 8 Kasım 2026 tarihinde silinecektir. Kaydının daha önce silinmesini istersen daha önce aldığın e-postadaki “ayrıl” bağlantısını kullanabilir ya da bize yazabilirsin.
## Verilerin kimlere aktarılır?
- Barındırma, içerik dağıtımı ve (iznin varsa) ölçüm: Automattic Inc. (WordPress.com, Jetpack). Automattic ABD merkezli bir şirkettir ve verileri yurt dışındaki sunucularda işleyebilir.
- E-posta: Bize yazdığın e-postalar, Google LLC tarafından sağlanan Gmail hizmetindeki skyra.astro@gmail.com kutusunda tutulur. Google da verileri yurt dışındaki sunucularda işleyebilir.
- Yetkili kamu kurum ve kuruluşları: yalnızca kanunların öngördüğü durumlarda ve talep edilen ölçüde.
## Yurt dışına aktarım
Yukarıdaki hizmet sağlayıcılar nedeniyle kişisel verilerin yurt dışındaki sunucularda işlenmektedir. Ölçüm araçları yalnızca açık rızanla çalışır. Barındırma ve e-posta hizmetleri için KVKK’nın 9. maddesinde öngörülen aktarım güvencelerine ilişkin değerlendirme sürmektedir; tamamlandığında bu bölüm güncellenecektir.
## Saklama süreleri
- E-posta yazışmaları: talebin sonuçlandıktan sonra en fazla 6 ay; bir uyuşmazlık veya yasal yükümlülük varsa bunun gerektirdiği süre boyunca.
- Hesaplama araçlarına girdiğin bilgiler: site yazılımı tarafından saklanmaz.
- Barındırma sağlayıcısının teknik kayıtları ve ölçüm verileri: sağlayıcının politikalarında belirtilen süre. Jetpack İstatistik, IP adreslerini içeren kayıtları 28 gün tuttuğunu açıklamaktadır.
Saklama sebebi ortadan kalkan veriler silinir, yok edilir veya anonim hâle getirilir.
## Çocuklar
Skyra Astro 18 yaşından küçükleri hedeflemez. Bize yazmak için 18 yaşını doldurmuş olmalısın. 18 yaşından küçük birinin bize kişisel bilgi gönderdiğini fark edersen bize yazabilirsin; bu bilgileri sileriz.
## Hakların
KVKK’nın 11. maddesi uyarınca; kişisel verilerinin işlenip işlenmediğini öğrenme, işlenmişse buna ilişkin bilgi talep etme, işlenme amacını ve amacına uygun kullanılıp kullanılmadığını öğrenme, yurt içinde veya yurt dışında aktarıldığı üçüncü kişileri bilme, eksik veya yanlış işlenmişse düzeltilmesini isteme, KVKK’nın 7. maddesindeki şartlar çerçevesinde silinmesini veya yok edilmesini isteme, bu düzeltme ve silme işlemlerinin aktarıldığı üçüncü kişilere bildirilmesini isteme, münhasıran otomatik sistemlerle analiz edilmesi nedeniyle aleyhine bir sonucun ortaya çıkmasına itiraz etme ve kanuna aykırı işleme sebebiyle zarara uğraman hâlinde zararın giderilmesini talep etme haklarına sahipsin.
## Başvuru
Başvurunu <a href="mailto:skyra.astro@gmail.com">skyra.astro@gmail.com</a> adresine iletebilirsin. Başvurunda adını ve soyadını, iletişim bilgini ve talebini açıkça belirtmen gerekir; kimliğini doğrulamak için yalnızca gerekli olan bilgiyi isteriz. Başvurunu en kısa sürede ve en geç 30 gün içinde ücretsiz olarak yanıtlarız; işlemin ayrıca bir maliyet gerektirmesi hâlinde Kurulca belirlenen tarifedeki ücret alınabilir.
Son güncelleme: 8 Ekim 2026
TXT,
	),
	array(
		'slug'    => 'gizlilik-politikasi',
		'title'   => 'Gizlilik Politikası',
		'excerpt' => 'Skyra Astro’da hangi bilgilerin nereden geçtiği ve nasıl korunduğu.',
		'body'    => <<<'TXT'
Skyra Astro’yu kullanırken hangi bilgilerin nereden geçtiğini açık ve sade bir dille anlatmak istiyoruz. Siteyi Şuara Güncü işletir. Hukuki sebepler, aktarımlar ve hakların <a href="/kvkk/">KVKK Aydınlatma Metni</a>’nde; tarayıcında tutulan kayıtlar <a href="/cerez-politikasi/">Çerez Politikası</a>’nda ayrıntılı olarak yer alır.
## Hesap ve üyelik yok
Sitede üyelik, hesap ya da ödeme yoktur. Siteyi kullanmak için kimliğini paylaşman gerekmez.
## Doğum bilgilerin
Doğum haritası ve burç araçlarına girdiğin tarih, saat ve yer yalnızca hesaplama için site sunucusuna gönderilir. Sitenin yazılımı bu bilgileri veritabanına yazmaz ve sayfa adresine eklemez. Ana sayfadaki özetten tam haritaya geçerken bilgiler tarayıcı sekmende (sessionStorage) kısa süre tutulur ve harita sayfası açıldığında silinir. Yer aramasına yazdığın ifade sunucuya sorgu olarak iletilir; arama sitenin içindeki yer listesinde yapılır, bu arama için başka bir hizmete istek gönderilmez.
## E-posta ile iletişim
Bize e-postayla yazdığında paylaştığın bilgiler yalnızca talebini ele almak için kullanılır. Sitede iletişim formu bulunmuyor.
## Barındırma ve içerik dağıtımı
Site WordPress.com (Automattic Inc.) altyapısında barındırılır. Sayfalar, görseller, site ikonu ve yazı tipleri bu altyapıdan yüklendiği için tarayıcın IP adresini ve teknik istek bilgilerini Automattic’e iletir. Bu, sitenin çalışması için gereklidir.
## Tarot sayfası
Tarot sayfasında soru yazma alanı yoktur. Kart seçimlerin ve açılımın yalnızca tarayıcında oluşturulur; sunucuya gönderilmez, kaydedilmez ve tarayıcında da saklanmaz.
## Ziyaret istatistikleri
Jetpack İstatistik ve WordPress.com performans ölçümü yalnızca <a href="#cerez-tercihleri">çerez tercihlerinden</a> izin verirsen çalışır. İzin vermezsen bu araçlara hiçbir istek gönderilmez. Sitede reklam, yeniden hedefleme ya da sosyal medya takip pikseli yoktur.
## Bülten
Bülten hizmeti şu anda sunulmuyor ve sitede bülten kayıt formu bulunmuyor. Daha önceki kayıtlarla ilgili bilgi KVKK Aydınlatma Metni’nde yer alır.
## Dış bağlantılar
Sayfalardaki Instagram veya GeoNames bağlantılarına tıkladığında ilgili hizmetin sitesine geçersin; oradaki işlemler o hizmetin kendi gizlilik kurallarına tabidir. Bu bağlantılar sen tıklamadıkça arka planda hiçbir şey yüklemez.
## Güvenlik
Verilere erişimi site yönetimiyle sınırlı tutarız. İnternet üzerinden yapılan hiçbir aktarım için mutlak güvenlik garantisi verilemeyeceğini de açıkça belirtmek isteriz.
## Değişiklikler ve iletişim
Veri akışları değişirse bu sayfayı ve ilgili metinleri yeni işlem başlamadan önce güncelleriz. Soruların ve KVKK başvuruların için: <a href="mailto:skyra.astro@gmail.com">skyra.astro@gmail.com</a>
Son güncelleme: 8 Ekim 2026
TXT,
	),
	array(
		'slug'    => 'cerez-politikasi',
		'title'   => 'Çerez Politikası',
		'excerpt' => 'Skyra Astro’nun tarayıcında tuttuğu kayıtlar ve izne bağlı ölçüm araçları.',
		'body'    => <<<'TXT'
Skyra Astro ziyaretçilerine çerez yazmaz. Bazı tercihlerini hatırlamak için tarayıcının kendi depolama alanını (localStorage ve sessionStorage) kullanır. Bu kayıtlar cihazında kalır ve bize gönderilmez. Ziyaret istatistikleri ise çerez kullanmasa da teknik veri işlediği için yalnızca iznin varsa çalışır.
<a href="#cerez-tercihleri">Çerez tercihlerini aç</a>
## Gerekli kayıtlar (her zaman açık)
Bunlar senin istediğin bir işlevin çalışması için gereklidir:
- skyra-theme (localStorage): açık ya da koyu görünüm tercihini hatırlar. Sen silene kadar kalır.
- skyra-sign (localStorage): hesaplama sonucundaki ya da seçtiğin burcu hatırlar ve günlük yorumlarda öne çıkarır. Burç sayfalarındaki “burcum” seçimini kaldırarak ya da tarayıcı verilerini silerek temizleyebilirsin.
- skyra-birth (sessionStorage): ana sayfadaki özetten tam harita sayfasına geçerken doğum bilgilerini taşır; harita sayfası açıldığında silinir.
- skyra-consent (localStorage): çerez ve ölçüm tercihini ve tercih tarihini hatırlar, böylece her sayfada yeniden sorulmaz.
Tarot sayfası tarayıcında hiçbir kayıt tutmaz.
Site yöneticileri ve editörler yönetim paneline giriş yaptığında WordPress oturum çerezleri kullanılır; bunlar ziyaretçilere yazılmaz.
## İzne bağlı ölçüm araçları (varsayılan olarak kapalı)
- Jetpack İstatistik (Automattic Inc.): hangi sayfaların ne kadar ziyaret edildiğini ölçer. IP adresi, sayfa adresi, yönlendiren adres, tarayıcı ve cihaz bilgisi işlenir. Çerez yazmaz. Automattic, IP adreslerini içeren istatistik kayıtlarını 28 gün tuttuğunu açıklamaktadır.
- WordPress.com performans ölçümü (Automattic Inc.): sayfanın ne kadar hızlı yüklendiğini ölçer; IP adresi ve teknik sayfa yükleme bilgileri işlenir.
Bu iki araç, izin vermediğin sürece tarayıcında hiç çalışmaz ve onlara hiçbir istek gönderilmez. “Tümünü Reddet” seçeneği sitenin hiçbir işlevini kısıtlamaz.
## Tercihini değiştirmek
Sayfaların en altındaki “Çerez tercihleri” bağlantısından ya da bu sayfadaki bağlantıdan iznini istediğin zaman verebilir veya geri alabilirsin. Tarayıcının site verilerini silme seçeneğiyle bu kayıtların tamamını da temizleyebilirsin; bu durumda tercihin yeniden sorulur. Tarayıcındaki kayıtları silmek, ölçüm araçlarının daha önce topladığı verileri kendiliğinden silmez; bunun için KVKK Aydınlatma Metni’ndeki başvuru yolunu kullanabilirsin.
Son güncelleme: 8 Ekim 2026
TXT,
	),
);
