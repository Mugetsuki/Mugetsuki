<?php
/**
 * Moon phase: illustration, data and the eight-phase cycle.
 *
 * @package Skyra\Core
 */

use Skyra\Astro\Events;
use Skyra\Astro\Zodiac;
use Skyra\Core\Blocks;
use Skyra\Core\Data;
use Skyra\Core\View;

defined( 'ABSPATH' ) || exit;

$sky     = Data::snapshot();
$phase   = $sky['phase'];
$text    = Data::phase_text( $phase['key'] );
$head_id = View::uid( 'moon' );
$moon    = Zodiac::by_slug( $sky['moon']['sign'] );
$facts   = array(
	'Aydınlanma'      => Data::percent( $phase['illumination'] ),
	"Ay'ın yaşı"      => number_format( $phase['age'], 1, ',', '' ) . ' gün',
	"Ay'ın burcu"     => $moon['name'] . ' ' . $sky['moon']['degree'],
	'Sonraki Yeni Ay' => Data::format( $phase['next']['new'], 'j F, H:i' ),
	'Sonraki Dolunay' => Data::format( $phase['next']['full'], 'j F, H:i' ),
);
// Illumination at the centre of each of the eight phases.
$cycle = array( 0.0, 0.1464, 0.5, 0.8536, 1.0, 0.8536, 0.5, 0.1464 );
?>
<section <?php echo get_block_wrapper_attributes( array( 'class' => 'sk-section sk-moonphase', 'aria-labelledby' => $head_id ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>>
	<div class="sk-container sk-moonphase__grid">
		<div class="sk-moonphase__visual" data-moon-anim>
			<svg class="sk-moonphase__rings" viewBox="0 0 400 400" aria-hidden="true" focusable="false"><circle cx="200" cy="200" r="196"/><circle cx="200" cy="200" r="160"/></svg>
			<?php echo View::moon( $phase['illumination'], $phase['waxing'], 260, 'sk-moonphase__moon' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
			<p class="sk-moonphase__pct" aria-hidden="true"><?php echo esc_html( Data::percent( $phase['illumination'] ) ); ?></p>
		</div>
		<div class="sk-moonphase__copy">
			<?php
			echo View::head( // phpcs:ignore WordPress.Security.EscapeOutput
				array(
					'eyebrow' => Blocks::attr( $attributes, 'eyebrow' ),
					'heading' => Blocks::attr( $attributes, 'heading', 'Ay bugün hangi hikâyeyi anlatıyor?' ),
					'intro'   => Blocks::attr( $attributes, 'intro' ),
					'id'      => $head_id,
				)
			);
			?>
			<h3 class="sk-moonphase__name"><?php echo esc_html( $phase['name'] ); ?> <span><?php echo esc_html( $moon['loc'] ); ?></span></h3>
			<p class="sk-moonphase__text"><?php echo esc_html( $text['text'] ); ?></p>
			<dl class="sk-facts">
				<?php foreach ( $facts as $label => $value ) : ?>
				<div><dt><?php echo esc_html( $label ); ?></dt><dd><?php echo esc_html( $value ); ?></dd></div>
				<?php endforeach; ?>
			</dl>
		</div>
		<ol class="sk-phases" aria-label="Ay döngüsü">
			<?php foreach ( Events::PHASES as $i => $p ) : ?>
			<li class="sk-phases__item<?php echo $i === $phase['index'] ? ' is-current' : ''; ?>"<?php echo $i === $phase['index'] ? ' aria-current="step"' : ''; ?>>
				<?php echo View::moon( $cycle[ $i ], $i < 4, 36 ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				<span><?php echo esc_html( $p['name'] ); ?></span>
			</li>
			<?php endforeach; ?>
		</ol>
	</div>
</section>
