<?php
/**
 * Reference data for signs, planets and aspects (Turkish).
 *
 * Grammar forms are stored explicitly because Turkish suffixes follow vowel
 * harmony and consonant softening; building them at runtime would be brittle.
 *
 * @package Skyra\Astro
 */

namespace Skyra\Astro;

defined( 'ABSPATH' ) || defined( 'SKYRA_ENGINE_STANDALONE' ) || exit;

final class Zodiac {

	/**
	 * Signs in ecliptic order starting at 0° Aries.
	 * loc: locative ("Koç'ta"), dat: dative ("Koç'a"), abl: ablative ("Koç'tan").
	 */
	public const SIGNS = array(
		array( 'slug' => 'koc', 'name' => 'Koç', 'en' => 'Aries', 'loc' => "Koç'ta", 'dat' => "Koç'a", 'abl' => "Koç'tan", 'gen' => "Koç'un", 'element' => 'ates', 'modality' => 'oncu', 'ruler' => 'mars', 'from' => '03-21', 'to' => '04-19', 'line' => 'Başlatır. Harekete geçirir. Beklemeyi sevmez.', 'style' => 'doğrudan, hızlı ve cesur bir şekilde', 'keywords' => array( 'cesaret', 'başlangıç', 'irade' ) ),
		array( 'slug' => 'boga', 'name' => 'Boğa', 'en' => 'Taurus', 'loc' => "Boğa'da", 'dat' => "Boğa'ya", 'abl' => "Boğa'dan", 'gen' => "Boğa'nın", 'element' => 'toprak', 'modality' => 'sabit', 'ruler' => 'venus', 'from' => '04-20', 'to' => '05-20', 'line' => 'Yavaşlar. Değer verir. Kalıcı olanı kurar.', 'style' => 'sakin, somut ve istikrarlı bir şekilde', 'keywords' => array( 'istikrar', 'değer', 'beden' ) ),
		array( 'slug' => 'ikizler', 'name' => 'İkizler', 'en' => 'Gemini', 'loc' => "İkizler'de", 'dat' => "İkizler'e", 'abl' => "İkizler'den", 'gen' => "İkizler'in", 'element' => 'hava', 'modality' => 'degisken', 'ruler' => 'mercury', 'from' => '05-21', 'to' => '06-20', 'line' => 'Merak eder. Bağlantı kurar. Soruyla ilerler.', 'style' => 'meraklı, çok yönlü ve hareketli bir şekilde', 'keywords' => array( 'merak', 'iletişim', 'öğrenme' ) ),
		array( 'slug' => 'yengec', 'name' => 'Yengeç', 'en' => 'Cancer', 'loc' => "Yengeç'te", 'dat' => "Yengeç'e", 'abl' => "Yengeç'ten", 'gen' => "Yengeç'in", 'element' => 'su', 'modality' => 'oncu', 'ruler' => 'moon', 'from' => '06-21', 'to' => '07-22', 'line' => 'Korur. Hisseder. Yuva kurar.', 'style' => 'koruyucu, duygusal ve sezgisel bir şekilde', 'keywords' => array( 'aidiyet', 'bakım', 'duygu' ) ),
		array( 'slug' => 'aslan', 'name' => 'Aslan', 'en' => 'Leo', 'loc' => "Aslan'da", 'dat' => "Aslan'a", 'abl' => "Aslan'dan", 'gen' => "Aslan'ın", 'element' => 'ates', 'modality' => 'sabit', 'ruler' => 'sun', 'from' => '07-23', 'to' => '08-22', 'line' => 'Parlar. Yaratır. Kalpten konuşur.', 'style' => 'sıcak, yaratıcı ve görünür bir şekilde', 'keywords' => array( 'yaratıcılık', 'cömertlik', 'ifade' ) ),
		array( 'slug' => 'basak', 'name' => 'Başak', 'en' => 'Virgo', 'loc' => "Başak'ta", 'dat' => "Başak'a", 'abl' => "Başak'tan", 'gen' => "Başak'ın", 'element' => 'toprak', 'modality' => 'degisken', 'ruler' => 'mercury', 'from' => '08-23', 'to' => '09-22', 'line' => 'İnceler. Düzenler. Özenle iyileştirir.', 'style' => 'özenli, analitik ve pratik bir şekilde', 'keywords' => array( 'özen', 'düzen', 'hizmet' ) ),
		array( 'slug' => 'terazi', 'name' => 'Terazi', 'en' => 'Libra', 'loc' => "Terazi'de", 'dat' => "Terazi'ye", 'abl' => "Terazi'den", 'gen' => "Terazi'nin", 'element' => 'hava', 'modality' => 'oncu', 'ruler' => 'venus', 'from' => '09-23', 'to' => '10-22', 'line' => 'Tartar. Uzlaştırır. Güzelliği arar.', 'style' => 'dengeli, ilişki odaklı ve estetik bir şekilde', 'keywords' => array( 'denge', 'ilişki', 'estetik' ) ),
		array( 'slug' => 'akrep', 'name' => 'Akrep', 'en' => 'Scorpio', 'loc' => "Akrep'te", 'dat' => "Akrep'e", 'abl' => "Akrep'ten", 'gen' => "Akrep'in", 'element' => 'su', 'modality' => 'sabit', 'ruler' => 'pluto', 'from' => '10-23', 'to' => '11-21', 'line' => 'Derine iner. Dönüştürür. Yüzeyle yetinmez.', 'style' => 'yoğun, derin ve odaklı bir şekilde', 'keywords' => array( 'derinlik', 'dönüşüm', 'bağlılık' ) ),
		array( 'slug' => 'yay', 'name' => 'Yay', 'en' => 'Sagittarius', 'loc' => "Yay'da", 'dat' => "Yay'a", 'abl' => "Yay'dan", 'gen' => "Yay'ın", 'element' => 'ates', 'modality' => 'degisken', 'ruler' => 'jupiter', 'from' => '11-22', 'to' => '12-21', 'line' => 'Keşfeder. Anlam arar. Ufkunu genişletir.', 'style' => 'geniş açılı, iyimser ve özgür bir şekilde', 'keywords' => array( 'keşif', 'anlam', 'özgürlük' ) ),
		array( 'slug' => 'oglak', 'name' => 'Oğlak', 'en' => 'Capricorn', 'loc' => "Oğlak'ta", 'dat' => "Oğlak'a", 'abl' => "Oğlak'tan", 'gen' => "Oğlak'ın", 'element' => 'toprak', 'modality' => 'oncu', 'ruler' => 'saturn', 'from' => '12-22', 'to' => '01-19', 'line' => 'Planlar. Tırmanır. Zamanla inşa eder.', 'style' => 'disiplinli, sorumlu ve uzun vadeli bir şekilde', 'keywords' => array( 'yapı', 'sorumluluk', 'sabır' ) ),
		array( 'slug' => 'kova', 'name' => 'Kova', 'en' => 'Aquarius', 'loc' => "Kova'da", 'dat' => "Kova'ya", 'abl' => "Kova'dan", 'gen' => "Kova'nın", 'element' => 'hava', 'modality' => 'sabit', 'ruler' => 'uranus', 'from' => '01-20', 'to' => '02-18', 'line' => 'Sorgular. Yeniler. Kendi yolunu çizer.', 'style' => 'özgün, bağımsız ve topluluğu gözeten bir şekilde', 'keywords' => array( 'özgünlük', 'topluluk', 'gelecek' ) ),
		array( 'slug' => 'balik', 'name' => 'Balık', 'en' => 'Pisces', 'loc' => "Balık'ta", 'dat' => "Balık'a", 'abl' => "Balık'tan", 'gen' => "Balık'ın", 'element' => 'su', 'modality' => 'degisken', 'ruler' => 'neptune', 'from' => '02-19', 'to' => '03-20', 'line' => 'Sezer. Hayal eder. Sınırları yumuşatır.', 'style' => 'hassas, hayal gücü yüksek ve akışkan bir şekilde', 'keywords' => array( 'sezgi', 'şefkat', 'hayal' ) ),
	);

	public const ELEMENTS = array(
		'ates'   => array( 'name' => 'Ateş', 'desc' => 'Harekete geçiren, ilham veren enerji.' ),
		'toprak' => array( 'name' => 'Toprak', 'desc' => 'Somutlaştıran, kalıcılık kuran enerji.' ),
		'hava'   => array( 'name' => 'Hava', 'desc' => 'Bağlantı kuran, düşünceyi dolaştıran enerji.' ),
		'su'     => array( 'name' => 'Su', 'desc' => 'Hisseden, sezgiyle akan enerji.' ),
	);

	public const MODALITIES = array(
		'oncu'     => 'Öncü',
		'sabit'    => 'Sabit',
		'degisken' => 'Değişken',
	);

	/** Planets: display name, grammar and the life area they symbolise. */
	public const PLANETS = array(
		'sun'     => array( 'name' => 'Güneş', 'short' => 'Gü', 'gen' => "Güneş'in", 'theme' => 'kimliğini ve yaşam enerjini', 'area' => 'kimlik ve yaşam enerjisi' ),
		'moon'    => array( 'name' => 'Ay', 'short' => 'Ay', 'gen' => "Ay'ın", 'theme' => 'duygularını ve güvende hissetme ihtiyacını', 'area' => 'duygular ve ihtiyaçlar' ),
		'mercury' => array( 'name' => 'Merkür', 'short' => 'Me', 'gen' => "Merkür'ün", 'theme' => 'düşüncelerini ve iletişimini', 'area' => 'düşünce ve iletişim' ),
		'venus'   => array( 'name' => 'Venüs', 'short' => 'Ve', 'gen' => "Venüs'ün", 'theme' => 'sevgini, zevklerini ve ilişki kurma biçimini', 'area' => 'ilişkiler ve değerler' ),
		'mars'    => array( 'name' => 'Mars', 'short' => 'Ma', 'gen' => "Mars'ın", 'theme' => 'isteklerini ve harekete geçme biçimini', 'area' => 'eylem ve istek' ),
		'jupiter' => array( 'name' => 'Jüpiter', 'short' => 'Jü', 'gen' => "Jüpiter'in", 'theme' => 'büyüme ve anlam arayışını', 'area' => 'büyüme ve anlam' ),
		'saturn'  => array( 'name' => 'Satürn', 'short' => 'Sa', 'gen' => "Satürn'ün", 'theme' => 'sorumluluk ve sınır duygunu', 'area' => 'yapı ve sorumluluk' ),
		'uranus'  => array( 'name' => 'Uranüs', 'short' => 'Ur', 'gen' => "Uranüs'ün", 'theme' => 'değişim ve özgürlük ihtiyacını', 'area' => 'değişim ve özgürlük' ),
		'neptune' => array( 'name' => 'Neptün', 'short' => 'Ne', 'gen' => "Neptün'ün", 'theme' => 'hayallerini ve sezgilerini', 'area' => 'sezgi ve hayal gücü' ),
		'pluto'   => array( 'name' => 'Plüton', 'short' => 'Pl', 'gen' => "Plüton'un", 'theme' => 'dönüşüm ve derinlik ihtiyacını', 'area' => 'dönüşüm ve derinlik' ),
		'node'    => array( 'name' => 'Kuzey Ay Düğümü', 'short' => 'KD', 'gen' => "Kuzey Ay Düğümü'nün", 'theme' => 'gelişim yönünü', 'area' => 'gelişim yönü' ),
	);

	/** Major aspects: angle, natal orb, glyph key and tone. */
	public const ASPECTS = array(
		'conjunction' => array( 'angle' => 0, 'orb' => 8, 'name' => 'Kavuşum', 'symbol' => '☌', 'tone' => 'birleştirir', 'kind' => 'neutral' ),
		'sextile'     => array( 'angle' => 60, 'orb' => 4, 'name' => 'Altmışlık', 'symbol' => '⚹', 'tone' => 'kolaylaştırır', 'kind' => 'soft' ),
		'square'      => array( 'angle' => 90, 'orb' => 7, 'name' => 'Kare', 'symbol' => '□', 'tone' => 'zorlar', 'kind' => 'hard' ),
		'trine'       => array( 'angle' => 120, 'orb' => 7, 'name' => 'Üçgen', 'symbol' => '△', 'tone' => 'akıtır', 'kind' => 'soft' ),
		'opposition'  => array( 'angle' => 180, 'orb' => 8, 'name' => 'Karşıt', 'symbol' => '☍', 'tone' => 'dengeletir', 'kind' => 'hard' ),
	);

	/** Life areas of the twelve houses (short). */
	public const HOUSES = array(
		1  => 'benlik ve görünüş',
		2  => 'kaynaklar ve değerler',
		3  => 'iletişim ve yakın çevre',
		4  => 'ev, aile ve kökler',
		5  => 'yaratıcılık, keyif ve aşk',
		6  => 'gündelik düzen ve beden',
		7  => 'ilişkiler ve ortaklıklar',
		8  => 'paylaşılanlar ve dönüşüm',
		9  => 'inançlar, yolculuk ve öğrenme',
		10 => 'kariyer ve görünürlük',
		11 => 'arkadaşlıklar ve gelecek',
		12 => 'iç dünya ve dinlenme',
	);

	public static function index_of( float $lon ): int {
		return (int) floor( Ephemeris::norm( $lon ) / 30.0 ) % 12;
	}

	public static function sign( float $lon ): array {
		return self::SIGNS[ self::index_of( $lon ) ];
	}

	public static function by_slug( string $slug ): ?array {
		foreach ( self::SIGNS as $i => $sign ) {
			if ( $sign['slug'] === $slug ) {
				return $sign + array( 'index' => $i );
			}
		}
		return null;
	}

	public static function slugs(): array {
		return array_column( self::SIGNS, 'slug' );
	}

	/** Degree within sign as "7°21′". */
	public static function degree( float $lon ): string {
		$in  = fmod( Ephemeris::norm( $lon ), 30.0 );
		$deg = (int) floor( $in );
		$min = (int) floor( ( $in - $deg ) * 60 );
		return sprintf( '%d°%02d′', $deg, $min );
	}

	/** Turkish date range such as "21 Mart – 19 Nisan". */
	public static function date_range( array $sign ): string {
		return self::md( $sign['from'] ) . ' – ' . self::md( $sign['to'] );
	}

	public const MONTHS = array( 1 => 'Ocak', 'Şubat', 'Mart', 'Nisan', 'Mayıs', 'Haziran', 'Temmuz', 'Ağustos', 'Eylül', 'Ekim', 'Kasım', 'Aralık' );
	public const WEEKDAYS = array( 'Pazar', 'Pazartesi', 'Salı', 'Çarşamba', 'Perşembe', 'Cuma', 'Cumartesi' );
	public const WEEKDAYS_SHORT = array( 'Paz', 'Pzt', 'Sal', 'Çar', 'Per', 'Cum', 'Cmt' );

	private static function md( string $md ): string {
		[ $m, $d ] = array_map( 'intval', explode( '-', $md ) );
		return $d . ' ' . self::MONTHS[ $m ];
	}

	/** Sun sign for a month/day, using the conventional date ranges. */
	public static function sign_for_date( int $month, int $day ): array {
		$md = sprintf( '%02d-%02d', $month, $day );
		foreach ( self::SIGNS as $i => $sign ) {
			$in = $sign['from'] <= $sign['to']
				? ( $md >= $sign['from'] && $md <= $sign['to'] )
				: ( $md >= $sign['from'] || $md <= $sign['to'] );
			if ( $in ) {
				return $sign + array( 'index' => $i );
			}
		}
		return self::SIGNS[0] + array( 'index' => 0 );
	}
}
