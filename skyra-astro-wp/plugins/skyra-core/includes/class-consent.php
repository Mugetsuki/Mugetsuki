<?php
/**
 * Consent gate for optional measurement scripts.
 *
 * WordPress.com adds Jetpack Stats (stats.wp.com → pixel.wp.com/g.gif) and
 * its performance beacon "bilmur" (s0.wp.com → pixel.wp.com/boom.gif) to
 * every page. Neither sets cookies, but both send the visitor's IP and the
 * page address to Automattic, so they wait for consent.
 *
 * The page HTML stays identical for every visitor (edge cache safe): the
 * matching <script> tags are rewritten to type="text/plain", which browsers
 * never run or fetch. consent.js turns them back into real scripts only
 * after the visitor allows the "analytics" category. Without JavaScript
 * they never run.
 *
 * @package Skyra\Core
 */

namespace Skyra\Core;

defined( 'ABSPATH' ) || exit;

final class Consent {

	/** Category => substrings matched against a script's src or id. */
	public const GATES = array(
		'analytics' => array(
			'src' => array( '//stats.wp.com/', '//s0.wp.com/wp-content/js/bilmur', '//pixel.wp.com/' ),
			'id'  => array( 'jetpack-stats-js' ),
		),
	);

	public static function init(): void {
		add_action( 'template_redirect', array( self::class, 'buffer' ), 0 );
		add_action( 'wp_enqueue_scripts', array( self::class, 'assets' ) );
		add_filter( 'render_block_skyra-theme/site-footer', array( self::class, 'footer_link' ) );
	}

	/** Gates matching scripts in the final HTML of front-end pages. */
	public static function buffer(): void {
		if ( is_admin() || wp_doing_ajax() || is_feed() || is_embed() || wp_is_serving_rest_request() ) {
			return;
		}
		ob_start( array( self::class, 'gate' ) );
	}

	public static function gate( string $html ): string {
		if ( false === stripos( $html, '<script' ) ) {
			return $html;
		}
		/**
		 * Script gates by consent category.
		 *
		 * @param array $gates Category => [ 'src' => substrings, 'id' => id prefixes ].
		 */
		$gates = (array) apply_filters( 'skyra_consent_gates', self::GATES );
		return (string) preg_replace_callback(
			'#<script\b([^>]*)>#i',
			static function ( array $m ) use ( $gates ): string {
				$attrs = $m[1];
				if ( str_contains( $attrs, 'data-skyra-consent' ) ) {
					return $m[0];
				}
				$category = self::category( $attrs, $gates );
				if ( ! $category ) {
					return $m[0];
				}
				$attrs = (string) preg_replace( '#\stype=("|\')[^"\']*\1#i', '', $attrs );
				return '<script type="text/plain" data-skyra-consent="' . esc_attr( $category ) . '"' . $attrs . '>';
			},
			$html
		);
	}

	private static function category( string $attrs, array $gates ): string {
		$src = preg_match( '#\ssrc=("|\')([^"\']+)\1#i', $attrs, $s ) ? $s[2] : '';
		$id  = preg_match( '#\sid=("|\')([^"\']+)\1#i', $attrs, $i ) ? $i[2] : '';
		foreach ( $gates as $category => $rules ) {
			foreach ( (array) ( $rules['src'] ?? array() ) as $needle ) {
				if ( '' !== $src && str_contains( $src, $needle ) ) {
					return (string) $category;
				}
			}
			foreach ( (array) ( $rules['id'] ?? array() ) as $prefix ) {
				if ( '' !== $id && str_starts_with( $id, $prefix ) ) {
					return (string) $category;
				}
			}
		}
		return '';
	}

	public static function assets(): void {
		$ver = SKYRA_CORE_VERSION;
		wp_enqueue_style( 'skyra-consent', SKYRA_CORE_URL . 'assets/css/consent.css', array(), $ver );
		wp_enqueue_script(
			'skyra-consent',
			SKYRA_CORE_URL . 'assets/js/consent.js',
			array(),
			$ver,
			array(
				'strategy'  => 'defer',
				'in_footer' => true,
			)
		);
		wp_add_inline_script(
			'skyra-consent',
			'window.skyraConsentConfig=' . wp_json_encode(
				array(
					'version' => 1,
					'cookies' => Data::page_url( 'cerez-politikasi' ),
					'privacy' => Data::page_url( 'kvkk' ),
				)
			) . ';',
			'before'
		);
	}

	/** "Çerez tercihleri" next to the GeoNames credit, always reachable. */
	public static function footer_link( string $html ): string {
		$link = '<a href="#cerez-tercihleri" data-skyra-consent-open>Çerez tercihleri</a>';
		$out  = preg_replace( '#(\(CC BY 4\.0\))(\s*</p>)#', '$1 · ' . $link . '$2', $html, 1, $count );
		return $count ? (string) $out : $html;
	}
}
