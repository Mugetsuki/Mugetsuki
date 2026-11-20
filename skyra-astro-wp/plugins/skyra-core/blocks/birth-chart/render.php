<?php
/**
 * Birth chart: home conversion section (step by step) or the full tool page.
 *
 * @package Skyra\Core
 */

use Skyra\Astro\Chart;
use Skyra\Core\Blocks;
use Skyra\Core\Data;
use Skyra\Core\Icons;
use Skyra\Core\Rest;
use Skyra\Core\Results;
use Skyra\Core\View;
use Skyra\Core\Wheel;

defined( 'ABSPATH' ) || exit;

$home    = 'home' === ( $attributes['variant'] ?? 'full' );
$values  = array();
$result  = '';
$error   = '';
if ( ! $home && Blocks::posted( 'chart' ) ) {
	$values = Blocks::post_values();
	$data   = Rest::compute( 'chart', array_merge( $values, array( 'variant' => 'full' ) ) );
	if ( is_wp_error( $data ) ) {
		$error = $data->get_error_message();
	} else {
		$result = $data['html'];
	}
}

$form = View::birth_form(
	array(
		'tool'    => 'chart',
		'variant' => $home ? 'compact' : 'full',
		'action'  => Data::page_url( 'dogum-haritasi' ),
		'submit'  => 'Haritamı Oluştur',
		'stepper' => $home,
		'level'   => $home ? 3 : 2,
		'values'  => $values,
	)
);

if ( $home ) :
	// A fixed, labelled example so the section shows what a chart looks like.
	$example = Chart::natal(
		array(
			'date' => '2000-01-01',
			'time' => '12:00',
			'lat'  => 41.0138,
			'lon'  => 28.9497,
			'tz'   => 'Europe/Istanbul',
		)
	);
	$head_id = View::uid( 'chart' );
	?>
<section <?php echo get_block_wrapper_attributes( array( 'class' => 'sk-section sk-chartcta', 'aria-labelledby' => $head_id ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>>
	<div class="sk-container sk-chartcta__grid">
		<div class="sk-chartcta__copy">
			<?php
			echo View::head( // phpcs:ignore WordPress.Security.EscapeOutput
				array(
					'eyebrow' => Blocks::attr( $attributes, 'eyebrow' ),
					'heading' => Blocks::attr( $attributes, 'heading', 'Gökyüzünün sana özel halini keşfet.' ),
					'intro'   => Blocks::attr( $attributes, 'intro' ),
					'id'      => $head_id,
				)
			);
			?>
			<ul class="sk-checklist">
				<li><?php echo Icons::svg( 'check', array( 'size' => 18 ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>Güneş, Ay ve yükselen burcun</li>
				<li><?php echo Icons::svg( 'check', array( 'size' => 18 ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>Gezegenlerin evleri ve aralarındaki açılar</li>
				<li><?php echo Icons::svg( 'check', array( 'size' => 18 ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>Tarihsel saat dilimi ve yaz saati hesaba katılır</li>
			</ul>
			<figure class="sk-chartcta__preview" data-draw>
				<?php
				echo Wheel::svg( // phpcs:ignore WordPress.Security.EscapeOutput
					$example['bodies'],
					array(
						'asc'     => $example['angles']['asc']['lon'],
						'mc'      => $example['angles']['mc']['lon'],
						'cusps'   => $example['houses']['cusps'],
						'aspects' => $example['aspects'],
						'title'   => 'Örnek doğum haritası',
						'class'   => 'is-example',
					)
				);
				?>
				<figcaption>Örnek harita · 1 Ocak 2000, 12:00 · İstanbul</figcaption>
			</figure>
		</div>
		<div class="sk-chartcta__panel" id="harita-formu">
			<p class="sk-chartcta__panel-title">Doğum bilgilerin</p>
			<?php echo $form; // phpcs:ignore WordPress.Security.EscapeOutput ?>
			<div class="sk-tool__result" data-result-slot aria-live="polite"></div>
		</div>
	</div>
</section>
<?php else : ?>
<div <?php echo get_block_wrapper_attributes( array( 'class' => 'sk-tool sk-tool--chart' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>>
	<div class="sk-tool__form">
		<h2 class="sk-tool__form-title">Doğum bilgilerin</h2>
		<?php echo $form; // phpcs:ignore WordPress.Security.EscapeOutput ?>
	</div>
	<div class="sk-tool__result" id="sonuc" data-result-slot aria-live="polite">
		<?php if ( $error ) : ?>
			<?php echo Results::notice( $error, 'alert' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
		<?php elseif ( $result ) : ?>
			<?php echo $result; // phpcs:ignore WordPress.Security.EscapeOutput ?>
		<?php else : ?>
		<div class="sk-empty" data-empty>
			<p class="sk-eyebrow">Neler göreceksin?</p>
			<ul class="sk-empty__list">
				<li><?php echo Icons::svg( 'chart', array( 'size' => 22 ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?><div><strong>Haritanın kendisi</strong><span>12 burç, 12 ev ve gezegenlerin doğduğun andaki yerleri.</span></div></li>
				<li><?php echo Icons::svg( 'rising', array( 'size' => 22 ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?><div><strong>Güneş, Ay ve yükselen</strong><span>Kimliğin, duygusal ihtiyaçların ve dünyaya bakışın.</span></div></li>
				<li><?php echo Icons::svg( 'aspect-trine', array( 'size' => 22 ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?><div><strong>Açılar</strong><span>Gezegenler arasındaki uyum ve gerilim noktaları.</span></div></li>
				<li><?php echo Icons::svg( 'orbit', array( 'size' => 22 ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?><div><strong>Element dengesi</strong><span>Ateş, toprak, hava ve su arasında enerjinin dağılımı.</span></div></li>
			</ul>
		</div>
		<?php endif; ?>
	</div>
</div>
<?php endif; ?>
