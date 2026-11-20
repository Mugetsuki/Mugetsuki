<?php
/**
 * Retrograde periods of the year, per planet, with a 12-month track.
 *
 * @package Skyra\Core
 */

use Skyra\Astro\Readings;
use Skyra\Astro\Zodiac;
use Skyra\Core\Data;
use Skyra\Core\Icons;
use Skyra\Core\View;

defined( 'ABSPATH' ) || exit;

$current = (int) Data::today();
// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- public, read-only year switch.
$year  = isset( $_GET['yil'] ) ? (int) $_GET['yil'] : ( (int) ( $attributes['year'] ?? 0 ) ?: $current );
$year  = max( $current - 5, min( $current + 5, $year ) );
$data  = Data::retro_year( $year );
$now   = Data::now();
$zone  = Data::zone();
$start = ( new DateTimeImmutable( "$year-01-01 00:00", $zone ) )->getTimestamp();
$end   = ( new DateTimeImmutable( ( $year + 1 ) . '-01-01 00:00', $zone ) )->getTimestamp();
$span  = $end - $start;
$pct   = static fn( int $t ) => max( 0, min( 100, ( $t - $start ) / $span * 100 ) );
$base  = get_permalink();
$active = array();
foreach ( $data as $body => $periods ) {
	foreach ( $periods as $p ) {
		if ( $p['start'] <= $now && $now < $p['end'] ) {
			$active[ $body ] = $p;
		}
	}
}
?>
<div <?php echo get_block_wrapper_attributes( array( 'class' => 'sk-retro' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>>
	<div class="sk-calendar__bar">
		<nav class="sk-yearnav" aria-label="Yıl seç">
			<a href="<?php echo esc_url( add_query_arg( 'yil', $year - 1, $base ) ); ?>" rel="nofollow"><?php echo Icons::svg( 'chevron-left', array( 'size' => 18 ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?><span class="sk-vh">Önceki yıl</span><?php echo (int) ( $year - 1 ); ?></a>
			<strong aria-current="page"><?php echo (int) $year; ?></strong>
			<a href="<?php echo esc_url( add_query_arg( 'yil', $year + 1, $base ) ); ?>" rel="nofollow"><?php echo (int) ( $year + 1 ); ?><span class="sk-vh">Sonraki yıl</span><?php echo Icons::svg( 'chevron-right', array( 'size' => 18 ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?></a>
		</nav>
	</div>

	<?php if ( $year === $current ) : ?>
	<div class="sk-retro__now sk-card">
		<p class="sk-label">Şu an retro</p>
		<?php if ( $active ) : ?>
		<ul class="sk-planet-list">
			<?php foreach ( $active as $body => $p ) : ?>
			<li><?php echo Icons::planet( $body, array( 'size' => 18 ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?><span><?php echo esc_html( Zodiac::PLANETS[ $body ]['name'] ); ?></span><small><?php echo esc_html( Data::until( $p['end'] ) ); ?></small></li>
			<?php endforeach; ?>
		</ul>
		<?php else : ?>
		<p>Şu an geri harekette gezegen yok.</p>
		<?php endif; ?>
	</div>
	<?php endif; ?>

	<div class="sk-retro__months" aria-hidden="true">
		<?php for ( $m = 1; $m <= 12; $m++ ) : ?><span><?php echo esc_html( mb_substr( Zodiac::MONTHS[ $m ], 0, 3 ) ); ?></span><?php endfor; ?>
	</div>
	<ol class="sk-retro__list" role="list">
		<?php foreach ( $data as $body => $periods ) : ?>
		<li class="sk-retro__planet">
			<div class="sk-retro__who">
				<?php echo View::planet_badge( $body ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				<div>
					<h2 class="sk-retro__name"><?php echo esc_html( Zodiac::PLANETS[ $body ]['name'] . ' retrosu' ); ?></h2>
					<p class="sk-muted sk-small"><?php echo esc_html( Readings::RETRO[ $body ][0] ); ?></p>
				</div>
			</div>
			<div class="sk-retro__track" aria-hidden="true">
				<?php if ( $year === $current ) : ?><span class="sk-retro__today" style="left:<?php echo esc_attr( number_format( $pct( $now ), 2, '.', '' ) ); ?>%"></span><?php endif; ?>
				<?php foreach ( $periods as $p ) : $l = $pct( $p['start'] ); $w = max( 0.8, $pct( $p['end'] ) - $l ); ?>
				<span class="sk-retro__bar<?php echo isset( $active[ $body ] ) && $active[ $body ] === $p ? ' is-active' : ''; ?>" style="left:<?php echo esc_attr( number_format( $l, 2, '.', '' ) ); ?>%;width:<?php echo esc_attr( number_format( $w, 2, '.', '' ) ); ?>%"></span>
				<?php endforeach; ?>
			</div>
			<ul class="sk-retro__periods" role="list">
				<?php foreach ( $periods as $p ) : ?>
				<li>
					<strong><?php echo esc_html( Data::format( $p['start'], 'j F Y' ) . ' – ' . Data::format( $p['end'], 'j F Y' ) ); ?></strong>
					<span><?php echo esc_html( sprintf( '%s %s → %s %s', View::sign_name( $p['sign_start'] ), $p['degree_start'], View::sign_name( $p['sign_end'] ), $p['degree_end'] ) ); ?></span>
				</li>
				<?php endforeach; ?>
				<?php if ( ! $periods ) : ?><li class="sk-muted">Bu yıl retro dönemi yok.</li><?php endif; ?>
			</ul>
			<p class="sk-retro__meaning"><?php echo esc_html( Readings::RETRO[ $body ][1] ); ?></p>
		</li>
		<?php endforeach; ?>
	</ol>
	<p class="sk-footnote">Başlangıç ve bitiş, gezegenin durağan göründüğü (istasyon) anlardır; tarihler Türkiye saatine göredir. Yıl sınırını aşan dönemler de listelenir.</p>
</div>
