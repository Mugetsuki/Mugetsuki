<?php
/**
 * The day's astrological tone: a typographic statement plus element balance.
 *
 * @package Skyra\Core
 */

use Skyra\Astro\Readings;
use Skyra\Astro\Zodiac;
use Skyra\Core\Blocks;
use Skyra\Core\Data;
use Skyra\Core\Results;
use Skyra\Core\View;

defined( 'ABSPATH' ) || exit;

$sky     = Data::snapshot();
$moon    = Zodiac::by_slug( $sky['moon']['sign'] );
$aspect  = $sky['aspects'][0] ?? null;
$bal     = $sky['balance'];
$top     = array_search( max( $bal['elements'] ), $bal['elements'], true );
$head_id = View::uid( 'energy' );
?>
<section <?php echo get_block_wrapper_attributes( array( 'class' => 'sk-section sk-energy', 'aria-labelledby' => $head_id ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>>
	<div class="sk-container sk-energy__grid">
		<div class="sk-energy__copy" data-reveal>
			<p class="sk-eyebrow"><?php echo esc_html( Blocks::attr( $attributes, 'eyebrow', 'Günün enerjisi' ) ); ?></p>
			<h2 class="sk-energy__heading" id="<?php echo esc_attr( $head_id ); ?>"><?php echo esc_html( Blocks::attr( $attributes, 'heading', 'Bugünün tonu' ) ); ?>: <span><?php echo esc_html( $sky['theme'][0] ); ?></span></h2>
			<p class="sk-energy__why">
				<?php
				echo esc_html(
					sprintf( 'Ay %s. %s', $moon['loc'], $sky['theme'][1] )
					. ( $aspect ? ' ' . Readings::sky_aspect( $aspect ) : '' )
				);
				?>
			</p>
			<p class="sk-small sk-muted"><?php echo View::tag( 'reading' ); // phpcs:ignore WordPress.Security.EscapeOutput ?> Ay'ın burcundan ve günün en sıkı açısından derlenir; bir öneri alanıdır, kehanet değil.</p>
		</div>
		<div class="sk-energy__data sk-card" data-reveal>
			<div class="sk-card__top"><h3 class="sk-card__title">Element dengesi</h3><?php echo View::tag( 'data' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
			<p class="sk-muted"><?php echo esc_html( sprintf( 'On gezegenin şu anki burçlarına göre. Bugün öne çıkan element: %s.', Zodiac::ELEMENTS[ $top ]['name'] ) ); ?></p>
			<?php echo Results::balance( $bal ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
		</div>
	</div>
</section>
