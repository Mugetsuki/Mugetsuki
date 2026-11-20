<?php
/**
 * Sign compatibility: two selects, result in place. Pre-selection and a
 * server-rendered result via ?a=&b= (links from sign pages, no-JS).
 *
 * @package Skyra\Core
 */

use Skyra\Astro\Zodiac;
use Skyra\Core\Rest;
use Skyra\Core\View;

defined( 'ABSPATH' ) || exit;

// phpcs:disable WordPress.Security.NonceVerification.Recommended -- public, read-only.
$a = isset( $_GET['a'] ) ? sanitize_key( $_GET['a'] ) : '';
$b = isset( $_GET['b'] ) ? sanitize_key( $_GET['b'] ) : '';
// phpcs:enable
$result = '';
if ( Zodiac::by_slug( $a ) && Zodiac::by_slug( $b ) ) {
	$data   = Rest::compute( 'compatibility', array( 'a' => $a, 'b' => $b ) );
	$result = is_wp_error( $data ) ? '' : $data['html'];
} else {
	$a = $attributes['a'] ?? 'koc';
	$b = $attributes['b'] ?? 'terazi';
}
$id     = View::uid( 'compat' );
$select = static function ( string $name, string $label, string $value ) use ( $id ) {
	$out = sprintf( '<div class="sk-field"><label for="%1$s-%2$s">%3$s</label><select id="%1$s-%2$s" name="%2$s">', esc_attr( $id ), esc_attr( $name ), esc_html( $label ) );
	foreach ( Zodiac::SIGNS as $s ) {
		$out .= sprintf( '<option value="%s"%s>%s</option>', esc_attr( $s['slug'] ), selected( $value, $s['slug'], false ), esc_html( $s['name'] . ' (' . Zodiac::date_range( $s ) . ')' ) );
	}
	return $out . '</select></div>';
};
?>
<div <?php echo get_block_wrapper_attributes( array( 'class' => 'sk-tool sk-tool--compat' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>>
	<div class="sk-tool__form">
		<h2 class="sk-tool__form-title">İki burç seç</h2>
		<form class="sk-form" method="get" action="<?php echo esc_url( get_permalink() . '#sonuc' ); ?>" data-tool="compatibility" data-level="2">
			<?php echo $select( 'a', 'Birinci burç', $a ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
			<?php echo $select( 'b', 'İkinci burç', $b ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
			<button type="submit" class="sk-btn sk-btn--primary sk-btn--lg" data-submit><span>Uyumu Oku</span></button>
			<p class="sk-status" role="status" aria-live="polite" data-status></p>
		</form>
	</div>
	<div class="sk-tool__result" id="sonuc" data-result-slot aria-live="polite">
		<?php if ( $result ) : ?>
			<?php echo $result; // phpcs:ignore WordPress.Security.EscapeOutput ?>
		<?php else : ?>
		<div class="sk-empty" data-empty>
			<p class="sk-eyebrow">Nasıl okunur?</p>
			<p>İki burç arasındaki açı (aynı burç, altmışlık, kare, üçgen, karşıt…) ve elementlerin birbirini nasıl etkilediği, ilişkinin genel dinamiği hakkında fikir verir. Bu bir yargı değil, konuşmaya açılan bir kapıdır.</p>
		</div>
		<?php endif; ?>
	</div>
</div>
