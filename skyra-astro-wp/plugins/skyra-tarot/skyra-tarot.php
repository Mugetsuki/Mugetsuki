<?php
/**
 * Plugin Name:       Skyra Tarot
 * Plugin URI:        https://skyraastro.com/
 * Description:       Etkileşimli, sembolik tarot açılımı (tek kart, üç kart, Kelt Haçı, ilişki). Varsayılan olarak yalnızca yöneticilerin görebildiği test modunda çalışır; hukuki değerlendirme tamamlanmadan herkese açılmamalıdır.
 * Version:           0.1.0
 * Requires at least: 6.6
 * Requires PHP:      8.1
 * Author:            Skyra Astro
 * License:           GPL-2.0-or-later
 * Text Domain:       skyra-tarot
 *
 * @package Skyra\Tarot
 */

namespace Skyra\Tarot;

defined( 'ABSPATH' ) || exit;

const VERSION = '0.1.0';
const SLUG    = 'tarot';
const OPTION  = 'skyra_tarot_public';

/** The page goes live only when an administrator switches this on. */
function is_public(): bool {
	return (bool) get_option( OPTION, false );
}

function can_view(): bool {
	return is_public() || current_user_can( 'read_private_pages' );
}

function page(): ?\WP_Post {
	$page = get_page_by_path( SLUG );
	return $page instanceof \WP_Post ? $page : null;
}

/* Activation: create /tarot/ as a private page (visitors get 404). */
register_activation_hook(
	__FILE__,
	static function (): void {
		add_option( OPTION, 0 );
		if ( page() ) {
			return;
		}
		wp_insert_post(
			array(
				'post_type'    => 'page',
				'post_status'  => 'private',
				'post_title'   => 'Tarot',
				'post_name'    => SLUG,
				'post_excerpt' => 'Kartlarla sembolik bir düşünme alanı: bir açılım seç, kartlarını kendin seç ve üzerine düşün.',
				'post_content' => '<!-- wp:skyra-tarot/reading {"align":"wide"} /-->',
			)
		);
	}
);

add_action(
	'init',
	static function (): void {
		$url = plugin_dir_url( __FILE__ );
		wp_register_style( 'skyra-tarot', $url . 'assets/css/tarot.css', array(), VERSION );
		wp_register_script( 'skyra-tarot-engine', $url . 'assets/js/tarot-engine.js', array(), VERSION, array( 'strategy' => 'defer', 'in_footer' => true ) );
		wp_register_script( 'skyra-tarot', $url . 'assets/js/tarot.js', array( 'skyra-tarot-engine' ), VERSION, array( 'strategy' => 'defer', 'in_footer' => true ) );
		wp_register_script( 'skyra-tarot-editor', $url . 'assets/js/editor.js', array( 'wp-blocks', 'wp-element', 'wp-block-editor' ), VERSION, true );
		register_block_type( __DIR__ . '/blocks/reading' );
	}
);

/* No "Özel:" prefix in front of the page title while in test mode. */
add_filter(
	'private_title_format',
	static function ( string $format, $post ): string {
		return ( $post instanceof \WP_Post && SLUG === $post->post_name && 'page' === $post->post_type ) ? '%s' : $format;
	},
	10,
	2
);

/* Not for search engines while in test mode. */
add_filter(
	'wp_robots',
	static function ( array $robots ): array {
		if ( ! is_public() && is_page( SLUG ) ) {
			$robots['noindex']  = true;
			$robots['nofollow'] = true;
		}
		return $robots;
	}
);

/* Ayarlar → Skyra Tarot: the single publish switch. */
add_action(
	'admin_menu',
	static function (): void {
		add_options_page( 'Skyra Tarot', 'Skyra Tarot', 'manage_options', 'skyra-tarot', __NAMESPACE__ . '\settings_page' );
	}
);

add_action(
	'admin_init',
	static function (): void {
		register_setting(
			'skyra_tarot',
			OPTION,
			array(
				'type'              => 'boolean',
				'default'           => false,
				'sanitize_callback' => static function ( $value ): int {
					$on   = ! empty( $value ) ? 1 : 0;
					$page = page();
					if ( $page ) {
						wp_update_post(
							array(
								'ID'          => $page->ID,
								'post_status' => $on ? 'publish' : 'private',
							)
						);
					}
					return $on;
				},
			)
		);
	}
);

function settings_page(): void {
	$page = page();
	echo '<div class="wrap"><h1>Skyra Tarot</h1>';
	echo '<p>Tarot sayfası şu an <strong>' . ( is_public() ? 'herkese açık' : 'test modunda' ) . '</strong>. Test modunda yalnızca giriş yapmış yöneticiler ve editörler görebilir; ziyaretçiler 404 sayfası görür ve arama motorları sayfayı dizine eklemez.</p>';
	if ( $page ) {
		echo '<p>Sayfa: <a href="' . esc_url( get_permalink( $page ) ) . '">' . esc_html( get_permalink( $page ) ) . '</a></p>';
	} else {
		echo '<p>Tarot sayfası bulunamadı. Eklentiyi devre dışı bırakıp yeniden etkinleştirmek sayfayı oluşturur.</p>';
	}
	echo '<div class="notice notice-warning inline"><p><strong>Herkese açmadan önce:</strong> Tarot ve fal içeriklerinin Türkiye’deki mevzuat (677 sayılı Kanun ve Ticari Reklam ve Haksız Ticari Uygulamalar Yönetmeliği m.27/3) açısından avukat değerlendirmesi tamamlanmalıdır. Bu sayfada ücretli hizmet, randevu çağrısı ya da satış bağlantısı yer almamalıdır.</p></div>';
	echo '<form method="post" action="options.php">';
	settings_fields( 'skyra_tarot' );
	echo '<p><label><input type="checkbox" name="' . esc_attr( OPTION ) . '" value="1" ' . checked( is_public(), true, false ) . '> Tarot sayfasını herkese aç (sayfa yayımlanır; kutuyu boşaltırsan yeniden özel olur)</label></p>';
	submit_button( 'Kaydet' );
	echo '</form></div>';
}

/** Data for the block: deck, spreads and copy, inlined as JSON. */
function data(): array {
	$deck    = json_decode( (string) file_get_contents( __DIR__ . '/data/deck.json' ), true );
	$spreads = json_decode( (string) file_get_contents( __DIR__ . '/data/spreads.json' ), true );
	return array(
		'deck'    => $deck,
		'spreads' => $spreads,
		'config'  => array(
			/** Which interpreter turns a reading into text. Only "local" exists in this version. */
			'interpreter' => (string) apply_filters( 'skyra_tarot_interpreter', 'local' ),
			'disclaimer'  => 'Bu okuma sembolik bir düşünme alıştırmasıdır. Geleceği öngörmez, kesin sonuç bildirmez; sağlık, psikolojik destek, hukuk ya da finans konularında uzman görüşünün yerini tutmaz.',
		),
	);
}
