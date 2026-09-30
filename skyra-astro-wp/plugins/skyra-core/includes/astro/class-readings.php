<?php
/**
 * Interpretive texts. Written in Skyra's voice: symbolic, open, never fatalistic.
 *
 * Every generated reading is built from real sky data (sign, house, aspect);
 * the wording frames it as a possibility, leaving room for the reader.
 *
 * @package Skyra\Astro
 */

namespace Skyra\Astro;

defined( 'ABSPATH' ) || defined( 'SKYRA_ENGINE_STANDALONE' ) || exit;

final class Readings {

	/** The Moon through the solar houses of a sign: [short theme, reading]. */
	public const MOON_HOUSE = array(
		1  => array( 'Kendine alan', 'Ay senin burcunda. Duyguların daha görünür; kendi ihtiyaçlarını fark etmek ve onlara alan açmak için iyi bir gün olabilir.' ),
		2  => array( 'Değerlerin', 'Ay kaynaklar alanında. Neye değer verdiğini, zamanını ve enerjini nereye harcadığını sakince gözden geçirmek isteyebilirsin.' ),
		3  => array( 'Bağlantılar', 'Ay iletişim alanında. Kısa konuşmalar, mesajlar ve yakın çevreyle bağlantılar günün ritmini belirleyebilir.' ),
		4  => array( 'Kökler', 'Ay yuva alanında. Evine, ailene ya da seni besleyen köklere biraz daha fazla zaman ayırmak iyi gelebilir.' ),
		5  => array( 'Keyif', 'Ay keyif alanında. Yaratıcı bir uğraş, oyunbaz bir an ya da kalbini ısıtan küçük bir şey günü hafifletebilir.' ),
		6  => array( 'Düzen', 'Ay gündelik düzen alanında. Yapılacaklar listesini sadeleştirmek ve bedenini dinlemek sana alan açabilir.' ),
		7  => array( 'İlişkiler', 'Ay ilişkiler alanında. Karşındakinin bakış açısını dinlemek, ortak bir dil bulmayı kolaylaştırabilir.' ),
		8  => array( 'Derinlik', 'Ay paylaşım ve derinlik alanında. Yüzeyin altında kalan bir duyguyu fark etmek ya da ortak kaynakları konuşmak gündeme gelebilir.' ),
		9  => array( 'Ufuk', 'Ay ufuk alanında. Yeni bir fikir, bir kitap ya da bir yolculuk planı merakını canlandırabilir.' ),
		10 => array( 'Görünürlük', 'Ay görünürlük alanında. İşin ve hedeflerin daha fazla dikkat çekebilir; öncelik sırasını netleştirmek isteyebilirsin.' ),
		11 => array( 'Topluluk', 'Ay topluluk alanında. Arkadaşlarla temas, ortak bir proje ya da gelecek planları iyi gelebilir.' ),
		12 => array( 'Dinlenme', 'Ay iç dünya alanında. Yavaşlamak, dinlenmek ve günü biraz daha sessiz geçirmek sana iyi gelebilir.' ),
	);

	/** Theme of the day by the Moon's sign: [title, sentence]. */
	public const MOON_SIGN_THEME = array(
		'koc'     => array( 'Başlamak için cesaret', 'Ertelediğin küçük bir adımı atmak için enerji yüksek olabilir.' ),
		'boga'    => array( 'Yavaşlamak ve değer vermek', 'Beden, konfor ve somut olan ön plana çıkabilir.' ),
		'ikizler' => array( 'Merak ve hafif temaslar', 'Konuşmalar, sorular ve yeni bilgiler günü hareketlendirebilir.' ),
		'yengec'  => array( 'Yuvaya dönmek', 'Duygusal güven ve yakın bağlar daha çok önem kazanabilir.' ),
		'aslan'   => array( 'Kalpten ifade', 'Yaratıcılık, cömertlik ve görünür olma isteği artabilir.' ),
		'basak'   => array( 'Özenli küçük adımlar', 'Düzenlemek, sadeleştirmek ve iyileştirmek kolaylaşabilir.' ),
		'terazi'  => array( 'İlişkilerde denge', 'Uzlaşma, nezaket ve estetik öne çıkabilir.' ),
		'akrep'   => array( 'Derine bakmak', 'Yoğun duygular ve odaklı bir dikkat gündeme gelebilir.' ),
		'yay'     => array( 'Ufku genişletmek', 'Anlam arayışı, öğrenme ve özgürlük ihtiyacı belirginleşebilir.' ),
		'oglak'   => array( 'Sağlam zemin', 'Sorumluluklar, planlar ve uzun vadeli adımlar öne çıkabilir.' ),
		'kova'    => array( 'Farklı bir açı', 'Alışılmışın dışında fikirler ve topluluk duygusu canlanabilir.' ),
		'balik'   => array( 'Sezgiye alan açmak', 'Hayal gücü, şefkat ve sessiz anlar daha anlamlı gelebilir.' ),
	);

	public const RISING = array(
		'koc'     => 'Dünyaya doğrudan ve enerjik bir tavırla yaklaşırsın. İlk izlenimde cesur, hızlı karar veren ve inisiyatif alan biri olarak görülebilirsin.',
		'boga'    => 'Sakin, güven veren ve ayakları yere basan bir ilk izlenim bırakırsın. Acele etmeden, kendi ritminde ilerlemeyi seversin.',
		'ikizler' => 'Meraklı, konuşkan ve hareketli bir ilk izlenimin var. Yeni insanlarla ve fikirlerle kolayca bağlantı kurabilirsin.',
		'yengec'  => 'Dünyayı yumuşak, koruyucu ve sezgisel bir tavırla karşılarsın. İnsanlar yanında kendini güvende hissedebilir.',
		'aslan'   => 'Sıcak, parlak ve kendinden emin bir ilk izlenim bırakırsın. Bulunduğun ortama doğal bir canlılık katabilirsin.',
		'basak'   => 'Özenli, dikkatli ve yardımsever bir ilk izlenimin var. Ayrıntıları fark eder, işleri düzene sokmak istersin.',
		'terazi'  => 'Nazik, dengeli ve estetik bir ilk izlenim bırakırsın. Uyum aramak ve karşındakini anlamak sana doğal gelir.',
		'akrep'   => 'Derin, gözlemci ve etkileyici bir duruşun var. İnsanlar seni ilk anda tam çözemeyebilir; güvenini zamanla açarsın.',
		'yay'     => 'İyimser, açık sözlü ve keşfe hazır bir ilk izlenim bırakırsın. Özgürlük ve anlam arayışı tavrına yansır.',
		'oglak'   => 'Ciddi, ağırbaşlı ve güvenilir bir ilk izlenimin var. Hedeflerine planlı ve sabırlı adımlarla ilerlersin.',
		'kova'    => 'Özgün, bağımsız ve biraz beklenmedik bir ilk izlenim bırakırsın. Farklı bakış açıları getirmek sana doğal gelir.',
		'balik'   => 'Yumuşak, empatik ve hayal gücü geniş bir ilk izlenimin var. Ortamın duygusunu hızla sezebilirsin.',
	);

	public const MOON_SIGN = array(
		'koc'     => 'Duygularını hızlı ve doğrudan yaşarsın. Harekete geçmek, içindeki gerginliği boşaltmanın en iyi yolu olabilir.',
		'boga'    => 'Güvende hissetmek için istikrar, konfor ve somut dokunuşlara ihtiyaç duyarsın. Değişime zaman tanımak sana iyi gelir.',
		'ikizler' => 'Duygularını konuşarak ve anlamlandırarak işlersin. Merakını besleyen bir sohbet ruh halini hızla değiştirebilir.',
		'yengec'  => 'Derin bir aidiyet ve bakım ihtiyacın var. Sevdiklerinle kurduğun bağ, duygusal dengenin merkezinde olabilir.',
		'aslan'   => 'Görülmek, takdir edilmek ve içten ifade etmek sana iyi gelir. Cömert bir kalple sevgi gösterirsin.',
		'basak'   => 'Düzen ve işe yarar olmak sana güven verir. Küçük rutinler, duygusal dalgalanmalarda sana zemin olabilir.',
		'terazi'  => 'Duygusal dengeni ilişkilerde ve uyumlu ortamlarda bulursun. Çatışmadan çok, anlaşmaya yakın hissedersin.',
		'akrep'   => 'Duyguları yoğun ve derin yaşarsın. Güven, senin için yüzeyden çok daha önemlidir.',
		'yay'     => 'Özgürlük, hareket ve anlam arayışı ruh halini besler. Yeni ufuklar duygusal olarak da seni canlandırabilir.',
		'oglak'   => 'Duygularını kontrollü ve sorumluluk bilinciyle taşırsın. Kendine de şefkat göstermek dengeyi kolaylaştırabilir.',
		'kova'    => 'Duygularına biraz mesafeden bakmayı seversin. Kendin olabildiğin, özgür topluluklarda rahatlarsın.',
		'balik'   => 'Hassas, sezgisel ve empatik bir iç dünyan var. Sınırlarını korumak, duygusal enerjini dengelemene yardımcı olabilir.',
	);

	/** Default Moon phase texts; editors can override them with Moon Phase Content posts. */
	public const PHASE = array(
		'new'             => 'Döngünün en karanlık, en sessiz anı. Niyetlerini sade tutmak ve yeni bir başlangıca alan açmak için uygun bir zaman olabilir.',
		'waxing-crescent' => 'Işık artmaya başlıyor. Küçük ama somut ilk adımlar, niyetini günlük hayata taşımana yardım edebilir.',
		'first-quarter'   => 'Ay\'ın yarısı aydınlık. Karşına çıkan küçük engeller, neyi gerçekten istediğini netleştirmeni sağlayabilir.',
		'waxing-gibbous'  => 'Dolunaya az kaldı. İnce ayar yapmak, sabretmek ve süreci gözden geçirmek için iyi bir dönem.',
		'full'            => 'Işık en yüksek noktasında. Duygular görünürleşebilir; neyin tamamlandığını fark etmek ve kutlamak isteyebilirsin.',
		'waning-gibbous'  => 'Işık azalmaya başlıyor. Öğrendiklerini paylaşmak ve sindirmek için alan açabilirsin.',
		'last-quarter'    => 'Bırakma zamanı. Artık işe yaramayan alışkanlıkları gözden geçirmek sana hafiflik getirebilir.',
		'waning-crescent' => 'Döngünün son günleri. Dinlenmek, yavaşlamak ve bir sonraki başlangıca hazırlanmak iyi gelebilir.',
	);

	/** What a retrograde of each planet is traditionally read as. */
	public const RETRO = array(
		'mercury' => array( 'Yılda üç kez, yaklaşık üç hafta', 'İletişim, planlar ve teknik konularda gözden geçirme dönemi olarak yorumlanır.' ),
		'venus'   => array( 'Yaklaşık 18 ayda bir, altı hafta kadar', 'İlişkileri, değerleri ve zevkleri yeniden tartma dönemi olarak yorumlanır.' ),
		'mars'    => array( 'Yaklaşık iki yılda bir, iki buçuk ay kadar', 'Enerjiyi, hedefleri ve öfkeyi içe dönerek yeniden düzenleme dönemi olarak yorumlanır.' ),
		'jupiter' => array( 'Her yıl, yaklaşık dört ay', 'Büyüme ve inanç konularında iç muhasebe dönemi olarak yorumlanır.' ),
		'saturn'  => array( 'Her yıl, yaklaşık dört buçuk ay', 'Sorumlulukları ve yapıları sessizce yeniden değerlendirme dönemi olarak yorumlanır.' ),
		'uranus'  => array( 'Her yıl, yaklaşık beş ay', 'Değişim isteğinin içe döndüğü, özgürlüğün yeniden tanımlandığı dönem olarak yorumlanır.' ),
		'neptune' => array( 'Her yıl, yaklaşık beş ay', 'Hayallerin ve yanılsamaların daha net görülebildiği dönem olarak yorumlanır.' ),
		'pluto'   => array( 'Her yıl, yaklaşık beş ay', 'Derin dönüşüm süreçlerinin içsel olarak işlendiği dönem olarak yorumlanır.' ),
	);

	private const ASPECT_SKY = array(
		'conjunction' => 'yoğun bir birleşme yaratıyor',
		'sextile'     => 'küçük bir fırsat kapısı aralıyor',
		'square'      => 'çözülmek isteyen bir gerilim yaratabilir',
		'trine'       => 'akıcı bir uyum kuruyor',
		'opposition'  => 'denge arayışını öne çıkarıyor',
	);

	private const ASPECT_NATAL = array(
		'conjunction' => 'iç içe geçer; bu iki alan birbirini güçlü biçimde renklendirir',
		'sextile'     => 'birbirini destekler; küçük bir çabayla kolaylık sağlar',
		'square'      => 'zaman zaman gerilir; bu gerilim bilinçli kullanıldığında büyümeye dönüşebilir',
		'trine'       => 'kolayca akar; doğal bir yetenek gibi hissedilebilir',
		'opposition'  => 'iki ucu temsil eder; aralarında denge kurmak bir yaşam teması olabilir',
	);

	private const COMPAT_DISTANCE = array(
		0 => array( 'Ayna etkisi', 'Aynı burçtan iki kişi birbirini hızla tanır; ortak ritim güçlüdür. Benzer eksiklikler de büyüyebilir; farklı alanlarda birbirinize yer açmak dengeyi korur.' ),
		1 => array( 'Komşu burçlar', 'Birbirinize yakın ama farklı dillerden konuşursunuz. Birinizin doğal olanı, diğerine öğrenme alanı sunar; merak ve sabır bu bağı besler.' ),
		2 => array( 'Kolay akış', 'Altmışlık açı arkadaşça ve destekleyici bir bağ anlatır. Ortak projeler ve paylaşılan fikirler ilişkiyi canlı tutabilir.' ),
		3 => array( 'Büyüten sürtünme', 'Kare açı farklı ihtiyaçlar ve tempo anlamına gelebilir. Çekişme gibi görünen şey, açıkça konuşulduğunda ikinizi de büyüten bir dinamiğe dönüşebilir.' ),
		4 => array( 'Doğal uyum', 'Üçgen açı, aynı elementi paylaşan burçları buluşturur. Birbirinizi az sözle anlayabilirsiniz; rahatlığın rutine dönüşmemesine dikkat etmek iyi olabilir.' ),
		5 => array( 'Ayar gerektiren bağ', 'Aranızdaki 150°’lik açı, ortak noktası az iki farklı dünyayı anlatır. İlişki, küçük ayarlamalar ve açık iletişimle anlam kazanır.' ),
		6 => array( 'Tamamlayan zıtlık', 'Karşıt burçlar aynı eksenin iki ucudur. Güçlü bir çekim ve karşılıklı öğrenme olabilir; denge, iki tarafın alanına saygıyla kurulur.' ),
	);

	private const COMPAT_ELEMENTS = array(
		'ates-ates'     => 'İki ateş burcu: coşku ve cesaret bol; birlikte dinlenmeyi de unutmayın.',
		'ates-hava'     => 'Ateş ve hava birbirini besler: fikirler hızla eyleme dönüşebilir.',
		'ates-toprak'   => 'Ateş ivme, toprak süreklilik getirir: tempo farkını konuşmak önemli.',
		'ates-su'       => 'Ateş ve su: tutku ve duygu yoğun; birbirinizin hassasiyetini gözetmek dengeyi korur.',
		'toprak-toprak' => 'İki toprak burcu: güven, plan ve somut adımlar bu bağın temeli.',
		'hava-toprak'   => 'Toprak somut olanı, hava fikirleri sever: birbirinizin diline çeviri yapmak işe yarar.',
		'su-toprak'     => 'Toprak ve su birbirini besler: güven ve şefkat doğal olarak büyüyebilir.',
		'hava-hava'     => 'İki hava burcu: sohbet ve zihinsel uyum güçlü; duyguları da konuşmaya davet edin.',
		'hava-su'       => 'Hava mantığı, su duyguyu öne alır: ikisini de geçerli saymak bağı güçlendirir.',
		'su-su'         => 'İki su burcu: derin bir duygusal anlayış mümkün; sınırları konuşmak da önemli.',
	);

	private const MODALITY_PAIR = array(
		'oncu'     => 'İkiniz de öncü burçsunuz: başlatmayı seversiniz; kimin ne zaman yön vereceğini konuşmak işleri kolaylaştırır.',
		'sabit'    => 'İkiniz de sabit burçsunuz: bağlılık güçlü, esneklik ise bilinçli bir çaba isteyebilir.',
		'degisken' => 'İkiniz de değişken burçsunuz: uyum kolay, ama ortak bir yön belirlemek zaman alabilir.',
	);

	/* ------------------------------------------------------------------ */

	/**
	 * Daily reading for a sun sign, derived from the day's sky.
	 *
	 * @param string $slug Sign slug.
	 * @param array  $sky  Sky::day() output for the date.
	 */
	public static function daily( string $slug, array $sky ): array {
		$sign   = Zodiac::by_slug( $slug );
		$moon_i = Zodiac::by_slug( $sky['moon']['sign'] )['index'];
		$house  = ( ( $moon_i - $sign['index'] + 12 ) % 12 ) + 1;
		$sun_h  = ( ( Zodiac::by_slug( $sky['sun']['sign'] )['index'] - $sign['index'] + 12 ) % 12 ) + 1;
		[ $theme, $text ] = self::MOON_HOUSE[ $house ];

		$parts = array( $text );
		if ( ! empty( $sky['moon']['ingress'] ) ) {
			$to      = Zodiac::by_slug( $sky['moon']['ingress']['to'] );
			$next_h  = ( ( $to['index'] - $sign['index'] + 12 ) % 12 ) + 1;
			$parts[] = sprintf( 'Saat %s civarında Ay %s geçiyor; akşama doğru odak %s alanına kayabilir.', $sky['moon']['ingress']['time'], $to['dat'], Zodiac::HOUSES[ $next_h ] );
		}
		if ( ! empty( $sky['aspect'] ) ) {
			$parts[] = self::sky_aspect( $sky['aspect'] );
		}
		$parts[] = sprintf( 'Bu dönemde Güneş, senin için %s alanını aydınlatıyor.', Zodiac::HOUSES[ $sun_h ] );

		return array(
			'sign'   => $slug,
			'house'  => $house,
			'theme'  => $theme,
			'teaser' => $text,
			'text'   => implode( ' ', $parts ),
			'source' => 'computed',
		);
	}

	/** One sentence about a sky aspect. */
	public static function sky_aspect( array $aspect ): string {
		$a = Zodiac::PLANETS[ $aspect['a'] ];
		$b = Zodiac::PLANETS[ $aspect['b'] ];
		return sprintf(
			'%s ile %s arasındaki %s, %s ile %s arasında %s.',
			$a['name'],
			$b['name'],
			mb_strtolower( Zodiac::ASPECTS[ $aspect['type'] ]['name'], 'UTF-8' ),
			$a['area'],
			$b['area'],
			self::ASPECT_SKY[ $aspect['type'] ]
		);
	}

	public static function natal_aspect( array $aspect ): string {
		$a = Zodiac::PLANETS[ $aspect['a'] ];
		$b = Zodiac::PLANETS[ $aspect['b'] ];
		return sprintf( '%s ile %s: %s', self::ucfirst_tr( $a['area'] ), $b['area'], self::ASPECT_NATAL[ $aspect['type'] ] ) . '.';
	}

	/** Planet in sign (and house) sentence for the natal chart. */
	public static function placement( string $planet, string $sign_slug, ?int $house = null ): string {
		$p    = Zodiac::PLANETS[ $planet ];
		$sign = Zodiac::by_slug( $sign_slug );
		if ( 'node' === $planet ) {
			$text = sprintf( 'Gelişim yönün %s temalarına işaret ediyor olabilir', implode( ', ', $sign['keywords'] ) );
		} else {
			$text = sprintf( '%s %s ifade etmeye eğilimli olabilirsin', self::ucfirst_tr( $p['theme'] ), $sign['style'] );
		}
		if ( null !== $house ) {
			$text .= sprintf( '; bu enerji en çok %s alanında hissedilebilir', Zodiac::HOUSES[ $house ] );
		}
		if ( in_array( $planet, array( 'uranus', 'neptune', 'pluto' ), true ) ) {
			$text .= ' (bu yerleşimi yakın yıllarda doğan herkes paylaşır)';
		}
		return $text . '.';
	}

	/** Sign-level compatibility. */
	public static function compatibility( string $slug_a, string $slug_b ): array {
		$a        = Zodiac::by_slug( $slug_a );
		$b        = Zodiac::by_slug( $slug_b );
		$distance = abs( $a['index'] - $b['index'] );
		$distance = min( $distance, 12 - $distance );
		$pair     = array( $a['element'], $b['element'] );
		sort( $pair );
		[ $title, $text ] = self::COMPAT_DISTANCE[ $distance ];

		$angles = array( 0 => 'conjunction', 2 => 'sextile', 3 => 'square', 4 => 'trine', 6 => 'opposition' );
		return array(
			'a'        => $slug_a,
			'b'        => $slug_b,
			'distance' => $distance,
			'angle'    => $distance * 30,
			'aspect'   => $angles[ $distance ] ?? null,
			'title'    => $title,
			'text'     => $text,
			'elements' => self::COMPAT_ELEMENTS[ implode( '-', $pair ) ],
			'modality' => $a['modality'] === $b['modality'] ? self::MODALITY_PAIR[ $a['modality'] ] : null,
		);
	}

	/**
	 * Title and summary for a calendar event.
	 *
	 * @return array{title: string, summary: string}
	 */
	public static function event( array $e ): array {
		$sign = Zodiac::SIGNS[ Zodiac::index_of( $e['lon'] ) ];
		switch ( $e['type'] ) {
			case 'new_moon':
				return array(
					'title'   => 'Yeni Ay · ' . $sign['name'],
					'summary' => sprintf( 'Ay ve Güneş %s buluşuyor; yeni bir Ay döngüsü başlıyor. %s konuları için niyet zamanı olarak okunur.', $sign['loc'], self::ucfirst_tr( implode( ', ', $sign['keywords'] ) ) ),
				);
			case 'full_moon':
				$opp = Zodiac::SIGNS[ ( Zodiac::index_of( $e['lon'] ) + 6 ) % 12 ];
				return array(
					'title'   => 'Dolunay · ' . $sign['name'],
					'summary' => sprintf( 'Ay %s, Güneş karşısında %s. Tamamlanma ve görünür olma teması öne çıkabilir.', $sign['loc'], $opp['loc'] ),
				);
			case 'solar_eclipse':
				$types = array( 'total' => 'Tam', 'annular' => 'Halkalı', 'hybrid' => 'Hibrit', 'partial' => 'Parçalı' );
				return array(
					'title'   => $types[ $e['eclipse']['type'] ] . ' Güneş Tutulması · ' . $sign['name'],
					'summary' => sprintf( 'Yeni Ay, Ay düğümlerine yakın gerçekleşiyor ve %s bir tutulmaya dönüşüyor. Görünürlük bölgesi konuma göre değişir.', $sign['loc'] ),
				);
			case 'lunar_eclipse':
				$types = array( 'total' => 'Tam', 'partial' => 'Parçalı', 'penumbral' => 'Yarıgölge' );
				return array(
					'title'   => $types[ $e['eclipse']['type'] ] . ' Ay Tutulması · ' . $sign['name'],
					'summary' => sprintf( 'Dolunay %s, Dünya\'nın gölgesiyle buluşuyor. Tutulma, gecenin yaşandığı bölgelerden görülebilir.', $sign['loc'] ),
				);
			case 'ingress':
				$p = Zodiac::PLANETS[ $e['body'] ];
				if ( 'sun' === $e['body'] ) {
					$seasons = array( 0 => 'İlkbahar ekinoksu. ', 3 => 'Yaz gündönümü. ', 6 => 'Sonbahar ekinoksu. ', 9 => 'Kış gündönümü. ' );
					return array(
						'title'   => 'Güneş ' . $sign['dat'] . ' geçiyor',
						'summary' => ( $seasons[ Zodiac::index_of( $e['lon'] ) ] ?? '' ) . sprintf( '%s sezonu başlıyor: %s temaları öne çıkabilir.', $sign['name'], implode( ', ', $sign['keywords'] ) ),
					);
				}
				return array(
					'title'   => $e['retro'] ? sprintf( '%s geri hareketle %s dönüyor', $p['name'], $sign['dat'] ) : sprintf( '%s %s geçiyor', $p['name'], $sign['dat'] ),
					'summary' => sprintf( '%s konuları %s tonuyla renklenebilir: %s.', self::ucfirst_tr( $p['area'] ), $sign['name'], implode( ', ', $sign['keywords'] ) ),
				);
			case 'station_retro':
				$p = Zodiac::PLANETS[ $e['body'] ];
				return array(
					'title'   => $p['name'] . ' retrosu başlıyor',
					'summary' => sprintf( '%s %s %s noktasında geri hareket etmeye başlıyor. %s', $p['name'], $sign['loc'], Zodiac::degree( $e['lon'] ), self::RETRO[ $e['body'] ][1] ),
				);
			case 'station_direct':
				$p = Zodiac::PLANETS[ $e['body'] ];
				return array(
					'title'   => $p['name'] . ' düz harekete geçiyor',
					'summary' => sprintf( '%s %s %s noktasında yeniden ileri hareket etmeye başlıyor; retro döneminde gözden geçirilenler netleşebilir.', $p['name'], $sign['loc'], Zodiac::degree( $e['lon'] ) ),
				);
		}
		return array(
			'title'   => '',
			'summary' => '',
		);
	}

	/** Upper-case the first letter with Turkish rules (i → İ, ı → I). */
	public static function ucfirst_tr( string $text ): string {
		$first = mb_substr( $text, 0, 1, 'UTF-8' );
		$map   = array(
			'i' => 'İ',
			'ı' => 'I',
		);
		$upper = $map[ $first ] ?? mb_strtoupper( $first, 'UTF-8' );
		return $upper . mb_substr( $text, 1, null, 'UTF-8' );
	}
}
