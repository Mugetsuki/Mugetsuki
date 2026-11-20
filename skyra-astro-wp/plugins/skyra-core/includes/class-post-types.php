<?php
/**
 * Content model: custom post types, taxonomies, meta fields and editor boxes.
 *
 * Blog articles use core posts + categories. Everything that editors should
 * change without touching templates lives here.
 *
 * @package Skyra\Core
 */

namespace Skyra\Core;

use Skyra\Astro\Events;
use Skyra\Astro\Zodiac;

defined( 'ABSPATH' ) || exit;

final class Post_Types {

	public const SIGN       = 'skyra_sign';
	public const PLANET     = 'skyra_planet';
	public const HOROSCOPE  = 'skyra_horoscope';
	public const EVENT      = 'skyra_event';
	public const TRANSIT    = 'skyra_transit';
	public const MOON_PHASE = 'skyra_moon_phase';
	public const SUBSCRIBER = 'skyra_subscriber';
	public const MESSAGE    = 'skyra_message';

	public static function init(): void {
		add_action( 'init', array( self::class, 'register' ) );
		add_action( 'add_meta_boxes', array( self::class, 'meta_boxes' ) );
		add_action( 'save_post', array( self::class, 'save' ), 10, 2 );
		add_filter( 'manage_' . self::HOROSCOPE . '_posts_columns', array( self::class, 'horoscope_columns' ) );
		add_action( 'manage_' . self::HOROSCOPE . '_posts_custom_column', array( self::class, 'horoscope_column' ), 10, 2 );
		add_filter( 'manage_' . self::SUBSCRIBER . '_posts_columns', array( self::class, 'subscriber_columns' ) );
		add_action( 'manage_' . self::SUBSCRIBER . '_posts_custom_column', array( self::class, 'subscriber_column' ), 10, 2 );
	}

	/** Meta field definitions per post type: key => [label, type, options]. */
	public static function fields(): array {
		$signs   = array_combine( Zodiac::slugs(), array_column( Zodiac::SIGNS, 'name' ) );
		$planets = array_map( static fn( $p ) => $p['name'], Zodiac::PLANETS );
		$phases  = array_combine( array_column( Events::PHASES, 'key' ), array_column( Events::PHASES, 'name' ) );
		return array(
			self::SIGN       => array(
				'skyra_sign' => array( 'Burç', 'select', $signs ),
			),
			self::PLANET     => array(
				'skyra_planet' => array( 'Gezegen', 'select', $planets ),
			),
			self::HOROSCOPE  => array(
				'skyra_sign'   => array( 'Burç', 'select', $signs ),
				'skyra_period' => array(
					'Dönem',
					'select',
					array(
						'daily'  => 'Günlük',
						'weekly' => 'Haftalık',
					),
				),
				'skyra_date'   => array( 'Tarih (dönemin ilk günü)', 'date', null ),
			),
			self::EVENT      => array(
				'skyra_event_type' => array(
					'Olay türü',
					'select',
					array(
						'new_moon'       => 'Yeni Ay',
						'full_moon'      => 'Dolunay',
						'solar_eclipse'  => 'Güneş tutulması',
						'lunar_eclipse'  => 'Ay tutulması',
						'station_retro'  => 'Retro başlangıcı',
						'station_direct' => 'Retro bitişi',
						'ingress'        => 'Burç geçişi',
					),
				),
				'skyra_event_body' => array( 'Gezegen', 'select', array( '' => '—' ) + $planets ),
				'skyra_event_date' => array( 'Tarih', 'date', null ),
			),
			self::TRANSIT    => array(
				'skyra_planet' => array( 'Gezegen', 'select', $planets ),
				'skyra_sign'   => array( 'Burç', 'select', $signs ),
			),
			self::MOON_PHASE => array(
				'skyra_phase' => array( 'Ay fazı', 'select', $phases ),
			),
		);
	}

	public static function register(): void {
		$common = array(
			'show_in_rest' => true,
			'map_meta_cap' => true,
		);

		register_post_type(
			self::SIGN,
			$common + array(
				'labels'        => self::labels( 'Burç', 'Burçlar' ),
				'public'        => true,
				'has_archive'   => 'burclar',
				'rewrite'       => array(
					'slug'       => 'burclar',
					'with_front' => false,
				),
				'menu_icon'     => 'dashicons-star-empty',
				'menu_position' => 21,
				'supports'      => array( 'title', 'editor', 'excerpt', 'thumbnail', 'page-attributes', 'custom-fields', 'revisions' ),
				'template'      => array( array( 'core/paragraph' ) ),
			)
		);
		register_post_type(
			self::PLANET,
			$common + array(
				'labels'        => self::labels( 'Gezegen', 'Gezegenler' ),
				'public'        => true,
				'has_archive'   => 'gezegenler',
				'rewrite'       => array(
					'slug'       => 'gezegenler',
					'with_front' => false,
				),
				'menu_icon'     => 'dashicons-marker',
				'menu_position' => 22,
				'supports'      => array( 'title', 'editor', 'excerpt', 'thumbnail', 'page-attributes', 'custom-fields', 'revisions' ),
			)
		);
		register_post_type(
			self::HOROSCOPE,
			$common + array(
				'labels'             => self::labels( 'Burç yorumu', 'Burç yorumları' ),
				'description'        => 'Editörün yazdığı günlük/haftalık yorumlar. Yayındaki yorum, o gün için otomatik gökyüzü okumasının yerine geçer.',
				'public'             => false,
				'show_ui'            => true,
				'publicly_queryable' => false,
				'menu_icon'          => 'dashicons-format-quote',
				'menu_position'      => 23,
				'supports'           => array( 'title', 'editor', 'excerpt', 'author', 'custom-fields', 'revisions' ),
			)
		);
		register_post_type(
			self::EVENT,
			$common + array(
				'labels'        => self::labels( 'Gökyüzü olayı notu', 'Gökyüzü olayı notları' ),
				'description'   => 'Hesaplanan takvim olaylarına eklenen editoryal yorumlar.',
				'public'        => true,
				'has_archive'   => false,
				'rewrite'       => array(
					'slug'       => 'astroloji-takvimi',
					'with_front' => false,
				),
				'menu_icon'     => 'dashicons-calendar-alt',
				'menu_position' => 24,
				'supports'      => array( 'title', 'editor', 'excerpt', 'thumbnail', 'author', 'custom-fields', 'revisions' ),
			)
		);
		register_post_type(
			self::TRANSIT,
			$common + array(
				'labels'        => self::labels( 'Transit yazısı', 'Transit yazıları' ),
				'description'   => 'Uzun süreli gezegen geçişleri için yorumlar; gezegen o burçtayken Transitler sayfasında gösterilir.',
				'public'        => true,
				'has_archive'   => false,
				'rewrite'       => array(
					'slug'       => 'transitler',
					'with_front' => false,
				),
				'menu_icon'     => 'dashicons-update',
				'menu_position' => 25,
				'supports'      => array( 'title', 'editor', 'excerpt', 'author', 'custom-fields', 'revisions' ),
			)
		);
		register_post_type(
			self::MOON_PHASE,
			$common + array(
				'labels'        => self::labels( 'Ay fazı metni', 'Ay fazı metinleri' ),
				'public'        => false,
				'show_ui'       => true,
				'menu_icon'     => 'dashicons-visibility',
				'menu_position' => 26,
				'supports'      => array( 'title', 'editor', 'excerpt', 'custom-fields', 'revisions' ),
			)
		);
		foreach ( array(
			self::SUBSCRIBER => array( 'Bülten abonesi', 'Bülten aboneleri', 'dashicons-email-alt' ),
			self::MESSAGE    => array( 'İletişim mesajı', 'İletişim mesajları', 'dashicons-feedback' ),
		) as $type => [ $one, $many, $icon ] ) {
			register_post_type(
				$type,
				array(
					'labels'        => self::labels( $one, $many ),
					'public'        => false,
					'show_ui'       => true,
					'show_in_rest'  => false,
					'menu_icon'     => $icon,
					'menu_position' => 80,
					'supports'      => array( 'title', 'editor' ),
					'capabilities'  => array( 'create_posts' => 'do_not_allow' ),
					'map_meta_cap'  => true,
				)
			);
		}

		foreach ( self::fields() as $type => $fields ) {
			foreach ( $fields as $key => $field ) {
				register_post_meta(
					$type,
					$key,
					array(
						'type'              => 'string',
						'single'            => true,
						'show_in_rest'      => true,
						'sanitize_callback' => 'sanitize_text_field',
						'auth_callback'     => static fn() => current_user_can( 'edit_posts' ),
					)
				);
			}
		}
	}

	private static function labels( string $one, string $many ): array {
		return array(
			'name'          => $many,
			'singular_name' => $one,
			'add_new'       => 'Yeni ekle',
			'add_new_item'  => $one . ' ekle',
			'edit_item'     => $one . ' düzenle',
			'new_item'      => 'Yeni ' . mb_strtolower( $one ),
			'view_item'     => $one . ' görüntüle',
			'search_items'  => $many . ' içinde ara',
			'not_found'     => 'Kayıt bulunamadı',
			'all_items'     => 'Tümü',
			'menu_name'     => $many,
		);
	}

	public static function meta_boxes(): void {
		foreach ( self::fields() as $type => $fields ) {
			add_meta_box( 'skyra-meta', 'Skyra alanları', array( self::class, 'render_box' ), $type, 'side', 'high' );
		}
		foreach ( array( self::SUBSCRIBER, self::MESSAGE ) as $type ) {
			add_meta_box( 'skyra-record', 'Kayıt', array( self::class, 'render_record' ), $type, 'side' );
		}
	}

	public static function render_box( \WP_Post $post ): void {
		wp_nonce_field( 'skyra_meta', 'skyra_meta_nonce' );
		foreach ( self::fields()[ $post->post_type ] as $key => [ $label, $type, $options ] ) {
			$value = (string) get_post_meta( $post->ID, $key, true );
			printf( '<p><label for="%1$s"><strong>%2$s</strong></label><br>', esc_attr( $key ), esc_html( $label ) );
			if ( 'select' === $type ) {
				printf( '<select id="%1$s" name="%1$s" style="width:100%%">', esc_attr( $key ) );
				foreach ( $options as $val => $text ) {
					printf( '<option value="%s"%s>%s</option>', esc_attr( $val ), selected( $value, (string) $val, false ), esc_html( $text ) );
				}
				echo '</select>';
			} else {
				printf( '<input type="date" id="%1$s" name="%1$s" value="%2$s" style="width:100%%">', esc_attr( $key ), esc_attr( $value ) );
			}
			echo '</p>';
		}
		if ( self::HOROSCOPE === $post->post_type ) {
			echo '<p class="description">Yayındaki günlük yorum, seçilen burç ve tarih için sitedeki otomatik gökyüzü okumasının yerine gösterilir.</p>';
		}
		if ( self::EVENT === $post->post_type ) {
			echo '<p class="description">Takvimdeki aynı tür, gezegen ve tarihteki (±1 gün) hesaplanan olaya bu yazının bağlantısı eklenir.</p>';
		}
	}

	public static function render_record( \WP_Post $post ): void {
		$meta = get_post_meta( $post->ID );
		echo '<dl>';
		foreach ( $meta as $key => $values ) {
			if ( str_starts_with( $key, '_skyra_' ) && '_skyra_token' !== $key ) {
				printf( '<dt><strong>%s</strong></dt><dd>%s</dd>', esc_html( substr( $key, 7 ) ), esc_html( (string) $values[0] ) );
			}
		}
		echo '</dl>';
	}

	public static function save( int $post_id, \WP_Post $post ): void {
		if ( ! isset( self::fields()[ $post->post_type ] ) || ! isset( $_POST['skyra_meta_nonce'] ) ) {
			return;
		}
		if ( ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['skyra_meta_nonce'] ) ), 'skyra_meta' ) || ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}
		foreach ( self::fields()[ $post->post_type ] as $key => [ $label, $type, $options ] ) {
			if ( ! isset( $_POST[ $key ] ) ) {
				continue;
			}
			$value = sanitize_text_field( wp_unslash( $_POST[ $key ] ) );
			if ( 'select' === $type && ! array_key_exists( $value, $options ) ) {
				continue;
			}
			if ( 'date' === $type && '' !== $value && ! preg_match( '/^\d{4}-\d{2}-\d{2}$/', $value ) ) {
				continue;
			}
			update_post_meta( $post_id, $key, $value );
		}
	}

	public static function horoscope_columns( array $cols ): array {
		return array_slice( $cols, 0, 2 ) + array(
			'skyra_sign'   => 'Burç',
			'skyra_period' => 'Dönem',
			'skyra_date'   => 'Tarih',
		) + $cols;
	}

	public static function horoscope_column( string $col, int $post_id ): void {
		if ( in_array( $col, array( 'skyra_sign', 'skyra_period', 'skyra_date' ), true ) ) {
			echo esc_html( (string) get_post_meta( $post_id, $col, true ) );
		}
	}

	public static function subscriber_columns( array $cols ): array {
		return array(
			'cb'           => $cols['cb'] ?? '',
			'title'        => 'E-posta',
			'skyra_status' => 'Durum',
			'date'         => 'Kayıt',
		);
	}

	public static function subscriber_column( string $col, int $post_id ): void {
		if ( 'skyra_status' === $col ) {
			echo 'confirmed' === get_post_meta( $post_id, '_skyra_status', true ) ? 'Onaylı' : 'Onay bekliyor';
		}
	}
}
