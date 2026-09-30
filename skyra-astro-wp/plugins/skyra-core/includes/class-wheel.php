<?php
/**
 * SVG chart wheel. One renderer serves the natal chart (via REST), the
 * transit wheel and the "sky right now" hero visual, so every wheel on the
 * site is real data drawn the same way.
 *
 * Geometry: viewBox 600 × 600, centre 300. The ascendant (or 0° Aries when
 * no birth time is known) sits at 9 o'clock; longitude runs counter-clockwise.
 *
 * @package Skyra\Core
 */

namespace Skyra\Core;

use Skyra\Astro\Ephemeris;
use Skyra\Astro\Zodiac;

defined( 'ABSPATH' ) || exit;

final class Wheel {

	private const C       = 300;
	private const R_OUT   = 292;
	private const R_SIGN  = 250;
	private const R_PLAN  = 212;
	private const R_DEG   = 184;
	private const R_ASP   = 150;

	/**
	 * @param array $bodies Keyed by planet with at least 'lon' and 'retro'.
	 * @param array $opts   asc, mc, cusps (1–12), aspects (list), id, title, desc, class.
	 */
	public static function svg( array $bodies, array $opts = array() ): string {
		$asc   = isset( $opts['asc'] ) ? (float) $opts['asc'] : 0.0;
		$id    = $opts['id'] ?? wp_unique_id( 'skyra-wheel-' );
		$title = $opts['title'] ?? 'Harita';
		$desc  = $opts['desc'] ?? '';

		$o   = array();
		$o[] = sprintf(
			'<svg class="skyra-wheel %s" viewBox="0 0 600 600" role="img" aria-labelledby="%s-t%s" xmlns="http://www.w3.org/2000/svg">',
			esc_attr( $opts['class'] ?? '' ),
			esc_attr( $id ),
			$desc ? ' ' . esc_attr( $id ) . '-d' : ''
		);
		$o[] = sprintf( '<title id="%s-t">%s</title>', esc_attr( $id ), esc_html( $title ) );
		if ( $desc ) {
			$o[] = sprintf( '<desc id="%s-d">%s</desc>', esc_attr( $id ), esc_html( $desc ) );
		}

		// Rings.
		$o[] = '<g class="wh-rings" fill="none">';
		foreach ( array( self::R_OUT, self::R_SIGN, self::R_ASP ) as $i => $r ) {
			$o[] = sprintf( '<circle class="wh-ring wh-draw" style="--i:%d" cx="300" cy="300" r="%d" pathLength="1"/>', $i, $r );
		}
		$o[] = '</g>';

		// Sign divisions, degree ticks and glyphs.
		$o[] = '<g class="wh-signs">';
		for ( $s = 0; $s < 12; $s++ ) {
			[ $x1, $y1 ] = self::xy( $s * 30, self::R_SIGN, $asc );
			[ $x2, $y2 ] = self::xy( $s * 30, self::R_OUT, $asc );
			$o[]         = sprintf( '<line class="wh-div" x1="%.1f" y1="%.1f" x2="%.1f" y2="%.1f"/>', $x1, $y1, $x2, $y2 );
			[ $gx, $gy ] = self::xy( $s * 30 + 15, ( self::R_SIGN + self::R_OUT ) / 2, $asc );
			$sign        = Zodiac::SIGNS[ $s ];
			$o[]         = sprintf(
				'<g class="wh-sign wh-el-%s" transform="translate(%.1f %.1f)"><title>%s</title><g transform="translate(-11 -11) scale(.9167)" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">%s</g></g>',
				esc_attr( $sign['element'] ),
				$gx,
				$gy,
				esc_html( $sign['name'] ),
				Icons::PATHS[ 'zodiac-' . $sign['slug'] ]
			);
		}
		$ticks = '';
		for ( $d = 0; $d < 360; $d += 5 ) {
			$len         = 0 === $d % 10 ? 7 : 4;
			[ $x1, $y1 ] = self::xy( $d, self::R_SIGN, $asc );
			[ $x2, $y2 ] = self::xy( $d, self::R_SIGN - $len, $asc );
			$ticks      .= sprintf( 'M%.1f %.1fL%.1f %.1f', $x1, $y1, $x2, $y2 );
		}
		$o[] = '<path class="wh-tick" d="' . $ticks . '"/>';
		$o[] = '</g>';

		// Houses.
		if ( ! empty( $opts['cusps'] ) ) {
			$o[] = '<g class="wh-houses">';
			for ( $h = 1; $h <= 12; $h++ ) {
				$lon         = (float) $opts['cusps'][ $h ];
				$axis        = in_array( $h, array( 1, 4, 7, 10 ), true );
				[ $x1, $y1 ] = self::xy( $lon, self::R_ASP, $asc );
				[ $x2, $y2 ] = self::xy( $lon, self::R_SIGN, $asc );
				$o[]         = sprintf( '<line class="wh-cusp%s" x1="%.1f" y1="%.1f" x2="%.1f" y2="%.1f"/>', $axis ? ' is-axis' : '', $x1, $y1, $x2, $y2 );
				$next        = (float) $opts['cusps'][ $h % 12 + 1 ];
				$mid         = $lon + Ephemeris::norm( $next - $lon ) / 2;
				[ $nx, $ny ] = self::xy( $mid, self::R_ASP - 13, $asc );
				$o[]         = sprintf( '<text class="wh-house-no" x="%.1f" y="%.1f">%d</text>', $nx, $ny + 4, $h );
			}
			foreach ( array(
				'ASC' => $opts['asc'],
				'MC'  => $opts['mc'] ?? null,
			) as $label => $lon ) {
				if ( null === $lon ) {
					continue;
				}
				// Beside the axis line, just outside the aspect circle.
				[ $ix, $iy ] = self::xy( (float) $lon + 6, self::R_ASP + 14, $asc );
				$o[]         = sprintf( '<text class="wh-axis-label" x="%.1f" y="%.1f">%s</text>', $ix, $iy + 4, $label );
			}
			$o[] = '</g>';
		}

		// Aspect lines.
		if ( ! empty( $opts['aspects'] ) ) {
			$o[] = '<g class="wh-aspects">';
			foreach ( $opts['aspects'] as $i => $a ) {
				if ( 'conjunction' === $a['type'] || ! isset( $bodies[ $a['a'] ], $bodies[ $a['b'] ] ) ) {
					continue;
				}
				[ $x1, $y1 ] = self::xy( (float) $bodies[ $a['a'] ]['lon'], self::R_ASP, $asc );
				[ $x2, $y2 ] = self::xy( (float) $bodies[ $a['b'] ]['lon'], self::R_ASP, $asc );
				$kind        = Zodiac::ASPECTS[ $a['type'] ]['kind'];
				$o[]         = sprintf( '<line class="wh-asp is-%s wh-draw" style="--i:%d" pathLength="1" x1="%.1f" y1="%.1f" x2="%.1f" y2="%.1f"/>', $kind, min( $i, 12 ) + 4, $x1, $y1, $x2, $y2 );
			}
			$o[] = '</g>';
		}

		// Planets, spread so glyphs never overlap.
		$place = self::spread( $bodies, 8.5 );
		$o[]   = '<g class="wh-planets">';
		$i     = 0;
		foreach ( $bodies as $key => $b ) {
			if ( ! isset( Icons::PATHS[ 'planet-' . $key ] ) ) {
				continue;
			}
			$lon         = (float) $b['lon'];
			[ $tx1, $ty1 ] = self::xy( $lon, self::R_SIGN, $asc );
			[ $tx2, $ty2 ] = self::xy( $lon, self::R_SIGN - 12, $asc );
			[ $px, $py ]   = self::xy( $place[ $key ], self::R_PLAN, $asc );
			[ $dx, $dy ]   = self::xy( $place[ $key ], self::R_DEG, $asc );
			[ $ax, $ay ]   = self::xy( $lon, self::R_ASP, $asc );
			$label         = Zodiac::PLANETS[ $key ]['name'] . ' ' . Zodiac::sign( $lon )['name'] . ' ' . Zodiac::degree( $lon ) . ( ! empty( $b['retro'] ) && 'node' !== $key ? ' R' : '' );
			$o[]           = sprintf(
				'<g class="wh-planet wh-p-%1$s" style="--i:%2$d"><title>%3$s</title><line class="wh-ptick" x1="%4$.1f" y1="%5$.1f" x2="%6$.1f" y2="%7$.1f"/><circle class="wh-pdot" cx="%8$.1f" cy="%9$.1f" r="2.6"/><g transform="translate(%10$.1f %11$.1f) scale(.9167)" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">%12$s</g><text class="wh-deg" x="%13$.1f" y="%14$.1f">%15$d°%16$s</text></g>',
				esc_attr( $key ),
				$i++,
				esc_html( $label ),
				$tx1,
				$ty1,
				$tx2,
				$ty2,
				$ax,
				$ay,
				$px - 11,
				$py - 11,
				Icons::PATHS[ 'planet-' . $key ],
				$dx,
				$dy + 4,
				(int) floor( fmod( $lon, 30 ) ),
				! empty( $b['retro'] ) && 'node' !== $key ? '℞' : ''
			);
		}
		$o[] = '</g></svg>';
		return implode( '', $o );
	}

	/** Screen coordinates for an ecliptic longitude at radius r. */
	private static function xy( float $lon, float $r, float $asc ): array {
		$a = deg2rad( $lon - $asc );
		return array( self::C - $r * cos( $a ), self::C + $r * sin( $a ) );
	}

	/**
	 * Display longitudes with a minimum separation (degrees), keeping each
	 * glyph as close as possible to its true position.
	 */
	private static function spread( array $bodies, float $min ): array {
		$items = array();
		foreach ( $bodies as $key => $b ) {
			$items[] = array(
				'key' => $key,
				'lon' => Ephemeris::norm( (float) $b['lon'] ),
			);
		}
		usort( $items, static fn( $a, $b ) => $a['lon'] <=> $b['lon'] );
		$n   = count( $items );
		$pos = array_column( $items, 'lon' );
		for ( $pass = 0; $pass < 60; $pass++ ) {
			$moved = false;
			for ( $i = 0; $i < $n; $i++ ) {
				$j   = ( $i + 1 ) % $n;
				$gap = Ephemeris::norm( $pos[ $j ] - $pos[ $i ] );
				if ( $n > 1 && $gap < $min ) {
					$push      = ( $min - $gap ) / 2 + 0.01;
					$pos[ $i ] = Ephemeris::norm( $pos[ $i ] - $push );
					$pos[ $j ] = Ephemeris::norm( $pos[ $j ] + $push );
					$moved     = true;
				}
			}
			if ( ! $moved ) {
				break;
			}
		}
		$out = array();
		foreach ( $items as $i => $it ) {
			$out[ $it['key'] ] = $pos[ $i ];
		}
		return $out;
	}
}
