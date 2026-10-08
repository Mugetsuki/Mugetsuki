<?php
/**
 * The twelve signs grouped by element, with an element filter.
 *
 * @package Skyra\Core
 */

use Skyra\Astro\Zodiac;
use Skyra\Core\Blocks;
use Skyra\Core\Data;
use Skyra\Core\Icons;
use Skyra\Core\View;

defined( 'ABSPATH' ) || exit;

$head_id = View::uid( 'zodiac' );
$groups  = array();
foreach ( Zodiac::SIGNS as $sign ) {
	$groups[ $sign['element'] ][] = $sign;
}
?>
<section <?php echo get_block_wrapper_attributes( array( 'class' => 'sk-section sk-zodiac', 'aria-labelledby' => $head_id ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?> data-filter-scope>
	<div class="sk-container">
		<?php
		echo View::head( // phpcs:ignore WordPress.Security.EscapeOutput
			array(
				'eyebrow' => Blocks::attr( $attributes, 'eyebrow' ),
				'heading' => Blocks::attr( $attributes, 'heading', 'Burçları keşfet' ),
				'intro'   => Blocks::attr( $attributes, 'intro' ),
				'id'      => $head_id,
			)
		);
		?>
		<div class="sk-chips" role="group" aria-label="Elemente göre filtrele" data-filter hidden>
			<button type="button" class="sk-chip" aria-pressed="true" data-value="">Tümü</button>
			<?php foreach ( Zodiac::ELEMENTS as $key => $el ) : ?>
			<button type="button" class="sk-chip sk-el-<?php echo esc_attr( $key ); ?>" aria-pressed="false" data-value="<?php echo esc_attr( $key ); ?>"><?php echo Icons::svg( 'element-' . $key, array( 'size' => 16 ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?><?php echo esc_html( $el['name'] ); ?></button>
			<?php endforeach; ?>
		</div>
		<div class="sk-zodiac__grid">
			<?php foreach ( $groups as $element => $signs ) : ?>
			<div class="sk-zodiac__group sk-el-<?php echo esc_attr( $element ); ?>" data-filter-item="<?php echo esc_attr( $element ); ?>">
				<div class="sk-zodiac__el">
					<?php echo Icons::svg( 'element-' . $element, array( 'size' => 22 ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
					<p class="sk-zodiac__el-name"><?php echo esc_html( Zodiac::ELEMENTS[ $element ]['name'] ); ?></p>
					<p class="sk-zodiac__el-desc"><?php echo esc_html( Zodiac::ELEMENTS[ $element ]['desc'] ); ?></p>
				</div>
				<ul class="sk-zodiac__list" role="list">
					<?php foreach ( $signs as $sign ) : ?>
					<li class="sk-ztile" data-reveal>
						<span class="sk-ztile__glyph"><?php echo Icons::zodiac( $sign['slug'], array( 'size' => 36 ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
						<div class="sk-ztile__body">
							<h3 class="sk-ztile__name"><a class="sk-stretch" href="<?php echo esc_url( Data::sign_url( $sign['slug'] ) ); ?>"><?php echo esc_html( $sign['name'] ); ?></a></h3>
							<p class="sk-ztile__meta"><?php echo esc_html( Zodiac::ELEMENTS[ $sign['element'] ]['name'] . ' · ' . Zodiac::MODALITIES[ $sign['modality'] ] . ' · ' . Zodiac::PLANETS[ $sign['ruler'] ]['name'] ); ?></p>
							<p class="sk-ztile__line"><?php echo esc_html( $sign['line'] ); ?></p>
							<p class="sk-ztile__dates"><?php echo esc_html( Zodiac::date_range( $sign ) ); ?></p>
						</div>
					</li>
					<?php endforeach; ?>
				</ul>
			</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>
