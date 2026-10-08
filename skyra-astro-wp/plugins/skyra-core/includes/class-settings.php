<?php
/**
 * Site-wide Skyra settings (Ayarlar → Skyra Astro): social links, contact
 * address, brand line and the header/hero CTA wording.
 *
 * @package Skyra\Core
 */

namespace Skyra\Core;

defined( 'ABSPATH' ) || exit;

final class Settings {

	public const OPTION = 'skyra_settings';

	/** key => [label, type, default]. */
	public static function fields(): array {
		return array(
			'tagline'       => array( 'Marka cümlesi (footer)', 'text', 'Gökyüzünü anla. Kendine alan aç.' ),
			'cta_label'     => array( 'Header harita butonu', 'text', 'Haritanı Keşfet' ),
			'cta_short'     => array( 'Mobil header harita butonu', 'text', 'Harita' ),
			'instagram'     => array( 'Instagram adresi', 'url', 'https://www.instagram.com/skyra.astro/' ),
			'tiktok'        => array( 'TikTok adresi', 'url', '' ),
			'youtube'       => array( 'YouTube adresi', 'url', '' ),
			'contact_email' => array( 'İletişim formu alıcısı', 'email', (string) get_option( 'admin_email' ) ),
		);
	}

	public static function init(): void {
		add_action( 'admin_menu', array( self::class, 'menu' ) );
		add_action( 'admin_init', array( self::class, 'register' ) );
	}

	public static function get( string $key ): string {
		$saved = get_option( self::OPTION, array() );
		$field = self::fields()[ $key ] ?? null;
		if ( is_array( $saved ) && isset( $saved[ $key ] ) && '' !== $saved[ $key ] ) {
			return (string) $saved[ $key ];
		}
		if ( is_array( $saved ) && array_key_exists( $key, $saved ) && $field && 'url' === $field[1] ) {
			return ''; // An emptied social link stays empty.
		}
		return $field ? (string) $field[2] : '';
	}

	/** Social profiles with a URL set, in display order. */
	public static function socials(): array {
		$out = array();
		foreach ( array(
			'instagram' => 'Instagram',
			'tiktok'    => 'TikTok',
			'youtube'   => 'YouTube',
		) as $key => $label ) {
			$url = self::get( $key );
			if ( $url ) {
				$out[ $key ] = array(
					'label' => $label,
					'url'   => $url,
				);
			}
		}
		return $out;
	}

	public static function menu(): void {
		add_options_page( 'Skyra Astro', 'Skyra Astro', 'manage_options', 'skyra-settings', array( self::class, 'page' ) );
	}

	public static function register(): void {
		register_setting(
			'skyra',
			self::OPTION,
			array(
				'type'              => 'object',
				'sanitize_callback' => array( self::class, 'sanitize' ),
			)
		);
	}

	public static function sanitize( $input ): array {
		$out = array();
		foreach ( self::fields() as $key => [ $label, $type ] ) {
			$value       = (string) ( $input[ $key ] ?? '' );
			$out[ $key ] = match ( $type ) {
				'url'   => esc_url_raw( $value ),
				'email' => sanitize_email( $value ),
				default => sanitize_text_field( $value ),
			};
		}
		return $out;
	}

	public static function page(): void {
		echo '<div class="wrap"><h1>Skyra Astro</h1><form method="post" action="options.php">';
		settings_fields( 'skyra' );
		echo '<table class="form-table" role="presentation">';
		foreach ( self::fields() as $key => [ $label, $type ] ) {
			printf(
				'<tr><th scope="row"><label for="skyra-%1$s">%2$s</label></th><td><input class="regular-text" type="%3$s" id="skyra-%1$s" name="%4$s[%1$s]" value="%5$s"></td></tr>',
				esc_attr( $key ),
				esc_html( $label ),
				esc_attr( 'text' === $type ? 'text' : $type ),
				esc_attr( self::OPTION ),
				esc_attr( self::get( $key ) )
			);
		}
		echo '</table>';
		submit_button();
		echo '</form>';
		echo '<h2>Veri ve hesaplama</h2><p>Gökyüzü verileri Skyra Core içindeki efemeris motoruyla sunucuda hesaplanır ve önbelleğe alınır (anlık veriler 10 dk, günlük veriler 1 gün). Doğum bilgileri yalnızca istek sırasında kullanılır; saklanmaz, loglanmaz. Tam sayfa önbellek kullanıyorsan ana sayfa ve araç sayfaları için süreyi 1 saati aşmayacak şekilde ayarla.</p>';
		echo '<p>Demo içeriğini yeniden kurmak için: <a href="' . esc_url( admin_url( 'tools.php?page=skyra-setup' ) ) . '">Araçlar → Skyra kurulum</a>.</p></div>';
	}
}
