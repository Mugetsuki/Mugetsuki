<?php
/**
 * Skyra Astro theme: assets, menus, header/footer blocks and small
 * front-end integrations. Content model and tools live in Skyra Core.
 *
 * @package Skyra\Theme
 */

namespace Skyra\Theme;

defined( 'ABSPATH' ) || exit;

const VERSION = '1.0.1';

add_action(
	'after_setup_theme',
	static function () {
		add_theme_support( 'editor-styles' );
		add_editor_style( 'assets/css/skyra.css' );
		add_theme_support( 'responsive-embeds' );
		add_theme_support( 'post-thumbnails' );
		register_nav_menus(
			array(
				'primary'        => 'Ana menü',
				'footer-explore' => 'Footer: Keşfet',
				'footer-company' => 'Footer: Kurumsal',
			)
		);
	}
);

add_action(
	'init',
	static function () {
		register_block_type( __DIR__ . '/blocks/site-header' );
		register_block_type( __DIR__ . '/blocks/site-footer' );
	}
);

add_action(
	'wp_enqueue_scripts',
	static function () {
		wp_enqueue_style( 'skyra', get_theme_file_uri( 'assets/css/skyra.css' ), array(), VERSION );
		wp_enqueue_script(
			'skyra',
			get_theme_file_uri( 'assets/js/skyra.js' ),
			array(),
			VERSION,
			array(
				'strategy'  => 'defer',
				'in_footer' => true,
			)
		);
	}
);

/**
 * Before first paint: apply the saved theme choice (no flash) and mark JS.
 * Font preloads: the Latin subsets used by the first screen, plus DM Sans
 * Latin Extended because Turkish body copy needs ş, ğ and İ immediately.
 */
add_action(
	'wp_head',
	static function () {
		echo "<script>(function(d){d.classList.add('js');try{var t=localStorage.getItem('skyra-theme');if(t==='dark'||t==='light'){d.setAttribute('data-theme',t);}}catch(e){}})(document.documentElement);</script>\n";
		foreach ( array( 'quicksand-latin', 'dmsans-latin', 'dmsans-latin-ext' ) as $font ) {
			printf( '<link rel="preload" href="%s" as="font" type="font/woff2" crossorigin>' . "\n", esc_url( get_theme_file_uri( "assets/fonts/{$font}.woff2" ) ) );
		}
		echo '<meta name="theme-color" content="#F8F5EF" media="(prefers-color-scheme: light)">' . "\n";
		echo '<meta name="theme-color" content="#17131F" media="(prefers-color-scheme: dark)">' . "\n";
		if ( ! has_site_icon() ) {
			printf( '<link rel="icon" type="image/png" href="%s">' . "\n", esc_url( get_theme_file_uri( 'assets/img/skyra-mark.png' ) ) );
		}
	},
	1
);

// All content is Turkish, whatever the admin language is.
add_filter(
	'language_attributes',
	static function ( string $output ): string {
		return str_contains( $output, 'lang="tr' ) ? $output : (string) preg_replace( '/lang="[^"]*"/', 'lang="tr"', $output );
	}
);

add_filter(
	'body_class',
	static function ( array $classes ): array {
		if ( is_front_page() ) {
			$classes[] = 'is-front';
		}
		return $classes;
	}
);

// Performance: no emoji polyfill, no oEmbed discovery on the front end.
remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
remove_action( 'wp_print_styles', 'print_emoji_styles' );
remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );

// Posts without a featured image get a brand cover instead of an empty slot.
add_filter(
	'post_thumbnail_html',
	static function ( $html, $post_id ) {
		if ( $html || is_admin() || 'post' !== get_post_type( $post_id ) || ! class_exists( '\Skyra\Core\View' ) ) {
			return $html;
		}
		return \Skyra\Core\View::cover( get_post( $post_id ), '4-3' );
	},
	10,
	2
);

// Search results title in Turkish regardless of the admin language.
add_filter(
	'render_block_core/query-title',
	static function ( string $content, array $block ): string {
		if ( 'search' !== ( $block['attrs']['type'] ?? '' ) ) {
			return $content;
		}
		return sprintf( '<h1 class="wp-block-query-title sk-page__title">“%s” için sonuçlar</h1>', esc_html( get_search_query() ) );
	},
	10,
	2
);

// Front-end strings from core that visitors see, in Turkish before the language pack is installed.
add_filter(
	'gettext',
	static function ( string $translation, string $text, string $domain ): string {
		if ( 'default' !== $domain || is_admin() || str_starts_with( get_locale(), 'tr' ) ) {
			return $translation;
		}
		$tr = array(
			'Skip to content' => 'İçeriğe atla',
			'Search'          => 'Ara',
			'Next Page'       => 'Sonraki sayfa',
			'Previous Page'   => 'Önceki sayfa',
		);
		return $tr[ $text ] ?? $translation;
	},
	10,
	3
);

// Post dates in Turkish even when the site language is not (yet) Turkish.
add_filter(
	'render_block_core/post-date',
	static function ( string $content ): string {
		if ( str_starts_with( get_locale(), 'tr' ) || ! class_exists( '\\Skyra\\Core\\Data' ) ) {
			return $content;
		}
		return (string) preg_replace_callback(
			'/(<time datetime="([^"]+)"[^>]*>)[^<]*(<\/time>)/',
			static fn( $m ) => $m[1] . esc_html( \Skyra\Core\Data::format( (int) strtotime( $m[2] ), 'j F Y' ) ) . $m[3],
			$content
		);
	}
);

// The blog excerpt block should not append "Read more".
add_filter( 'excerpt_more', static fn() => '…' );

/**
 * Logo lockup: the preserved cat emblem on its cream tile + live Quicksand
 * wordmark (Brand Kit §09). The emblem file is never recoloured or inverted.
 */
function logo( bool $link = true ): string {
	$img  = sprintf(
		'<span class="sk-logo__mark"><img src="%s" width="184" height="184" alt="" decoding="async"></span>',
		esc_url( add_query_arg( 'ver', VERSION, get_theme_file_uri( 'assets/img/skyra-mark.png' ) ) )
	);
	$text = '<span class="sk-logo__text"><span class="sk-logo__name">skyra</span> <span class="sk-logo__sub">ASTRO</span></span>';
	if ( ! $link ) {
		return '<span class="sk-logo">' . $img . $text . '</span>';
	}
	return sprintf( '<a class="sk-logo" href="%s" rel="home">%s%s<span class="sk-vh"> ana sayfa</span></a>', esc_url( home_url( '/' ) ), $img, $text );
}

/** Theme icon helper: Skyra Core's set when active, otherwise nothing. */
function icon( string $name, int $size = 22 ): string {
	return function_exists( '\Skyra\Core\icon' ) ? \Skyra\Core\icon( $name, array( 'size' => $size ) ) : '';
}

/**
 * Menu for a location, falling back to the menu Setup created by name, then
 * to a plain list of the key pages.
 */
function menu( string $location, string $fallback_name, string $class ): string {
	$args = array(
		'container'   => false,
		'menu_class'  => $class,
		'depth'       => 1,
		'echo'        => false,
		'fallback_cb' => false,
	);
	if ( has_nav_menu( $location ) ) {
		return (string) wp_nav_menu( $args + array( 'theme_location' => $location ) );
	}
	$named = wp_get_nav_menu_object( $fallback_name );
	if ( $named ) {
		return (string) wp_nav_menu( $args + array( 'menu' => $named ) );
	}
	$pages = array(
		'Burçlar'           => '/burclar/',
		'Doğum Haritası'    => '/dogum-haritasi/',
		'Astroloji Takvimi' => '/astroloji-takvimi/',
		'Blog'              => '/blog/',
	);
	$out = '<ul class="' . esc_attr( $class ) . '">';
	foreach ( $pages as $label => $path ) {
		$out .= sprintf( '<li class="menu-item"><a href="%s">%s</a></li>', esc_url( home_url( $path ) ), esc_html( $label ) );
	}
	return $out . '</ul>';
}

function setting( string $key, string $fallback ): string {
	return class_exists( '\Skyra\Core\Settings' ) ? \Skyra\Core\Settings::get( $key ) : $fallback;
}
