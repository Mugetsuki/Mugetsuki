<?php
/**
 * REST API: skyra/v1.
 *
 * Public sky data is cacheable. Personal calculations (chart, rising, moon
 * sign) are POST-only, marked no-store and never persisted or logged.
 *
 * @package Skyra\Core
 */

namespace Skyra\Core;

use Skyra\Astro\Chart;
use Skyra\Astro\Readings;
use Skyra\Astro\Zodiac;

defined( 'ABSPATH' ) || exit;

final class Rest {

	public const NS = 'skyra/v1';

	public static function init(): void {
		add_action( 'rest_api_init', array( self::class, 'routes' ) );
	}

	public static function routes(): void {
		$public = '__return_true';

		register_rest_route(
			self::NS,
			'/sky',
			array(
				'methods'             => 'GET',
				'permission_callback' => $public,
				'callback'            => static fn() => self::cacheable( Data::snapshot(), 600 ),
			)
		);
		register_rest_route(
			self::NS,
			'/events',
			array(
				'methods'             => 'GET',
				'permission_callback' => $public,
				'args'                => array(
					'year' => array(
						'type'    => 'integer',
						'minimum' => 1950,
						'maximum' => 2099,
					),
				),
				'callback'            => static function ( \WP_REST_Request $r ) {
					$year = (int) ( $r['year'] ?? (int) Data::today() );
					return self::cacheable( Data::year_events( $year ), DAY_IN_SECONDS );
				},
			)
		);
		register_rest_route(
			self::NS,
			'/horoscope/(?P<sign>[a-z]+)',
			array(
				'methods'             => 'GET',
				'permission_callback' => $public,
				'args'                => array(
					'sign' => array( 'enum' => Zodiac::slugs() ),
					'date' => array(
						'type'    => 'string',
						'pattern' => '^\d{4}-\d{2}-\d{2}$',
					),
				),
				'callback'            => static function ( \WP_REST_Request $r ) {
					$reading = Data::reading( $r['sign'], $r['date'] ?? null );
					unset( $reading['computed'] );
					return self::cacheable( $reading, HOUR_IN_SECONDS );
				},
			)
		);
		register_rest_route(
			self::NS,
			'/places',
			array(
				'methods'             => 'GET',
				'permission_callback' => $public,
				'args'                => array(
					'q' => array(
						'type'      => 'string',
						'required'  => true,
						'minLength' => 2,
						'maxLength' => 60,
					),
				),
				'callback'            => static fn( \WP_REST_Request $r ) => self::cacheable( Places::search( (string) $r['q'] ), DAY_IN_SECONDS ),
			)
		);
		register_rest_route(
			self::NS,
			'/compatibility',
			array(
				'methods'             => 'GET',
				'permission_callback' => $public,
				'args'                => array(
					'a' => array(
						'enum'     => Zodiac::slugs(),
						'required' => true,
					),
					'b' => array(
						'enum'     => Zodiac::slugs(),
						'required' => true,
					),
				),
				'callback'            => static function ( \WP_REST_Request $r ) {
					$data         = Readings::compatibility( $r['a'], $r['b'] );
					$data['html'] = Results::compatibility( $data, self::level( $r ) );
					return self::cacheable( $data, WEEK_IN_SECONDS );
				},
			)
		);

		$birth_args = array(
			'date'  => array(
				'type'     => 'string',
				'required' => true,
			),
			'time'  => array( 'type' => 'string' ),
			'place' => array( 'type' => 'integer' ),
			'lat'   => array(
				'type'    => 'number',
				'minimum' => -90,
				'maximum' => 90,
			),
			'lon'   => array(
				'type'    => 'number',
				'minimum' => -180,
				'maximum' => 180,
			),
			'tz'      => array( 'type' => 'string' ),
			'variant' => array( 'enum' => array( 'full', 'compact', 'rising' ) ),
			'level'   => array( 'type' => 'integer' ),
		);
		foreach ( array(
			'/chart'     => 'chart',
			'/moon-sign' => 'moon_sign',
		) as $route => $method ) {
			register_rest_route(
				self::NS,
				$route,
				array(
					'methods'             => 'POST',
					'permission_callback' => $public,
					'args'                => $birth_args,
					'callback'            => array( self::class, $method ),
				)
			);
		}
	}

	/** Full natal chart with interpretations and the SVG wheel. */
	public static function chart( \WP_REST_Request $r ) {
		$input = self::birth_input( $r );
		if ( is_wp_error( $input ) ) {
			return $input;
		}
		try {
			$chart = Chart::natal( $input );
		} catch ( \InvalidArgumentException $e ) {
			return self::invalid( $e->getMessage() );
		}

		foreach ( $chart['bodies'] as $key => &$b ) {
			$b['text'] = Readings::placement( $key, $b['sign'], $b['house'] );
		}
		unset( $b );
		foreach ( $chart['aspects'] as &$a ) {
			$a['text'] = Readings::natal_aspect( $a );
		}
		unset( $a );

		$chart['place'] = $input['label'];
		$chart['big3']  = array(
			'sun'    => array(
				'sign' => $chart['bodies']['sun']['sign'],
				'text' => Zodiac::by_slug( $chart['bodies']['sun']['sign'] )['line'],
			),
			'moon'   => array(
				'sign' => $chart['bodies']['moon']['sign'],
				'text' => Readings::MOON_SIGN[ $chart['bodies']['moon']['sign'] ],
			),
			'rising' => $chart['angles'] ? array(
				'sign' => $chart['angles']['asc']['sign'],
				'text' => Readings::RISING[ $chart['angles']['asc']['sign'] ],
			) : null,
		);
		$chart['svg']   = Wheel::svg(
			$chart['bodies'],
			array(
				'asc'     => $chart['angles']['asc']['lon'] ?? null,
				'mc'      => $chart['angles']['mc']['lon'] ?? null,
				'cusps'   => $chart['houses']['cusps'] ?? null,
				'aspects' => $chart['aspects'],
				'title'   => 'Doğum haritası',
				'desc'    => 'Gezegenlerin burç çemberindeki konumları ve aralarındaki açılar. Ayrıntılar yandaki tabloda.',
				'class'   => 'is-natal',
			)
		);
		$chart['html'] = match ( $r['variant'] ?? 'full' ) {
			'compact' => Results::chart_compact( $chart ),
			'rising'  => Results::rising( $chart, self::level( $r ) ),
			default   => Results::chart( $chart, self::level( $r ) ),
		};
		return self::private_response( $chart );
	}

	/** Moon sign, handling an unknown birth time honestly. */
	public static function moon_sign( \WP_REST_Request $r ) {
		$input = self::birth_input( $r );
		if ( is_wp_error( $input ) ) {
			return $input;
		}
		try {
			$day = Chart::moon_signs_for_day( $input['date'], $input['tz'] );
			if ( $input['time'] ) {
				$chart = Chart::natal( $input );
				$sign  = $chart['bodies']['moon']['sign'];
				$out   = array(
					'certain' => true,
					'sign'    => $sign,
					'degree'  => $chart['bodies']['moon']['degree'],
				);
			} else {
				$out = array(
					'certain' => 1 === count( $day['signs'] ),
					'sign'    => $day['signs'][0],
					'degree'  => null,
				);
			}
		} catch ( \InvalidArgumentException $e ) {
			return self::invalid( $e->getMessage() );
		}
		$out['day']   = $day;
		$out['texts'] = array();
		foreach ( array_unique( array_merge( array( $out['sign'] ), $day['signs'] ) ) as $slug ) {
			$out['texts'][ $slug ] = Readings::MOON_SIGN[ $slug ];
		}
		$out['place'] = $input['label'];
		$out['html']  = Results::moon_sign( $out, self::level( $r ) );
		return self::private_response( $out );
	}

	/**
	 * Run a tool without HTTP (no-JS form POST path). Returns the same data
	 * as the REST endpoint, or a WP_Error.
	 *
	 * @param string $tool   chart|moon-sign|compatibility.
	 * @param array  $params Raw form values.
	 * @return array|\WP_Error
	 */
	public static function compute( string $tool, array $params ) {
		$req = new \WP_REST_Request( 'POST' );
		foreach ( array( 'date', 'time', 'place', 'place_q', 'variant', 'level', 'a', 'b' ) as $key ) {
			if ( isset( $params[ $key ] ) && '' !== $params[ $key ] ) {
				$req->set_param( $key, sanitize_text_field( (string) $params[ $key ] ) );
			}
		}
		if ( ! empty( $params['time_unknown'] ) ) {
			$req->set_param( 'time', '' );
		}
		if ( 'compatibility' === $tool ) {
			$a = (string) $req['a'];
			$b = (string) $req['b'];
			if ( ! Zodiac::by_slug( $a ) || ! Zodiac::by_slug( $b ) ) {
				return self::invalid( 'sign' );
			}
			$data         = Readings::compatibility( $a, $b );
			$data['html'] = Results::compatibility( $data, self::level( $req ) );
			return $data;
		}
		$res = 'moon-sign' === $tool ? self::moon_sign( $req ) : self::chart( $req );
		return $res instanceof \WP_REST_Response ? $res->get_data() : $res;
	}

	private static function level( \WP_REST_Request $r ): int {
		return max( 2, min( 4, (int) ( $r['level'] ?? 2 ) ) );
	}

	/**
	 * Normalise birth data: a place id from our dataset, or explicit coordinates + zone.
	 *
	 * @return array|\WP_Error
	 */
	private static function birth_input( \WP_REST_Request $r ) {
		$date = (string) $r['date'];
		$time = trim( (string) ( $r['time'] ?? '' ) );
		if ( ! $r['place'] && $r['place_q'] ) {
			// No-JS path: take the best match for the typed place name.
			$match = Places::search( (string) $r['place_q'], 1 );
			$r->set_param( 'place', $match ? $match[0]['id'] : 0 );
		}
		if ( $r['place'] ) {
			$place = Places::get( (int) $r['place'] );
			if ( ! $place ) {
				return self::invalid( 'place' );
			}
			$lat   = $place['lat'];
			$lon   = $place['lon'];
			$tz    = $place['tz'];
			$label = $place['label'];
		} elseif ( null !== $r['lat'] && null !== $r['lon'] && $r['tz'] ) {
			$lat   = (float) $r['lat'];
			$lon   = (float) $r['lon'];
			$tz    = (string) $r['tz'];
			$label = sprintf( '%.2f, %.2f', $lat, $lon );
			if ( ! in_array( $tz, \DateTimeZone::listIdentifiers(), true ) ) {
				return self::invalid( 'tz' );
			}
		} else {
			return self::invalid( 'place' );
		}
		return array(
			'date'  => $date,
			'time'  => '' === $time ? null : $time,
			'lat'   => $lat,
			'lon'   => $lon,
			'tz'    => $tz,
			'label' => $label,
		);
	}

	private const MESSAGES = array(
		'date'       => 'Doğum tarihini gün, ay ve yıl olarak gir.',
		'date_range' => 'Hesaplama 1900–2100 arasındaki tarihler için yapılabiliyor.',
		'time'       => 'Doğum saatini saat:dakika biçiminde gir (ör. 14:30).',
		'tz'         => 'Bu konumun saat dilimi bulunamadı.',
		'place'      => 'Doğum yerini listeden seç.',
		'sign'       => 'İki burcu da seç.',
	);

	private static function invalid( string $field ): \WP_Error {
		return new \WP_Error(
			'skyra_invalid_' . $field,
			self::MESSAGES[ $field ] ?? 'Bilgileri kontrol edip yeniden dene.',
			array(
				'status' => 400,
				'field'  => $field,
			)
		);
	}

	private static function cacheable( $data, int $ttl ): \WP_REST_Response {
		$res = new \WP_REST_Response( $data );
		$res->header( 'Cache-Control', 'public, max-age=' . $ttl );
		return $res;
	}

	private static function private_response( $data ): \WP_REST_Response {
		$res = new \WP_REST_Response( $data );
		$res->header( 'Cache-Control', 'no-store, private' );
		return $res;
	}
}
