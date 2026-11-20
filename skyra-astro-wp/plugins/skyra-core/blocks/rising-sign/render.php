<?php
/**
 * Rising sign calculator.
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
if ( Blocks::posted( 'chart' ) ) {
	$values = Blocks::post_values();
	$data   = Rest::compute( 'chart', array_merge( $values, array( 'variant' => 'rising' ) ) );
	$result = is_wp_error( $data ) ? Results::notice( $data->get_error_message(), 'alert' ) : $data['html'];
}
?>
<div <?php echo get_block_wrapper_attributes( array( 'class' => 'sk-tool sk-tool--rising' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>>
	<div class="sk-tool__form">
		<h2 class="sk-tool__form-title">Doğum bilgilerin</h2>
		<?php
		echo View::birth_form( // phpcs:ignore WordPress.Security.EscapeOutput
			array(
				'tool'          => 'chart',
				'variant'       => 'rising',
				'action'        => get_permalink(),
				'submit'        => 'Yükselenimi Hesapla',
				'time_required' => true,
				'values'        => $values,
			)
		);
		?>
	</div>
	<div class="sk-tool__result" id="sonuc" data-result-slot aria-live="polite">
		<?php if ( $result ) : ?>
			<?php echo $result; // phpcs:ignore WordPress.Security.EscapeOutput ?>
		<?php else : ?>
		<div class="sk-empty" data-empty>
			<p class="sk-eyebrow">Yükselen burç nedir?</p>
			<p>Doğduğun anda doğu ufkunda yükselen burçtur. İlk izlenimini, dünyaya yaklaşımını ve haritandaki evlerin başlangıcını belirler. Yaklaşık iki saatte bir değiştiği için doğum saatin olmadan hesaplanamaz.</p>
		</div>
		<?php endif; ?>
	</div>
</div>
