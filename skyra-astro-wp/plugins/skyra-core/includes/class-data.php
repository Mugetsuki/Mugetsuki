<?php
/**
 * Cached access to computed sky data and CMS overrides, plus Turkish formatting.
 *
 * All computed results are deterministic for a given instant, so they are
 * cached as transients keyed by engine version and time bucket.
 *
 * @package Skyra\Core
 */

namespace Skyra\Core;

use Skyra\Astro\Readings;
use Skyra\Astro\Sky;
use Skyra\Astro\Zodiac;

defined( 'ABSPATH' ) || exit;

final class Data {

	/** Bump when engine output changes to invalidate cached results. */
	private const CACHE_VERSION = 'e1';

	/** Time zone for "today" and all displayed times. Filterable. */
	public static function tz(): string {
		return (string) apply_filters( 'skyra_timezone', 'Europe/Istanbul' );
	}

	public static function zone(): \DateTimeZone {
		return new \DateTimeZone( self::tz() );
	}

	/** Current time; a filter lets tests pin "now". */
	public static function now(): int {
		return (int) apply_filters( 'skyra_now', time() );
	}

	public static function today(): string {
		return ( new \DateTimeImmutable( '@' . self::now() ) )->setTimezone( self::zone() )->format( 'Y-m-d' );
	}

	/** Sky right now, refreshed every 10 minutes. */
	public static function snapshot(): array {
		$bucket = (int) ( floor( self::now() / 600 ) * 600 );
		return self::cached(
			'now_' . $bucket,
			15 * MINUTE_IN_SECONDS,
			static function () use ( $bucket ) {
				$snap                = Sky::snapshot( $bucket, self::tz() );
				$snap['retro_until'] = Sky::retro_until( $bucket );
				return $snap;
			}
		);
	}

	/** The sky of a local day (noon), used for daily readings. */
	public static function day( ?string $date = null ): array {
		$date = $date ?? self::today();
		return self::cached( 'day_' . $date, DAY_IN_SECONDS, static fn() => Sky::day( $date, self::tz() ) );
	}

	/** Events of a local calendar year. */
	public static function year_events( int $year ): array {
		return self::cached(
			'year_' . $year,
			30 * DAY_IN_SECONDS,
			static function () use ( $year ) {
				$zone = self::zone();
				$from = ( new \DateTimeImmutable( "$year-01-01 00:00", $zone ) )->getTimestamp();
				$to   = ( new \DateTimeImmutable( ( $year + 1 ) . '-01-01 00:00', $zone ) )->getTimestamp();
				return Sky::events( $from, $to );
			}
		);
	}

	/**
	 * Next events from now.
	 *
	 * @param int           $limit Maximum number of events.
	 * @param string[]|null $types Restrict to these event types.
	 */
	public static function upcoming( int $limit = 6, ?array $types = null ): array {
		$now  = self::now();
		$year = (int) self::today();
		$all  = array_merge( self::year_events( $year ), self::year_events( $year + 1 ) );
		$out  = array();
		foreach ( $all as $e ) {
			if ( $e['unix'] < $now || ( $types && ! in_array( $e['type'], $types, true ) ) ) {
				continue;
			}
			$out[] = $e;
			if ( count( $out ) >= $limit ) {
				break;
			}
		}
		return $out;
	}

	public static function retro_year( int $year ): array {
		return self::cached( 'retro_' . $year, 30 * DAY_IN_SECONDS, static fn() => Sky::retro_year( $year, self::tz() ) );
	}

	/**
	 * Daily reading for a sign: a published editorial Horoscope post wins,
	 * otherwise the reading computed from the day's sky.
	 */
	public static function reading( string $sign, ?string $date = null ): array {
		$date     = $date ?? self::today();
		$computed = Readings::daily( $sign, self::day( $date ) );
		$post     = self::editorial_reading( $sign, $date );
		if ( ! $post ) {
			return $computed + array( 'date' => $date );
		}
		$text = trim( wp_strip_all_tags( $post->post_content ) );
		return array(
			'sign'     => $sign,
			'date'     => $date,
			'house'    => $computed['house'],
			'theme'    => get_the_title( $post ),
			'teaser'   => has_excerpt( $post ) ? get_the_excerpt( $post ) : wp_trim_words( $text, 22 ),
			'text'     => $text,
			'html'     => apply_filters( 'the_content', $post->post_content ),
			'author'   => get_the_author_meta( 'display_name', (int) $post->post_author ),
			'source'   => 'editorial',
			'computed' => $computed,
		);
	}

	public static function editorial_reading( string $sign, string $date ): ?\WP_Post {
		$posts = get_posts(
			array(
				'post_type'        => Post_Types::HOROSCOPE,
				'post_status'      => 'publish',
				'posts_per_page'   => 1,
				'no_found_rows'    => true,
				'suppress_filters' => false,
				'meta_query'       => array(
					array(
						'key'   => 'skyra_sign',
						'value' => $sign,
					),
					array(
						'key'   => 'skyra_date',
						'value' => $date,
					),
					array(
						'key'   => 'skyra_period',
						'value' => 'daily',
					),
				),
			)
		);
		return $posts[0] ?? null;
	}

	/** Moon phase interpretation: Moon Phase Content post, else the default text. */
	public static function phase_text( string $key ): array {
		$posts = get_posts(
			array(
				'post_type'      => Post_Types::MOON_PHASE,
				'post_status'    => 'publish',
				'posts_per_page' => 1,
				'no_found_rows'  => true,
				'meta_key'       => 'skyra_phase',
				'meta_value'     => $key,
			)
		);
		if ( $posts ) {
			return array(
				'text'   => wp_strip_all_tags( $posts[0]->post_excerpt ? $posts[0]->post_excerpt : $posts[0]->post_content ),
				'source' => 'editorial',
			);
		}
		return array(
			'text'   => Readings::PHASE[ $key ] ?? '',
			'source' => 'default',
		);
	}

	/** Editorial note (Astrology Event post) attached to a computed event, if any. */
	public static function event_note( array $event ): ?array {
		static $notes = null;
		if ( null === $notes ) {
			$notes = array();
			$posts = get_posts(
				array(
					'post_type'      => Post_Types::EVENT,
					'post_status'    => 'publish',
					'posts_per_page' => 200,
					'no_found_rows'  => true,
				)
			);
			foreach ( $posts as $p ) {
				$notes[] = array(
					'type' => get_post_meta( $p->ID, 'skyra_event_type', true ),
					'body' => get_post_meta( $p->ID, 'skyra_event_body', true ),
					'date' => get_post_meta( $p->ID, 'skyra_event_date', true ),
					'url'  => get_permalink( $p ),
					'post' => $p,
				);
			}
		}
		$date = self::format( $event['unix'], 'Y-m-d' );
		foreach ( $notes as $n ) {
			if ( $n['type'] !== $event['type'] || ( $n['body'] && $n['body'] !== $event['body'] ) || ! $n['date'] ) {
				continue;
			}
			if ( abs( strtotime( $n['date'] ) - strtotime( $date ) ) <= DAY_IN_SECONDS ) {
				return array(
					'url'     => $n['url'],
					'title'   => get_the_title( $n['post'] ),
					'excerpt' => get_the_excerpt( $n['post'] ),
				);
			}
		}
		return null;
	}

	/* ------------------------------------------------------------ URLs */

	public static function sign_url( string $slug ): string {
		return home_url( user_trailingslashit( 'burclar/' . $slug ) );
	}

	public static function daily_url( string $slug ): string {
		return home_url( user_trailingslashit( 'gunluk-burc-yorumlari/' . $slug ) );
	}

	public static function page_url( string $path ): string {
		return home_url( user_trailingslashit( trim( $path, '/' ) ) );
	}

	/* ------------------------------------------------------ Formatting */

	/**
	 * Date/time in the Skyra time zone with Turkish month and day names.
	 * Supported tokens: j d n m Y H i F M l D (subset of PHP date()).
	 */
	public static function format( int $unix, string $format = 'j F Y' ): string {
		$dt  = ( new \DateTimeImmutable( '@' . $unix ) )->setTimezone( self::zone() );
		$out = '';
		foreach ( str_split( $format ) as $ch ) {
			switch ( $ch ) {
				case 'F':
					$out .= Zodiac::MONTHS[ (int) $dt->format( 'n' ) ];
					break;
				case 'M':
					$out .= mb_substr( Zodiac::MONTHS[ (int) $dt->format( 'n' ) ], 0, 3 );
					break;
				case 'l':
					$out .= Zodiac::WEEKDAYS[ (int) $dt->format( 'w' ) ];
					break;
				case 'D':
					$out .= Zodiac::WEEKDAYS_SHORT[ (int) $dt->format( 'w' ) ];
					break;
				case 'j':
				case 'd':
				case 'n':
				case 'm':
				case 'Y':
				case 'H':
				case 'i':
					$out .= $dt->format( $ch );
					break;
				default:
					$out .= $ch;
			}
		}
		return $out;
	}

	/** Format a Y-m-d date string without time-zone shifts. */
	public static function format_date( string $ymd, string $format = 'j F Y' ): string {
		$dt = new \DateTimeImmutable( $ymd . ' 12:00', self::zone() );
		return self::format( $dt->getTimestamp(), $format );
	}

	/** "11 Aralık'a kadar": dative suffix follows the month name. */
	public static function until( int $unix ): string {
		$dative = array( 1 => "'a", "'a", "'a", "'a", "'a", "'a", "'a", "'a", "'e", "'e", "'a", "'a" );
		return self::format( $unix, 'j F' ) . $dative[ (int) self::format( $unix, 'n' ) ] . ' kadar';
	}

	public static function percent( float $fraction ): string {
		return '%' . (int) round( $fraction * 100 );
	}

	/* ----------------------------------------------------------- Cache */

	private static function cached( string $key, int $ttl, callable $compute ): array {
		$full  = 'skyra_' . self::CACHE_VERSION . '_' . $key;
		$value = get_transient( $full );
		if ( is_array( $value ) ) {
			return $value;
		}
		$value = $compute();
		set_transient( $full, $value, $ttl );
		return $value;
	}
}
