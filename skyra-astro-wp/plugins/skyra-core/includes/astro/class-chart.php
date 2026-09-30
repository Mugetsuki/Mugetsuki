<?php
/**
 * Natal chart assembly: local birth time → UT, positions, houses, aspects.
 *
 * Historical UTC offsets (including daylight saving and Turkey's 2016 change)
 * come from PHP's bundled IANA time zone database.
 *
 * @package Skyra\Astro
 */

namespace Skyra\Astro;

defined( 'ABSPATH' ) || defined( 'SKYRA_ENGINE_STANDALONE' ) || exit;

final class Chart {

	/**
	 * @param array{date: string, time: ?string, lat: float, lon: float, tz: string} $input
	 * @return array Chart data; see REST schema in class-rest.php.
	 * @throws \InvalidArgumentException On invalid input.
	 */
	public static function natal( array $input ): array {
		$time_known = ! empty( $input['time'] );
		$local      = self::local_datetime( $input['date'], $time_known ? $input['time'] : '12:00', $input['tz'] );
		$unix       = (float) $local->getTimestamp();
		$jd         = Ephemeris::jd( $unix );

		$positions         = Ephemeris::positions( $jd );
		$jd_tt             = $jd + Ephemeris::delta_t( $jd ) / 86400.0;
		$positions['node'] = array(
			'lon'   => Ephemeris::mean_node( $jd_tt ),
			'lat'   => 0.0,
			'speed' => -0.053,
			'retro' => true,
		);

		$houses = $time_known ? Houses::compute( $jd, (float) $input['lat'], (float) $input['lon'] ) : null;

		$bodies = array();
		foreach ( $positions as $key => $p ) {
			$sign           = Zodiac::sign( $p['lon'] );
			$bodies[ $key ] = array(
				'key'    => $key,
				'name'   => Zodiac::PLANETS[ $key ]['name'],
				'lon'    => round( $p['lon'], 4 ),
				'sign'   => $sign['slug'],
				'degree' => Zodiac::degree( $p['lon'] ),
				'retro'  => 'node' !== $key && $p['retro'],
				'house'  => $houses ? Houses::house_of( $p['lon'], $houses['cusps'] ) : null,
			);
		}

		$warnings = array();
		if ( ! $time_known ) {
			$warnings[] = 'time_unknown';
			$moon_day   = self::moon_signs_for_day( $input['date'], $input['tz'] );
			if ( count( $moon_day['signs'] ) > 1 ) {
				$warnings[] = 'moon_sign_uncertain';
			}
		}
		if ( $houses ) {
			$in = fmod( $houses['asc'], 30.0 );
			if ( $in < 1.5 || $in > 28.5 ) {
				$warnings[] = 'asc_near_cusp';
			}
			if ( 'porphyry' === $houses['system'] ) {
				$warnings[] = 'polar_houses';
			}
		}

		return array(
			'input'    => array(
				'local'      => $local->format( 'Y-m-d H:i' ),
				'utc'        => gmdate( 'Y-m-d H:i', (int) $unix ),
				'offset'     => $local->format( 'P' ),
				'tz'         => $input['tz'],
				'time_known' => $time_known,
				'lat'        => round( (float) $input['lat'], 4 ),
				'lon'        => round( (float) $input['lon'], 4 ),
			),
			'bodies'   => $bodies,
			'angles'   => $houses ? array(
				'asc' => array(
					'lon'    => round( $houses['asc'], 4 ),
					'sign'   => Zodiac::sign( $houses['asc'] )['slug'],
					'degree' => Zodiac::degree( $houses['asc'] ),
				),
				'mc'  => array(
					'lon'    => round( $houses['mc'], 4 ),
					'sign'   => Zodiac::sign( $houses['mc'] )['slug'],
					'degree' => Zodiac::degree( $houses['mc'] ),
				),
			) : null,
			'houses'   => $houses ? array(
				'system' => $houses['system'],
				'cusps'  => array_map( static fn( $c ) => round( $c, 4 ), $houses['cusps'] ),
			) : null,
			'aspects'  => self::aspects( $bodies, $time_known ),
			'balance'  => self::balance( $bodies, $houses ),
			'warnings' => $warnings,
		);
	}

	/**
	 * Aspects between bodies, tightest first.
	 *
	 * @param array $bodies     Keyed bodies with 'lon'.
	 * @param bool  $with_moon  Include the Moon (false when birth time is unknown).
	 * @param float $orb_scale  Multiplier for the natal orbs.
	 */
	public static function aspects( array $bodies, bool $with_moon = true, float $orb_scale = 1.0 ): array {
		$keys = array_values( array_filter( array_keys( $bodies ), static fn( $k ) => 'node' !== $k && ( $with_moon || 'moon' !== $k ) ) );
		$out  = array();
		$n    = count( $keys );
		for ( $i = 0; $i < $n; $i++ ) {
			for ( $j = $i + 1; $j < $n; $j++ ) {
				$a   = $bodies[ $keys[ $i ] ];
				$b   = $bodies[ $keys[ $j ] ];
				$sep = abs( Ephemeris::diff( $a['lon'], $b['lon'] ) );
				foreach ( Zodiac::ASPECTS as $type => $asp ) {
					$orb = $asp['orb'] * $orb_scale;
					// The Sun and Moon get one extra degree of orb.
					if ( in_array( $keys[ $i ], array( 'sun', 'moon' ), true ) || in_array( $keys[ $j ], array( 'sun', 'moon' ), true ) ) {
						$orb += $orb_scale;
					}
					$delta = abs( $sep - $asp['angle'] );
					if ( $delta <= $orb ) {
						$out[] = array(
							'a'    => $keys[ $i ],
							'b'    => $keys[ $j ],
							'type' => $type,
							'orb'  => round( $delta, 2 ),
						);
						break;
					}
				}
			}
		}
		usort( $out, static fn( $x, $y ) => $x['orb'] <=> $y['orb'] );
		return $out;
	}

	/** Element and modality counts over the ten planets (plus ASC when known). */
	public static function balance( array $bodies, ?array $houses = null ): array {
		$elements   = array_fill_keys( array_keys( Zodiac::ELEMENTS ), 0 );
		$modalities = array_fill_keys( array_keys( Zodiac::MODALITIES ), 0 );
		$lons       = array();
		foreach ( $bodies as $key => $b ) {
			if ( 'node' !== $key ) {
				$lons[] = $b['lon'];
			}
		}
		if ( $houses ) {
			$lons[] = $houses['asc'];
		}
		foreach ( $lons as $lon ) {
			$sign = Zodiac::sign( $lon );
			++$elements[ $sign['element'] ];
			++$modalities[ $sign['modality'] ];
		}
		return array(
			'elements'   => $elements,
			'modalities' => $modalities,
			'total'      => count( $lons ),
		);
	}

	/**
	 * Moon sign(s) over a local calendar day, with the ingress time if it changes sign.
	 *
	 * @return array{signs: string[], ingress: ?string}
	 */
	public static function moon_signs_for_day( string $date, string $tz ): array {
		$start = self::local_datetime( $date, '00:00', $tz );
		$end   = ( clone $start )->modify( '+1 day' );
		$jd0   = Ephemeris::jd( (float) $start->getTimestamp() );
		$jd1   = Ephemeris::jd( (float) $end->getTimestamp() );
		$s0    = Zodiac::sign( Ephemeris::lon( 'moon', $jd0 ) )['slug'];
		$ing   = Events::ingresses( 'moon', $jd0, $jd1, 0.25 );
		if ( ! $ing ) {
			return array(
				'signs'   => array( $s0 ),
				'ingress' => null,
			);
		}
		$when = ( new \DateTimeImmutable( '@' . (int) round( Ephemeris::unix( $ing[0]['jd'] ) ) ) )->setTimezone( new \DateTimeZone( $tz ) );
		return array(
			'signs'   => array( $s0, Zodiac::SIGNS[ $ing[0]['to'] ]['slug'] ),
			'ingress' => $when->format( 'H:i' ),
		);
	}

	/** @throws \InvalidArgumentException */
	public static function local_datetime( string $date, string $time, string $tz ): \DateTimeImmutable {
		if ( ! preg_match( '/^(\d{4})-(\d{2})-(\d{2})$/', $date, $d ) || ! checkdate( (int) $d[2], (int) $d[3], (int) $d[1] ) ) {
			throw new \InvalidArgumentException( 'date' );
		}
		if ( (int) $d[1] < 1900 || (int) $d[1] > 2100 ) {
			throw new \InvalidArgumentException( 'date_range' );
		}
		if ( ! preg_match( '/^([01]\d|2[0-3]):([0-5]\d)$/', $time ) ) {
			throw new \InvalidArgumentException( 'time' );
		}
		try {
			$zone = new \DateTimeZone( $tz );
		} catch ( \Exception $e ) {
			throw new \InvalidArgumentException( 'tz' );
		}
		return new \DateTimeImmutable( "$date $time:00", $zone );
	}
}
