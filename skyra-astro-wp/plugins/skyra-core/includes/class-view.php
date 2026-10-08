<?php
/**
 * Small markup helpers shared by the block render templates.
 *
 * @package Skyra\Core
 */

namespace Skyra\Core;

use Skyra\Astro\Zodiac;

defined( 'ABSPATH' ) || exit;

final class View {

	/**
	 * Section header: eyebrow, heading, intro, optional meta line and link.
	 *
	 * @param array $o eyebrow, heading, intro, meta, id, level, link => [url, label], class.
	 */
	public static function head( array $o ): string {
		$level = (int) ( $o['level'] ?? 2 );
		$out   = '<header class="sk-head ' . esc_attr( $o['class'] ?? '' ) . '"><div class="sk-head__text">';
		if ( ! empty( $o['eyebrow'] ) ) {
			$out .= '<p class="sk-eyebrow">' . esc_html( $o['eyebrow'] ) . '</p>';
		}
		if ( ! empty( $o['heading'] ) ) {
			$out .= sprintf( '<h%1$d class="sk-head__title"%2$s>%3$s</h%1$d>', $level, ! empty( $o['id'] ) ? ' id="' . esc_attr( $o['id'] ) . '"' : '', esc_html( $o['heading'] ) );
		}
		if ( ! empty( $o['intro'] ) ) {
			$out .= '<p class="sk-head__intro">' . esc_html( $o['intro'] ) . '</p>';
		}
		if ( ! empty( $o['meta'] ) ) {
			$out .= '<p class="sk-head__meta">' . $o['meta'] . '</p>';
		}
		$out .= '</div>';
		if ( ! empty( $o['link'] ) ) {
			$out .= self::arrow_link( $o['link'][0], $o['link'][1], 'sk-head__link' );
		}
		return $out . '</header>';
	}

	public static function arrow_link( string $url, string $label, string $class = '', string $icon = 'arrow-right' ): string {
		return sprintf( '<a class="sk-link %s" href="%s"><span>%s</span>%s</a>', esc_attr( $class ), esc_url( $url ), esc_html( $label ), Icons::svg( $icon, array( 'size' => 18 ) ) );
	}

	public static function button( string $url, string $label, string $variant = 'primary', string $icon = 'arrow-right', string $extra = '' ): string {
		return sprintf( '<a class="sk-btn sk-btn--%s" href="%s"%s><span>%s</span>%s</a>', esc_attr( $variant ), esc_url( $url ), $extra, esc_html( $label ), $icon ? Icons::svg( $icon, array( 'size' => 18 ) ) : '' );
	}

	/** Zodiac glyph in a round badge. */
	public static function sign_badge( string $slug, string $size = 'md' ): string {
		$sign = Zodiac::by_slug( $slug );
		return sprintf( '<span class="sk-badge sk-badge--%s sk-el-%s">%s</span>', esc_attr( $size ), esc_attr( $sign['element'] ?? '' ), Icons::zodiac( $slug, array( 'size' => 'lg' === $size ? 32 : 22 ) ) );
	}

	public static function planet_badge( string $key, string $size = 'md' ): string {
		return sprintf( '<span class="sk-badge sk-badge--%s sk-badge--planet">%s</span>', esc_attr( $size ), Icons::planet( $key, array( 'size' => 'lg' === $size ? 32 : 22 ) ) );
	}

	/** "Veri" / "Yorum" provenance tag. */
	public static function tag( string $kind ): string {
		$labels = array(
			'data'      => 'Veri',
			'reading'   => 'Yorum',
			'editorial' => 'Editör yorumu',
			'computed'  => 'Gökyüzü okuması',
		);
		return sprintf( '<span class="sk-tag sk-tag--%s">%s</span>', esc_attr( $kind ), esc_html( $labels[ $kind ] ?? $kind ) );
	}

	public static function sign_name( string $slug ): string {
		return Zodiac::by_slug( $slug )['name'] ?? '';
	}

	/**
	 * Moon disc with the lit part for an illumination fraction.
	 * Northern-hemisphere view: waxing lit on the right.
	 */
	public static function moon( float $illum, bool $waxing, int $size = 120, string $class = '' ): string {
		return sprintf(
			'<svg class="sk-moon %s" width="%d" height="%d" viewBox="-50 -50 100 100" aria-hidden="true" focusable="false" data-illum="%.4f" data-waxing="%d"><circle class="sk-moon__dark" r="48"/><path class="sk-moon__lit" d="%s"/><circle class="sk-moon__rim" r="48"/></svg>',
			esc_attr( $class ),
			$size,
			$size,
			$illum,
			$waxing ? 1 : 0,
			self::moon_path( $illum, $waxing, 48 )
		);
	}

	public static function moon_path( float $k, bool $waxing, float $r ): string {
		$k  = max( 0.0, min( 1.0, $k ) );
		$rx = $r * abs( 1 - 2 * $k );
		if ( $waxing ) {
			$limb  = sprintf( 'M0 %1$.2f A%2$.2f %2$.2f 0 0 1 0 %3$.2f', -$r, $r, $r );
			$sweep = $k < 0.5 ? 0 : 1;
		} else {
			$limb  = sprintf( 'M0 %1$.2f A%2$.2f %2$.2f 0 0 0 0 %3$.2f', -$r, $r, $r );
			$sweep = $k < 0.5 ? 1 : 0;
		}
		return sprintf( '%s A%.2f %.2f 0 0 %d 0 %.2f Z', $limb, $rx, $r, $sweep, -$r );
	}

	public const EVENT_TYPES = array(
		'new_moon'       => array( 'Yeni Ay', 'planet-moon' ),
		'full_moon'      => array( 'Dolunay', 'planet-moon' ),
		'solar_eclipse'  => array( 'Güneş tutulması', 'planet-sun' ),
		'lunar_eclipse'  => array( 'Ay tutulması', 'planet-moon' ),
		'station_retro'  => array( 'Retro başlıyor', 'retro' ),
		'station_direct' => array( 'Retro bitiyor', 'arrow-right' ),
		'ingress'        => array( 'Burç geçişi', 'transit' ),
	);

	/** Timeline / list card for a computed event. */
	public static function event_card( array $e, string $heading_tag = 'h3' ): string {
		[ $label, $icon ] = self::EVENT_TYPES[ $e['type'] ];
		$note             = Data::event_note( $e );
		$icon_html        = 'ingress' === $e['type'] || str_starts_with( $e['type'], 'station' ) ? Icons::planet( $e['body'], array( 'size' => 20 ) ) : Icons::svg( $icon, array( 'size' => 20 ) );
		if ( 'full_moon' === $e['type'] || 'lunar_eclipse' === $e['type'] ) {
			$icon_html = '<span class="sk-dot-moon is-full"></span>';
		} elseif ( 'new_moon' === $e['type'] || 'solar_eclipse' === $e['type'] ) {
			$icon_html = '<span class="sk-dot-moon is-new"></span>';
		}
		$out  = sprintf( '<article class="sk-event sk-event--%s" data-type="%s">', esc_attr( str_replace( '_', '-', $e['type'] ) ), esc_attr( $e['type'] ) );
		$out .= sprintf(
			'<time class="sk-event__date" datetime="%s"><span class="sk-event__day">%s</span><span class="sk-event__month">%s</span><span class="sk-event__weekday">%s</span></time>',
			esc_attr( gmdate( 'c', $e['unix'] ) ),
			esc_html( Data::format( $e['unix'], 'j' ) ),
			esc_html( Data::format( $e['unix'], 'M' ) ),
			esc_html( Data::format( $e['unix'], 'D' ) )
		);
		$out .= '<span class="sk-event__icon" aria-hidden="true">' . $icon_html . '</span>';
		$out .= '<div class="sk-event__body">';
		$out .= sprintf( '<p class="sk-event__type">%s</p>', esc_html( $label ) );
		$out .= sprintf( '<%1$s class="sk-event__title">%2$s</%1$s>', $heading_tag, esc_html( $e['title'] ) );
		$out .= sprintf(
			'<p class="sk-event__meta">%s · %s %s · %s</p>',
			esc_html( Data::format( $e['unix'], 'j F Y' ) ),
			esc_html( self::sign_name( $e['sign'] ) ),
			esc_html( $e['degree'] ),
			esc_html( Data::format( $e['unix'], 'H:i' ) . ' TSİ' )
		);
		$out .= sprintf( '<p class="sk-event__summary">%s</p>', esc_html( $e['summary'] ) );
		if ( $note ) {
			$out .= self::arrow_link( $note['url'], 'Editör notu: ' . $note['title'], 'sk-event__note' );
		}
		return $out . '</div></article>';
	}

	/** Featured image, or a generated cover in the brand palette. */
	public static function cover( \WP_Post $post, string $ratio = '4-3', string $size = 'medium_large', bool $eager = false ): string {
		if ( has_post_thumbnail( $post ) ) {
			return sprintf(
				'<div class="sk-cover sk-cover--%s">%s</div>',
				esc_attr( $ratio ),
				get_the_post_thumbnail(
					$post,
					$size,
					array(
						'loading'       => $eager ? 'eager' : 'lazy',
						'fetchpriority' => $eager ? 'high' : 'auto',
						'sizes'         => '(min-width: 1024px) 50vw, 100vw',
					)
				)
			);
		}
		$cats  = get_the_category( $post->ID );
		$slug  = $cats ? $cats[0]->slug : 'genel';
		$motif = array(
			'astroloji-101'      => 'orbit',
			'gezegenler'         => 'planet',
			'burclar'            => 'glyph',
			'iliskiler'          => 'pair',
			'gokyuzu-gundemi'    => 'phases',
			'astrolojik-olaylar' => 'eclipse',
		)[ $slug ] ?? 'orbit';
		$tone  = array( 'dawn', 'lunar', 'night', 'sand' )[ $post->ID % 4 ];
		$svg   = array(
			'orbit'   => '<ellipse cx="200" cy="150" rx="150" ry="58" transform="rotate(-18 200 150)"/><ellipse cx="200" cy="150" rx="96" ry="96"/><circle cx="200" cy="150" r="9" class="f"/><circle cx="330" cy="104" r="6" class="f a"/>',
			'planet'  => '<circle cx="200" cy="150" r="70" class="s"/><ellipse cx="200" cy="150" rx="130" ry="26" transform="rotate(-14 200 150)"/><circle cx="92" cy="70" r="3" class="f"/><circle cx="318" cy="228" r="2" class="f"/>',
			'glyph'   => '<g transform="translate(140 90) scale(5)" stroke-width=".35">' . Icons::PATHS[ 'zodiac-' . Zodiac::SIGNS[ $post->ID % 12 ]['slug'] ] . '</g>',
			'pair'    => '<circle cx="165" cy="150" r="72"/><circle cx="235" cy="150" r="72"/><circle cx="200" cy="150" r="4" class="f a"/>',
			'phases'  => '<circle cx="80" cy="150" r="26"/><circle cx="140" cy="150" r="26"/><circle cx="200" cy="150" r="26" class="s"/><circle cx="260" cy="150" r="26"/><circle cx="320" cy="150" r="26"/>',
			'eclipse' => '<circle cx="200" cy="150" r="80" class="s"/><circle cx="228" cy="138" r="78" class="k"/><circle cx="96" cy="64" r="2.5" class="f"/>',
		)[ $motif ];
		return sprintf(
			'<div class="sk-cover sk-cover--%1$s sk-cover--art is-%2$s" aria-hidden="true"><svg viewBox="0 0 400 300" preserveAspectRatio="xMidYMid slice" fill="none" stroke="currentColor" stroke-width="1.2">%3$s</svg></div>',
			esc_attr( $ratio ),
			esc_attr( $tone ),
			$svg
		);
	}

	public static function reading_time( \WP_Post $post ): int {
		$words = count( preg_split( '/\s+/u', trim( wp_strip_all_tags( $post->post_content ) ), -1, PREG_SPLIT_NO_EMPTY ) );
		return max( 1, (int) round( $words / 200 ) );
	}

	/**
	 * Birth data form shared by the chart, rising-sign and moon-sign tools.
	 *
	 * Works without JavaScript (plain POST to the tool page); tools.js adds the
	 * place combobox, the step-by-step mode and in-page results.
	 *
	 * @param array $o tool (chart|moon-sign), variant (full|compact|rising), action, submit,
	 *                 time_required, stepper, level, values.
	 */
	public static function birth_form( array $o ): string {
		$id       = self::uid( 'f' );
		$v        = $o['values'] ?? array();
		$required = ! empty( $o['time_required'] );
		$stepper  = ! empty( $o['stepper'] );
		$unknown  = ! empty( $v['time_unknown'] );
		$field    = static function ( string $name, string $label, string $input, string $help, bool $req = true ) use ( $id ) {
			return sprintf(
				'<div class="sk-field" data-field="%1$s"><label for="%2$s-%1$s">%3$s <span class="sk-req">%4$s</span></label>%5$s<p class="sk-help" id="%2$s-%1$s-help">%6$s</p><p class="sk-error" id="%2$s-%1$s-err" hidden></p></div>',
				esc_attr( $name ),
				esc_attr( $id ),
				esc_html( $label ),
				$req ? 'gerekli' : 'isteğe bağlı',
				$input,
				esc_html( $help )
			);
		};
		$describe = static fn( string $name ) => sprintf( 'aria-describedby="%1$s-%2$s-help %1$s-%2$s-err"', esc_attr( $id ), esc_attr( $name ) );
		$nav      = static function ( bool $back, bool $next ) {
			$out = '<div class="sk-step__nav" data-step-nav hidden>';
			if ( $back ) {
				$out .= '<button type="button" class="sk-btn sk-btn--ghost" data-prev>' . Icons::svg( 'arrow-left', array( 'size' => 18 ) ) . '<span>Geri</span></button>';
			}
			if ( $next ) {
				$out .= '<button type="button" class="sk-btn sk-btn--primary" data-next><span>Devam</span>' . Icons::svg( 'arrow-right', array( 'size' => 18 ) ) . '</button>';
			}
			return $out . '</div>';
		};

		$date = sprintf(
			'<input id="%1$s-date" type="date" name="date" required min="1900-01-01" max="2100-12-31" value="%2$s" autocomplete="bday" %3$s>',
			esc_attr( $id ),
			esc_attr( $v['date'] ?? '' ),
			$describe( 'date' )
		);
		$time = sprintf(
			'<input id="%1$s-time" type="time" name="time" step="60" value="%2$s"%3$s%4$s %5$s>',
			esc_attr( $id ),
			esc_attr( $v['time'] ?? '' ),
			$required ? ' required' : '',
			$unknown ? ' disabled' : '',
			$describe( 'time' )
		);
		if ( ! $required ) {
			$time .= sprintf(
				'<label class="sk-check"><input type="checkbox" name="time_unknown" value="1" data-time-unknown%s><span>Saatimi bilmiyorum</span></label>',
				checked( $unknown, true, false )
			);
		}
		$place = sprintf(
			'<div class="sk-combo" data-combo><input id="%1$s-place" type="text" name="place_q" value="%2$s" required role="combobox" aria-autocomplete="list" aria-expanded="false" aria-controls="%1$s-list" autocomplete="off" spellcheck="false" %4$s><input type="hidden" name="place" value="%3$s"><ul class="sk-combo__list" id="%1$s-list" role="listbox" aria-label="Eşleşen yerler" hidden></ul></div>',
			esc_attr( $id ),
			esc_attr( $v['place_q'] ?? '' ),
			esc_attr( $v['place'] ?? '' ),
			$describe( 'place' )
		);

		$help_time = $required
			? 'Yükselen burç yaklaşık iki saatte bir değişir; kayıttaki saat en güvenilir kaynaktır.'
			: 'Bilmiyorsan işaretle: yükselen ve evler gösterilmez.';

		$out  = sprintf(
			'<form class="sk-form%s" method="post" action="%s" data-tool="%s" data-variant="%s" data-level="%d" novalidate%s>',
			$stepper ? ' is-stepper' : '',
			esc_url( $o['action'] . '#sonuc' ),
			esc_attr( $o['tool'] ),
			esc_attr( $o['variant'] ?? 'full' ),
			(int) ( $o['level'] ?? 2 ),
			$stepper ? ' data-stepper' : ''
		);
		$out .= sprintf( '<input type="hidden" name="skyra_tool" value="%s"><input type="hidden" name="variant" value="%s">', esc_attr( $o['tool'] ), esc_attr( $o['variant'] ?? 'full' ) );
		if ( $stepper ) {
			$out .= '<div class="sk-steps" data-steps hidden><p class="sk-steps__label">Adım <span data-step-no>1</span> / 3</p><ol class="sk-steps__dots" aria-hidden="true"><li class="is-active"></li><li></li><li></li></ol></div>';
		}
		$out .= '<div class="sk-step" data-step="1">' . $field( 'date', 'Doğum tarihi', $date, 'Gün, ay ve yıl olarak.' ) . ( $stepper ? $nav( false, true ) : '' ) . '</div>';
		$out .= '<div class="sk-step" data-step="2">' . $field( 'time', 'Doğum saati', $time, $help_time, $required ) . ( $stepper ? $nav( true, true ) : '' ) . '</div>';
		$out .= '<div class="sk-step" data-step="3">' . $field( 'place', 'Doğum yeri', $place, 'Şehir ya da ilçe adını yaz ve listeden seç. İlçen yoksa bağlı olduğu ili seçebilirsin.' );
		$out .= '<div class="sk-step__nav sk-step__nav--final">' . ( $stepper ? '<button type="button" class="sk-btn sk-btn--ghost" data-prev hidden>' . Icons::svg( 'arrow-left', array( 'size' => 18 ) ) . '<span>Geri</span></button>' : '' );
		$out .= sprintf( '<button type="submit" class="sk-btn sk-btn--primary sk-btn--lg" data-submit><span>%s</span>%s</button></div></div>', esc_html( $o['submit'] ), Icons::svg( 'arrow-right', array( 'size' => 18 ) ) );
		$out .= '<p class="sk-privacy">' . Icons::svg( 'info', array( 'size' => 16 ) ) . '<span>Doğum bilgilerin yalnızca bu hesaplama için kullanılır; saklanmaz ve adres çubuğuna yazılmaz. Konum verisi: GeoNames (CC BY 4.0).</span></p>';
		$out .= '<p class="sk-status" role="status" aria-live="polite" data-status></p>';
		return $out . '</form>';
	}

	/** Unique, stable-ish id for form controls inside a block instance. */
	public static function uid( string $prefix ): string {
		return wp_unique_id( 'sk-' . $prefix . '-' );
	}
}
