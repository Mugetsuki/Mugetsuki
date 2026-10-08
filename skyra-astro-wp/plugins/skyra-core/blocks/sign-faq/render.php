<?php
/**
 * Sign FAQ (mirrors the FAQPage structured data) and related signs.
 *
 * @package Skyra\Core
 */

use Skyra\Astro\Readings;
use Skyra\Astro\Zodiac;
use Skyra\Core\Data;
use Skyra\Core\Icons;
use Skyra\Core\Seo;
use Skyra\Core\View;

defined( 'ABSPATH' ) || exit;

$post_id = (int) ( $block->context['postId'] ?? get_the_ID() );
$slug    = $attributes['sign'] ?: (string) get_post_meta( $post_id, 'skyra_sign', true );
$sign    = Zodiac::by_slug( $slug );
if ( ! $sign ) {
	return;
}
$related = array();
foreach ( Zodiac::SIGNS as $i => $s ) {
	if ( $s['slug'] !== $slug && ( $s['element'] === $sign['element'] || ( $sign['index'] + 6 ) % 12 === $i ) ) {
		$related[] = $s;
	}
}
$prev = Zodiac::SIGNS[ ( $sign['index'] + 11 ) % 12 ];
$next = Zodiac::SIGNS[ ( $sign['index'] + 1 ) % 12 ];
?>
<div <?php echo get_block_wrapper_attributes( array( 'class' => 'sk-signextra' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>>
	<section class="sk-faq" aria-labelledby="sk-faq-h">
		<h2 id="sk-faq-h"><?php echo esc_html( $sign['name'] . ' hakkında sık sorulanlar' ); ?></h2>
		<div class="sk-faq__list">
			<?php foreach ( Seo::sign_faq_items( $slug ) as [ $q, $a ] ) : ?>
			<details class="sk-faq__item"><summary><?php echo esc_html( $q ); ?><?php echo Icons::svg( 'chevron-down', array( 'size' => 20 ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?></summary><p><?php echo esc_html( $a ); ?></p></details>
			<?php endforeach; ?>
		</div>
	</section>
	<section class="sk-related" aria-labelledby="sk-rel-h">
		<h2 id="sk-rel-h"><?php echo esc_html( $sign['name'] . ' ve diğer burçlar' ); ?></h2>
		<ul class="sk-related__list" role="list">
			<?php foreach ( $related as $s ) : $c = Readings::compatibility( $slug, $s['slug'] ); ?>
			<li class="sk-card">
				<?php echo View::sign_badge( $s['slug'] ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				<div>
					<h3><a class="sk-stretch" href="<?php echo esc_url( add_query_arg( array( 'a' => $slug, 'b' => $s['slug'] ), Data::page_url( 'burc-uyumu' ) ) . '#sonuc' ); ?>"><?php echo esc_html( $sign['name'] . ' ve ' . $s['name'] ); ?></a></h3>
					<p class="sk-muted"><?php echo esc_html( $c['title'] . ' · ' . $c['angle'] . '°' ); ?></p>
				</div>
			</li>
			<?php endforeach; ?>
		</ul>
	</section>
	<nav class="sk-pager" aria-label="Diğer burçlar">
		<a href="<?php echo esc_url( Data::sign_url( $prev['slug'] ) ); ?>" rel="prev"><?php echo Icons::svg( 'arrow-left', array( 'size' => 18 ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?><span><small>Önceki burç</small><?php echo esc_html( $prev['name'] ); ?></span></a>
		<a href="<?php echo esc_url( Data::sign_url( $next['slug'] ) ); ?>" rel="next"><span><small>Sonraki burç</small><?php echo esc_html( $next['name'] ); ?></span><?php echo Icons::svg( 'arrow-right', array( 'size' => 18 ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?></a>
	</nav>
</div>
