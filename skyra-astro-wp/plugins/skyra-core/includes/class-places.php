<?php
/**
 * Birth place lookup against the bundled GeoNames extract.
 *
 * The dataset stays on the server; the browser only receives the handful of
 * matches for what the visitor typed.
 *
 * @package Skyra\Core
 */

namespace Skyra\Core;

defined( 'ABSPATH' ) || exit;

final class Places {

	private static ?array $data = null;

	private static function data(): array {
		if ( null === self::$data ) {
			$json       = file_get_contents( SKYRA_CORE_DIR . 'data/places.json' ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
			self::$data = json_decode( (string) $json, true ) ?: array(
				'zones'  => array(),
				'places' => array(),
			);
		}
		return self::$data;
	}

	/** Lower-case, Turkish-aware, accent-free form used for matching. */
	public static function fold( string $text ): string {
		$text = str_replace( array( 'İ', 'I' ), array( 'i', 'ı' ), $text );
		$text = mb_strtolower( $text, 'UTF-8' );
		$text = strtr(
			$text,
			array(
				'ı' => 'i',
				'ç' => 'c',
				'ğ' => 'g',
				'ö' => 'o',
				'ş' => 's',
				'ü' => 'u',
				'â' => 'a',
				'î' => 'i',
				'û' => 'u',
				'á' => 'a',
				'à' => 'a',
				'ä' => 'a',
				'ã' => 'a',
				'å' => 'a',
				'é' => 'e',
				'è' => 'e',
				'ê' => 'e',
				'ë' => 'e',
				'í' => 'i',
				'ì' => 'i',
				'ï' => 'i',
				'ó' => 'o',
				'ò' => 'o',
				'ô' => 'o',
				'õ' => 'o',
				'ø' => 'o',
				'ú' => 'u',
				'ù' => 'u',
				'ñ' => 'n',
				'ł' => 'l',
				'ś' => 's',
				'š' => 's',
				'ž' => 'z',
				'ź' => 'z',
				'ż' => 'z',
				'č' => 'c',
				'ć' => 'c',
				'ř' => 'r',
				'ý' => 'y',
				'ă' => 'a',
				'ș' => 's',
				'ş' => 's',
				'ț' => 't',
				'ő' => 'o',
				'ű' => 'u',
				'ə' => 'e',
			)
		);
		if ( class_exists( '\Normalizer' ) ) {
			$norm = \Normalizer::normalize( $text, \Normalizer::FORM_KD );
			if ( false !== $norm ) {
				$text = (string) preg_replace( '/\p{Mn}/u', '', $norm );
			}
		}
		return trim( (string) preg_replace( '/\s+/', ' ', $text ) );
	}

	/**
	 * Best matches for a query: prefix matches on the name first, then on any
	 * search key, then substring matches; Türkiye first, larger places first.
	 *
	 * @return array<int, array{id: int, name: string, label: string, country: string, lat: float, lon: float, tz: string}>
	 */
	public static function search( string $query, int $limit = 8 ): array {
		$q = self::fold( $query );
		if ( mb_strlen( $q ) < 2 ) {
			return array();
		}
		$d      = self::data();
		$scored = array();
		foreach ( $d['places'] as $i => $p ) {
			$name = self::fold( $p[1] );
			$keys = $p[8];
			if ( str_starts_with( $name, $q ) ) {
				$score = 3;
			} elseif ( str_starts_with( $keys, $q ) || str_contains( $keys, ' ' . $q ) ) {
				$score = 2;
			} elseif ( str_contains( $keys, $q ) ) {
				$score = 1;
			} else {
				continue;
			}
			$scored[] = array( $score, 'TR' === $p[3] ? 1 : 0, $p[7], $i );
		}
		usort( $scored, static fn( $a, $b ) => array( $b[0], $b[1], $b[2] ) <=> array( $a[0], $a[1], $a[2] ) );

		$out = array();
		foreach ( array_slice( $scored, 0, $limit ) as $s ) {
			$out[] = self::shape( $d['places'][ $s[3] ], $d['zones'] );
		}
		return $out;
	}

	public static function get( int $id ): ?array {
		$d = self::data();
		foreach ( $d['places'] as $p ) {
			if ( (int) $p[0] === $id ) {
				return self::shape( $p, $d['zones'] );
			}
		}
		return null;
	}

	private static function shape( array $p, array $zones ): array {
		$sub = $p[2] ? $p[2] : ( 'TR' === $p[3] ? 'Türkiye' : $p[3] );
		return array(
			'id'      => (int) $p[0],
			'name'    => $p[1],
			'sub'     => $sub,
			'label'   => $p[1] . ', ' . $sub,
			'country' => $p[3],
			'lat'     => (float) $p[4],
			'lon'     => (float) $p[5],
			'tz'      => $zones[ $p[6] ] ?? 'UTC',
		);
	}
}
