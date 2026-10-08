<?php
/**
 * Light/dark mode when the site runs a theme other than Skyra Astro (which
 * ships its own toggle). Applies the saved or system choice before first
 * paint, adds a toggle next to the header navigation (or a floating one if
 * the header has no navigation block) and loads the dark palette.
 *
 * @package Skyra\Core
 */

namespace Skyra\Core;

defined( 'ABSPATH' ) || exit;

final class Color_Mode {

	private static bool $placed = false;

	public static function init(): void {
		add_action( 'after_setup_theme', array( self::class, 'boot' ) );
	}

	public static function boot(): void {
		/**
		 * Whether Skyra Core should provide light/dark mode for the active theme.
		 *
		 * @param bool $enabled False when the Skyra Astro theme handles it itself.
		 */
		if ( ! apply_filters( 'skyra_color_mode', ! defined( 'Skyra\Theme\VERSION' ) ) ) {
			return;
		}
		add_action( 'wp_head', array( self::class, 'head' ), 1 );
		add_action( 'wp_enqueue_scripts', array( self::class, 'assets' ) );
		add_filter( 'render_block_core/navigation', array( self::class, 'after_navigation' ) );
		add_action( 'wp_footer', array( self::class, 'fallback' ) );
	}

	/** Resolves the mode before first paint so the page never flashes. */
	public static function head(): void {
		echo "<script>(function(d){var t;try{t=localStorage.getItem('skyra-theme');}catch(e){}if(t!=='dark'&&t!=='light'){t=window.matchMedia&&matchMedia('(prefers-color-scheme: dark)').matches?'dark':'light';}d.setAttribute('data-theme',t);})(document.documentElement);</script>\n";
	}

	public static function assets(): void {
		$ver = SKYRA_CORE_VERSION;
		wp_enqueue_style( 'skyra-color-mode', SKYRA_CORE_URL . 'assets/css/color-mode.css', array(), $ver );
		wp_enqueue_script(
			'skyra-color-mode',
			SKYRA_CORE_URL . 'assets/js/color-mode.js',
			array(),
			$ver,
			array(
				'strategy'  => 'defer',
				'in_footer' => true,
			)
		);
	}

	/** Puts the toggle right after the first navigation block (the header's). */
	public static function after_navigation( string $html ): string {
		if ( self::$placed || is_admin() || wp_is_serving_rest_request() ) {
			return $html;
		}
		self::$placed = true;
		return $html . self::button( '' );
	}

	public static function fallback(): void {
		if ( ! self::$placed ) {
			echo self::button( 'is-floating' ); // phpcs:ignore WordPress.Security.EscapeOutput
		}
	}

	private static function button( string $class ): string {
		return sprintf(
			'<button type="button" class="sk-mode-toggle %s" data-mode-toggle aria-label="Koyu temaya geç" hidden>%s%s</button>',
			esc_attr( $class ),
			Icons::svg( 'theme-dark', array( 'size' => 20 ) ),
			Icons::svg( 'theme-light', array( 'size' => 20 ) )
		);
	}
}
