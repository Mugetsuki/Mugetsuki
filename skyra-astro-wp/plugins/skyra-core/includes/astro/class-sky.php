<?php
/**
 * Sky snapshots and event lists built on the Ephemeris.
 *
 * @package Skyra\Astro
 */

namespace Skyra\Astro;

defined( 'ABSPATH' ) || defined( 'SKYRA_ENGINE_STANDALONE' ) || exit;

final class Sky {

	/** Orb multiplier for sky-to-sky aspects (≈3° conjunction, 1.5° sextile). */
	private const SKY_ORB = 0.375;

	/**
	 * Positions, Moon phase, aspects and retrogrades at an instant.
	 */
	public static function snapshot( int $unix, string $tz ): array {
		$jd  = Ephemeris::jd( (float) $unix );
		$pos = Ephemeris::positions( $jd );

		$bodies = array();
		foreach ( $pos as $key => $p ) {
			$bodies[ $key ] = array(
				'key'    => $key,
				'name'   => Zodiac::PLANETS[ $key ]['name'],
				'lon'    => round( $p['lon'], 4 ),
				'sign'   => Zodiac::sign( $p['lon'] )['slug'],
				'degree' => Zodiac::degree( $p['lon'] ),
				'speed'  => round( $p['speed'], 4 ),
				'retro'  => $p['retro'],
			);
		}

		$phase = Events::moon_phase( $jd );
		foreach ( $phase['next'] as $k => $t ) {
			$phase['next'][ $k ] = (int) round( Ephemeris::unix( $t ) );
		}

		$ingress = Events::ingresses( 'moon', $jd, $jd + 3, 0.25 )[0] ?? null;

		$retro = array();
		foreach ( Events::MOVERS as $body ) {
			if ( $bodies[ $body ]['retro'] ) {
				$retro[] = $body;
			}
		}

		$moon_sign = $bodies['moon']['sign'];
		return array(
			'unix'         => $unix,
			'tz'           => $tz,
			'bodies'       => $bodies,
			'sun'          => array(
				'sign'   => $bodies['sun']['sign'],
				'degree' => $bodies['sun']['degree'],
			),
			'moon'         => array(
				'sign'   => $moon_sign,
				'degree' => $bodies['moon']['degree'],
				'next'   => $ingress ? array(
					'unix' => (int) round( Ephemeris::unix( $ingress['jd'] ) ),
					'to'   => Zodiac::SIGNS[ $ingress['to'] ]['slug'],
				) : null,
			),
			'phase'        => $phase,
			'aspects'      => self::aspects( $bodies, $jd ),
			'retro'        => $retro,
			'balance'      => Chart::balance( $bodies ),
			'theme'        => Readings::MOON_SIGN_THEME[ $moon_sign ],
		);
	}

	/**
	 * The sky of a local calendar day, as used by daily readings.
	 *
	 * The Moon's "primary" sign is the one it holds for most of the waking day;
	 * a sign change between 12:00 and 22:00 is reported so readings can mention it.
	 */
	public static function day( string $date, string $tz ): array {
		$noon = Chart::local_datetime( $date, '12:00', $tz );
		$snap = self::snapshot( $noon->getTimestamp(), $tz );
		$day  = Chart::moon_signs_for_day( $date, $tz );

		$snap['date']            = $date;
		$snap['moon']['ingress'] = null;
		if ( $day['ingress'] && $day['ingress'] >= '12:00' && $day['ingress'] < '22:00' ) {
			$snap['moon']['sign']    = $day['signs'][0];
			$snap['moon']['ingress'] = array(
				'time' => $day['ingress'],
				'to'   => $day['signs'][1],
			);
		}
		$snap['aspect'] = $snap['aspects'][0] ?? null;
		return $snap;
	}

	/** Tight aspects between the planets (Moon excluded), tightest first, with applying flag. */
	public static function aspects( array $bodies, float $jd ): array {
		$planets = array_diff_key( $bodies, array( 'moon' => true ) );
		$list    = Chart::aspects( $planets, false, self::SKY_ORB );
		foreach ( $list as &$a ) {
			$angle        = Zodiac::ASPECTS[ $a['type'] ]['angle'];
			$later        = abs( abs( Ephemeris::diff( Ephemeris::lon( $a['a'], $jd + 0.5 ), Ephemeris::lon( $a['b'], $jd + 0.5 ) ) ) - $angle );
			$a['applying'] = $later < $a['orb'];
		}
		return $list;
	}

	/**
	 * All tracked events in [start, end): lunations/eclipses, ingresses, stations.
	 *
	 * @return array<int, array> Sorted by time.
	 */
	public static function events( int $start, int $end ): array {
		$jd0 = Ephemeris::jd( (float) $start );
		$jd1 = Ephemeris::jd( (float) $end );
		$raw = array();

		foreach ( Events::lunations( $jd0, $jd1 ) as $l ) {
			$type = $l['type'];
			if ( $l['eclipse'] ) {
				$type = 'solar' === $l['eclipse']['kind'] ? 'solar_eclipse' : 'lunar_eclipse';
			}
			$raw[] = array(
				'type'    => $type,
				'body'    => 'moon',
				'jd'      => $l['jd'],
				'lon'     => $l['lon'],
				'eclipse' => $l['eclipse'],
				'retro'   => false,
			);
		}
		foreach ( array_merge( array( 'sun' ), Events::MOVERS ) as $body ) {
			foreach ( Events::ingresses( $body, $jd0, $jd1 ) as $i ) {
				$raw[] = array(
					'type'    => 'ingress',
					'body'    => $body,
					'jd'      => $i['jd'],
					'lon'     => $i['retro'] ? $i['to'] * 30 + 29.999 : $i['to'] * 30.0,
					'eclipse' => null,
					'retro'   => $i['retro'],
				);
			}
		}
		foreach ( Events::MOVERS as $body ) {
			foreach ( Events::stations( $body, $jd0, $jd1 ) as $s ) {
				$raw[] = array(
					'type'    => 'retro' === $s['type'] ? 'station_retro' : 'station_direct',
					'body'    => $body,
					'jd'      => $s['jd'],
					'lon'     => $s['lon'],
					'eclipse' => null,
					'retro'   => false,
				);
			}
		}

		$out = array();
		foreach ( $raw as $e ) {
			$unix  = (int) round( Ephemeris::unix( $e['jd'] ) );
			$text  = Readings::event( $e );
			$out[] = array(
				'id'      => sprintf( '%s-%s-%s', str_replace( '_', '-', $e['type'] ), $e['body'], gmdate( 'Ymd', $unix ) ),
				'type'    => $e['type'],
				'body'    => $e['body'],
				'unix'    => $unix,
				'sign'    => Zodiac::sign( $e['lon'] )['slug'],
				'degree'  => Zodiac::degree( $e['lon'] ),
				'eclipse' => $e['eclipse'],
				'retro'   => $e['retro'],
				'title'   => $text['title'],
				'summary' => $text['summary'],
			);
		}
		usort( $out, static fn( $a, $b ) => $a['unix'] <=> $b['unix'] );
		return $out;
	}

	/**
	 * Retrograde periods overlapping a local calendar year, per planet.
	 *
	 * @return array<string, array<int, array{start: int, end: int, sign_start: string, sign_end: string, degree_start: string, degree_end: string}>>
	 */
	public static function retro_year( int $year, string $tz ): array {
		$zone = new \DateTimeZone( $tz );
		$jd0  = Ephemeris::jd( (float) ( new \DateTimeImmutable( "$year-01-01 00:00", $zone ) )->getTimestamp() );
		$jd1  = Ephemeris::jd( (float) ( new \DateTimeImmutable( ( $year + 1 ) . '-01-01 00:00', $zone ) )->getTimestamp() );
		$out  = array();
		foreach ( Events::MOVERS as $body ) {
			$out[ $body ] = array_map(
				static fn( $p ) => array(
					'start'        => (int) round( Ephemeris::unix( $p['start'] ) ),
					'end'          => (int) round( Ephemeris::unix( $p['end'] ) ),
					'sign_start'   => Zodiac::sign( $p['start_lon'] )['slug'],
					'sign_end'     => Zodiac::sign( $p['end_lon'] )['slug'],
					'degree_start' => Zodiac::degree( $p['start_lon'] ),
					'degree_end'   => Zodiac::degree( $p['end_lon'] ),
				),
				Events::retro_periods( $body, $jd0, $jd1 )
			);
		}
		return $out;
	}

	/** For each planet retrograde at $unix, the time it stations direct. */
	public static function retro_until( int $unix ): array {
		$jd  = Ephemeris::jd( (float) $unix );
		$out = array();
		foreach ( Events::MOVERS as $body ) {
			if ( Ephemeris::speed( $body, $jd ) >= 0 ) {
				continue;
			}
			$span = in_array( $body, array( 'mercury', 'venus', 'mars' ), true ) ? 90 : 170;
			foreach ( Events::stations( $body, $jd, $jd + $span ) as $s ) {
				if ( 'direct' === $s['type'] ) {
					$out[ $body ] = (int) round( Ephemeris::unix( $s['jd'] ) );
					break;
				}
			}
		}
		return $out;
	}
}
