<?php
/**
 * Ascendant, Midheaven and house cusps.
 *
 * Placidus is computed by iterating the semi-arc definition; above the polar
 * circles (where Placidus is undefined) Porphyry is used and reported.
 *
 * @package Skyra\Astro
 */

namespace Skyra\Astro;

defined( 'ABSPATH' ) || defined( 'SKYRA_ENGINE_STANDALONE' ) || exit;

final class Houses {

	/**
	 * @param float $jd_ut Julian day (UT).
	 * @param float $lat   Geographic latitude, degrees north.
	 * @param float $lon   Geographic longitude, degrees east.
	 * @return array{asc: float, mc: float, ramc: float, cusps: float[], system: string}
	 */
	public static function compute( float $jd_ut, float $lat, float $lon ): array {
		$jd_tt = $jd_ut + Ephemeris::delta_t( $jd_ut ) / 86400.0;
		$eps   = Ephemeris::obliquity( $jd_tt );
		$ramc  = Ephemeris::norm( Ephemeris::gmst( $jd_ut ) + $lon );

		$mc  = self::mc( $ramc, $eps );
		$asc = self::asc( $ramc, $eps, $lat );

		$cusps  = self::placidus( $ramc, $eps, $lat, $asc, $mc );
		$system = 'placidus';
		if ( null === $cusps ) {
			$cusps  = self::porphyry( $asc, $mc );
			$system = 'porphyry';
		}

		return array(
			'asc'    => $asc,
			'mc'     => $mc,
			'ramc'   => $ramc,
			'cusps'  => $cusps,
			'system' => $system,
		);
	}

	public static function mc( float $ramc, float $eps ): float {
		$r = deg2rad( $ramc );
		return Ephemeris::norm( rad2deg( atan2( sin( $r ), cos( $r ) * cos( deg2rad( $eps ) ) ) ) );
	}

	public static function asc( float $ramc, float $eps, float $lat ): float {
		$r = deg2rad( $ramc );
		$e = deg2rad( $eps );
		$y = cos( $r );
		$x = -( sin( $r ) * cos( $e ) + tan( deg2rad( $lat ) ) * sin( $e ) );
		return Ephemeris::norm( rad2deg( atan2( $y, $x ) ) );
	}

	/** House number (1–12) of an ecliptic longitude for the given cusps. */
	public static function house_of( float $lon, array $cusps ): int {
		for ( $h = 1; $h <= 12; $h++ ) {
			$start = $cusps[ $h ];
			$end   = $cusps[ $h % 12 + 1 ];
			$span  = Ephemeris::norm( $end - $start );
			if ( Ephemeris::norm( $lon - $start ) < $span ) {
				return $h;
			}
		}
		return 1;
	}

	/** @return array<int, float>|null Cusps 1–12, or null if undefined at this latitude. */
	private static function placidus( float $ramc, float $eps, float $lat, float $asc, float $mc ): ?array {
		if ( abs( $lat ) >= 90 - $eps ) {
			return null;
		}
		$cusps = array(
			1  => $asc,
			10 => $mc,
		);
		// [house, fraction of semi-arc, above horizon?]
		$defs = array( array( 11, 1 / 3, true ), array( 12, 2 / 3, true ), array( 2, 2 / 3, false ), array( 3, 1 / 3, false ) );
		foreach ( $defs as [ $house, $f, $above ] ) {
			$lon = self::placidus_cusp( $ramc, $eps, $lat, $f, $above );
			if ( null === $lon ) {
				return null;
			}
			$cusps[ $house ] = $lon;
		}
		$cusps[4] = Ephemeris::norm( $cusps[10] + 180 );
		$cusps[5] = Ephemeris::norm( $cusps[11] + 180 );
		$cusps[6] = Ephemeris::norm( $cusps[12] + 180 );
		$cusps[7] = Ephemeris::norm( $cusps[1] + 180 );
		$cusps[8] = Ephemeris::norm( $cusps[2] + 180 );
		$cusps[9] = Ephemeris::norm( $cusps[3] + 180 );
		ksort( $cusps );
		return $cusps;
	}

	/**
	 * Solve RA = RAMC + f·DSA (above horizon) or RA = RAMC + 180 − f·NSA (below),
	 * where the semi-arcs depend on the declination of the cusp itself.
	 */
	private static function placidus_cusp( float $ramc, float $eps, float $lat, float $f, bool $above ): ?float {
		$e   = deg2rad( $eps );
		$phi = deg2rad( $lat );
		$ad  = 0.0;
		for ( $k = 0; $k < 50; $k++ ) {
			$ra   = $above ? $ramc + $f * ( 90 + $ad ) : $ramc + 180 - $f * ( 90 - $ad );
			$dec  = atan( tan( $e ) * sin( deg2rad( $ra ) ) );
			$arg  = tan( $phi ) * tan( $dec );
			if ( abs( $arg ) > 1 ) {
				return null;
			}
			$next = rad2deg( asin( $arg ) );
			if ( abs( $next - $ad ) < 1e-9 ) {
				$ad = $next;
				break;
			}
			$ad = $next;
		}
		$ra = deg2rad( $above ? $ramc + $f * ( 90 + $ad ) : $ramc + 180 - $f * ( 90 - $ad ) );
		return Ephemeris::norm( rad2deg( atan2( sin( $ra ), cos( $ra ) * cos( $e ) ) ) );
	}

	/** Porphyry: trisect each quadrant in ecliptic longitude. */
	private static function porphyry( float $asc, float $mc ): array {
		$ic    = Ephemeris::norm( $mc + 180 );
		$q1    = Ephemeris::norm( $ic - $asc ) / 3;   // ASC → IC.
		$q2    = Ephemeris::norm( $asc - $mc ) / 3;   // MC → ASC.
		$cusps = array(
			1  => $asc,
			2  => Ephemeris::norm( $asc + $q1 ),
			3  => Ephemeris::norm( $asc + 2 * $q1 ),
			10 => $mc,
			11 => Ephemeris::norm( $mc + $q2 ),
			12 => Ephemeris::norm( $mc + 2 * $q2 ),
		);
		foreach ( array( 4 => 10, 5 => 11, 6 => 12, 7 => 1, 8 => 2, 9 => 3 ) as $h => $opp ) {
			$cusps[ $h ] = Ephemeris::norm( $cusps[ $opp ] + 180 );
		}
		ksort( $cusps );
		return $cusps;
	}
}
