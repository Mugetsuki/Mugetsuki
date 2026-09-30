<?php
/**
 * Registers the Skyra blocks, their shared stylesheet and scripts.
 *
 * Every block is dynamic (server-rendered) so live sky data is always
 * current and fully present in the HTML for search engines. Scripts are
 * only enqueued on pages that contain a block which needs them.
 *
 * @package Skyra\Core
 */

namespace Skyra\Core;

defined( 'ABSPATH' ) || exit;

final class Blocks {

	public static function init(): void {
		add_action( 'init', array( self::class, 'register' ) );
		add_filter( 'block_categories_all', array( self::class, 'category' ) );
	}

	public static function register(): void {
		$ver = SKYRA_CORE_VERSION;
		wp_register_style( 'skyra-blocks', SKYRA_CORE_URL . 'assets/css/blocks.css', array(), $ver );
		wp_style_add_data( 'skyra-blocks', 'path', SKYRA_CORE_DIR . 'assets/css/blocks.css' );

		wp_register_script(
			'skyra-ui',
			SKYRA_CORE_URL . 'assets/js/ui.js',
			array(),
			$ver,
			array(
				'strategy'  => 'defer',
				'in_footer' => true,
			)
		);
		wp_register_script(
			'skyra-tools',
			SKYRA_CORE_URL . 'assets/js/tools.js',
			array(),
			$ver,
			array(
				'strategy'  => 'defer',
				'in_footer' => true,
			)
		);
		$config = 'window.skyraConfig=' . wp_json_encode(
			array(
				'rest'      => esc_url_raw( rest_url( Rest::NS . '/' ) ),
				'chartPage' => Data::page_url( 'dogum-haritasi' ),
			)
		) . ';';
		wp_add_inline_script( 'skyra-ui', $config, 'before' );
		wp_add_inline_script( 'skyra-tools', $config, 'before' );

		wp_register_script(
			'skyra-editor',
			SKYRA_CORE_URL . 'assets/js/editor.js',
			array( 'wp-blocks', 'wp-element', 'wp-block-editor', 'wp-components', 'wp-server-side-render' ),
			$ver,
			true
		);

		$names = array();
		foreach ( glob( SKYRA_CORE_DIR . 'blocks/*/block.json' ) as $file ) {
			$type = register_block_type( dirname( $file ) );
			if ( $type ) {
				$names[] = $type->name;
			}
		}
		wp_add_inline_script( 'skyra-editor', 'window.skyraBlocks=' . wp_json_encode( $names ) . ';', 'before' );
	}

	public static function category( array $cats ): array {
		array_unshift(
			$cats,
			array(
				'slug'  => 'skyra',
				'title' => 'Skyra Astro',
				'icon'  => null,
			)
		);
		return $cats;
	}

	/** Attribute value with a fallback when the editor left it empty. */
	public static function attr( array $attributes, string $key, string $fallback = '' ): string {
		$value = isset( $attributes[ $key ] ) ? trim( (string) $attributes[ $key ] ) : '';
		return '' !== $value ? $value : $fallback;
	}

	/** True when this request is a no-JS form POST for the given tool. */
	public static function posted( string $tool ): bool {
		// phpcs:ignore WordPress.Security.NonceVerification.Missing -- read-only calculation, nothing is stored.
		return 'POST' === ( $_SERVER['REQUEST_METHOD'] ?? '' ) && isset( $_POST['skyra_tool'] ) && $tool === $_POST['skyra_tool'];
	}

	/** Posted values for the no-JS path. */
	public static function post_values(): array {
		// phpcs:ignore WordPress.Security.NonceVerification.Missing -- see posted().
		return array_map( static fn( $v ) => is_string( $v ) ? sanitize_text_field( wp_unslash( $v ) ) : '', $_POST );
	}
}
