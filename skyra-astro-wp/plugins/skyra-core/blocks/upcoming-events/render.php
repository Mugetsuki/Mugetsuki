<?php
/**
 * Upcoming sky events: month overview + timeline.
 *
 * @package Skyra\Core
 */

use Skyra\Core\Blocks;
use Skyra\Core\Data;
use Skyra\Core\View;

defined( 'ABSPATH' ) || exit;

$count   = max( 3, min( 12, (int) ( $attributes['count'] ?? 6 ) ) );
$events  = Data::upcoming( $count );
$head_id = View::uid( 'events' );

// Month overview for the current month.
$now    = ( new DateTimeImmutable( '@' . Data::now() ) )->setTimezone( Data::zone() );
$first  = $now->modify( 'first day of this month' )->setTime( 0, 0 );
$days   = (int) $first->format( 't' );
$offset = ( (int) $first->format( 'N' ) ) - 1;
$marks  = array();
foreach ( Data::year_events( (int) $first->format( 'Y' ) ) as $e ) {
	if ( Data::format( $e['unix'], 'Y-m' ) === $first->format( 'Y-m' ) ) {
		$marks[ (int) Data::format( $e['unix'], 'j' ) ][] = $e['type'];
	}
}
?>
<section <?php echo get_block_wrapper_attributes( array( 'class' => 'sk-section sk-upcoming', 'aria-labelledby' => $head_id ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>>
	<div class="sk-container sk-upcoming__grid">
		<div class="sk-upcoming__side">
			<?php
			echo View::head( // phpcs:ignore WordPress.Security.EscapeOutput
				array(
					'eyebrow' => Blocks::attr( $attributes, 'eyebrow' ),
					'heading' => Blocks::attr( $attributes, 'heading', 'Yaklaşan gökyüzü olayları' ),
					'intro'   => Blocks::attr( $attributes, 'intro' ),
					'id'      => $head_id,
				)
			);
			?>
			<div class="sk-month-mini" aria-hidden="true">
				<p class="sk-month-mini__title"><?php echo esc_html( Data::format( $first->getTimestamp() + 43200, 'F Y' ) ); ?></p>
				<div class="sk-month-mini__grid">
					<?php foreach ( array( 'Pt', 'Sa', 'Ça', 'Pe', 'Cu', 'Ct', 'Pz' ) as $wd ) : ?><span class="is-wd"><?php echo esc_html( $wd ); ?></span><?php endforeach; ?>
					<?php for ( $i = 0; $i < $offset; $i++ ) : ?><span></span><?php endfor; ?>
					<?php for ( $d = 1; $d <= $days; $d++ ) : ?>
					<span class="<?php echo esc_attr( trim( ( (int) $now->format( 'j' ) === $d ? 'is-today ' : '' ) . ( isset( $marks[ $d ] ) ? 'has-event is-' . str_replace( '_', '-', $marks[ $d ][0] ) : '' ) ) ); ?>"><?php echo (int) $d; ?></span>
					<?php endfor; ?>
				</div>
			</div>
			<?php echo View::arrow_link( Data::page_url( 'astroloji-takvimi' ), 'Takvimin tamamı', 'sk-upcoming__all' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
		</div>
		<ol class="sk-timeline" role="list">
			<?php foreach ( $events as $e ) : ?>
			<li data-reveal><?php echo View::event_card( $e ); // phpcs:ignore WordPress.Security.EscapeOutput ?></li>
			<?php endforeach; ?>
		</ol>
	</div>
</section>
