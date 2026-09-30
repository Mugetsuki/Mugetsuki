<?php
/**
 * Geocentric ecliptic positions of the Sun, Moon and planets.
 *
 * Orbital elements and perturbation terms follow Paul Schlyter,
 * "How to compute planetary positions" (public domain), with ΔT from
 * Espenak & Meeus and nutation in longitude from Meeus ch. 22 (main terms).
 * Typical accuracy against astronomy-engine is a few arc-minutes between
 * 1900 and 2100 (see tests/engine-accuracy.mjs), which is well inside the
 * precision astrology needs for sign, degree and aspect work.
 *
 * Pure PHP, no WordPress dependency, so it can be unit tested on its own.
 *
 * @package Skyra\Astro
 */

namespace Skyra\Astro;

defined( 'ABSPATH' ) || defined( 'SKYRA_ENGINE_STANDALONE' ) || exit;

final class Ephemeris {

	public const BODIES = array( 'sun', 'moon', 'mercury', 'venus', 'mars', 'jupiter', 'saturn', 'uranus', 'neptune', 'pluto' );

	/** Julian day (UT) for a Unix timestamp. */
	public static function jd( float $unix ): float {
		return $unix / 86400.0 + 2440587.5;
	}

	/** Unix timestamp for a Julian day (UT). */
	public static function unix( float $jd ): float {
		return ( $jd - 2440587.5 ) * 86400.0;
	}

	/** ΔT (TT − UT) in seconds, Espenak & Meeus polynomial fits. */
	public static function delta_t( float $jd ): float {
		$y = 2000.0 + ( $jd - 2451544.5 ) / 365.2425;
		if ( $y < 1900 ) {
			$t = ( $y - 1860 );
			return 7.62 + 0.5737 * $t - 0.251754 * $t ** 2 + 0.01680668 * $t ** 3 - 0.0004473624 * $t ** 4 + $t ** 5 / 233174;
		}
		if ( $y < 1920 ) {
			$t = $y - 1900;
			return -2.79 + 1.494119 * $t - 0.0598939 * $t ** 2 + 0.0061966 * $t ** 3 - 0.000197 * $t ** 4;
		}
		if ( $y < 1941 ) {
			$t = $y - 1920;
			return 21.20 + 0.84493 * $t - 0.076100 * $t ** 2 + 0.0020936 * $t ** 3;
		}
		if ( $y < 1961 ) {
			$t = $y - 1950;
			return 29.07 + 0.407 * $t - $t ** 2 / 233 + $t ** 3 / 2547;
		}
		if ( $y < 1986 ) {
			$t = $y - 1975;
			return 45.45 + 1.067 * $t - $t ** 2 / 260 - $t ** 3 / 718;
		}
		if ( $y < 2005 ) {
			$t = $y - 2000;
			return 63.86 + 0.3345 * $t - 0.060374 * $t ** 2 + 0.0017275 * $t ** 3 + 0.000651814 * $t ** 4 + 0.00002373599 * $t ** 5;
		}
		if ( $y < 2050 ) {
			$t = $y - 2000;
			return 62.92 + 0.32217 * $t + 0.005589 * $t ** 2;
		}
		$u = ( $y - 1820 ) / 100;
		return -20 + 32 * $u ** 2 - 0.5628 * ( 2150 - $y );
	}

	/** Mean obliquity of the ecliptic (degrees) for a TT Julian day. */
	public static function obliquity( float $jd_tt ): float {
		$t = ( $jd_tt - 2451545.0 ) / 36525.0;
		return 23.439291111 - 0.0130041667 * $t - 1.6389e-7 * $t ** 2 + 5.036e-7 * $t ** 3;
	}

	/** Nutation in longitude (degrees), main terms. */
	public static function nutation( float $jd_tt ): float {
		$t     = ( $jd_tt - 2451545.0 ) / 36525.0;
		$omega = deg2rad( 125.04452 - 1934.136261 * $t );
		$l     = deg2rad( 280.4665 + 36000.7698 * $t );
		$lp    = deg2rad( 218.3165 + 481267.8813 * $t );
		$arcs  = -17.20 * sin( $omega ) - 1.32 * sin( 2 * $l ) - 0.23 * sin( 2 * $lp ) + 0.21 * sin( 2 * $omega );
		return $arcs / 3600.0;
	}

	/** Mean lunar ascending node (degrees). */
	public static function mean_node( float $jd_tt ): float {
		$t = ( $jd_tt - 2451545.0 ) / 36525.0;
		return self::norm( 125.0445479 - 1934.1362891 * $t + 0.0020754 * $t ** 2 + $t ** 3 / 467441 );
	}

	/** Greenwich mean sidereal time in degrees for a UT Julian day. */
	public static function gmst( float $jd_ut ): float {
		$t = ( $jd_ut - 2451545.0 ) / 36525.0;
		return self::norm( 280.46061837 + 360.98564736629 * ( $jd_ut - 2451545.0 ) + 0.000387933 * $t ** 2 - $t ** 3 / 38710000 );
	}

	/**
	 * Apparent geocentric ecliptic longitude/latitude (degrees, ecliptic of date).
	 *
	 * @return array{lon: float, lat: float, dist: float}
	 */
	public static function position( string $body, float $jd_ut ): array {
		$jd_tt = $jd_ut + self::delta_t( $jd_ut ) / 86400.0;
		$d     = $jd_tt - 2451543.5;
		$dpsi  = self::nutation( $jd_tt );

		if ( 'moon' === $body ) {
			$p = self::moon( $d );
		} else {
			$sun = self::sun( $d );
			if ( 'sun' === $body ) {
				// Annual aberration of the Sun: −20.4898″ / R.
				$p = array( $sun['lon'] - ( 20.4898 / 3600.0 ) / $sun['r'], 0.0, $sun['r'] );
			} else {
				$p = self::planet( $body, $d, $sun );
			}
		}
		return array(
			'lon'  => self::norm( $p[0] + $dpsi ),
			'lat'  => $p[1],
			'dist' => $p[2],
		);
	}

	/**
	 * Longitude, latitude and daily motion for several bodies.
	 *
	 * @param string[]|null $bodies Subset of self::BODIES, all by default.
	 * @return array<string, array{lon: float, lat: float, speed: float, retro: bool}>
	 */
	public static function positions( float $jd_ut, ?array $bodies = null ): array {
		$out = array();
		foreach ( $bodies ?? self::BODIES as $body ) {
			$now  = self::position( $body, $jd_ut );
			$h    = 'moon' === $body ? 0.05 : 0.25;
			$prev = self::position( $body, $jd_ut - $h )['lon'];
			$next = self::position( $body, $jd_ut + $h )['lon'];
			$spd  = self::diff( $next, $prev ) / ( 2 * $h );

			$out[ $body ] = array(
				'lon'   => $now['lon'],
				'lat'   => $now['lat'],
				'speed' => $spd,
				'retro' => $spd < 0,
			);
		}
		return $out;
	}

	/** Longitude only; the cheap call used by searches. */
	public static function lon( string $body, float $jd_ut ): float {
		return self::position( $body, $jd_ut )['lon'];
	}

	/** Daily motion in degrees/day (negative when retrograde). */
	public static function speed( string $body, float $jd_ut ): float {
		$h = 'moon' === $body ? 0.05 : 0.25;
		return self::diff( self::lon( $body, $jd_ut + $h ), self::lon( $body, $jd_ut - $h ) ) / ( 2 * $h );
	}

	/** Signed shortest angular difference a − b in (−180, 180]. */
	public static function diff( float $a, float $b ): float {
		$d = fmod( $a - $b, 360.0 );
		if ( $d > 180 ) {
			$d -= 360;
		} elseif ( $d <= -180 ) {
			$d += 360;
		}
		return $d;
	}

	public static function norm( float $deg ): float {
		$deg = fmod( $deg, 360.0 );
		return $deg < 0 ? $deg + 360.0 : $deg;
	}

	/* ---------------------------------------------------------------- */

	/** Sun: geocentric longitude (deg) and distance (AU). */
	private static function sun( float $d ): array {
		$w   = 282.9404 + 4.70935e-5 * $d;
		$e   = 0.016709 - 1.151e-9 * $d;
		$m   = self::norm( 356.0470 + 0.9856002585 * $d );
		$ecc = self::kepler( $m, $e );
		$xv  = cos( deg2rad( $ecc ) ) - $e;
		$yv  = sqrt( 1 - $e * $e ) * sin( deg2rad( $ecc ) );
		$v   = rad2deg( atan2( $yv, $xv ) );
		$r   = sqrt( $xv * $xv + $yv * $yv );
		$lon = self::norm( $v + $w );
		return array(
			'lon' => $lon,
			'r'   => $r,
			'x'   => $r * cos( deg2rad( $lon ) ),
			'y'   => $r * sin( deg2rad( $lon ) ),
			'm'   => $m,
			'l'   => self::norm( $m + $w ),
		);
	}

	/** Moon: geocentric longitude, latitude (deg) and distance (Earth radii). */
	private static function moon( float $d ): array {
		$n = 125.1228 - 0.0529538083 * $d;
		$i = 5.1454;
		$w = 318.0634 + 0.1643573223 * $d;
		$a = 60.2666;
		$e = 0.054900;
		$m = self::norm( 115.3654 + 13.0649929509 * $d );

		$ecc = self::kepler( $m, $e );
		$xv  = $a * ( cos( deg2rad( $ecc ) ) - $e );
		$yv  = $a * sqrt( 1 - $e * $e ) * sin( deg2rad( $ecc ) );
		$v   = rad2deg( atan2( $yv, $xv ) );
		$r   = sqrt( $xv * $xv + $yv * $yv );

		[ $lon, $lat ] = self::to_ecliptic( $n, $i, $w, $v, $r );

		$sun = self::sun( $d );
		$ms  = deg2rad( $sun['m'] );
		$mm  = deg2rad( $m );
		$lm  = self::norm( $m + $w + $n );
		$dd  = deg2rad( $lm - $sun['l'] );
		$f   = deg2rad( $lm - $n );

		$lon += -1.274 * sin( $mm - 2 * $dd )
			+ 0.658 * sin( 2 * $dd )
			- 0.186 * sin( $ms )
			- 0.059 * sin( 2 * $mm - 2 * $dd )
			- 0.057 * sin( $mm - 2 * $dd + $ms )
			+ 0.053 * sin( $mm + 2 * $dd )
			+ 0.046 * sin( 2 * $dd - $ms )
			+ 0.041 * sin( $mm - $ms )
			- 0.035 * sin( $dd )
			- 0.031 * sin( $mm + $ms )
			- 0.015 * sin( 2 * $f - 2 * $dd )
			+ 0.011 * sin( $mm - 4 * $dd );

		$lat += -0.173 * sin( $f - 2 * $dd )
			- 0.055 * sin( $mm - $f - 2 * $dd )
			- 0.046 * sin( $mm + $f - 2 * $dd )
			+ 0.033 * sin( $f + 2 * $dd )
			+ 0.017 * sin( 2 * $mm + $f );

		$r += -0.58 * cos( $mm - 2 * $dd ) - 0.46 * cos( 2 * $dd );

		return array( self::norm( $lon ), $lat, $r );
	}

	/** Planet: geocentric longitude, latitude (deg) and distance (AU). */
	private static function planet( string $body, float $d, array $sun ): array {
		// Light-time: the planet is seen where it was dist × 0.0057755 days ago.
		$tau = 0.0;
		for ( $pass = 0; $pass < 2; $pass++ ) {
			[ $x, $y, $z ] = self::helio_xyz( $body, $d - $tau );
			$xg   = $x + $sun['x'];
			$yg   = $y + $sun['y'];
			$zg   = $z;
			$dist = sqrt( $xg * $xg + $yg * $yg + $zg * $zg );
			$tau  = $dist * 0.0057755183;
		}

		$glon = rad2deg( atan2( $yg, $xg ) );
		$glat = rad2deg( atan2( $zg, sqrt( $xg * $xg + $yg * $yg ) ) );
		// Annual aberration in longitude (κ = 20.49552″).
		$glon += -( 20.49552 / 3600.0 ) * cos( deg2rad( $sun['lon'] - $glon ) ) / cos( deg2rad( $glat ) );

		return array( self::norm( $glon ), $glat, $dist );
	}

	/** Heliocentric ecliptic rectangular coordinates (AU), equinox of date. */
	private static function helio_xyz( string $body, float $d ): array {
		if ( 'pluto' === $body ) {
			[ $lon, $lat, $r ] = self::pluto_helio( $d );
		} else {
			$el  = self::elements( $body, $d );
			$ecc = self::kepler( $el['M'], $el['e'] );
			$xv  = $el['a'] * ( cos( deg2rad( $ecc ) ) - $el['e'] );
			$yv  = $el['a'] * sqrt( 1 - $el['e'] ** 2 ) * sin( deg2rad( $ecc ) );
			$v   = rad2deg( atan2( $yv, $xv ) );
			$r   = sqrt( $xv * $xv + $yv * $yv );

			[ $lon, $lat ] = self::to_ecliptic( $el['N'], $el['i'], $el['w'], $v, $r );
			[ $dlon, $dlat ] = self::perturbations( $body, $d );
			$lon += $dlon;
			$lat += $dlat;
		}
		$lr = deg2rad( $lon );
		$br = deg2rad( $lat );
		return array( $r * cos( $lr ) * cos( $br ), $r * sin( $lr ) * cos( $br ), $r * sin( $br ) );
	}

	private static function elements( string $body, float $d ): array {
		switch ( $body ) {
			case 'mercury':
				return array( 'N' => 48.3313 + 3.24587e-5 * $d, 'i' => 7.0047 + 5.00e-8 * $d, 'w' => 29.1241 + 1.01444e-5 * $d, 'a' => 0.387098, 'e' => 0.205635 + 5.59e-10 * $d, 'M' => self::norm( 168.6562 + 4.0923344368 * $d ) );
			case 'venus':
				return array( 'N' => 76.6799 + 2.46590e-5 * $d, 'i' => 3.3946 + 2.75e-8 * $d, 'w' => 54.8910 + 1.38374e-5 * $d, 'a' => 0.723330, 'e' => 0.006773 - 1.302e-9 * $d, 'M' => self::norm( 48.0052 + 1.6021302244 * $d ) );
			case 'mars':
				return array( 'N' => 49.5574 + 2.11081e-5 * $d, 'i' => 1.8497 - 1.78e-8 * $d, 'w' => 286.5016 + 2.92961e-5 * $d, 'a' => 1.523688, 'e' => 0.093405 + 2.516e-9 * $d, 'M' => self::norm( 18.6021 + 0.5240207766 * $d ) );
			case 'jupiter':
				return array( 'N' => 100.4542 + 2.76854e-5 * $d, 'i' => 1.3030 - 1.557e-7 * $d, 'w' => 273.8777 + 1.64505e-5 * $d, 'a' => 5.20256, 'e' => 0.048498 + 4.469e-9 * $d, 'M' => self::norm( 19.8950 + 0.0830853001 * $d ) );
			case 'saturn':
				return array( 'N' => 113.6634 + 2.38980e-5 * $d, 'i' => 2.4886 - 1.081e-7 * $d, 'w' => 339.3939 + 2.97661e-5 * $d, 'a' => 9.55475, 'e' => 0.055546 - 9.499e-9 * $d, 'M' => self::norm( 316.9670 + 0.0334442282 * $d ) );
			case 'uranus':
				return array( 'N' => 74.0005 + 1.3978e-5 * $d, 'i' => 0.7733 + 1.9e-8 * $d, 'w' => 96.6612 + 3.0565e-5 * $d, 'a' => 19.18171 - 1.55e-8 * $d, 'e' => 0.047318 + 7.45e-9 * $d, 'M' => self::norm( 142.5905 + 0.011725806 * $d ) );
			case 'neptune':
				return array( 'N' => 131.7806 + 3.0173e-5 * $d, 'i' => 1.7700 - 2.55e-7 * $d, 'w' => 272.8461 - 6.027e-6 * $d, 'a' => 30.05826 + 3.313e-8 * $d, 'e' => 0.008606 + 2.15e-9 * $d, 'M' => self::norm( 260.2471 + 0.005995147 * $d ) );
		}
		throw new \InvalidArgumentException( 'Unknown body: ' . $body );
	}

	/** Jupiter–Saturn–Uranus mutual perturbations (degrees). */
	private static function perturbations( string $body, float $d ): array {
		if ( ! in_array( $body, array( 'jupiter', 'saturn', 'uranus' ), true ) ) {
			return array( 0.0, 0.0 );
		}
		$mj = deg2rad( 19.8950 + 0.0830853001 * $d );
		$ms = deg2rad( 316.9670 + 0.0334442282 * $d );
		$mu = deg2rad( 142.5905 + 0.011725806 * $d );
		$r  = static fn( float $deg ): float => deg2rad( $deg );

		if ( 'jupiter' === $body ) {
			return array(
				-0.332 * sin( 2 * $mj - 5 * $ms - $r( 67.6 ) )
				- 0.056 * sin( 2 * $mj - 2 * $ms + $r( 21 ) )
				+ 0.042 * sin( 3 * $mj - 5 * $ms + $r( 21 ) )
				- 0.036 * sin( $mj - 2 * $ms )
				+ 0.022 * cos( $mj - $ms )
				+ 0.023 * sin( 2 * $mj - 3 * $ms + $r( 52 ) )
				- 0.016 * sin( $mj - 5 * $ms - $r( 69 ) ),
				0.0,
			);
		}
		if ( 'saturn' === $body ) {
			return array(
				0.812 * sin( 2 * $mj - 5 * $ms - $r( 67.6 ) )
				- 0.229 * cos( 2 * $mj - 4 * $ms - $r( 2 ) )
				+ 0.119 * sin( $mj - 2 * $ms - $r( 3 ) )
				+ 0.046 * sin( 2 * $mj - 6 * $ms - $r( 69 ) )
				+ 0.014 * sin( $mj - 3 * $ms + $r( 32 ) ),
				-0.020 * cos( 2 * $mj - 4 * $ms - $r( 2 ) )
				+ 0.018 * sin( 2 * $mj - 6 * $ms - $r( 49 ) ),
			);
		}
		return array(
			0.040 * sin( $ms - 2 * $mu + $r( 6 ) )
			+ 0.035 * sin( $ms - 3 * $mu + $r( 33 ) )
			- 0.015 * sin( $mj - $mu + $r( 20 ) ),
			0.0,
		);
	}

	/** Pluto heliocentric ecliptic position (Schlyter fit, equinox of date). */
	private static function pluto_helio( float $d ): array {
		$s = deg2rad( 50.03 + 0.033459652 * $d );
		$p = deg2rad( 238.95 + 0.003968789 * $d );

		$lon = 238.9508 + 0.00400703 * $d
			- 19.799 * sin( $p ) + 19.848 * cos( $p )
			+ 0.897 * sin( 2 * $p ) - 4.956 * cos( 2 * $p )
			+ 0.610 * sin( 3 * $p ) + 1.211 * cos( 3 * $p )
			- 0.341 * sin( 4 * $p ) - 0.190 * cos( 4 * $p )
			+ 0.128 * sin( 5 * $p ) - 0.034 * cos( 5 * $p )
			- 0.038 * sin( 6 * $p ) + 0.031 * cos( 6 * $p )
			+ 0.020 * sin( $s - $p ) - 0.010 * cos( $s - $p );

		$lat = -3.9082
			- 5.453 * sin( $p ) - 14.975 * cos( $p )
			+ 3.527 * sin( 2 * $p ) + 1.673 * cos( 2 * $p )
			- 1.051 * sin( 3 * $p ) + 0.328 * cos( 3 * $p )
			+ 0.179 * sin( 4 * $p ) - 0.292 * cos( 4 * $p )
			+ 0.019 * sin( 5 * $p ) + 0.100 * cos( 5 * $p )
			- 0.031 * sin( 6 * $p ) - 0.026 * cos( 6 * $p )
			+ 0.011 * cos( $s - $p );

		$r = 40.72
			+ 6.68 * sin( $p ) + 6.90 * cos( $p )
			- 1.18 * sin( 2 * $p ) - 0.03 * cos( 2 * $p )
			+ 0.15 * sin( 3 * $p ) - 0.14 * cos( 3 * $p );

		// Schlyter's fit already returns the equinox of date (verified against
		// astronomy-engine), so no precession term is added here.

		return array( self::norm( $lon ), $lat, $r );
	}

	/** Orbit-plane position → heliocentric (or geocentric for the Moon) ecliptic lon/lat. */
	private static function to_ecliptic( float $n, float $i, float $w, float $v, float $r ): array {
		$nr = deg2rad( $n );
		$ir = deg2rad( $i );
		$vw = deg2rad( $v + $w );
		$xh = $r * ( cos( $nr ) * cos( $vw ) - sin( $nr ) * sin( $vw ) * cos( $ir ) );
		$yh = $r * ( sin( $nr ) * cos( $vw ) + cos( $nr ) * sin( $vw ) * cos( $ir ) );
		$zh = $r * ( sin( $vw ) * sin( $ir ) );
		return array(
			self::norm( rad2deg( atan2( $yh, $xh ) ) ),
			rad2deg( atan2( $zh, sqrt( $xh * $xh + $yh * $yh ) ) ),
		);
	}

	/** Solve Kepler's equation; angles in degrees. */
	private static function kepler( float $m, float $e ): float {
		$mr  = deg2rad( $m );
		$ecc = $mr + $e * sin( $mr ) * ( 1.0 + $e * cos( $mr ) );
		for ( $k = 0; $k < 12; $k++ ) {
			$delta = ( $ecc - $e * sin( $ecc ) - $mr ) / ( 1 - $e * cos( $ecc ) );
			$ecc  -= $delta;
			if ( abs( $delta ) < 1e-10 ) {
				break;
			}
		}
		return rad2deg( $ecc );
	}
}
