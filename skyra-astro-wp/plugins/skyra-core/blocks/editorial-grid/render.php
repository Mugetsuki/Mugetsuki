<?php
/**
 * Blog showcase: one featured article and three secondary cards.
 *
 * @package Skyra\Core
 */

use Skyra\Core\Blocks;
use Skyra\Core\Data;
use Skyra\Core\View;

defined( 'ABSPATH' ) || exit;

$args = array(
	'post_type'           => 'post',
	'posts_per_page'      => 4,
	'ignore_sticky_posts' => false,
	'no_found_rows'       => true,
);
if ( ! empty( $attributes['category'] ) ) {
	$args['category_name'] = sanitize_title( $attributes['category'] );
}
$posts = get_posts( $args );
if ( ! $posts ) {
	return;
}
$blog    = (int) get_option( 'page_for_posts' );
$head_id = View::uid( 'editorial' );
$meta    = static function ( WP_Post $p ): string {
	return sprintf(
		'<p class="sk-post__meta"><span>%s</span><span>%d dk okuma</span><time datetime="%s">%s</time></p>',
		esc_html( get_the_author_meta( 'display_name', (int) $p->post_author ) ),
		View::reading_time( $p ),
		esc_attr( get_the_date( 'c', $p ) ),
		esc_html( Data::format( (int) get_post_time( 'U', true, $p ), 'j F Y' ) )
	);
};
$cat     = static function ( WP_Post $p ): string {
	$c = get_the_category( $p->ID );
	return $c ? '<p class="sk-post__cat">' . esc_html( $c[0]->name ) . '</p>' : '';
};
$feature = array_shift( $posts );
?>
<section <?php echo get_block_wrapper_attributes( array( 'class' => 'sk-section sk-editorial', 'aria-labelledby' => $head_id ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>>
	<div class="sk-container">
		<?php
		echo View::head( // phpcs:ignore WordPress.Security.EscapeOutput
			array(
				'eyebrow' => Blocks::attr( $attributes, 'eyebrow' ),
				'heading' => Blocks::attr( $attributes, 'heading', "Skyra'dan yazılar" ),
				'intro'   => Blocks::attr( $attributes, 'intro' ),
				'id'      => $head_id,
				'link'    => array( $blog ? get_permalink( $blog ) : home_url( '/blog/' ), 'Tüm yazılar' ),
			)
		);
		?>
		<div class="sk-editorial__grid">
			<article class="sk-post sk-post--feature" data-reveal>
				<?php echo View::cover( $feature, '16-10', 'large' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				<div class="sk-post__body">
					<?php echo $cat( $feature ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
					<h3 class="sk-post__title"><a class="sk-stretch" href="<?php echo esc_url( get_permalink( $feature ) ); ?>"><?php echo esc_html( get_the_title( $feature ) ); ?></a></h3>
					<p class="sk-post__excerpt"><?php echo esc_html( get_the_excerpt( $feature ) ); ?></p>
					<?php echo $meta( $feature ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				</div>
			</article>
			<div class="sk-editorial__list">
				<?php foreach ( $posts as $p ) : ?>
				<article class="sk-post" data-reveal>
					<?php echo View::cover( $p, '4-3' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
					<div class="sk-post__body">
						<?php echo $cat( $p ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
						<h3 class="sk-post__title"><a class="sk-stretch" href="<?php echo esc_url( get_permalink( $p ) ); ?>"><?php echo esc_html( get_the_title( $p ) ); ?></a></h3>
						<?php echo $meta( $p ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
					</div>
				</article>
				<?php endforeach; ?>
			</div>
		</div>
	</div>
</section>
