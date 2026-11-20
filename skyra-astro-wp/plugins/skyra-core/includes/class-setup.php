<?php
/**
 * One-click site setup: pages and URL structure, sign/planet content,
 * starter articles, menus and reading settings. Idempotent: existing
 * content (matched by slug) is never overwritten.
 *
 * Run from Araçlar → Skyra kurulum, or `wp skyra setup`.
 *
 * @package Skyra\Core
 */

namespace Skyra\Core;

use Skyra\Astro\Ephemeris;
use Skyra\Astro\Events;
use Skyra\Astro\Readings;
use Skyra\Astro\Zodiac;

defined( 'ABSPATH' ) || exit;

final class Setup {

	public static function init(): void {
		add_action(
			'init',
			static function () {
				if ( get_option( 'skyra_flush_rewrite' ) ) {
					delete_option( 'skyra_flush_rewrite' );
					flush_rewrite_rules();
				}
			},
			99
		);
		add_action( 'admin_menu', array( self::class, 'menu' ) );
		add_action( 'admin_post_skyra_setup', array( self::class, 'handle' ) );
		add_action( 'admin_post_skyra_legal', array( self::class, 'handle_legal' ) );
		if ( defined( 'WP_CLI' ) && WP_CLI ) {
			\WP_CLI::add_command(
				'skyra setup',
				static function () {
					$log = self::run();
					\WP_CLI::success( implode( "\n", $log ) );
				}
			);
		}
	}

	public static function menu(): void {
		add_management_page( 'Skyra kurulum', 'Skyra kurulum', 'manage_options', 'skyra-setup', array( self::class, 'admin_page' ) );
	}

	public static function admin_page(): void {
		echo '<div class="wrap"><h1>Skyra kurulum</h1>';
		echo '<p>Sayfaları (URL yapısıyla), 12 burç ve 10 gezegen içeriğini, Ay fazı metinlerini, başlangıç blog yazılarını, menüleri ve okuma ayarlarını oluşturur. Var olan içerik (aynı kısa ad) değiştirilmez; güvenle yeniden çalıştırılabilir.</p>';
		echo '<form method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '">';
		wp_nonce_field( 'skyra_setup' );
		echo '<input type="hidden" name="action" value="skyra_setup">';
		submit_button( 'Kurulumu çalıştır' );
		echo '</form>';
		$log = get_transient( 'skyra_setup_log' );
		if ( $log ) {
			echo '<h2>Son çalıştırma</h2><ul><li>' . implode( '</li><li>', array_map( 'esc_html', $log ) ) . '</li></ul>';
		}

		echo '<hr><h2>Yasal metinleri güncelle</h2>';
		echo '<p>KVKK Aydınlatma Metni, Gizlilik Politikası ve Çerez Politikası sayfalarının içeriğini bu eklenti sürümündeki metinlerle değiştirir. Sayfaların önceki hâli revizyon olarak saklanır; Sayfalar → ilgili sayfa → Revizyonlar ekranından geri yüklenebilir.</p>';
		$done = get_option( 'skyra_legal_version' );
		if ( $done ) {
			echo '<p>Son güncelleme: metin sürümü ' . esc_html( (string) $done ) . '.</p>';
		}
		echo '<form method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '">';
		wp_nonce_field( 'skyra_legal' );
		echo '<input type="hidden" name="action" value="skyra_legal">';
		submit_button( 'Yasal metinleri güncelle', 'secondary' );
		echo '</form>';
		$legal = get_transient( 'skyra_legal_log' );
		if ( $legal ) {
			echo '<ul><li>' . implode( '</li><li>', array_map( 'esc_html', $legal ) ) . '</li></ul>';
		}
		echo '</div>';
	}

	/** Slugs whose content the legal update replaces. */
	public const LEGAL = array( 'kvkk', 'gizlilik-politikasi', 'cerez-politikasi' );

	public static function handle_legal(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( 'Yetkin yok.' );
		}
		check_admin_referer( 'skyra_legal' );
		set_transient( 'skyra_legal_log', self::update_legal(), HOUR_IN_SECONDS );
		wp_safe_redirect( admin_url( 'tools.php?page=skyra-setup' ) );
		exit;
	}

	/**
	 * Replace the legal pages' content with this version's texts. Each
	 * update stores a revision, so the previous text can be restored.
	 *
	 * @return string[] Log lines.
	 */
	public static function update_legal(): array {
		$log = array();
		foreach ( require SKYRA_CORE_DIR . 'includes/content/pages.php' as $p ) {
			if ( ! in_array( $p['slug'], self::LEGAL, true ) ) {
				continue;
			}
			$content  = self::blocks( $p['body'] );
			$existing = get_page_by_path( $p['slug'] );
			if ( ! $existing ) {
				self::create_page( $p['slug'], $p['title'], $content, $p['excerpt'], 0, $log );
				continue;
			}
			if ( $existing->post_content === $content ) {
				$log[] = 'Değişiklik yok: /' . $p['slug'] . '/';
				continue;
			}
			$id    = wp_update_post(
				array(
					'ID'           => $existing->ID,
					'post_title'   => $p['title'],
					'post_content' => $content,
					'post_excerpt' => $p['excerpt'],
				),
				true
			);
			$log[] = is_wp_error( $id ) ? 'Hata: /' . $p['slug'] . '/ — ' . $id->get_error_message() : 'Güncellendi: /' . $p['slug'] . '/';
		}
		update_option( 'skyra_legal_version', SKYRA_CORE_VERSION, false );
		return $log;
	}

	public static function handle(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( 'Yetkin yok.' );
		}
		check_admin_referer( 'skyra_setup' );
		set_transient( 'skyra_setup_log', self::run(), HOUR_IN_SECONDS );
		wp_safe_redirect( admin_url( 'tools.php?page=skyra-setup' ) );
		exit;
	}

	/** @return string[] Log lines. */
	public static function run(): array {
		$log = array();

		update_option( 'blogname', 'Skyra Astro' );
		update_option( 'blogdescription', 'Gökyüzünü anla. Kendine alan aç.' );
		update_option( 'timezone_string', 'Europe/Istanbul' );
		update_option( 'date_format', 'j F Y' );
		update_option( 'time_format', 'H:i' );
		update_option( 'start_of_week', 1 );
		update_option( 'default_comment_status', 'closed' );
		global $wp_rewrite;
		$wp_rewrite->set_permalink_structure( '/blog/%postname%/' );
		$wp_rewrite->set_category_base( 'kategori' );

		Post_Types::register();

		// Categories.
		$cats = array(
			'astroloji-101'      => array( 'Astroloji 101', 'Astrolojinin temel kavramları: burçlar, gezegenler, evler ve açılar.' ),
			'gezegenler'         => array( 'Gezegenler', 'Gezegenlerin anlamları, retroları ve döngüleri.' ),
			'burclar'            => array( 'Burçlar', 'On iki burç, elementler ve nitelikler üzerine yazılar.' ),
			'iliskiler'          => array( 'İlişkiler', 'Uyum, sevgi dilleri ve ilişkilerde astroloji.' ),
			'gokyuzu-gundemi'    => array( 'Gökyüzü Gündemi', 'Ay fazları ve güncel gökyüzü hareketleri.' ),
			'astrolojik-olaylar' => array( 'Astrolojik Olaylar', 'Tutulmalar, retrolar ve önemli transitler.' ),
		);
		foreach ( $cats as $slug => [ $name, $desc ] ) {
			if ( ! get_category_by_slug( $slug ) ) {
				wp_insert_term(
					$name,
					'category',
					array(
						'slug'        => $slug,
						'description' => $desc,
					)
				);
			}
		}
		$uncat = get_category_by_slug( 'uncategorized' );
		if ( $uncat ) {
			wp_update_term(
				$uncat->term_id,
				'category',
				array(
					'name' => 'Genel',
					'slug' => 'genel',
				)
			);
		}

		// Home and blog pages.
		$home = self::create_page( 'ana-sayfa', 'Ana Sayfa', self::home_content(), '', 0, $log );
		$blog = self::create_page( 'blog', 'Blog', '', 'Astroloji 101, gezegenler, burçlar, ilişkiler ve gökyüzü gündemi üzerine yazılar.', 0, $log );
		update_option( 'show_on_front', 'page' );
		update_option( 'page_on_front', $home );
		update_option( 'page_for_posts', $blog );

		// Content pages.
		$ids = array();
		foreach ( require SKYRA_CORE_DIR . 'includes/content/pages.php' as $p ) {
			$ids[ $p['slug'] ] = self::create_page( $p['slug'], $p['title'], self::blocks( $p['body'] ), $p['excerpt'], 0, $log );
		}
		foreach ( Zodiac::SIGNS as $i => $s ) {
			self::create_page(
				$s['slug'],
				$s['name'] . ' Günlük Burç Yorumu',
				self::blocks( sprintf( "[[skyra/sign-reading {\"sign\":\"%s\",\"variant\":\"page\",\"align\":\"wide\"}]]\n%s burcu için günlük okuma, Ay’ın %s göre bulunduğu eve ve günün öne çıkan gezegen açısına dayanır. Yarının okumasına da aynı sayfadan bakabilirsin.", $s['slug'], $s['name'], $s['dat'] ) ),
				sprintf( '%s burcu için bugünün ve yarının yorumu: Ay’ın %s göre evi, günün gezegen açısı ve Güneş’in aydınlattığı alan.', $s['name'], $s['dat'] ),
				$ids['gunluk-burc-yorumlari'],
				$log,
				$i
			);
		}

		// Signs and planets.
		$texts = require SKYRA_CORE_DIR . 'includes/content/signs.php';
		foreach ( Zodiac::SIGNS as $i => $s ) {
			if ( self::find( Post_Types::SIGN, $s['slug'] ) ) {
				continue;
			}
			[ $overview, $strong, $growth, $love ] = $texts[ $s['slug'] ];
			$body = implode(
				"\n",
				array(
					$overview,
					'## Güçlü yanlar',
					$strong,
					'## Gelişim alanları',
					$growth,
					'## İlişkilerde ' . $s['name'],
					$love,
					'Güneş burcu tek başına bir insanı anlatmaz. Ay burcun, yükselenin ve diğer yerleşimlerin bu tabloyu kişiselleştirir; <a href="' . esc_url( Data::page_url( 'dogum-haritasi' ) ) . '">doğum haritanda</a> hepsini birlikte görebilirsin.',
				)
			);
			$id   = wp_insert_post(
				array(
					'post_type'    => Post_Types::SIGN,
					'post_status'  => 'publish',
					'post_title'   => $s['name'] . ' Burcu',
					'post_name'    => $s['slug'],
					'post_content' => self::blocks( $body ),
					'post_excerpt' => sprintf( '%s burcu (%s): %s Element, nitelik, yönetici gezegen ve ilişkilerde %s.', $s['name'], Zodiac::date_range( $s ), $s['line'], $s['name'] ),
					'menu_order'   => $i,
				)
			);
			update_post_meta( $id, 'skyra_sign', $s['slug'] );
			$log[] = 'Burç: ' . $s['name'];
		}
		$planets = array(
			'sun'     => array( 'gunes', 'Haritanın merkezi. Kimliği, yaşam enerjisini ve kendini ifade etme biçimini temsil eder. Bir burçta yaklaşık bir ay kalır; “burcun ne?” sorusunun yanıtı Güneş’in burcudur.' ),
			'moon'    => array( 'ay', 'Duyguları, ihtiyaçları ve güvende hissetme biçimini anlatır. Gökyüzünün en hızlı hareket eden cismidir: yaklaşık iki buçuk günde bir burç değiştirir, 29,5 günde fazlarını tamamlar.' ),
			'mercury' => array( 'merkur', 'Düşünce, iletişim, öğrenme ve kısa yolculuklarla ilişkilendirilir. Güneş’e en yakın gezegendir ve yılda üç kez, yaklaşık üç hafta retro görünür.' ),
			'venus'   => array( 'venus', 'Sevgi, ilişkiler, zevkler ve değerlerle ilgilidir. Güneş’ten en fazla 47° kadar uzaklaşır; yaklaşık 18 ayda bir retro yapar.' ),
			'mars'    => array( 'mars', 'İsteği, eylemi, cesareti ve öfkeyi temsil eder. Bir burçta ortalama altı hafta kalır; yaklaşık iki yılda bir retro dönemine girer.' ),
			'jupiter' => array( 'jupiter', 'Büyüme, anlam, inanç ve fırsatlarla ilişkilendirilir. Bir burcu yaklaşık bir yılda geçer; 12 yılda zodyağı tamamlar.' ),
			'saturn'  => array( 'saturn', 'Yapı, sorumluluk, sınırlar ve zamanla olgunlaşmayı anlatır. Bir burçta yaklaşık iki buçuk yıl kalır; 29,5 yılda zodyağı tamamlar.' ),
			'uranus'  => array( 'uranus', 'Değişim, özgürlük ve beklenmedik olanla ilgilidir. Bir burçta yaklaşık yedi yıl kalır; etkisi daha çok kuşaklar üzerinden okunur.' ),
			'neptune' => array( 'neptun', 'Hayal gücü, sezgi, şefkat ve belirsizlikle ilişkilendirilir. Bir burcu yaklaşık 14 yılda geçer.' ),
			'pluto'   => array( 'pluton', 'Dönüşüm, güç ve derinlikle ilgilidir. Eliptik yörüngesi nedeniyle bir burçta 12 ile 30 yıl arasında kalır.' ),
		);
		$order   = 0;
		foreach ( $planets as $key => [ $slug, $text ] ) {
			if ( self::find( Post_Types::PLANET, $slug ) ) {
				continue;
			}
			$id = wp_insert_post(
				array(
					'post_type'    => Post_Types::PLANET,
					'post_status'  => 'publish',
					'post_title'   => Zodiac::PLANETS[ $key ]['name'],
					'post_name'    => $slug,
					'post_content' => self::blocks( $text ),
					'post_excerpt' => $text,
					'menu_order'   => $order++,
				)
			);
			update_post_meta( $id, 'skyra_planet', $key );
		}

		// Moon phase texts (editable defaults).
		foreach ( Events::PHASES as $p ) {
			if ( self::find( Post_Types::MOON_PHASE, $p['key'] ) ) {
				continue;
			}
			$id = wp_insert_post(
				array(
					'post_type'    => Post_Types::MOON_PHASE,
					'post_status'  => 'publish',
					'post_title'   => $p['name'],
					'post_name'    => $p['key'],
					'post_content' => self::blocks( Readings::PHASE[ $p['key'] ] ),
					'post_excerpt' => Readings::PHASE[ $p['key'] ],
				)
			);
			update_post_meta( $id, 'skyra_phase', $p['key'] );
		}

		// Starter articles, newest first, spaced a few days apart.
		$articles = require SKYRA_CORE_DIR . 'includes/content/articles.php';
		$author   = self::author();
		foreach ( $articles as $i => $a ) {
			if ( get_page_by_path( $a['slug'], OBJECT, 'post' ) ) {
				continue;
			}
			$cat = get_category_by_slug( $a['category'] );
			wp_insert_post(
				array(
					'post_type'     => 'post',
					'post_status'   => 'publish',
					'post_title'    => $a['title'],
					'post_name'     => $a['slug'],
					'post_excerpt'  => $a['excerpt'],
					'post_content'  => self::blocks( $a['body'] ),
					'post_author'   => $author,
					'post_date'     => gmdate( 'Y-m-d H:i:s', Data::now() - ( $i * 4 + 1 ) * DAY_IN_SECONDS + 3 * HOUR_IN_SECONDS ),
					'post_category' => $cat ? array( $cat->term_id ) : array(),
				)
			);
			$log[] = 'Yazı: ' . $a['title'];
		}
		$hello = get_page_by_path( 'hello-world', OBJECT, 'post' );
		if ( $hello ) {
			wp_delete_post( $hello->ID, true );
		}
		$sample = get_page_by_path( 'sample-page' );
		if ( $sample ) {
			wp_delete_post( $sample->ID, true );
		}

		self::event_notes( $author, $log );
		self::transit_notes( $author, $log );
		self::menus( $ids, $blog, $log );

		flush_rewrite_rules();
		// Taxonomies registered earlier in this request still carry the old
		// category base, so rebuild the rules once more on the next request.
		update_option( 'skyra_flush_rewrite', 1 );
		update_option( 'skyra_setup_version', SKYRA_CORE_VERSION );
		$log[] = 'Kurulum tamamlandı.';
		return $log;
	}

	/** Editorial notes for the next Mercury retrograde and the next eclipse, from computed data. */
	private static function event_notes( int $author, array &$log ): void {
		$events = Data::upcoming( 200 );
		$retro  = null;
		$direct = null;
		$ecl    = null;
		foreach ( $events as $e ) {
			if ( ! $retro && 'station_retro' === $e['type'] && 'mercury' === $e['body'] ) {
				$retro = $e;
			} elseif ( $retro && ! $direct && 'station_direct' === $e['type'] && 'mercury' === $e['body'] ) {
				$direct = $e;
			}
			if ( ! $ecl && str_ends_with( $e['type'], 'eclipse' ) ) {
				$ecl = $e;
			}
		}
		if ( $retro && $direct ) {
			$sign  = Zodiac::by_slug( $retro['sign'] );
			$title = sprintf( 'Merkür retrosu %s: %s – %s', $sign['loc'], Data::format( $retro['unix'], 'j F' ), Data::format( $direct['unix'], 'j F Y' ) );
			$body  = implode(
				"\n",
				array(
					sprintf( 'Merkür %s %s %s noktasında durağanlaşıp geri harekete başlıyor ve %s tarihinde %s %s noktasında yeniden düz harekete geçiyor.', Data::format( $retro['unix'], 'j F' ), $sign['loc'], $retro['degree'], Data::format( $direct['unix'], 'j F' ), Zodiac::by_slug( $direct['sign'] )['loc'], $direct['degree'] ),
					'## Bu dönemde neye bakılabilir?',
					sprintf( '%s temaları (%s) üzerinden düşünce ve iletişim alışkanlıklarını gözden geçirmek için bir fırsat olarak okunabilir.', $sign['name'], implode( ', ', $sign['keywords'] ) ),
					'- Yarım kalmış yazışmaları ve planları yeniden ele almak',
					'- Önemli metinleri imzalamadan önce iki kez okumak',
					'- Cihazlarının ve dosyalarının yedeğini almak',
					'Retro bir uyarı değil, bir yavaşlama davetidir. Tarihleri Retro Takvimi’nde, yıl içindeki diğer dönemlerle birlikte görebilirsin.',
				)
			);
			self::note( Post_Types::EVENT, sanitize_title( 'merkur-retrosu-' . Data::format( $retro['unix'], 'Y-m' ) ), $title, $body, $author, array( 'skyra_event_type' => 'station_retro', 'skyra_event_body' => 'mercury', 'skyra_event_date' => Data::format( $retro['unix'], 'Y-m-d' ) ), $log );
		}
		if ( $ecl ) {
			$sign = Zodiac::by_slug( $ecl['sign'] );
			$body = implode(
				"\n",
				array(
					sprintf( '%s tarihinde, Türkiye saatiyle %s civarında, %s %s gerçekleşiyor. Tutulma %s %s noktasında.', Data::format( $ecl['unix'], 'j F Y' ), Data::format( $ecl['unix'], 'H:i' ), mb_strtolower( explode( ' · ', $ecl['title'] )[0], 'UTF-8' ), 'lunar_eclipse' === $ecl['type'] ? 've Dolunay’a denk geliyor' : 've Yeni Ay’a denk geliyor', $sign['loc'], $ecl['degree'] ),
					'## Nasıl okunabilir?',
					'lunar_eclipse' === $ecl['type']
						? sprintf( 'Ay tutulmaları geleneksel olarak bir şeyin tamamlanması ya da görünür olmasıyla ilişkilendirilir. %s temaları (%s) üzerinden neyin olgunlaştığını fark etmek için bir an olabilir.', $sign['name'], implode( ', ', $sign['keywords'] ) )
						: sprintf( 'Güneş tutulmaları geleneksel olarak yeni bir sayfayla ilişkilendirilir. %s temaları (%s) üzerinden neye alan açmak istediğini düşünmek için bir an olabilir.', $sign['name'], implode( ', ', $sign['keywords'] ) ),
					'Tutulmayı çıplak gözle izlemek bulunduğun yere bağlıdır. Güneş tutulmalarını asla korumasız gözle izleme.',
				)
			);
			self::note( Post_Types::EVENT, sanitize_title( 'tutulma-' . Data::format( $ecl['unix'], 'Y-m-d' ) ), $ecl['title'], $body, $author, array( 'skyra_event_type' => $ecl['type'], 'skyra_event_body' => 'moon', 'skyra_event_date' => Data::format( $ecl['unix'], 'Y-m-d' ) ), $log );
		}
	}

	/** Transit posts for Jupiter and Saturn's current signs, with their next ingress. */
	private static function transit_notes( int $author, array &$log ): void {
		$sky = Data::snapshot();
		$jd  = Ephemeris::jd( (float) Data::now() );
		foreach ( array( 'jupiter', 'saturn' ) as $body ) {
			$sign = Zodiac::by_slug( $sky['bodies'][ $body ]['sign'] );
			$next = null;
			foreach ( Events::ingresses( $body, $jd, $jd + 1100, 2.0 ) as $i ) {
				if ( ! $i['retro'] && $i['from'] === $sign['index'] ) {
					$next = $i;
					break;
				}
			}
			$p     = Zodiac::PLANETS[ $body ];
			$title = $p['name'] . ' ' . $sign['loc'];
			$body_text = implode(
				"\n",
				array(
					sprintf( '%s şu an %s ilerliyor. %s konuları bu dönemde %s temalarıyla — %s — renklenebilir.', $p['name'], $sign['loc'], Readings::ucfirst_tr( $p['area'] ), $sign['name'], implode( ', ', $sign['keywords'] ) ),
					$next ? sprintf( '%s, %s civarında %s geçiyor; geri hareket dönemlerinde sınıra yeniden yaklaşabilir.', $p['name'], Data::format( (int) round( Ephemeris::unix( $next['jd'] ) ), 'F Y' ), Zodiac::SIGNS[ $next['to'] ]['dat'] ) : '',
					'## Kendine sorabileceklerin',
					sprintf( '- %s hayatımda hangi alanda daha görünür hale geliyor?', Readings::ucfirst_tr( $p['area'] ) ),
					sprintf( '- %s niteliklerinden hangisini daha bilinçli kullanmak isterim?', $sign['name'] ),
					'Bu yazı genel bir transit okumasıdır; kişisel etkisini görmek için gezegenin doğum haritandaki hangi eve düştüğüne bakabilirsin.',
				)
			);
			self::note( Post_Types::TRANSIT, sanitize_title( $body . '-' . $sign['slug'] ), $title, $body_text, $author, array( 'skyra_planet' => $body, 'skyra_sign' => $sign['slug'] ), $log );
		}
	}

	private static function note( string $type, string $slug, string $title, string $body, int $author, array $meta, array &$log ): void {
		if ( self::find( $type, $slug ) ) {
			return;
		}
		$id = wp_insert_post(
			array(
				'post_type'    => $type,
				'post_status'  => 'publish',
				'post_title'   => $title,
				'post_name'    => $slug,
				'post_content' => self::blocks( $body ),
				'post_excerpt' => wp_trim_words( wp_strip_all_tags( strtok( $body, "\n" ) ), 28, '…' ),
				'post_author'  => $author,
			)
		);
		foreach ( $meta as $k => $v ) {
			update_post_meta( $id, $k, $v );
		}
		$log[] = 'Not: ' . $title;
	}

	private static function menus( array $ids, int $blog, array &$log ): void {
		$menus = array(
			'primary'        => array(
				'Ana menü',
				array(
					array( 'Ana Sayfa', home_url( '/' ) ),
					array( 'Burçlar', home_url( '/burclar/' ) ),
					array( 'Doğum Haritası', get_permalink( $ids['dogum-haritasi'] ) ),
					array( 'Astroloji Araçları', get_permalink( $ids['astroloji-araclari'] ) ),
					array( 'Astroloji Takvimi', get_permalink( $ids['astroloji-takvimi'] ) ),
					array( 'Blog', get_permalink( $blog ) ),
				),
			),
			'footer-explore' => array(
				'Keşfet',
				array(
					array( 'Burçlar', home_url( '/burclar/' ) ),
					array( 'Günlük Burç Yorumları', get_permalink( $ids['gunluk-burc-yorumlari'] ) ),
					array( 'Doğum Haritası', get_permalink( $ids['dogum-haritasi'] ) ),
					array( 'Astroloji Araçları', get_permalink( $ids['astroloji-araclari'] ) ),
					array( 'Astroloji Takvimi', get_permalink( $ids['astroloji-takvimi'] ) ),
					array( 'Blog', get_permalink( $blog ) ),
				),
			),
			'footer-company' => array(
				'Kurumsal',
				array(
					array( 'Hakkımızda', get_permalink( $ids['hakkimizda'] ) ),
					array( 'İletişim', get_permalink( $ids['iletisim'] ) ),
					array( 'Gizlilik Politikası', get_permalink( $ids['gizlilik-politikasi'] ) ),
					array( 'KVKK', get_permalink( $ids['kvkk'] ) ),
					array( 'Çerez Politikası', get_permalink( $ids['cerez-politikasi'] ) ),
				),
			),
		);
		$locations = (array) get_theme_mod( 'nav_menu_locations', array() );
		foreach ( $menus as $location => [ $name, $items ] ) {
			$menu = wp_get_nav_menu_object( $name );
			if ( ! $menu ) {
				$menu_id = wp_create_nav_menu( $name );
				foreach ( $items as $pos => [ $title, $url ] ) {
					wp_update_nav_menu_item(
						$menu_id,
						0,
						array(
							'menu-item-title'    => $title,
							'menu-item-url'      => $url,
							'menu-item-status'   => 'publish',
							'menu-item-type'     => 'custom',
							'menu-item-position' => $pos + 1,
						)
					);
				}
				$log[] = 'Menü: ' . $name;
			} else {
				$menu_id = $menu->term_id;
			}
			if ( empty( $locations[ $location ] ) ) {
				$locations[ $location ] = $menu_id;
			}
		}
		set_theme_mod( 'nav_menu_locations', $locations );
	}

	/** Create a page if missing; returns its ID. */
	private static function create_page( string $slug, string $title, string $content, string $excerpt, int $parent, array &$log, int $order = 0 ): int {
		$path     = $parent ? get_page_uri( $parent ) . '/' . $slug : $slug;
		$existing = get_page_by_path( $path );
		if ( $existing ) {
			return (int) $existing->ID;
		}
		$admins = get_users(
			array(
				'role'   => 'administrator',
				'number' => 1,
				'fields' => 'ID',
			)
		);
		$id     = wp_insert_post(
			array(
				'post_type'    => 'page',
				'post_status'  => 'publish',
				'post_author'  => get_current_user_id() ? get_current_user_id() : (int) ( $admins[0] ?? 0 ),
				'post_title'   => $title,
				'post_name'    => $slug,
				'post_content' => $content,
				'post_excerpt' => $excerpt,
				'post_parent'  => $parent,
				'menu_order'   => $order,
			)
		);
		$log[]  = 'Sayfa: /' . $path . '/';
		return (int) $id;
	}

	private static function find( string $type, string $slug ): ?\WP_Post {
		$posts = get_posts(
			array(
				'post_type'      => $type,
				'name'           => $slug,
				'post_status'    => 'any',
				'posts_per_page' => 1,
				'no_found_rows'  => true,
			)
		);
		return $posts[0] ?? null;
	}

	/** Byline for starter content: a dedicated author account without a usable password. */
	private static function author(): int {
		$user = get_user_by( 'login', 'skyra-editor' );
		if ( $user ) {
			return (int) $user->ID;
		}
		$id = wp_insert_user(
			array(
				'user_login'   => 'skyra-editor',
				'user_pass'    => wp_generate_password( 40, true, true ),
				'user_email'   => 'skyra-editor@example.invalid',
				'display_name' => 'Skyra Editörü',
				'nickname'     => 'Skyra Editörü',
				'first_name'   => 'Skyra',
				'last_name'    => 'Editörü',
				'role'         => 'author',
				'description'  => 'Skyra Astro yayın ekibi.',
			)
		);
		return is_wp_error( $id ) ? 1 : (int) $id;
	}

	/**
	 * Mini markup → serialized blocks.
	 * "## T {#id}" → H2, "### T" → H3, "- x" → list, "[[ns/block {json}]]" → block, else paragraph.
	 */
	public static function blocks( string $text ): string {
		$out  = array();
		$list = array();
		$flush_list = static function () use ( &$list, &$out ) {
			if ( $list ) {
				$items = '';
				foreach ( $list as $li ) {
					$items .= "<!-- wp:list-item -->\n<li>" . $li . "</li>\n<!-- /wp:list-item -->\n";
				}
				$out[] = "<!-- wp:list -->\n<ul class=\"wp-block-list\">" . $items . "</ul>\n<!-- /wp:list -->";
				$list  = array();
			}
		};
		foreach ( preg_split( '/\R/', trim( $text ) ) as $line ) {
			$line = trim( $line );
			if ( '' === $line ) {
				continue;
			}
			if ( str_starts_with( $line, '- ' ) ) {
				$list[] = substr( $line, 2 );
				continue;
			}
			$flush_list();
			if ( preg_match( '/^\[\[([a-z0-9-]+\/[a-z0-9-]+)\s*(\{.*\})?\]\]$/', $line, $m ) ) {
				$attrs = isset( $m[2] ) && '{}' !== $m[2] ? ' ' . $m[2] : '';
				$out[] = '<!-- wp:' . $m[1] . $attrs . ' /-->';
			} elseif ( preg_match( '/^(#{2,3})\s+(.+?)(?:\s+\{#([a-z0-9-]+)\})?$/u', $line, $m ) ) {
				$level  = strlen( $m[1] );
				$anchor = $m[3] ?? '';
				$attrs  = array();
				if ( 3 === $level ) {
					$attrs['level'] = 3;
				}
				if ( $anchor ) {
					$attrs['anchor'] = $anchor;
				}
				$out[] = sprintf(
					'<!-- wp:heading%1$s -->' . "\n" . '<h%2$d class="wp-block-heading"%3$s>%4$s</h%2$d>' . "\n" . '<!-- /wp:heading -->',
					$attrs ? ' ' . wp_json_encode( $attrs ) : '',
					$level,
					$anchor ? ' id="' . esc_attr( $anchor ) . '"' : '',
					$m[2]
				);
			} else {
				$out[] = "<!-- wp:paragraph -->\n<p>" . $line . "</p>\n<!-- /wp:paragraph -->";
			}
		}
		$flush_list();
		return implode( "\n\n", $out );
	}

	/** Hero section from core blocks, so every word stays editable. */
	public static function hero_pattern(): string {
		return <<<'HTML'
<!-- wp:group {"tagName":"section","className":"sk-hero","layout":{"type":"default"}} -->
<section class="wp-block-group sk-hero"><!-- wp:group {"className":"sk-container sk-hero__grid","layout":{"type":"default"}} -->
<div class="wp-block-group sk-container sk-hero__grid"><!-- wp:group {"className":"sk-hero__copy","layout":{"type":"default"}} -->
<div class="wp-block-group sk-hero__copy"><!-- wp:paragraph {"className":"sk-eyebrow"} -->
<p class="sk-eyebrow">Skyra Astro</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":1,"className":"sk-hero__title"} -->
<h1 class="wp-block-heading sk-hero__title">Gökyüzü sana ne anlatıyor?</h1>
<!-- /wp:heading -->

<!-- wp:paragraph {"className":"sk-hero__lead"} -->
<p class="sk-hero__lead">Doğum haritandan günlük gökyüzü hareketlerine kadar, astrolojiyi kendini keşfetmenin daha anlaşılır bir yoluna dönüştür.</p>
<!-- /wp:paragraph -->

<!-- wp:buttons {"className":"sk-hero__actions"} -->
<div class="wp-block-buttons sk-hero__actions"><!-- wp:button -->
<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="#harita-formu">Haritamı Keşfet</a></div>
<!-- /wp:button -->

<!-- wp:button {"className":"is-style-outline"} -->
<div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button" href="#bugun">Bugünün Gökyüzü</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons -->

<!-- wp:skyra/sky-strip /--></div>
<!-- /wp:group -->

<!-- wp:skyra/sky-wheel /--></div>
<!-- /wp:group --></section>
<!-- /wp:group -->
HTML;
	}

	/** "Skyra Astro nedir?" section from core blocks. */
	public static function about_pattern(): string {
		return <<<'HTML'
<!-- wp:group {"tagName":"section","className":"sk-section sk-about","layout":{"type":"default"}} -->
<section class="wp-block-group sk-section sk-about"><!-- wp:group {"className":"sk-container sk-about__grid","layout":{"type":"default"}} -->
<div class="wp-block-group sk-container sk-about__grid"><!-- wp:group {"className":"sk-about__intro","layout":{"type":"default"}} -->
<div class="wp-block-group sk-about__intro"><!-- wp:paragraph {"className":"sk-eyebrow"} -->
<p class="sk-eyebrow">Yaklaşımımız</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"className":"sk-head__title"} -->
<h2 class="wp-block-heading sk-head__title">Skyra Astro nedir?</h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"className":"sk-head__intro"} -->
<p class="sk-head__intro">Gökyüzünü anlamak karmaşık olabilir. Skyra, astrolojiyi editoryal içerik ve özenle hesaplanan araçlarla kişisel, anlaşılır ve güzel bir deneyime dönüştürür.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"className":"sk-about__link"} -->
<p class="sk-about__link"><a href="/hakkimizda/#yontem">Yöntemimizi oku</a></p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<!-- wp:group {"className":"sk-about__points","layout":{"type":"default"}} -->
<div class="wp-block-group sk-about__points"><!-- wp:group {"className":"sk-point sk-point--data","layout":{"type":"default"}} -->
<div class="wp-block-group sk-point sk-point--data"><!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading">Önce veri, sonra yorum</h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Gezegen konumları her gün yeniden hesaplanır. Hangi bilginin ölçüm, hangisinin yorum olduğunu her kartta açıkça etiketleriz.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<!-- wp:group {"className":"sk-point sk-point--space","layout":{"type":"default"}} -->
<div class="wp-block-group sk-point sk-point--space"><!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading">Kader değil, alan</h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Korkutan kehanetler yerine düşünmeye alan açan okumalar. Yorumu nasıl yaşayacağına sen karar verirsin.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<!-- wp:group {"className":"sk-point sk-point--privacy","layout":{"type":"default"}} -->
<div class="wp-block-group sk-point sk-point--privacy"><!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading">Bilgin sende kalır</h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Doğum bilgilerin yalnızca hesaplama için kullanılır; saklanmaz, adres çubuğuna yazılmaz, üçüncü kişilerle paylaşılmaz.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group --></div>
<!-- /wp:group --></div>
<!-- /wp:group --></section>
<!-- /wp:group -->
HTML;
	}

	/** Home page: hero + Skyra sections + about. */
	public static function home_content(): string {
		$hero  = self::hero_pattern();
		$about = self::about_pattern();

		return implode(
			"\n\n",
			array(
				$hero,
				'<!-- wp:skyra/today-sky {"anchor":"bugun"} /-->',
				'<!-- wp:skyra/daily-horoscopes /-->',
				'<!-- wp:skyra/birth-chart {"variant":"home"} /-->',
				'<!-- wp:skyra/tools-grid /-->',
				'<!-- wp:skyra/daily-energy /-->',
				'<!-- wp:skyra/moon-phase /-->',
				'<!-- wp:skyra/upcoming-events /-->',
				'<!-- wp:skyra/zodiac-explorer /-->',
				'<!-- wp:skyra/editorial-grid /-->',
				$about,
				Forms::newsletter_enabled() ? '<!-- wp:skyra/newsletter /-->' : '',
				'<!-- wp:skyra/social-links /-->',
			)
		);
	}
}
