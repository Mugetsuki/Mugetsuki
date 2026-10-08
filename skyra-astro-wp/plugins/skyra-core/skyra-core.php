<?php
/**
 * Plugin Name:       Skyra Core
 * Plugin URI:        https://skyraastro.com/
 * Description:       Skyra Astro içerik modeli (burçlar, yorumlar, gökyüzü olayları), astroloji hesaplama motoru, REST API ve dinamik bloklar.
 * Version:           1.1.0
 * Requires at least: 6.6
 * Requires PHP:      8.1
 * Author:            Skyra Astro
 * License:           GPL-2.0-or-later
 * Text Domain:       skyra
 *
 * @package Skyra\Core
 */

namespace Skyra\Core;

defined( 'ABSPATH' ) || exit;

define( 'SKYRA_CORE_VERSION', '1.1.0' );
define( 'SKYRA_CORE_FILE', __FILE__ );
define( 'SKYRA_CORE_DIR', plugin_dir_path( __FILE__ ) );
define( 'SKYRA_CORE_URL', plugin_dir_url( __FILE__ ) );

foreach ( array( 'ephemeris', 'zodiac', 'houses', 'events', 'chart', 'readings', 'sky' ) as $skyra_file ) {
	require_once SKYRA_CORE_DIR . "includes/astro/class-{$skyra_file}.php";
}
foreach ( array( 'data', 'places', 'icons', 'wheel', 'view', 'results', 'post-types', 'rest', 'blocks', 'forms', 'settings', 'seo', 'setup', 'color-mode', 'consent' ) as $skyra_file ) {
	require_once SKYRA_CORE_DIR . "includes/class-{$skyra_file}.php";
}
unset( $skyra_file );

Post_Types::init();
Rest::init();
Blocks::init();
Forms::init();
Settings::init();
Seo::init();
Setup::init();
Color_Mode::init();
Consent::init();

register_activation_hook(
	__FILE__,
	static function () {
		Post_Types::register();
		flush_rewrite_rules();
	}
);
register_deactivation_hook( __FILE__, 'flush_rewrite_rules' );

/**
 * Inline SVG icon from the Skyra set; the theme uses this when the plugin is active.
 *
 * @param string $name  Icon key, e.g. "arrow-right", "zodiac-koc", "planet-venus".
 * @param array  $attrs Extra attributes (class, width, height, title).
 */
function icon( string $name, array $attrs = array() ): string {
	return Icons::svg( $name, $attrs );
}
