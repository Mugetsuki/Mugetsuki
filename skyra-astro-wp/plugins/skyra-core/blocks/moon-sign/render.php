<?php
/**
 * Moon sign calculator; birth time optional.
 *
 * @package Skyra\Core
 */

use Skyra\Core\Blocks;
use Skyra\Core\Rest;
use Skyra\Core\Results;
use Skyra\Core\View;

defined( 'ABSPATH' ) || exit;

$values = array();
$result = '';
if ( Blocks::posted( 'moon-sign' ) ) {
	$values = Blocks::post_values();
	$data   = Rest::compute( 'moon-sign', $values );
	$result = is_wp_error( $data ) ? Results::notice( $data->get_error_message(), 'alert' ) : $data['html'];
}
?>
<div <?php echo get_block_wrapper_attributes( array( 'class' => 'sk-tool sk-tool--moon' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>>
	<div class="sk-tool__form">
		<h2 class="sk-tool__form-title">Doğum bilgilerin</h2>
		<?php
		echo View::birth_form( // phpcs:ignore WordPress.Security.EscapeOutput
			array(
				'tool'    => 'moon-sign',
				'variant' => 'full',
				'action'  => get_permalink(),
				'submit'  => 'Ay Burcumu Bul',
				'values'  => $values,
			)
		);
		?>
	</div>
	<div class="sk-tool__result" id="sonuc" data-result-slot aria-live="polite">
		<?php if ( $result ) : ?>
			<?php echo $result; // phpcs:ignore WordPress.Security.EscapeOutput ?>
		<?php else : ?>
		<div class="sk-empty" data-empty>
			<p class="sk-eyebrow">Ay burcu nedir?</p>
			<p>Doğduğun anda Ay'ın bulunduğu burçtur; duygusal ihtiyaçlarını, güvende hissetme biçimini ve iç dünyanı anlatır. Ay yaklaşık iki buçuk günde bir burç değiştirdiği için çoğu zaman doğum saati olmadan da bulunabilir. O gün burç değiştiyse iki olasılığı da saatiyle birlikte gösteririz.</p>
		</div>
		<?php endif; ?>
	</div>
</div>
