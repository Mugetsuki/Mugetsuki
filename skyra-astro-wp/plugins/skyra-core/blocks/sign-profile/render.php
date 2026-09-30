<?php
/**
 * Sign page header (contains the page H1) and key facts.
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
$slug    = $slug ?: (string) get_post_field( 'post_name', $post_id );
$sign    = Zodiac::by_slug( $slug );
if ( ! $sign ) {
	return;
}
$opp   = Zodiac::SIGNS[ ( $sign['index'] + 6 ) % 12 ];
$title = $post_id ? get_the_title( $post_id ) : $sign['name'];
$facts = array(
	'Tarih aralığı'   => Zodiac::date_range( $sign ),
	'Element'         => Zodiac::ELEMENTS[ $sign['element'] ]['name'],
	'Nitelik'         => Zodiac::MODALITIES[ $sign['modality'] ],
	'Yönetici'        => Zodiac::PLANETS[ $sign['ruler'] ]['name'],
	'Karşıt burç'     => $opp['name'],
	'Anahtar kelimeler' => implode( ', ', $sign['keywords'] ),
);
?>
<header <?php echo get_block_wrapper_attributes( array( 'class' => 'sk-signhero sk-el-' . $sign['element'] ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?> data-sign-page="<?php echo esc_attr( $slug ); ?>">
	<div class="sk-container sk-signhero__grid">
		<div class="sk-signhero__glyph" aria-hidden="true">
			<svg class="sk-signhero__rings" viewBox="0 0 300 300" focusable="false"><circle cx="150" cy="150" r="146"/><circle cx="150" cy="150" r="110"/><ellipse cx="150" cy="150" rx="146" ry="54" transform="rotate(-24 150 150)"/></svg>
			<?php echo Icons::zodiac( $slug, array( 'size' => 120, 'stroke' => '1.1' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
		</div>
		<div class="sk-signhero__copy">
			<p class="sk-eyebrow"><?php echo esc_html( Zodiac::ELEMENTS[ $sign['element'] ]['name'] . ' burcu · ' . Zodiac::MODALITIES[ $sign['modality'] ] ); ?></p>
			<h1 class="sk-signhero__title"><?php echo esc_html( $title ); ?></h1>
			<p class="sk-signhero__line"><?php echo esc_html( $sign['line'] ); ?></p>
			<dl class="sk-facts sk-facts--grid">
				<?php foreach ( $facts as $label => $value ) : ?>
				<div><dt><?php echo esc_html( $label ); ?></dt><dd><?php echo esc_html( $value ); ?></dd></div>
				<?php endforeach; ?>
			</dl>
			<div class="sk-actions">
				<?php echo View::button( Data::daily_url( $slug ), $sign['name'] . ' günlük yorumu', 'primary' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				<?php echo View::button( Data::page_url( 'dogum-haritasi' ), 'Doğum haritanı keşfet', 'secondary', '' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
			</div>
		</div>
	</div>
</header>
