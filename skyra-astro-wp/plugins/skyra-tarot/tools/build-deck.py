"""Builds data/deck.json from the card texts below (source of truth).

Card texts are original Skyra Astro copy: symbolic, reflective, no
predictions, no fear language. Run: python3 tools/build-deck.py
"""
import json, os

MAJOR = [
 ("Deli", ["başlangıç", "merak", "cesaret"],
  "Bilinmeyene açık, hafif ve meraklı bir enerji. Her şeyi planlamadan ilk adımı atmaya, yeni bir deneyime öğrenci gibi yaklaşmaya davet ediyor olabilir.",
  "Heyecan ile dikkatsizlik arasındaki çizgi incelmiş olabilir. Atlamadan önce zemini yoklamak ya da ertelediğin bir başlangıca neden çekindiğini fark etmek iyi gelebilir.",
  "Hangi konuda kendine yeniden başlama izni verebilirsin?"),
 ("Büyücü", ["irade", "beceri", "odak"],
  "Elindeki araçların farkına varma ve niyeti eyleme dönüştürme kartı. Dağınık görünen kaynaklarını tek bir hedef etrafında toplayabileceğini hatırlatıyor olabilir.",
  "Enerji dağılmış ya da yeteneklerin yeterince kullanılmıyor olabilir. Söylenenle yapılan arasında bir boşluk varsa onu dürüstçe görmek için iyi bir an.",
  "Bugün elindeki hangi kaynağı daha bilinçli kullanabilirsin?"),
 ("Başrahibe", ["sezgi", "sessizlik", "iç bilgi"],
  "Sessizleşip içini dinlemenin değerini anlatır. Her şeyin hemen açıklanması gerekmez; bazı cevaplar acele etmeden, kendiliğinden belirginleşebilir.",
  "İç sesin gürültünün altında kalmış olabilir. Başkalarının görüşleri arasında kendi sezgini nereye bıraktığını sormak isteyebilirsin.",
  "Sessiz kaldığında içinden hangi cevap yükseliyor?"),
 ("İmparatoriçe", ["bereket", "yaratıcılık", "şefkat"],
  "Besleyen, büyüten ve yaratan bir enerji. Kendine ve çevrene şefkatle alan açtığında fikirlerin ve ilişkilerinin doğal bir biçimde serpilebileceğini hatırlatır.",
  "Herkese bakarken kendini ihmal ediyor ya da yaratıcılığını erteliyor olabilirsin. Beslenme ihtiyacını kendine de tanımak iyi gelebilir.",
  "Kendini beslemek için bu hafta neye alan açabilirsin?"),
 ("İmparator", ["yapı", "sınır", "sorumluluk"],
  "Düzen kurma, sınır çizme ve sorumluluk alma kartı. Net bir yapı, özgürlüğünü kısıtlamak yerine ona dayanabileceği bir zemin sağlayabilir.",
  "Kontrol ihtiyacı katılığa dönüşmüş ya da tam tersine dağınıklık artmış olabilir. Hangi kuralların sana hâlâ hizmet ettiğini gözden geçirebilirsin.",
  "Hayatının hangi alanı daha net bir sınırdan fayda görebilir?"),
 ("Hierofant", ["gelenek", "öğrenme", "değerler"],
  "Bilginin aktarıldığı yerleri, öğretmenleri ve paylaşılan değerleri temsil eder. Bir gelenekten, bir ustadan ya da düzenli bir öğrenmeden destek almak iyi gelebilir.",
  "Sana dayatılan kurallarla kendi değerlerin çatışıyor olabilir. Neyi gerçekten benimsediğini, neyi alışkanlıkla sürdürdüğünü ayırt etmek isteyebilirsin.",
  "Hangi değerleri gerçekten kendin seçtin?"),
 ("Âşıklar", ["seçim", "uyum", "bağ"],
  "Kalbin ve değerlerin aynı yöne baktığı seçimleri anlatır. Bir ilişki ya da karar, kim olduğunla ne kadar uyumlu olduğunu sorgulamaya davet ediyor olabilir.",
  "İçindeki iki istek arasında bölünmüş ya da bir seçimin sorumluluğundan kaçıyor olabilirsin. Uyumsuzluğu adlandırmak ilk adım olabilir.",
  "Bu seçim, değer verdiğin şeylerle ne kadar örtüşüyor?"),
 ("Savaş Arabası", ["kararlılık", "yön", "ilerleme"],
  "Zıt güçleri tek bir yönde toplayıp ilerlemenin kartı. Odak ve kararlılıkla engellerin arasından yolunu bulabileceğini hatırlatıyor olabilir.",
  "Hız yönü kaybettirmiş ya da çabalar farklı yönlere çekiliyor olabilir. Durup rotayı yeniden belirlemek ilerlemenin bir parçası olabilir.",
  "Gerçekten hangi yöne gitmek istiyorsun?"),
 ("Güç", ["cesaret", "sabır", "yumuşak güç"],
  "Gücün baskıdan değil sabırdan ve şefkatten geldiğini anlatır. Zorlayıcı bir duyguyu bastırmak yerine onu nazikçe yönlendirebileceğini hatırlatıyor olabilir.",
  "Kendinden şüphe ya da yorgunluk öne çıkmış olabilir. Kendine katı davranmak yerine güvenini yavaş yavaş yeniden kurmak iyi gelebilir.",
  "Kendine karşı nerede daha sabırlı olabilirsin?"),
 ("Ermiş", ["içe dönüş", "arayış", "bilgelik"],
  "Kalabalıktan bir adım geri çekilip kendi ışığınla yolu aydınlatma kartı. Yalnız kalmak, düşünmek ve kendi cevaplarını aramak için iyi bir zaman olabilir.",
  "İçe çekilmek yalnızlığa dönüşmüş ya da tam tersine kendinle baş başa kalmaktan kaçıyor olabilirsin. Dengeyi yeniden kurmak isteyebilirsin.",
  "Kendine ayırdığın sessiz zaman sana ne söylüyor?"),
 ("Kader Çarkı", ["döngü", "değişim", "akış"],
  "Hayatın döngüsel hareketini hatırlatır. Değişen koşullara direnmek yerine akışın içinde nerede durduğunu fark etmek yeni olasılıklar gösterebilir.",
  "Tekrar eden bir döngünün içinde sıkışmış gibi hissediyor olabilirsin. Bu döngüde kendi seçimlerinin payını görmek onu kırmanın yolunu açabilir.",
  "Hangi döngü kendini tekrarlıyor ve sen onda nerede duruyorsun?"),
 ("Adalet", ["denge", "dürüstlük", "sorumluluk"],
  "Dürüst bir değerlendirme ve dengeli kararlar kartı. Eylemlerle sonuçlar arasındaki bağı açıkça görmek adil bir yol bulmana yardım edebilir.",
  "Bir durumda dengenin bozulduğunu ya da kendine karşı dürüst olmadığını hissediyor olabilirsin. Sorumluluğun hangi kısmının sana ait olduğunu tartabilirsin.",
  "Bu durumda adil olan ne olurdu, sana karşı da?"),
 ("Asılan Adam", ["bekleyiş", "teslimiyet", "yeni bakış"],
  "Durmanın ve bakış açısını değiştirmenin kartı. Bir şeyi zorlamak yerine bekleyip başka bir açıdan bakmak gözden kaçanı görünür kılabilir.",
  "Beklemek bir erteleme alışkanlığına dönüşmüş olabilir. Neyi feda ettiğini ve bu bekleyişin sana gerçekten hizmet edip etmediğini sorabilirsin.",
  "Bu duruma ters açıdan baksan ne görürdün?"),
 ("Ölüm", ["kapanış", "dönüşüm", "yenilenme"],
  "Bu kart gerçek bir ölümü değil, bir dönemin kapanışını simgeler. Artık sana hizmet etmeyen bir alışkanlığı, rolü ya da bakışı bırakmanın yeni bir şeye yer açabileceğini anlatır.",
  "Kapanması gereken bir şeye tutunuyor olabilirsin. Değişime karşı direncin neyi korumaya çalıştığını nazikçe sorabilirsin.",
  "Neyi bırakırsan kendine yer açmış olursun?"),
 ("Denge", ["ölçü", "uyum", "sabır"],
  "Zıtlıkları harmanlama ve ölçüyü bulma kartı. Acele etmeden, küçük ayarlamalarla daha uyumlu bir ritim kurabileceğini hatırlatıyor olabilir.",
  "Bir alanda aşırılık ya da dengesizlik öne çıkmış olabilir. Ritmini yeniden ayarlamak için neyi azaltıp neyi artıracağına bakabilirsin.",
  "Günlük ritminde neyi biraz azaltıp neyi biraz artırabilirsin?"),
 ("Şeytan", ["bağlılık", "arzu", "gölge"],
  "Bizi bağlayan alışkanlıkları, arzuları ve kaygıları görünür kılar. Zincirlerin çoğu zaman sandığımızdan gevşek olduğunu fark etmek özgürleştirici olabilir.",
  "Seni kısıtlayan bir kalıbı fark etmeye ve ondan uzaklaşmaya başlıyor olabilirsin. Küçük bir adım bile alanını genişletebilir.",
  "Hangi alışkanlık seni gerçekten istediğinden uzak tutuyor?"),
 ("Kule", ["sarsılma", "açığa çıkma", "yeniden kurma"],
  "Sağlam sanılan bir yapının sarsılmasını simgeler. Ani bir fark ediş rahatsız edici olabilir; ama gerçeğe dayanmayan şeylerin yerine daha sağlam bir zemin kurmaya alan açabilir.",
  "Kaçınılmaz görünen bir değişimi ertelemeye ya da içten içe yaşanan bir sarsıntıyı görmezden gelmeye çalışıyor olabilirsin. Küçük adımlarla yüzleşmek geçişi yumuşatabilir.",
  "Hangi yapının yeniden kurulmaya ihtiyacı var?"),
 ("Yıldız", ["umut", "iyileşme", "ilham"],
  "Fırtınadan sonra gelen sakin umudun kartı. Kendine yeniden güvenmeye, ilham veren şeylere açılmaya ve uzun vadeli bir hayale bağlanmaya davet ediyor olabilir.",
  "Umut yorgunluğa yenik düşmüş olabilir. İlham kaynaklarına küçük ve düzenli dokunuşlarla yeniden bağlanmayı deneyebilirsin.",
  "Seni yeniden umutlandıran küçük şey ne?"),
 ("Ay", ["belirsizlik", "hayal", "sezgi"],
  "Her şeyin net olmadığı, sezgilerin ve hayallerin öne çıktığı bir alanı anlatır. Belirsizlik içinde acele hüküm vermek yerine kaygılarla sezgileri ayırt etmeye çalışabilirsin.",
  "Bir kafa karışıklığı dağılmaya, saklı bir duygu açığa çıkmaya başlıyor olabilir. Neyin gerçek, neyin endişe olduğunu ayırmak kolaylaşabilir.",
  "Bu konuda kaygın ne söylüyor, sezgin ne söylüyor?"),
 ("Güneş", ["açıklık", "neşe", "canlılık"],
  "Açıklık, sıcaklık ve yaşama sevinci kartı. Kendini olduğun gibi göstermenin ve küçük mutlulukları kutlamanın enerjini yükseltebileceğini hatırlatıyor olabilir.",
  "Neşen bulutların arkasında kalmış ya da beklentiler çok yükselmiş olabilir. Basit ve gerçek sevinçlere dönmek iyi gelebilir.",
  "Bugün seni gerçekten ne gülümsetiyor?"),
 ("Mahkeme", ["uyanış", "değerlendirme", "iç çağrı"],
  "Geçmişe dürüstçe bakıp ondan ders çıkarma ve içten gelen bir çağrıya kulak verme kartı. Yeni bir sayfaya hazırlanmak için iyi bir değerlendirme anı olabilir.",
  "Kendini fazla sert yargılıyor ya da bir iç çağrıyı duymazdan geliyor olabilirsin. Geçmişle barışmak ilerlemeyi kolaylaştırabilir.",
  "Geçmişindeki hangi deneyimden yeni bir ders çıkarabilirsin?"),
 ("Dünya", ["tamamlanma", "bütünlük", "yeni döngü"],
  "Bir döngünün tamamlanmasını ve bütünlük duygusunu anlatır. Geldiğin yolu takdir etmek ve bir sonraki aşamaya hafiflemiş olarak geçmek için iyi bir zaman olabilir.",
  "Bir şeyi tamamlamaya çok yakınken son adımı erteliyor olabilirsin. Neyin eksik kaldığını netleştirmek kapanışı kolaylaştırabilir.",
  "Hangi emeğini henüz yeterince kutlamadın?"),
]

SUITS = {
 "asa": ("Asa", "Asalar", "ateş"),
 "kupa": ("Kupa", "Kupalar", "su"),
 "kilic": ("Kılıç", "Kılıçlar", "hava"),
 "tilsim": ("Tılsım", "Tılsımlar", "toprak"),
}
RANK_NAMES = {1: "Ası", 11: "Uşağı", 12: "Şövalyesi", 13: "Kraliçesi", 14: "Kralı"}
NUM_WORDS = {2: "İkilisi", 3: "Üçlüsü", 4: "Dörtlüsü", 5: "Beşlisi", 6: "Altılısı", 7: "Yedilisi", 8: "Sekizlisi", 9: "Dokuzlusu", 10: "Onlusu"}

MINOR = {
 "asa": [
  (["kıvılcım", "ilham", "yeni girişim"], "Yeni bir fikrin ya da isteğin ilk kıvılcımı. İçinde canlanan bir heyecanı küçük de olsa somut bir adıma dönüştürmeye davet ediyor olabilir.", "İlham var ama yönünü bulamamış ya da heyecan çabuk sönüyor olabilir. Seni gerçekten neyin ateşlediğini netleştirmek isteyebilirsin.", "İçindeki hangi kıvılcımı büyütmek istiyorsun?"),
  (["planlama", "ufuk", "karar"], "Elindekini değerlendirip daha geniş bir ufka bakma kartı. Bir sonraki adımı planlamak ve konfor alanının ötesini düşünmek için uygun bir an olabilir.", "Plan yapmak harekete geçmenin yerini almış ya da bilinmeyen gözüne korkutucu görünmeye başlamış olabilir. Küçük bir deneme adımı netlik getirebilir.", "Planının hangi kısmını küçük bir denemeyle sınayabilirsin?"),
  (["genişleme", "bekleyiş", "ileriyi görmek"], "Attığın adımların ilk sonuçlarını görmeye ve ufku genişletmeye başladığın bir aşamayı anlatır. Daha büyük düşünmek ve iş birliklerine açık olmak iyi gelebilir.", "İşler sandığından yavaş ilerliyor olabilir. Sabırla rotayı gözden geçirmek hayal kırıklığını azaltabilir.", "Ufkunu genişletmek için kiminle ya da neyle bağlantı kurabilirsin?"),
  (["kutlama", "yuva", "aidiyet"], "Bir aşamanın tamamlanışını, birlikte kutlamayı ve aidiyet duygusunu anlatır. Emeğini ve seni destekleyen çevreni takdir etmek için güzel bir zaman olabilir.", "Aidiyet duygusunda bir eksiklik ya da kutlamayı erteleyen bir mükemmeliyetçilik olabilir. Küçük başarıları da görmek iyi gelebilir.", "Bugüne kadar başardığın neyi kutlamayı unuttun?"),
  (["rekabet", "sürtüşme", "farklı sesler"], "Fikirlerin ve isteklerin çarpıştığı hareketli bir ortamı anlatır. Bu sürtüşme iyi yönetildiğinde yaratıcı bir enerjiye dönüşebilir.", "Bir çatışmadan kaçınmak ya da gerilimi içine atmak öne çıkmış olabilir. Ortak bir zemin aramak rahatlatabilir.", "Bu çatışmanın altında hangi ortak istek var?"),
  (["tanınma", "başarı", "özgüven"], "Emeğin görünür olduğu, takdir ve özgüvenle ilerlenen bir anı anlatır. Başarını kabul etmek ve paylaşmak motivasyonunu besleyebilir.", "Takdir görme ihtiyacı ya da başkalarının onayına bağlılık öne çıkmış olabilir. Kendi gözünde neyi başardığını sormak isteyebilirsin.", "Başkaları görmese de kendinle gurur duyduğun şey ne?"),
  (["duruş", "kararlılık", "sınır"], "Bir duruşu korumanın ve inandığın şeyin arkasında durmanın kartı. Sınırlarını açıkça ifade etmek için cesaretin olduğunu hatırlatıyor olabilir.", "Her şeye karşı savunmada kalmak yorucu olmaya başlamış olabilir. Hangi mücadelenin gerçekten sana ait olduğunu seçmek enerjini koruyabilir.", "Gerçekten savunmaya değer olan ne?"),
  (["hız", "hareket", "ivme"], "Hızlanan gelişmeleri ve ivme kazanan bir süreci anlatır. Uzun süredir bekleyen işler hareketlenebilir; akışa eşlik etmek iyi gelebilir.", "Aceleyle yapılan işler ya da gecikmeler sabrını zorluyor olabilir. Hızını koşullara göre ayarlamak dengeyi korumana yardım edebilir.", "Hangi işi hızlandırmak, hangisini yavaşlatmak istiyorsun?"),
  (["dayanıklılık", "son çaba", "temkin"], "Yorulmuş ama vazgeçmemiş bir dayanıklılığı anlatır. Son düzlüğe yaklaşırken deneyimlerinden öğrendiklerin sana güç verebilir.", "Sürekli tetikte olmak yorgunluğa dönüşmüş olabilir. Destek istemek ya da dinlenmek bir zayıflık değil, devam etmenin yolu olabilir.", "Devam etmek için bugün neye ihtiyacın var?"),
  (["yük", "sorumluluk", "fazla üstlenmek"], "Taşıdığın sorumlulukların ağırlaştığını gösterir. Hepsini tek başına taşıman gerekip gerekmediğini sorgulamak için iyi bir an olabilir.", "Bir yükü bırakmaya ya da paylaşmaya başlıyor olabilirsin. Önceliklerini sadeleştirmek nefes almanı sağlayabilir.", "Taşıdığın yüklerden hangisi aslında sana ait değil?"),
  (["keşif", "merak", "deneme"], "Merakla bir şeyi denemeye başlayan genç ve hevesli bir enerji. Yeni bir ilgi alanını keşfetmek ya da yaratıcı bir fikri oyun gibi denemek için uygun bir zaman olabilir.", "Heves çabuk dağılıyor ya da fikirler başlamadan erteleniyor olabilir. Küçük ama düzenli bir deneme ritmi kurmak yardımcı olabilir.", "Hangi merakına bugün küçük bir zaman ayırabilirsin?"),
  (["tutku", "macera", "atılganlık"], "Tutkuyla ileri atılan, cesur ve hareketli bir enerji. Bir hedefe doğru heyecanla ilerlemek için motivasyonun yüksek olabilir.", "Atılganlık sabırsızlığa ya da yarım kalan işlere dönüşmüş olabilir. Enerjini tek bir hedefe yönlendirmek onu daha verimli kılabilir.", "Tutkunu hangi tek hedefe yöneltebilirsin?"),
  (["özgüven", "sıcaklık", "karizma"], "Kendinden emin, sıcak ve ilham veren bir duruş. Kendini ifade ederken hem kararlı hem cömert olabileceğini hatırlatıyor olabilir.", "Özgüvenin dalgalanıyor ya da başkalarının ışığı seni gölgede bırakıyormuş gibi hissediyor olabilirsin. Kendi değerini yeniden hatırlamak iyi gelebilir.", "Kendini en canlı hissettiğin an hangisiydi?"),
  (["vizyon", "liderlik", "yön verme"], "Bir vizyonu olan ve başkalarına yön verebilen olgun bir enerji. Büyük resmi görüp cesur kararlar almak için deneyimine güvenebilirsin.", "Liderlik baskıcılığa ya da sabırsızlığa kaymış olabilir. Vizyonunu paylaşırken başkalarına da alan bırakmak dengeyi kurabilir.", "Vizyonunu başkalarıyla nasıl paylaşabilirsin?"),
 ],
 "kupa": [
  (["yeni bir duygu", "açılım", "şefkat"], "Duygusal bir açılımın, yeni bir yakınlığın ya da şefkatin başlangıcı. Kalbini açmak ve hissettiklerini kabul etmek için bir davet olabilir.", "Duygular içeride birikmiş ya da kendine şefkat göstermekte zorlanıyor olabilirsin. Hislerine isim vermek ilk adım olabilir.", "Şu an kalbin neye ihtiyaç duyuyor?"),
  (["karşılıklılık", "bağ", "uzlaşma"], "Karşılıklı ilgi ve saygıya dayanan bir bağı anlatır. Açık bir iletişim, anlaşılmanın ve dengenin kapısını aralayabilir.", "Bir ilişkide denge bozulmuş ya da karşılıklılık zayıflamış olabilir. Beklentileri açıkça konuşmak yakınlığı yeniden kurabilir.", "Bu ilişkide verdiğinle aldığın arasında nasıl bir denge var?"),
  (["dostluk", "kutlama", "topluluk"], "Dostlukla, paylaşılan sevinçle ve birlikte olmanın neşesiyle ilgili. Seni destekleyen insanlarla bağlarını güçlendirmek iyi gelebilir.", "Sosyal ilişkiler yüzeysel ya da yorucu gelmeye başlamış olabilir. Hangi bağların seni gerçekten beslediğini fark etmek isteyebilirsin.", "Kiminle birlikteyken kendin gibi hissediyorsun?"),
  (["doyumsuzluk", "içe kapanma", "fırsatı görmek"], "Elindekilere karşı bir isteksizlik ya da içe kapanma hâli. Önüne konan yeni fırsatları fark etmek için bakışını biraz kaldırmak isteyebilirsin.", "İçe kapanma dönemi sona eriyor ve yeniden ilgi duymaya başlıyor olabilirsin. Küçük bir merak kıvılcımı yeni bir kapı açabilir.", "Görmezden geldiğin hangi küçük fırsat var?"),
  (["kayıp", "hüzün", "kabullenme"], "Bir kaybın ya da hayal kırıklığının ardından gelen hüznü anlatır. Hissettiklerine alan tanırken hâlâ ayakta duran şeyleri de görmek mümkün olabilir.", "Hüznün yükü hafiflemeye ve yeniden umuda dönmeye başlıyor olabilirsin. Geçmişle barışmak ileriye bakmayı kolaylaştırabilir.", "Kaybettiğin şeyin yanında hâlâ elinde olan ne?"),
  (["anılar", "saflık", "nostalji"], "Geçmişin sıcak anılarını, çocuksu bir saflığı ve basit iyilikleri anlatır. Seni besleyen bir anıya ya da eski bir dostluğa dönmek iyi gelebilir.", "Geçmişe fazla tutunmak bugünü yaşamayı zorlaştırıyor olabilir. Anılardan güç alıp şimdiye dönmek isteyebilirsin.", "Geçmişten bugüne hangi güzelliği taşımak istersin?"),
  (["hayaller", "seçenekler", "yanılsama"], "Birçok seçenek ve hayal arasında kalmış bir zihni anlatır. Hayal kurmak değerli; ama hangisinin gerçekten senin için olduğunu ayırt etmek gerekebilir.", "Seçenekler netleşmeye ve bir karara yaklaşmaya başlıyor olabilirsin. Gerçekçi bir değerlendirme hayallerini somutlaştırabilir.", "Bu seçeneklerden hangisi değerlerinle örtüşüyor?"),
  (["ayrılış", "arayış", "anlam"], "Artık doyurmayan bir durumdan uzaklaşıp daha anlamlı bir şeyi aramanın kartı. Bırakmak cesaret ister; ama içsel bir arayışa alan açabilir.", "Gitmek ile kalmak arasında kararsız kalmış olabilirsin. Neyi aradığını netleştirmek kararını kolaylaştırabilir.", "Gerçekten aradığın şey ne?"),
  (["memnuniyet", "dilek", "keyif"], "Duygusal doyum ve memnuniyet kartı. İstediğin şeylerin bir kısmına kavuştuğunu fark etmek ve bunun tadını çıkarmak iyi gelebilir.", "Memnuniyet dış koşullara fazla bağlanmış ya da dilekler beklenen tatmini getirmemiş olabilir. İç huzurunun kaynağını sorabilirsin.", "Seni gerçekten tatmin eden şey ne?"),
  (["huzur", "yakınlık", "duygusal bütünlük"], "Sevgi dolu bağların ve duygusal huzurun kartı. Yakınlarınla paylaştığın uyumu ve seni evinde hissettiren şeyleri takdir etmek için güzel bir an olabilir.", "Beklediğin uyum ile gerçekte yaşanan arasında bir fark olabilir. İdealleri bir kenara bırakıp gerçek bağlara bakmak yardımcı olabilir.", "Seni evinde hissettiren ne?"),
  (["duygusal merak", "hassasiyet", "içten mesaj"], "Duygularına ve sezgilerine merakla yaklaşan, hassas ve yaratıcı bir enerji. Küçük bir duygusal fark ediş yeni bir bakış getirebilir.", "Duygusal tepkiler çabuk dalgalanıyor ya da hassasiyet seni kırılgan hissettiriyor olabilir. Duygularını yargılamadan gözlemlemek iyi gelebilir.", "Bugün hangi duygun sana bir şey söylemek istiyor?"),
  (["romantizm", "ideal", "ifade"], "Kalbini izleyen, romantik ve idealist bir enerji. Duygularını ifade etmek ya da yaratıcı bir hayalin peşinden gitmek için cesaretin olabilir.", "İdealler gerçeklikten kopmuş ya da duygusal vaatler havada kalmış olabilir. Hislerini eylemlerinle uyumlu hâle getirmek isteyebilirsin.", "Duygularını nasıl daha somut ifade edebilirsin?"),
  (["empati", "sezgi", "duygusal derinlik"], "Derin bir empati ve sezgiyle başkalarını anlayabilen şefkatli bir duruş. Kendi duygusal ihtiyaçlarına da aynı özeni göstermeyi hatırlatıyor olabilir.", "Başkalarının duygularını taşımaktan kendi duygularına yer kalmamış olabilir. Sınır koymak şefkatin bir parçası olabilir.", "Kendine nasıl şefkat gösterebilirsin?"),
  (["duygusal denge", "olgunluk", "sakinlik"], "Duygularını tanıyan ve onları sakinlikle yönetebilen olgun bir enerji. Zor anlarda bile dengeyi koruyabileceğini hatırlatıyor olabilir.", "Duygular bastırılmış ya da kontrol etme çabası mesafeye dönüşmüş olabilir. Hissettiklerini güvenli bir şekilde ifade etmek rahatlatabilir.", "Duygularını bastırmadan nasıl dengede kalabilirsin?"),
 ],
 "kilic": [
  (["netlik", "gerçek", "yeni fikir"], "Zihnin berraklaştığı, gerçeğin açıkça görüldüğü bir an. Yeni bir fikir ya da net bir karar düşüncelerini toparlayabilir.", "Düşünceler bulanıklaşmış ya da gerçeği söylemekte zorlanıyor olabilirsin. Bir adım geri çekilip bilgiyi toplamak netlik getirebilir.", "Bu durumda gerçekten bildiğin şey ne?"),
  (["kararsızlık", "denge arayışı", "ertelenen seçim"], "İki seçenek arasında beklemede kalmış bir zihni anlatır. Kararı ertelemek yerine eksik bilgiyi tamamlamak ve kalbini de dinlemek yardımcı olabilir.", "Uzun süren bir kararsızlık çözülmeye başlıyor ya da bir gerçek görünür hâle geliyor olabilir. Bir seçim yapmak rahatlatabilir.", "Karar vermeni zorlaştıran ne?"),
  (["kırgınlık", "hayal kırıklığı", "yüzleşme"], "Kırgınlığı ve canını sıkan bir fark edişi anlatır. Duyguyu kabul etmek iyileşmenin ilk adımı olabilir.", "Kırgınlık yavaş yavaş hafifliyor ya da eski bir yarayı bırakmaya hazırlanıyor olabilirsin. Kendine iyileşmek için zaman tanıyabilirsin.", "Bu kırgınlık sana neyi önemsediğini gösteriyor?"),
  (["dinlenme", "toparlanma", "sessizlik"], "Zihinsel ve bedensel olarak dinlenme, toparlanma kartı. Yeniden harekete geçmeden önce sessizliğe ve düşünmeye zaman ayırmak iyi gelebilir.", "Dinlenmeyi erteliyor ya da yeniden harekete geçmek için hazırlanıyor olabilirsin. Bedeninin ve zihninin sinyallerini dinleyebilirsin.", "Gerçekten dinlenmek için kendine neye izin vermen gerekiyor?"),
  (["çatışma", "kazanmak", "bedel"], "Kazanmanın bedelini sorgulatan bir çatışmayı anlatır. Haklı çıkmak ile ilişkiyi korumak arasında neyi seçtiğini fark etmek isteyebilirsin.", "Bir çatışmanın ardından barışmaya ya da onu geride bırakmaya yaklaşıyor olabilirsin. Kendi payını görmek rahatlatabilir.", "Bu tartışmada gerçekten neyi korumaya çalışıyorsun?"),
  (["geçiş", "uzaklaşma", "sakinlik"], "Zorlayıcı bir dönemden daha sakin bir yere geçişi anlatır. Değişim hüzünlü olsa da seni daha huzurlu bir alana taşıyabilir.", "Geçiş yavaşlamış ya da geride bırakmak istediğin bir şey seni takip ediyor olabilir. Neyi yanında götürmek istemediğini seçebilirsin.", "Yanında hangi düşünceleri taşımak istemiyorsun?"),
  (["strateji", "temkin", "tek başına hareket"], "Strateji, dikkat ve bazen tek başına hareket etmeyi anlatır. Planlarını korurken dürüstlüğünü de korumak dengeli bir yol olabilir.", "Saklanan bir gerçeği ya da bir kaçınma davranışını fark etmeye başlıyor olabilirsin. Açık olmak yükü hafifletebilir.", "Burada açık olmak neyi değiştirirdi?"),
  (["sıkışmışlık", "zihinsel engel", "seçenekler"], "Kendini sıkışmış hissettiğin ama engellerin bir kısmının zihinsel olduğu bir durumu anlatır. Seçeneklerini yeniden gözden geçirmek bir çıkış gösterebilir.", "Seni sınırlayan düşünce kalıplarından çıkmaya başlıyor olabilirsin. Küçük bir adım bile hareket alanını genişletebilir.", "Kendine hangi sınırlayıcı cümleyi tekrar ediyorsun?"),
  (["endişe", "zihinsel yük", "paylaşmak"], "Zihnin kaygılarla dolduğu ve düşüncelerin büyüdüğü bir anı anlatır. Endişelerini paylaşmak ya da yazıya dökmek onları küçültebilir; uzun süren kaygılar için bir uzmandan destek almak da iyi gelebilir.", "Kaygı hafiflemeye ya da en büyük endişenin abartıldığını görmeye başlıyor olabilirsin. Destek istemek yükünü paylaşmanı sağlayabilir.", "Bu endişeyi kiminle paylaşabilirsin?"),
  (["bitiş", "dip noktası", "yeni sayfa"], "Bir sürecin en zor noktaya ulaştığını ve artık sona erdiğini anlatır. Bir şey bittiğinde yeni bir sayfa açmak da mümkün hâle gelir.", "En zor dönem geride kalıyor ve toparlanma başlıyor olabilir. Kendine iyileşmek için zaman tanımak önemli olabilir.", "Bu bitiş hangi başlangıç için yer açıyor?"),
  (["merak", "gözlem", "soru sormak"], "Merakla soru soran, gözlemleyen ve öğrenmek isteyen bir zihin. Yeni bilgiler edinmek ve fikirlerini sınamak için iyi bir zaman olabilir.", "Aceleci yorumlar ya da savunmacı bir dil öne çıkmış olabilir. Konuşmadan önce dinlemek dengeyi kurabilir.", "Hangi soruyu sormaktan çekiniyorsun?"),
  (["hız", "kararlılık", "keskin dil"], "Fikirlerinin arkasında hızla ve kararlılıkla duran bir enerji. Hedefe odaklanmak işe yarar; ama sözlerinin keskinliğine dikkat etmek iyi olabilir.", "Aceleci kararlar ya da sert bir iletişim ilişkileri zorluyor olabilir. Yavaşlamak ve dinlemek daha iyi sonuçlar doğurabilir.", "Hızını ve sözlerini nasıl dengeleyebilirsin?"),
  (["açık sözlülük", "bağımsızlık", "berrak bakış"], "Deneyimlerinden süzülmüş berrak bir bakış ve açık sözlülük. Sınırlarını net ifade etmek ve gerçeği nazikçe söylemek için güçlü bir an olabilir.", "Mesafe soğukluğa ya da eleştiri sertliğe dönüşmüş olabilir. Netliğini şefkatle birleştirmek ilişkilerini güçlendirebilir.", "Gerçeği hem net hem nazik nasıl söyleyebilirsin?"),
  (["mantık", "adalet", "ilkeler"], "Mantığa, ilkelere ve adil düşünceye dayanan olgun bir zihin. Kararlarını duygulardan bir adım uzaklaşarak tartmak için uygun bir zaman olabilir.", "Katı kurallar ya da aşırı akılcılık empatiyi gölgeliyor olabilir. Mantığını insani tarafınla dengelemek isteyebilirsin.", "Bu kararda mantığın ve değerlerin ne söylüyor?"),
 ],
 "tilsim": [
  (["fırsat", "somut başlangıç", "kaynak"], "Somut bir fırsatın, yeni bir kaynağın ya da sağlam bir başlangıcın tohumu. Bir fikri gerçek dünyada yeşertmek için zemin hazır olabilir.", "Bir fırsat gözden kaçıyor ya da planlar somut adıma dönüşmüyor olabilir. Küçük ama gerçekçi bir başlangıç yapmak yardımcı olabilir.", "Bu fikri somutlaştırmak için atabileceğin ilk küçük adım ne?"),
  (["denge", "esneklik", "öncelikler"], "Birden fazla sorumluluğu dengeleme ve esnek kalma kartı. Önceliklerini akıllıca sıralamak ritmini korumana yardım edebilir.", "Çok fazla şeyi aynı anda taşımak dengeyi bozmuş olabilir. Bazı topları bilinçli olarak bırakmak rahatlatabilir.", "Şu an hangi önceliği bir süreliğine bırakabilirsin?"),
  (["iş birliği", "ustalık", "emek"], "Birlikte çalışmanın, becerinin ve özenli emeğin değerini anlatır. Bir işte başkalarının katkısıyla daha sağlam sonuçlar oluşabilir.", "Ekip içinde uyum eksikliği ya da emeğin görünmemesi öne çıkmış olabilir. Rolleri ve beklentileri netleştirmek yardımcı olabilir.", "Bu işte kimin katkısı seni güçlendirebilir?"),
  (["güvenlik", "tutunmak", "koruma"], "Sahip olduklarını korumak ve güvende hissetmek isteğini anlatır. Sağlam bir zemin değerli; ama sıkı tutmak akışı da kısıtlayabilir.", "Kontrolü bırakmaya ya da aşırı korumacılığı gevşetmeye başlıyor olabilirsin. Paylaşmak yeni bir denge yaratabilir.", "Neye sıkı tutunuyorsun ve neden?"),
  (["eksiklik", "dışarıda kalmak", "destek aramak"], "Maddi ya da duygusal bir eksiklik hissini anlatır. Yardım istemek ve sana açık olan desteği görmek zor dönemleri hafifletebilir.", "Zor bir dönemden çıkmaya ve yeniden toparlanmaya başlıyor olabilirsin. Küçük destekler büyük fark yaratabilir.", "Hangi destek kapısını henüz çalmadın?"),
  (["paylaşım", "denge", "cömertlik"], "Vermek ve almak arasındaki dengeyi, cömertliği ve paylaşımı anlatır. Kaynaklarını adil ve bilinçli şekilde paylaşmak ilişkileri güçlendirebilir.", "Verme ile alma arasındaki denge bozulmuş olabilir. Kendi ihtiyaçlarını da hesaba katmak sağlıklı bir sınır kurabilir.", "Verdiğin ve aldığın arasında nasıl bir denge var?"),
  (["sabır", "değerlendirme", "uzun vade"], "Emeğinin meyvelerini beklerken durup değerlendirme kartı. Uzun soluklu bir süreçte sabırlı olmak ve yöntemini gözden geçirmek iyi gelebilir.", "Sabırsızlık ya da emeğinin karşılığını görememe hissi öne çıkmış olabilir. Emeğini nereye harcadığını yeniden düşünebilirsin.", "Emeğini harcadığın şey uzun vadede sana ne kazandırıyor?"),
  (["ustalık", "çalışma", "özen"], "Bir beceriyi sabırla geliştirme ve işine özen gösterme kartı. Düzenli emeğin seni ustalığa taşıyabileceğini hatırlatıyor olabilir.", "Rutin sıkıcılaşmış ya da mükemmeliyetçilik ilerlemeyi yavaşlatıyor olabilir. İşinde yeniden anlam bulmak motivasyon getirebilir.", "Hangi beceriyi her gün biraz geliştirebilirsin?"),
  (["bağımsızlık", "emeğin karşılığı", "keyif"], "Kendi emeğinle kurduğun konforun ve bağımsızlığın tadını çıkarma kartı. Kendine ayırdığın zaman ve özen değerli olabilir.", "Bağımsızlık yalnızlığa dönüşmüş ya da kendine ayırdığın zaman azalmış olabilir. Ne kadar bağımsız olmak istediğini yeniden tanımlayabilirsin.", "Kendine ayırdığın zamanı nasıl değerlendiriyorsun?"),
  (["süreklilik", "kökler", "kalıcı değerler"], "Kalıcı değerleri, kuşaklar arası bağları ve uzun vadeli güven duygusunu anlatır. Gelecek için sağlam temeller kurmak üzerine düşünmek iyi gelebilir.", "Aile ya da kalıcılık beklentileri baskı yaratıyor olabilir. Neyi devralmak, neyi bırakmak istediğini seçebilirsin.", "Uzun vadede neyin kalıcı olmasını istiyorsun?"),
  (["öğrenme", "odak", "yeni beceri"], "Somut bir konuyu öğrenmeye hevesli, dikkatli ve sabırlı bir enerji. Yeni bir beceriye ya da plana küçük adımlarla başlamak için uygun bir zaman olabilir.", "Odak dağılmış ya da öğrenmek istediğin şeye zaman ayıramıyor olabilirsin. Gerçekçi bir çalışma planı kurmak yardımcı olabilir.", "Hangi beceriyi öğrenmeye başlamak istiyorsun?"),
  (["istikrar", "sorumluluk", "yavaş ama emin"], "Yavaş ama kararlı ilerleyen, güvenilir bir enerji. Düzenli ve sabırlı adımların seni sağlam bir sonuca götürebileceğini hatırlatıyor olabilir.", "Rutin katılaşmış ya da ilerleme duraksamış olabilir. Küçük bir değişiklik yeniden ivme kazandırabilir.", "Rutininde neyi değiştirmek seni canlandırabilir?"),
  (["özen", "pratiklik", "bereket"], "Hem pratik hem şefkatli, çevresini besleyen bir duruş. Bedenine, evine ve kaynaklarına özen göstermek sana iyi gelebilir.", "Başkalarına bakarken kendi ihtiyaçlarını unutmuş ya da maddi kaygılar öne çıkmış olabilir. Kendine de özen göstermek dengeyi kurabilir.", "Bedenine ve yaşam alanına nasıl özen gösterebilirsin?"),
  (["istikrar", "güven", "olgunluk"], "Emeğiyle istikrar kurmuş olgun bir enerji. Kaynaklarını akıllıca yönetmek ve başkalarına da destek olmak için iyi bir konumda olabilirsin.", "Başarı ya da güvenlik kaygısı esnekliğini azaltmış olabilir. Sahip olduklarının sana gerçekten ne anlattığını sorabilirsin.", "Senin için gerçek bolluk ne demek?"),
 ],
}

deck = []
for i, (name, kw, up, rev, q) in enumerate(MAJOR):
    deck.append({"id": "m%02d" % i, "n": name, "a": "major", "s": None, "r": i, "k": kw, "up": up, "rev": rev, "q": q})
for suit, cards in MINOR.items():
    single, plural, element = SUITS[suit]
    assert len(cards) == 14, suit
    for idx, (kw, up, rev, q) in enumerate(cards):
        rank = idx + 1
        name = "%sın %s" % (plural, RANK_NAMES.get(rank) or NUM_WORDS[rank])
        deck.append({"id": "%s-%02d" % (suit, rank), "n": name, "a": "minor", "s": suit, "r": rank, "k": kw, "up": up, "rev": rev, "q": q})

assert len(deck) == 78
out = os.path.join(os.path.dirname(__file__), "..", "data", "deck.json")
with open(out, "w", encoding="utf-8") as f:
    json.dump(deck, f, ensure_ascii=False, separators=(",", ":"))
print("cards:", len(deck), "bytes:", os.path.getsize(out))
print([c["n"] for c in deck if c["a"] == "minor"][:16])
