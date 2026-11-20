<?php
/**
 * Server-rendered result panels for the astrology tools.
 *
 * The same markup is returned by REST (JavaScript path) and printed after a
 * plain form POST (no-JS path), so there is a single source of truth.
 *
 * @package Skyra\Core
 */

namespace Skyra\Core;

use Skyra\Astro\Readings;
use Skyra\Astro\Zodiac;

defined( 'ABSPATH' ) || exit;

final class Results {

	private const WARNINGS = array(
		'time_unknown'        => 'Doğum saatini girmediğin için yükselen burç ve evler hesaplanmadı. Gezegenler gün ortasına (12:00) göre gösteriliyor.',
		'moon_sign_uncertain' => 'Ay doğduğun gün burç değiştirdi. Doğum saatin olmadan Ay burcun kesinleşmiyor; Ay Burcu aracında iki olasılığı da görebilirsin.',
		'asc_near_cusp'       => 'Yükselenin burç sınırına çok yakın. Doğum saatindeki birkaç dakikalık fark yükseleni komşu burca taşıyabilir.',
		'polar_houses'        => 'Kutup dairesine yakın enlemlerde Placidus evleri tanımsız olduğu için Porphyry ev sistemi kullanıldı.',
	);

	/** Full natal chart panel. */
	public static function chart( array $c, int $level = 2 ): string {
		$h    = 'h' . $level;
		$h2   = 'h' . min( 6, $level + 1 );
		$big  = $c['big3'];
		$name = static fn( $slug ) => Zodiac::by_slug( $slug )['name'];

		$title = sprintf( 'Güneş %s, Ay %s', $name( $big['sun']['sign'] ), $name( $big['moon']['sign'] ) );
		if ( $big['rising'] ) {
			$title .= ', Yükselen ' . $name( $big['rising']['sign'] );
		}

		$o   = array();
		$o[] = '<div class="sk-result" data-result tabindex="-1">';
		$o[] = '<header class="sk-result__head"><p class="sk-eyebrow">Doğum haritan</p>';
		$o[] = sprintf( '<%1$s class="sk-result__title">%2$s</%1$s>', $h, esc_html( $title ) );
		$o[] = '<p class="sk-result__meta">' . esc_html( self::input_line( $c ) ) . '</p>';
		$o[] = self::warnings( $c['warnings'] ) . '</header>';

		$o[] = '<div class="sk-big3">';
		foreach ( array(
			'sun'    => 'Güneş',
			'moon'   => 'Ay',
			'rising' => 'Yükselen',
		) as $key => $label ) {
			if ( ! $big[ $key ] ) {
				$o[] = '<div class="sk-big3__item is-empty"><p class="sk-label">Yükselen</p><p class="sk-big3__sign">Saat gerekli</p><p class="sk-big3__text">Yükselen burç doğum saatine bağlı; yaklaşık her iki saatte bir değişir.</p></div>';
				continue;
			}
			$deg = 'rising' === $key ? $c['angles']['asc']['degree'] : $c['bodies'][ $key ]['degree'];
			$o[] = sprintf(
				'<div class="sk-big3__item">%s<p class="sk-label">%s</p><p class="sk-big3__sign">%s <span>%s</span></p><p class="sk-big3__text">%s</p></div>',
				View::sign_badge( $big[ $key ]['sign'], 'lg' ),
				esc_html( $label ),
				esc_html( $name( $big[ $key ]['sign'] ) ),
				esc_html( $deg ),
				esc_html( $big[ $key ]['text'] )
			);
		}
		$o[] = '</div>';

		$o[] = '<div class="sk-result__chart"><figure class="sk-chart-figure" data-draw>' . $c['svg'];
		$o[] = '<figcaption class="sk-legend"><span class="sk-legend__item is-soft">Uyumlu açı (üçgen, altmışlık)</span><span class="sk-legend__item is-hard">Gerilimli açı (kare, karşıt)</span></figcaption></figure>';
		$o[] = self::positions_table( $c );
		$o[] = '</div>';

		$o[] = sprintf( '<section class="sk-result__section"><%1$s>Yerleşimler</%1$s><ul class="sk-placements">', $h2 );
		foreach ( $c['bodies'] as $key => $b ) {
			$o[] = sprintf(
				'<li>%s<div><p class="sk-placements__title">%s %s%s</p><p>%s</p></div></li>',
				View::planet_badge( $key ),
				esc_html( $b['name'] ),
				esc_html( Zodiac::by_slug( $b['sign'] )['loc'] ),
				$b['house'] ? ' · ' . (int) $b['house'] . '. ev' : '',
				esc_html( $b['text'] )
			);
		}
		$o[] = '</ul></section>';

		if ( $c['aspects'] ) {
			$o[] = sprintf( '<section class="sk-result__section"><%1$s>Öne çıkan açılar</%1$s><ul class="sk-aspect-list">', $h2 );
			foreach ( array_slice( $c['aspects'], 0, 8 ) as $a ) {
				$asp = Zodiac::ASPECTS[ $a['type'] ];
				$o[] = sprintf(
					'<li class="is-%s"><p class="sk-aspect-list__title">%s %s %s %s <span>orb %s°</span></p><p>%s</p></li>',
					esc_attr( $asp['kind'] ),
					esc_html( Zodiac::PLANETS[ $a['a'] ]['name'] ),
					Icons::svg( 'aspect-' . $a['type'], array( 'size' => 16, 'title' => $asp['name'] ) ),
					esc_html( Zodiac::PLANETS[ $a['b'] ]['name'] ),
					'<span class="sk-aspect-list__name">' . esc_html( $asp['name'] ) . '</span>',
					esc_html( number_format( $a['orb'], 1, ',', '' ) ),
					esc_html( $a['text'] )
				);
			}
			$o[] = '</ul></section>';
		}

		$o[] = sprintf( '<section class="sk-result__section"><%1$s>Element ve nitelik dengesi</%1$s>', $h2 );
		$o[] = self::balance( $c['balance'] ) . '</section>';

		$o[] = '<footer class="sk-result__foot"><p class="sk-method">' . esc_html( self::method_note( $c ) ) . '</p>';
		$o[] = '<button type="button" class="sk-btn sk-btn--secondary" data-reset>Yeni harita oluştur</button></footer>';
		$o[] = '</div>';
		return implode( '', $o );
	}

	/** Compact result for the home page section. */
	public static function chart_compact( array $c ): string {
		$big = $c['big3'];
		$o   = array( '<div class="sk-result sk-result--compact" data-result tabindex="-1">' );
		$o[] = '<p class="sk-eyebrow">Haritanın özeti</p>';
		$o[] = '<div class="sk-result__compact-grid"><figure class="sk-chart-figure" data-draw>' . $c['svg'] . '</figure><div>';
		$o[] = '<dl class="sk-mini3">';
		foreach ( array(
			'sun'    => 'Güneş',
			'moon'   => 'Ay',
			'rising' => 'Yükselen',
		) as $key => $label ) {
			$o[] = sprintf(
				'<div><dt>%s</dt><dd>%s</dd></div>',
				esc_html( $label ),
				$big[ $key ] ? View::sign_badge( $big[ $key ]['sign'], 'sm' ) . esc_html( Zodiac::by_slug( $big[ $key ]['sign'] )['name'] ) : 'Saat gerekli'
			);
		}
		$o[] = '</dl>' . self::warnings( $c['warnings'] );
		$o[] = '<p class="sk-result__meta">' . esc_html( self::input_line( $c ) ) . '</p>';
		$o[] = '<div class="sk-actions"><a class="sk-btn sk-btn--primary" data-handoff href="' . esc_url( Data::page_url( 'dogum-haritasi' ) ) . '"><span>Haritanın tamamını aç</span>' . Icons::svg( 'arrow-right', array( 'size' => 18 ) ) . '</a>';
		$o[] = '<button type="button" class="sk-btn sk-btn--ghost" data-reset>Yeniden başla</button></div>';
		$o[] = '</div></div></div>';
		return implode( '', $o );
	}

	/** Rising-sign tool result. */
	public static function rising( array $c, int $level = 2 ): string {
		if ( ! $c['angles'] ) {
			return self::notice( 'Yükselen burç için doğum saati gerekli.' );
		}
		$asc  = $c['angles']['asc'];
		$sign = Zodiac::by_slug( $asc['sign'] );
		$o    = array( '<div class="sk-result sk-result--single" data-result tabindex="-1">' );
		$o[]  = '<div class="sk-hero-result">' . View::sign_badge( $asc['sign'], 'lg' );
		$o[]  = '<div><p class="sk-eyebrow">Yükselen burcun</p>';
		$o[]  = sprintf( '<h%1$d class="sk-result__title">%2$s <span class="sk-muted">%3$s</span></h%1$d>', $level, esc_html( $sign['name'] ), esc_html( $asc['degree'] ) );
		$o[]  = '<p class="sk-lead">' . esc_html( Readings::RISING[ $asc['sign'] ] ) . '</p></div></div>';
		$o[]  = self::warnings( array_intersect( $c['warnings'], array( 'asc_near_cusp', 'polar_houses' ) ) );
		$o[]  = sprintf(
			'<dl class="sk-facts"><div><dt>Güneş</dt><dd>%s %s</dd></div><div><dt>Ay</dt><dd>%s %s</dd></div><div><dt>MC (tepe noktası)</dt><dd>%s %s</dd></div></dl>',
			esc_html( View::sign_name( $c['bodies']['sun']['sign'] ) ),
			esc_html( $c['bodies']['sun']['degree'] ),
			esc_html( View::sign_name( $c['bodies']['moon']['sign'] ) ),
			esc_html( $c['bodies']['moon']['degree'] ),
			esc_html( View::sign_name( $c['angles']['mc']['sign'] ) ),
			esc_html( $c['angles']['mc']['degree'] )
		);
		$o[] = '<p class="sk-result__meta">' . esc_html( self::input_line( $c ) ) . '</p>';
		$o[] = '<div class="sk-actions"><a class="sk-btn sk-btn--primary" data-handoff href="' . esc_url( Data::page_url( 'dogum-haritasi' ) ) . '"><span>Tüm doğum haritanı gör</span>' . Icons::svg( 'arrow-right', array( 'size' => 18 ) ) . '</a>';
		$o[] = '<a class="sk-btn sk-btn--secondary" href="' . esc_url( Data::sign_url( $asc['sign'] ) ) . '">' . esc_html( $sign['name'] ) . ' burcunu oku</a></div>';
		$o[] = '<p class="sk-method">' . esc_html( self::method_note( $c ) ) . '</p></div>';
		return implode( '', $o );
	}

	/** Moon-sign tool result, including the "changed sign that day" case. */
	public static function moon_sign( array $m, int $level = 2 ): string {
		$o = array( '<div class="sk-result sk-result--single" data-result tabindex="-1">' );
		if ( $m['certain'] ) {
			$sign = Zodiac::by_slug( $m['sign'] );
			$o[]  = '<div class="sk-hero-result">' . View::sign_badge( $m['sign'], 'lg' ) . '<div><p class="sk-eyebrow">Ay burcun</p>';
			$o[]  = sprintf( '<h%1$d class="sk-result__title">%2$s%3$s</h%1$d>', $level, esc_html( $sign['name'] ), $m['degree'] ? ' <span class="sk-muted">' . esc_html( $m['degree'] ) . '</span>' : '' );
			$o[]  = '<p class="sk-lead">' . esc_html( $m['texts'][ $m['sign'] ] ) . '</p></div></div>';
			if ( ! $m['degree'] ) {
				$o[] = self::notice( 'Doğum saatini girmedin; Ay o gün boyunca aynı burçta kaldığı için sonuç yine de kesin.' );
			}
		} else {
			[ $first, $second ] = $m['day']['signs'];
			$o[] = '<p class="sk-eyebrow">Ay burcun</p>';
			$o[] = sprintf( '<h%1$d class="sk-result__title">Ay doğduğun gün burç değiştirdi</h%1$d>', $level );
			$o[] = '<p class="sk-lead">' . esc_html( sprintf( 'Ay saat %s civarında %s %s geçti. Doğum saatine göre Ay burcun ikisinden biri:', $m['day']['ingress'], Zodiac::by_slug( $first )['abl'], Zodiac::by_slug( $second )['dat'] ) ) . '</p>';
			$o[] = '<div class="sk-split">';
			foreach ( array( $first => 'Saat ' . $m['day']['ingress'] . ' öncesi', $second => 'Saat ' . $m['day']['ingress'] . ' sonrası' ) as $slug => $when ) {
				$o[] = sprintf( '<div class="sk-card">%s<p class="sk-label">%s</p><p class="sk-big3__sign">%s</p><p>%s</p></div>', View::sign_badge( $slug ), esc_html( $when ), esc_html( View::sign_name( $slug ) ), esc_html( $m['texts'][ $slug ] ) );
			}
			$o[] = '</div>';
		}
		$o[] = '<p class="sk-result__meta">' . esc_html( $m['place'] ) . '</p>';
		$o[] = '<p class="sk-method">Ay yaklaşık iki buçuk günde bir burç değiştirir. Hesaplama geosentrik, tropikal zodyaka göre yapılır; doğum yerinin tarihsel saat dilimi dikkate alınır.</p></div>';
		return implode( '', $o );
	}

	/** Sign compatibility result. */
	public static function compatibility( array $r, int $level = 2 ): string {
		$a  = Zodiac::by_slug( $r['a'] );
		$b  = Zodiac::by_slug( $r['b'] );
		$o  = array( '<div class="sk-result sk-result--single sk-compat-result" data-result tabindex="-1">' );
		$o[] = '<div class="sk-compat-pair">' . View::sign_badge( $r['a'], 'lg' ) . sprintf( '<span class="sk-compat-angle"><span>%d°</span>%s</span>', (int) $r['angle'], $r['aspect'] ? esc_html( Zodiac::ASPECTS[ $r['aspect'] ]['name'] ) : '' ) . View::sign_badge( $r['b'], 'lg' ) . '</div>';
		$o[] = sprintf( '<p class="sk-eyebrow">%s ve %s</p>', esc_html( $a['name'] ), esc_html( $b['name'] ) );
		$o[] = sprintf( '<h%1$d class="sk-result__title">%2$s</h%1$d>', $level, esc_html( $r['title'] ) );
		$o[] = '<p class="sk-lead">' . esc_html( $r['text'] ) . '</p>';
		$o[] = '<ul class="sk-notes"><li>' . Icons::svg( 'element-' . $a['element'], array( 'size' => 18 ) ) . esc_html( $r['elements'] ) . '</li>';
		if ( $r['modality'] ) {
			$o[] = '<li>' . Icons::svg( 'orbit', array( 'size' => 18 ) ) . esc_html( $r['modality'] ) . '</li>';
		}
		$o[] = '</ul><p class="sk-method">Bu okuma iki Güneş burcu arasındaki geleneksel açı ve element ilişkisine dayanır. Daha kişisel bir karşılaştırma için iki doğum haritasındaki Ay, Venüs ve Mars yerleşimlerine de bakmak gerekir.</p></div>';
		return implode( '', $o );
	}

	public static function notice( string $text, string $kind = 'info' ): string {
		return sprintf( '<p class="sk-notice sk-notice--%s">%s<span>%s</span></p>', esc_attr( $kind ), Icons::svg( 'alert' === $kind ? 'alert' : 'info', array( 'size' => 18 ) ), esc_html( $text ) );
	}

	private static function warnings( array $codes ): string {
		$out = '';
		foreach ( $codes as $code ) {
			if ( isset( self::WARNINGS[ $code ] ) ) {
				$out .= self::notice( self::WARNINGS[ $code ] );
			}
		}
		return $out;
	}

	private static function input_line( array $c ): string {
		$in   = $c['input'];
		$date = Data::format_date( substr( $in['local'], 0, 10 ), 'j F Y' );
		$time = $in['time_known'] ? substr( $in['local'], 11 ) . ' (UTC' . $in['offset'] . ')' : 'saat bilinmiyor';
		return sprintf( '%s · %s · %s', $date, $time, $c['place'] ?? '' );
	}

	private static function method_note( array $c ): string {
		$houses = $c['houses'] ? ( 'placidus' === $c['houses']['system'] ? 'Placidus ev sistemi' : 'Porphyry ev sistemi' ) : 'ev hesabı yok';
		return 'Tropikal zodyak, geosentrik konumlar, ' . $houses . '. Konum hassasiyeti birkaç yay dakikasıdır. Doğum bilgilerin yalnızca bu hesaplama için kullanıldı ve saklanmadı.';
	}

	private static function positions_table( array $c ): string {
		$rows = '';
		foreach ( $c['bodies'] as $key => $b ) {
			$rows .= sprintf(
				'<tr><th scope="row">%s %s</th><td>%s</td><td>%s%s</td><td>%s</td></tr>',
				Icons::planet( $key, array( 'size' => 16 ) ),
				esc_html( $b['name'] ),
				esc_html( View::sign_name( $b['sign'] ) ),
				esc_html( $b['degree'] ),
				$b['retro'] ? ' <abbr title="Retro (geri hareket)">R</abbr>' : '',
				$b['house'] ? (int) $b['house'] : '—'
			);
		}
		if ( $c['angles'] ) {
			foreach ( array(
				'asc' => 'Yükselen (ASC)',
				'mc'  => 'Tepe noktası (MC)',
			) as $k => $label ) {
				$rows .= sprintf( '<tr><th scope="row">%s</th><td>%s</td><td>%s</td><td>%s</td></tr>', esc_html( $label ), esc_html( View::sign_name( $c['angles'][ $k ]['sign'] ) ), esc_html( $c['angles'][ $k ]['degree'] ), 'asc' === $k ? '1' : '10' );
			}
		}
		return '<div class="sk-table-wrap" tabindex="0" role="region" aria-label="Gezegen konumları tablosu"><table class="sk-table"><caption>Gezegen konumları</caption><thead><tr><th scope="col">Gezegen</th><th scope="col">Burç</th><th scope="col">Derece</th><th scope="col">Ev</th></tr></thead><tbody>' . $rows . '</tbody></table></div>';
	}

	/** Element and modality bars (also used by the daily energy block). */
	public static function balance( array $bal, bool $with_modalities = true ): string {
		$total = max( 1, (int) $bal['total'] );
		$o     = '<div class="sk-balance"><div class="sk-balance__bar" role="img" aria-label="' . esc_attr(
			implode(
				', ',
				array_map(
					static fn( $k, $v ) => Zodiac::ELEMENTS[ $k ]['name'] . ' ' . $v,
					array_keys( $bal['elements'] ),
					$bal['elements']
				)
			)
		) . '">';
		foreach ( $bal['elements'] as $el => $n ) {
			if ( $n > 0 ) {
				$o .= sprintf( '<span class="sk-el-%s" style="flex-grow:%d"></span>', esc_attr( $el ), (int) $n );
			}
		}
		$o .= '</div><ul class="sk-balance__legend">';
		foreach ( $bal['elements'] as $el => $n ) {
			$o .= sprintf( '<li class="sk-el-%s">%s<span>%s</span><strong>%d</strong></li>', esc_attr( $el ), Icons::svg( 'element-' . $el, array( 'size' => 16 ) ), esc_html( Zodiac::ELEMENTS[ $el ]['name'] ), (int) $n );
		}
		$o .= '</ul>';
		if ( $with_modalities ) {
			$o .= '<ul class="sk-balance__modes">';
			foreach ( $bal['modalities'] as $m => $n ) {
				$o .= sprintf( '<li><span>%s</span><strong>%d</strong><span class="sk-meter" style="--v:%.3f"></span></li>', esc_html( Zodiac::MODALITIES[ $m ] ), (int) $n, $n / $total );
			}
			$o .= '</ul>';
		}
		return $o . '</div>';
	}
}
