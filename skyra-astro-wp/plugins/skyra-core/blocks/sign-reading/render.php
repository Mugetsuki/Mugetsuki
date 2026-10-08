<?php
/**
 * Today's and tomorrow's reading for one sign.
 * "page" variant (daily horoscope pages) adds the sign switcher.
 *
 * @package Skyra\Core
 */

use Skyra\Astro\Zodiac;
use Skyra\Core\Data;
use Skyra\Core\Icons;
use Skyra\Core\View;

defined( 'ABSPATH' ) || exit;

$post_id = (int) ( $block->context['postId'] ?? get_the_ID() );
$slug    = $attributes['sign'] ?: (string) get_post_meta( $post_id, 'skyra_sign', true );
$sign    = Zodiac::by_slug( $slug );
if ( ! $sign ) {
	return;
}
$page     = 'page' === ( $attributes['variant'] ?? 'page' );
$today    = Data::today();
$tomorrow = ( new DateTimeImmutable( $today . ' 12:00', Data::zone() ) )->modify( '+1 day' )->format( 'Y-m-d' );
$days     = array(
	'today'    => array( 'Bugün', $today ),
	'tomorrow' => array( 'Yarın', $tomorrow ),
);
$id       = View::uid( 'reading' );
?>
<div <?php echo get_block_wrapper_attributes( array( 'class' => 'sk-reading' . ( $page ? ' is-page' : ' is-card' ) ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?> data-sign-page="<?php echo esc_attr( $slug ); ?>" <?php echo $page ? '' : 'id="bugun"'; ?>>
	<?php if ( $page ) : ?>
	<nav class="sk-signnav" aria-label="Burç seç">
		<ul role="list">
			<?php foreach ( Zodiac::SIGNS as $s ) : ?>
			<li><a href="<?php echo esc_url( Data::daily_url( $s['slug'] ) ); ?>"<?php echo $s['slug'] === $slug ? ' aria-current="page"' : ''; ?>><?php echo Icons::zodiac( $s['slug'], array( 'size' => 18 ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?><span><?php echo esc_html( $s['name'] ); ?></span></a></li>
			<?php endforeach; ?>
		</ul>
	</nav>
	<?php endif; ?>
	<div class="sk-reading__card">
		<div class="sk-reading__head">
			<?php echo View::sign_badge( $slug, 'lg' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
			<div>
				<p class="sk-eyebrow"><?php echo esc_html( $sign['name'] . ' · Günlük yorum' ); ?></p>
				<p class="sk-reading__dates"><?php echo esc_html( Zodiac::date_range( $sign ) ); ?></p>
			</div>
		</div>
		<div class="sk-tabs" role="tablist" aria-label="Gün seç" data-tabs hidden>
			<?php foreach ( $days as $key => [ $label ] ) : ?>
			<button type="button" role="tab" class="sk-tab" id="<?php echo esc_attr( "$id-$key-tab" ); ?>" aria-controls="<?php echo esc_attr( "$id-$key" ); ?>" aria-selected="<?php echo 'today' === $key ? 'true' : 'false'; ?>" <?php echo 'today' === $key ? '' : 'tabindex="-1"'; ?>><?php echo esc_html( $label ); ?></button>
			<?php endforeach; ?>
		</div>
		<?php
		foreach ( $days as $key => [ $label, $date ] ) :
			$r   = Data::reading( $slug, $date );
			$sky = Data::day( $date );
			?>
		<section class="sk-reading__panel" id="<?php echo esc_attr( "$id-$key" ); ?>" role="tabpanel" aria-labelledby="<?php echo esc_attr( "$id-$key-tab" ); ?>" data-panel>
			<p class="sk-reading__day"><?php echo esc_html( $label . ' · ' . Data::format_date( $date, 'j F Y, l' ) ); ?> <?php echo View::tag( 'editorial' === $r['source'] ? 'editorial' : 'computed' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></p>
			<h2 class="sk-reading__theme"><?php echo esc_html( $r['theme'] ); ?></h2>
			<?php if ( ! empty( $r['html'] ) ) : ?>
			<div class="sk-reading__text"><?php echo wp_kses_post( $r['html'] ); ?></div>
			<p class="sk-muted sk-small"><?php echo esc_html( 'Yazan: ' . $r['author'] ); ?></p>
			<?php else : ?>
			<p class="sk-reading__text"><?php echo esc_html( $r['text'] ); ?></p>
			<?php endif; ?>
			<dl class="sk-facts sk-facts--inline">
				<div><dt>Ay</dt><dd><?php echo esc_html( View::sign_name( $sky['moon']['sign'] ) . ' · ' . $sign['name'] . ' için ' . $r['house'] . '. ev' ); ?></dd></div>
				<div><dt>Güneş</dt><dd><?php echo esc_html( View::sign_name( $sky['sun']['sign'] ) ); ?></dd></div>
				<?php if ( $sky['phase'] ) : ?><div><dt>Ay fazı</dt><dd><?php echo esc_html( $sky['phase']['name'] ); ?></dd></div><?php endif; ?>
			</dl>
		</section>
		<?php endforeach; ?>
		<p class="sk-method">Otomatik okuma, Ay'ın <?php echo esc_html( $sign['name'] ); ?> burcuna göre bulunduğu eve (güneş burcu ev sistemi), Ay'ın burç değiştirme saatine ve günün en sıkı gezegen açısına dayanır. Kişisel doğum haritası daha ayrıntılı bir resim sunar.</p>
		<div class="sk-actions">
			<?php if ( $page ) : ?>
			<?php echo View::button( Data::sign_url( $slug ), $sign['name'] . ' burcunu tanı', 'secondary', '' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
			<?php endif; ?>
			<?php echo View::button( Data::page_url( 'dogum-haritasi' ), 'Doğum haritanla daha kişisel bak', $page ? 'ghost' : 'secondary' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
		</div>
	</div>
</div>
