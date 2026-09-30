<?php
/**
 * Structured data (JSON-LD), meta descriptions and breadcrumb trail.
 *
 * Kept deliberately small; if an SEO plugin is active it can take over by
 * filtering skyra_output_seo to false.
 *
 * @package Skyra\Core
 */

namespace Skyra\Core;

use Skyra\Astro\Zodiac;

defined( 'ABSPATH' ) || exit;

final class Seo {

	public static function init(): void {
		add_action( 'wp_head', array( self::class, 'head' ), 5 );
		add_filter( 'document_title_separator', static fn() => '·' );
	}

	public static function head(): void {
		if ( ! apply_filters( 'skyra_output_seo', ! defined( 'WPSEO_VERSION' ) && ! class_exists( 'RankMath' ) ) ) {
			return;
		}
		$desc = self::description();
		if ( $desc ) {
			printf( '<meta name="description" content="%s">' . "\n", esc_attr( $desc ) );
			printf( '<meta property="og:description" content="%s">' . "\n", esc_attr( $desc ) );
		}
		printf( '<meta property="og:title" content="%s">' . "\n", esc_attr( wp_get_document_title() ) );
		printf( '<meta property="og:site_name" content="%s">' . "\n", esc_attr( get_bloginfo( 'name' ) ) );
		echo '<meta property="og:locale" content="tr_TR">' . "\n";
		if ( is_singular() && has_post_thumbnail() ) {
			printf( '<meta property="og:image" content="%s">' . "\n", esc_url( (string) get_the_post_thumbnail_url( null, 'large' ) ) );
		}

		$graph = array( self::organization(), self::website() );
		$crumbs = self::breadcrumbs();
		if ( count( $crumbs ) > 1 ) {
			$graph[] = array(
				'@type'           => 'BreadcrumbList',
				'itemListElement' => array_map(
					static fn( $c, $i ) => array(
						'@type'    => 'ListItem',
						'position' => $i + 1,
						'name'     => $c['name'],
						'item'     => $c['url'],
					),
					$crumbs,
					array_keys( $crumbs )
				),
			);
		}
		if ( is_singular( 'post' ) || is_singular( Post_Types::EVENT ) || is_singular( Post_Types::TRANSIT ) ) {
			$graph[] = self::article();
		}
		if ( is_singular( Post_Types::SIGN ) ) {
			$graph[] = self::sign_faq();
		}
		printf(
			'<script type="application/ld+json">%s</script>' . "\n",
			wp_json_encode(
				array(
					'@context' => 'https://schema.org',
					'@graph'   => array_values( array_filter( $graph ) ),
				),
				JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
			)
		);
	}

	private static function description(): string {
		if ( is_front_page() ) {
			return 'Skyra Astro: bugünün gökyüzü, günlük burç yorumları, doğum haritası ve astroloji takvimi. Astrolojiyi kendini keşfetmenin sade ve anlaşılır bir yoluna dönüştür.';
		}
		if ( is_singular() ) {
			$post = get_post();
			if ( $post && has_excerpt( $post ) ) {
				return wp_strip_all_tags( get_the_excerpt( $post ) );
			}
			if ( $post ) {
				return wp_trim_words( wp_strip_all_tags( strip_shortcodes( $post->post_content ) ), 28, '…' );
			}
		}
		if ( is_post_type_archive( Post_Types::SIGN ) ) {
			return '12 burcun elementleri, nitelikleri, yönetici gezegenleri ve karakter özellikleri.';
		}
		if ( is_home() ) {
			return 'Astroloji 101, gezegenler, burçlar, ilişkiler ve gökyüzü gündemi üzerine Skyra Astro yazıları.';
		}
		if ( is_category() ) {
			return wp_strip_all_tags( category_description() );
		}
		return '';
	}

	private static function organization(): array {
		$logo = get_theme_file_uri( 'assets/img/skyra-mark.png' );
		return array(
			'@type'  => 'Organization',
			'@id'    => home_url( '/#organization' ),
			'name'   => 'Skyra Astro',
			'url'    => home_url( '/' ),
			'logo'   => $logo,
			'sameAs' => array_values( array_column( Settings::socials(), 'url' ) ),
		);
	}

	private static function website(): array {
		return array(
			'@type'           => 'WebSite',
			'@id'             => home_url( '/#website' ),
			'name'            => 'Skyra Astro',
			'url'             => home_url( '/' ),
			'inLanguage'      => 'tr-TR',
			'publisher'       => array( '@id' => home_url( '/#organization' ) ),
			'potentialAction' => array(
				'@type'       => 'SearchAction',
				'target'      => home_url( '/?s={search_term_string}' ),
				'query-input' => 'required name=search_term_string',
			),
		);
	}

	private static function article(): array {
		$post = get_post();
		return array(
			'@type'            => 'Article',
			'headline'         => get_the_title( $post ),
			'datePublished'    => get_the_date( 'c', $post ),
			'dateModified'     => get_the_modified_date( 'c', $post ),
			'author'           => array(
				'@type' => 'Person',
				'name'  => get_the_author_meta( 'display_name', (int) $post->post_author ),
			),
			'publisher'        => array( '@id' => home_url( '/#organization' ) ),
			'mainEntityOfPage' => get_permalink( $post ),
			'image'            => has_post_thumbnail( $post ) ? get_the_post_thumbnail_url( $post, 'large' ) : null,
			'inLanguage'       => 'tr-TR',
		);
	}

	/** FAQ for sign pages, from the same facts shown on the page. */
	public static function sign_faq_items( string $slug ): array {
		$s = Zodiac::by_slug( $slug );
		if ( ! $s ) {
			return array();
		}
		$ruler = Zodiac::PLANETS[ $s['ruler'] ]['name'];
		return array(
			array( $s['name'] . ' burcu tarihleri nelerdir?', sprintf( 'Güneş genellikle %s arasında %s burcundadır. Sınır günlerinde doğanlar için kesin sonuç, doğum yılına ve saatine göre değişebilir.', Zodiac::date_range( $s ), $s['name'] ) ),
			array( $s['name'] . ' burcunun elementi ve niteliği nedir?', sprintf( '%s, %s elementinin %s bir burcudur.', $s['name'], Zodiac::ELEMENTS[ $s['element'] ]['name'], mb_strtolower( Zodiac::MODALITIES[ $s['modality'] ], 'UTF-8' ) ) ),
			array( $s['name'] . ' burcunun yönetici gezegeni hangisidir?', sprintf( '%s burcunun modern astrolojideki yöneticisi %s olarak kabul edilir.', $s['name'], $ruler ) ),
		);
	}

	private static function sign_faq(): ?array {
		$slug = (string) get_post_meta( get_the_ID(), 'skyra_sign', true );
		$faq  = self::sign_faq_items( $slug ? $slug : (string) get_post_field( 'post_name' ) );
		if ( ! $faq ) {
			return null;
		}
		return array(
			'@type'      => 'FAQPage',
			'mainEntity' => array_map(
				static fn( $q ) => array(
					'@type'          => 'Question',
					'name'           => $q[0],
					'acceptedAnswer' => array(
						'@type' => 'Answer',
						'text'  => $q[1],
					),
				),
				$faq
			),
		);
	}

	/**
	 * Breadcrumb trail for the current request.
	 *
	 * @return array<int, array{name: string, url: string}>
	 */
	public static function breadcrumbs(): array {
		$trail = array(
			array(
				'name' => 'Ana Sayfa',
				'url'  => home_url( '/' ),
			),
		);
		if ( is_front_page() ) {
			return $trail;
		}
		if ( is_singular( Post_Types::SIGN ) || is_post_type_archive( Post_Types::SIGN ) ) {
			$trail[] = array(
				'name' => 'Burçlar',
				'url'  => home_url( '/burclar/' ),
			);
		}
		if ( is_singular( Post_Types::EVENT ) ) {
			$trail[] = array(
				'name' => 'Astroloji Takvimi',
				'url'  => Data::page_url( 'astroloji-takvimi' ),
			);
		}
		if ( is_singular( Post_Types::TRANSIT ) ) {
			$trail[] = array(
				'name' => 'Transitler',
				'url'  => Data::page_url( 'transitler' ),
			);
		}
		if ( is_singular( 'post' ) || is_category() ) {
			$blog    = (int) get_option( 'page_for_posts' );
			$trail[] = array(
				'name' => 'Blog',
				'url'  => $blog ? get_permalink( $blog ) : home_url( '/blog/' ),
			);
			if ( is_singular( 'post' ) ) {
				$cat = get_the_category();
				if ( $cat ) {
					$trail[] = array(
						'name' => $cat[0]->name,
						'url'  => get_category_link( $cat[0] ),
					);
				}
			}
		}
		if ( is_page() ) {
			foreach ( array_reverse( get_post_ancestors( get_the_ID() ) ) as $ancestor ) {
				$trail[] = array(
					'name' => get_the_title( $ancestor ),
					'url'  => get_permalink( $ancestor ),
				);
			}
		}
		if ( is_singular() ) {
			$trail[] = array(
				'name' => get_the_title(),
				'url'  => get_permalink(),
			);
		} elseif ( is_category() ) {
			$trail[] = array(
				'name' => single_cat_title( '', false ),
				'url'  => get_category_link( get_queried_object_id() ),
			);
		} elseif ( is_home() ) {
			$trail[] = array(
				'name' => 'Blog',
				'url'  => get_permalink( (int) get_option( 'page_for_posts' ) ),
			);
		} elseif ( is_search() ) {
			$trail[] = array(
				'name' => 'Arama',
				'url'  => get_search_link(),
			);
		}
		return $trail;
	}
}
