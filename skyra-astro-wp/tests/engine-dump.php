<?php
/**
 * Dumps engine output as JSON for tests/engine-accuracy.mjs.
 * Usage: php tests/engine-dump.php <repo-root>
 */
define( 'SKYRA_ENGINE_STANDALONE', true );
foreach ( array( 'ephemeris', 'zodiac', 'houses', 'events', 'chart', 'readings', 'sky' ) as $f ) {
	require $argv[1] . "/plugins/skyra-core/includes/astro/class-$f.php";
}
use Skyra\Astro\Chart;
use Skyra\Astro\Ephemeris as E;
use Skyra\Astro\Events as V;

$positions = array();
// 400 instants spread over 1900–2100 at varying times of day.
for ( $i = 0; $i < 400; $i++ ) {
	$unix = -2208988800 + $i * ( 6311433600 / 400 ) + ( $i * 7919 % 86400 );
	$row  = array( 't' => $unix );
	foreach ( E::BODIES as $b ) {
		$p         = E::position( $b, E::jd( $unix ) );
		$row[ $b ] = array( $p['lon'], $p['lat'] );
	}
	$positions[] = $row;
}
$s = E::jd( strtotime( '2024-01-01T00:00:00Z' ) );
$e = E::jd( strtotime( '2028-01-01T00:00:00Z' ) );
$stations = array();
foreach ( V::MOVERS as $b ) {
	$stations[ $b ] = V::stations( $b, $s, $e );
}
$ingress = array();
foreach ( array( 'sun', 'mercury', 'mars', 'jupiter' ) as $b ) {
	$ingress[ $b ] = V::ingresses( $b, E::jd( strtotime( '2026-01-01T00:00:00Z' ) ), E::jd( strtotime( '2027-01-01T00:00:00Z' ) ) );
}
$istanbul = Chart::natal( array( 'date' => '1990-05-15', 'time' => '14:30', 'lat' => 41.0138, 'lon' => 28.9497, 'tz' => 'Europe/Istanbul' ) );
echo json_encode(
	array(
		'positions' => $positions,
		'lunations' => V::lunations( $s, $e ),
		'stations'  => $stations,
		'ingress'   => $ingress,
		'phase'     => V::moon_phase( E::jd( strtotime( '2026-09-30T12:00:00Z' ) ) ),
		'natal'     => array(
			'input' => $istanbul['input'],
			'asc'   => $istanbul['angles']['asc']['lon'],
			'mc'    => $istanbul['angles']['mc']['lon'],
			'cusps' => $istanbul['houses']['cusps'],
		),
		'moonday'   => Chart::moon_signs_for_day( '1990-05-15', 'Europe/Istanbul' ),
	)
);
