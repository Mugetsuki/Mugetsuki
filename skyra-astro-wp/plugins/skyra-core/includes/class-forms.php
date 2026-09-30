<?php
/**
 * Newsletter (double opt-in) and contact form.
 *
 * Both work with JavaScript (REST) and without it (admin-post + redirect).
 * Records are private post types, visible only in wp-admin.
 *
 * @package Skyra\Core
 */

namespace Skyra\Core;

defined( 'ABSPATH' ) || exit;

final class Forms {

	public static function init(): void {
		add_action( 'rest_api_init', array( self::class, 'routes' ) );
		foreach ( array( 'skyra_newsletter', 'skyra_contact' ) as $action ) {
			add_action( 'admin_post_' . $action, array( self::class, 'fallback' ) );
			add_action( 'admin_post_nopriv_' . $action, array( self::class, 'fallback' ) );
		}
	}

	public static function routes(): void {
		register_rest_route(
			Rest::NS,
			'/newsletter',
			array(
				'methods'             => 'POST',
				'permission_callback' => '__return_true',
				'callback'            => static fn( \WP_REST_Request $r ) => self::respond( self::subscribe( $r->get_params() ) ),
			)
		);
		register_rest_route(
			Rest::NS,
			'/newsletter/confirm',
			array(
				'methods'             => 'GET',
				'permission_callback' => '__return_true',
				'callback'            => static function ( \WP_REST_Request $r ) {
					$ok = self::verify( (int) $r['id'], (string) $r['token'], 'confirm' );
					wp_safe_redirect( add_query_arg( 'bulten', $ok ? 'onaylandi' : 'gecersiz', home_url( '/' ) ) );
					exit;
				},
			)
		);
		register_rest_route(
			Rest::NS,
			'/newsletter/unsubscribe',
			array(
				'methods'             => 'GET',
				'permission_callback' => '__return_true',
				'callback'            => static function ( \WP_REST_Request $r ) {
					$ok = self::verify( (int) $r['id'], (string) $r['token'], 'unsubscribe' );
					wp_safe_redirect( add_query_arg( 'bulten', $ok ? 'ayrildi' : 'gecersiz', home_url( '/' ) ) );
					exit;
				},
			)
		);
		register_rest_route(
			Rest::NS,
			'/contact',
			array(
				'methods'             => 'POST',
				'permission_callback' => '__return_true',
				'callback'            => static fn( \WP_REST_Request $r ) => self::respond( self::contact( $r->get_params() ) ),
			)
		);
	}

	/** No-JS fallback: process, then redirect back with a status flag. */
	public static function fallback(): void {
		// phpcs:disable WordPress.Security.NonceVerification.Missing -- public form, spam checks below.
		$action = sanitize_key( $_POST['action'] ?? '' );
		$params = wp_unslash( $_POST );
		// phpcs:enable
		$result = 'skyra_contact' === $action ? self::contact( $params ) : self::subscribe( $params );
		$back   = wp_get_referer() ? wp_get_referer() : home_url( '/' );
		$back   = remove_query_arg( array( 'skyra_form', 'skyra_status' ), $back );
		$back   = add_query_arg(
			array(
				'skyra_form'   => 'skyra_contact' === $action ? 'contact' : 'newsletter',
				'skyra_status' => $result['code'],
			),
			$back
		);
		wp_safe_redirect( $back );
		exit;
	}

	public const MESSAGES = array(
		'subscribed'   => 'Neredeyse tamam. E-posta adresine gönderdiğimiz bağlantıyla kaydını onayla.',
		'already'      => 'Bu adres zaten bültene kayıtlı.',
		'mail_failed'  => 'Kaydını aldık ama onay e-postası şu an gönderilemedi. Birazdan tekrar dener misin?',
		'sent'         => 'Mesajın bize ulaştı. En kısa sürede e-posta ile dönüş yapacağız.',
		'email'        => 'Geçerli bir e-posta adresi gir (ör. ad@alanadi.com).',
		'consent'      => 'Devam etmek için aydınlatma metnini onayla.',
		'message'      => 'Mesajını en az 10 karakter olarak yaz.',
		'name'         => 'Adını yaz.',
		'rate'         => 'Kısa sürede çok fazla deneme yapıldı. Biraz sonra yeniden dene.',
		'spam'         => 'Gönderim tamamlanamadı. Sayfayı yenileyip yeniden dene.',
	);

	/** @return array{ok: bool, code: string, field?: string} */
	public static function subscribe( array $p ): array {
		$check = self::guard( $p, 'newsletter' );
		if ( $check ) {
			return $check;
		}
		$email = sanitize_email( (string) ( $p['email'] ?? '' ) );
		if ( ! is_email( $email ) ) {
			return self::fail( 'email', 'email' );
		}
		if ( empty( $p['consent'] ) ) {
			return self::fail( 'consent', 'consent' );
		}

		$existing = get_posts(
			array(
				'post_type'      => Post_Types::SUBSCRIBER,
				'post_status'    => 'private',
				'title'          => $email,
				'posts_per_page' => 1,
				'no_found_rows'  => true,
			)
		);
		if ( $existing && 'confirmed' === get_post_meta( $existing[0]->ID, '_skyra_status', true ) ) {
			return array(
				'ok'   => true,
				'code' => 'already',
			);
		}
		$id = $existing ? $existing[0]->ID : wp_insert_post(
			array(
				'post_type'   => Post_Types::SUBSCRIBER,
				'post_status' => 'private',
				'post_title'  => $email,
			)
		);
		if ( ! $id || is_wp_error( $id ) ) {
			return self::fail( 'spam' );
		}
		$token = wp_generate_password( 32, false );
		update_post_meta( $id, '_skyra_status', 'pending' );
		update_post_meta( $id, '_skyra_token', wp_hash_password( $token ) );
		update_post_meta( $id, '_skyra_consent_at', gmdate( 'c' ) );

		$confirm = add_query_arg(
			array(
				'id'    => $id,
				'token' => $token,
			),
			rest_url( Rest::NS . '/newsletter/confirm' )
		);
		$leave   = add_query_arg(
			array(
				'id'    => $id,
				'token' => $token,
			),
			rest_url( Rest::NS . '/newsletter/unsubscribe' )
		);
		$body    = "Merhaba,\n\nSkyra Astro'nun haftalık gökyüzü notuna kaydolmak için aşağıdaki bağlantıya tıkla:\n\n{$confirm}\n\nBu isteği sen yapmadıysan e-postayı yok sayabilirsin; onaylanmayan kayıtlar gönderim listesine eklenmez.\nKaydını silmek için: {$leave}\n\nSkyra Astro";
		$sent    = wp_mail( $email, 'Skyra Astro bülten kaydını onayla', $body );

		return array(
			'ok'   => (bool) $sent,
			'code' => $sent ? 'subscribed' : 'mail_failed',
		);
	}

	/** Confirm or remove a subscription from an e-mailed link. */
	private static function verify( int $id, string $token, string $action ): bool {
		$post = get_post( $id );
		if ( ! $post || Post_Types::SUBSCRIBER !== $post->post_type || '' === $token ) {
			return false;
		}
		$hash = (string) get_post_meta( $id, '_skyra_token', true );
		if ( ! $hash || ! wp_check_password( $token, $hash ) ) {
			return false;
		}
		if ( 'unsubscribe' === $action ) {
			wp_delete_post( $id, true );
			return true;
		}
		update_post_meta( $id, '_skyra_status', 'confirmed' );
		update_post_meta( $id, '_skyra_confirmed_at', gmdate( 'c' ) );
		return true;
	}

	/** @return array{ok: bool, code: string, field?: string} */
	public static function contact( array $p ): array {
		$check = self::guard( $p, 'contact' );
		if ( $check ) {
			return $check;
		}
		$name    = sanitize_text_field( (string) ( $p['name'] ?? '' ) );
		$email   = sanitize_email( (string) ( $p['email'] ?? '' ) );
		$message = sanitize_textarea_field( (string) ( $p['message'] ?? '' ) );
		if ( '' === $name ) {
			return self::fail( 'name', 'name' );
		}
		if ( ! is_email( $email ) ) {
			return self::fail( 'email', 'email' );
		}
		if ( mb_strlen( $message ) < 10 ) {
			return self::fail( 'message', 'message' );
		}
		if ( empty( $p['consent'] ) ) {
			return self::fail( 'consent', 'consent' );
		}
		$id = wp_insert_post(
			array(
				'post_type'    => Post_Types::MESSAGE,
				'post_status'  => 'private',
				'post_title'   => $name . ' <' . $email . '>',
				'post_content' => $message,
			)
		);
		if ( ! $id || is_wp_error( $id ) ) {
			return self::fail( 'spam' );
		}
		update_post_meta( $id, '_skyra_email', $email );
		update_post_meta( $id, '_skyra_topic', sanitize_text_field( (string) ( $p['topic'] ?? '' ) ) );
		wp_mail(
			Settings::get( 'contact_email' ),
			'Skyra Astro iletişim formu: ' . $name,
			$message . "\n\n— " . $name . ' <' . $email . '>',
			array( 'Reply-To: ' . $name . ' <' . $email . '>' )
		);
		return array(
			'ok'   => true,
			'code' => 'sent',
		);
	}

	/** Honeypot, minimum fill time and a per-IP hourly limit. */
	private static function guard( array $p, string $form ): ?array {
		if ( ! empty( $p['website'] ) ) {
			return self::fail( 'spam' );
		}
		$started = (int) ( $p['started'] ?? 0 );
		if ( $started && ( time() - $started ) < 2 ) {
			return self::fail( 'spam' );
		}
		$ip  = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
		$key = 'skyra_rl_' . $form . '_' . md5( wp_salt() . $ip );
		$n   = (int) get_transient( $key );
		if ( $n >= 6 ) {
			return self::fail( 'rate' );
		}
		set_transient( $key, $n + 1, HOUR_IN_SECONDS );
		return null;
	}

	private static function fail( string $code, ?string $field = null ): array {
		return array_filter(
			array(
				'ok'    => false,
				'code'  => $code,
				'field' => $field,
			),
			static fn( $v ) => null !== $v
		);
	}

	private static function respond( array $result ): \WP_REST_Response {
		$result['message'] = self::MESSAGES[ $result['code'] ] ?? '';
		$status            = $result['ok'] ? 200 : ( in_array( $result['code'], array( 'rate' ), true ) ? 429 : ( 'mail_failed' === $result['code'] ? 502 : 400 ) );
		$res               = new \WP_REST_Response( $result, $status );
		$res->header( 'Cache-Control', 'no-store, private' );
		return $res;
	}
}
