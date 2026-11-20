<?php
/**
 * Sky events: Moon phases, lunations, eclipses, sign ingresses and stations.
 *
 * Event times come from root-finding on the Ephemeris; eclipse detection and
 * classification follow Meeus, "Astronomical Algorithms" ch. 54.
 *
 * @package Skyra\Astro
 */

namespace Skyra\Astro;

defined( 'ABSPATH' ) || defined( 'SKYRA_ENGINE_STANDALONE' ) || exit;

final class Events {

	public const SYNODIC = 29.530588861;

	/** Planets whose stations and ingresses are tracked. */
	public const MOVERS = array( 'mercury', 'venus', 'mars', 'jupiter', 'saturn', 'uranus', 'neptune', 'pluto' );

	public const PHASES = array(
		array( 'key' => 'new', 'name' => 'Yeni Ay' ),
		array( 'key' => 'waxing-crescent', 'name' => 'Büyüyen Hilal' ),
		array( 'key' => 'first-quarter', 'name' => 'İlk Dördün' ),
		array( 'key' => 'waxing-gibbous', 'name' => 'Büyüyen Şişkin Ay' ),
		array( 'key' => 'full', 'name' => 'Dolunay' ),
		array( 'key' => 'waning-gibbous', 'name' => 'Küçülen Şişkin Ay' ),
		array( 'key' => 'last-quarter', 'name' => 'Son Dördün' ),
		array( 'key' => 'waning-crescent', 'name' => 'Küçülen Hilal' ),
	);

	/**
	 * Moon phase details at an instant.
	 *
	 * @return array{elongation: float, illumination: float, index: int, key: string, name: string, waxing: bool, age: float, next: array}
	 */
	public static function moon_phase( float $jd ): array {
		$sun   = Ephemeris::position( 'sun', $jd );
		$moon  = Ephemeris::position( 'moon', $jd );
		$elong = Ephemeris::norm( $moon['lon'] - $sun['lon'] );
		$psi   = acos( cos( deg2rad( $moon['lat'] ) ) * cos( deg2rad( $elong ) ) );
		$index = (int) floor( ( $elong + 22.5 ) / 45.0 ) % 8;

		return array(
			'elongation'   => $elong,
			'illumination' => ( 1 - cos( $psi ) ) / 2,
			'index'        => $index,
			'key'          => self::PHASES[ $index ]['key'],
			'name'         => self::PHASES[ $index ]['name'],
			'waxing'       => $elong < 180,
			'age'          => $elong / 360.0 * self::SYNODIC,
			'next'         => array(
				'new'           => self::next_phase( $jd, 0 ),
				'first-quarter' => self::next_phase( $jd, 90 ),
				'full'          => self::next_phase( $jd, 180 ),
				'last-quarter'  => self::next_phase( $jd, 270 ),
			),
		);
	}

	/** First instant after $jd when Moon − Sun elongation equals $target degrees. */
	public static function next_phase( float $jd, float $target ): float {
		$elong = Ephemeris::norm( Ephemeris::lon( 'moon', $jd ) - Ephemeris::lon( 'sun', $jd ) );
		$guess = $jd + Ephemeris::norm( $target - $elong ) / 12.1907;
		$t     = self::refine_phase( $guess, $target );
		if ( $t <= $jd ) {
			$t = self::refine_phase( $t + self::SYNODIC, $target );
		}
		return $t;
	}

	private static function refine_phase( float $t, float $target ): float {
		for ( $k = 0; $k < 10; $k++ ) {
			$e  = Ephemeris::diff( Ephemeris::lon( 'moon', $t ) - Ephemeris::lon( 'sun', $t ), $target );
			$t -= $e / 12.1907;
			if ( abs( $e ) < 1e-5 ) {
				break;
			}
		}
		return $t;
	}

	/**
	 * New and full Moons between two instants, with eclipse data where one occurs.
	 *
	 * @return array<int, array{type: string, jd: float, lon: float, eclipse: ?array}>
	 */
	public static function lunations( float $jd_start, float $jd_end ): array {
		$out = array();
		$k0  = (int) floor( ( $jd_start - 2451550.09766 ) / self::SYNODIC ) - 1;
		for ( $k = $k0; 2451550.09766 + self::SYNODIC * $k <= $jd_end + self::SYNODIC; $k++ ) {
			foreach ( array( 0.0, 0.5 ) as $phase ) {
				$mean = 2451550.09766 + self::SYNODIC * ( $k + $phase );
				$t    = self::refine_phase( $mean, $phase * 360 );
				if ( $t < $jd_start || $t >= $jd_end ) {
					continue;
				}
				$out[] = array(
					'type'    => 0.0 === $phase ? 'new_moon' : 'full_moon',
					'jd'      => $t,
					'lon'     => Ephemeris::lon( 'moon', $t ),
					'eclipse' => self::eclipse( $k + $phase ),
				);
			}
		}
		usort( $out, static fn( $a, $b ) => $a['jd'] <=> $b['jd'] );
		return $out;
	}

	/**
	 * Eclipse at lunation number k (integer: new Moon, +0.5: full Moon), Meeus ch. 54.
	 *
	 * @return array{kind: string, type: string, magnitude: float, gamma: float}|null
	 */
	public static function eclipse( float $k ): ?array {
		$t  = $k / 1236.85;
		$f  = 160.7108 + 390.67050284 * $k - 0.0016118 * $t ** 2 - 0.00000227 * $t ** 3 + 0.000000011 * $t ** 4;
		if ( abs( sin( deg2rad( $f ) ) ) > 0.36 ) {
			return null;
		}
		$m  = deg2rad( 2.5534 + 29.10535670 * $k - 0.0000014 * $t ** 2 - 0.00000011 * $t ** 3 );
		$mp = deg2rad( 201.5643 + 385.81693528 * $k + 0.0107582 * $t ** 2 + 0.00001238 * $t ** 3 - 0.000000058 * $t ** 4 );
		$om = deg2rad( 124.7746 - 1.56375588 * $k + 0.0020672 * $t ** 2 + 0.00000215 * $t ** 3 );
		$e  = 1 - 0.002516 * $t - 0.0000074 * $t ** 2;
		$f1 = deg2rad( $f - 0.02665 * sin( $om ) );

		$p = 0.2070 * $e * sin( $m ) + 0.0024 * $e * sin( 2 * $m ) - 0.0392 * sin( $mp ) + 0.0116 * sin( 2 * $mp )
			- 0.0073 * $e * sin( $mp + $m ) + 0.0067 * $e * sin( $mp - $m ) + 0.0118 * sin( 2 * $f1 );
		$q = 5.2207 - 0.0048 * $e * cos( $m ) + 0.0020 * $e * cos( 2 * $m ) - 0.3299 * cos( $mp )
			- 0.0060 * $e * cos( $mp + $m ) + 0.0041 * $e * cos( $mp - $m );
		$w = abs( cos( $f1 ) );

		$gamma = ( $p * cos( $f1 ) + $q * sin( $f1 ) ) * ( 1 - 0.0048 * $w );
		$u     = 0.0059 + 0.0046 * $e * cos( $m ) - 0.0182 * cos( $mp ) + 0.0004 * cos( 2 * $mp ) - 0.0005 * cos( $m + $mp );
		$g     = abs( $gamma );

		if ( floor( $k ) === $k ) {
			if ( $g > 1.5433 + $u ) {
				return null;
			}
			if ( $g < 0.9972 ) {
				if ( $u < 0 ) {
					$type = 'total';
				} elseif ( $u > 0.0047 ) {
					$type = 'annular';
				} else {
					$type = $u < 0.00464 * sqrt( 1 - $gamma ** 2 ) ? 'hybrid' : 'annular';
				}
				$mag = null;
			} else {
				$type = 'partial';
				$mag  = ( 1.5433 + $u - $g ) / ( 0.5461 + 2 * $u );
			}
			return array(
				'kind'      => 'solar',
				'type'      => $type,
				'magnitude' => null === $mag ? 1.0 : round( $mag, 3 ),
				'gamma'     => round( $gamma, 4 ),
			);
		}

		$penumbral = ( 1.5573 + $u - $g ) / 0.5450;
		$umbral    = ( 1.0128 - $u - $g ) / 0.5450;
		if ( $umbral >= 1 ) {
			$type = 'total';
		} elseif ( $umbral > 0 ) {
			$type = 'partial';
		} elseif ( $penumbral > 0 ) {
			$type = 'penumbral';
		} else {
			return null;
		}
		return array(
			'kind'      => 'lunar',
			'type'      => $type,
			'magnitude' => round( $umbral > 0 ? $umbral : $penumbral, 3 ),
			'gamma'     => round( $gamma, 4 ),
		);
	}

	/**
	 * Sign ingresses of a body in [start, end).
	 *
	 * @return array<int, array{body: string, jd: float, from: int, to: int, retro: bool}>
	 */
	public static function ingresses( string $body, float $jd_start, float $jd_end, float $step = 1.0 ): array {
		$out  = array();
		$t0   = $jd_start;
		$lon0 = Ephemeris::lon( $body, $t0 );
		while ( $t0 < $jd_end ) {
			$t1   = min( $t0 + $step, $jd_end );
			$lon1 = Ephemeris::lon( $body, $t1 );
			$s0   = Zodiac::index_of( $lon0 );
			$s1   = Zodiac::index_of( $lon1 );
			if ( $s0 !== $s1 ) {
				$forward  = Ephemeris::diff( $lon1, $lon0 ) > 0;
				$boundary = ( $forward ? $s1 : $s0 ) * 30.0;
				$a        = $t0;
				$b        = $t1;
				for ( $i = 0; $i < 22; $i++ ) {
					$mid  = ( $a + $b ) / 2;
					$past = Ephemeris::diff( Ephemeris::lon( $body, $mid ), $boundary ) >= 0;
					if ( $past === $forward ) {
						$b = $mid;
					} else {
						$a = $mid;
					}
				}
				$out[] = array(
					'body'  => $body,
					'jd'    => ( $a + $b ) / 2,
					'from'  => $s0,
					'to'    => $s1,
					'retro' => ! $forward,
				);
			}
			$t0   = $t1;
			$lon0 = $lon1;
		}
		return $out;
	}

	/**
	 * Stations (retrograde / direct turning points) in [start, end).
	 *
	 * @return array<int, array{body: string, jd: float, type: string, lon: float}>
	 */
	public static function stations( string $body, float $jd_start, float $jd_end ): array {
		$out   = array();
		$prev  = Ephemeris::lon( $body, $jd_start - 1 );
		$cur   = Ephemeris::lon( $body, $jd_start );
		$dprev = Ephemeris::diff( $cur, $prev );
		for ( $t = $jd_start; $t < $jd_end; $t += 1.0 ) {
			$next = Ephemeris::lon( $body, $t + 1 );
			$d    = Ephemeris::diff( $next, $cur );
			if ( ( $dprev > 0 ) !== ( $d > 0 ) ) {
				$retro = $d < 0;
				$a     = $t - 1;
				$b     = $t + 1;
				for ( $i = 0; $i < 22; $i++ ) {
					$mid = ( $a + $b ) / 2;
					if ( ( Ephemeris::speed( $body, $mid ) < 0 ) === $retro ) {
						$b = $mid;
					} else {
						$a = $mid;
					}
				}
				$jd = ( $a + $b ) / 2;
				if ( $jd >= $jd_start && $jd < $jd_end ) {
					$out[] = array(
						'body' => $body,
						'jd'   => $jd,
						'type' => $retro ? 'retro' : 'direct',
						'lon'  => Ephemeris::lon( $body, $jd ),
					);
				}
			}
			$cur   = $next;
			$dprev = $d;
		}
		return $out;
	}

	/**
	 * Retrograde periods that overlap [start, end), paired start/end.
	 *
	 * @return array<int, array{body: string, start: float, end: float, start_lon: float, end_lon: float}>
	 */
	public static function retro_periods( string $body, float $jd_start, float $jd_end ): array {
		// Look back/ahead far enough to pair stations of periods crossing the edges.
		$pad      = in_array( $body, array( 'mercury', 'venus', 'mars' ), true ) ? 90 : 170;
		$stations = self::stations( $body, $jd_start - $pad, $jd_end + $pad );
		$out      = array();
		$count    = count( $stations );
		for ( $i = 0; $i < $count; $i++ ) {
			if ( 'retro' !== $stations[ $i ]['type'] || ! isset( $stations[ $i + 1 ] ) ) {
				continue;
			}
			$begin = $stations[ $i ];
			$end   = $stations[ $i + 1 ];
			if ( $end['jd'] < $jd_start || $begin['jd'] >= $jd_end ) {
				continue;
			}
			$out[] = array(
				'body'      => $body,
				'start'     => $begin['jd'],
				'end'       => $end['jd'],
				'start_lon' => $begin['lon'],
				'end_lon'   => $end['lon'],
			);
		}
		return $out;
	}
}
