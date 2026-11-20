<?php
/**
 * Full-year astrology calendar, month by month, filterable by event type.
 *
 * @package Skyra\Core
 */

use Skyra\Astro\Zodiac;
use Skyra\Core\Data;
use Skyra\Core\Icons;
use Skyra\Core\View;

defined( 'ABSPATH' ) || exit;

$current = (int) Data::today();
// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- public, read-only year switch.
$year   = isset( $_GET['yil'] ) ? (int) $_GET['yil'] : ( (int) ( $attributes['year'] ?? 0 ) ?: $current );
$year   = max( $current - 5, min( $current + 5, $year ) );
$events = Data::year_events( $year );
$now    = Data::now();
$months = array();
foreach ( $events as $e ) {
	$months[ (int) Data::format( $e['unix'], 'n' ) ][] = $e;
}
$groups = array(
	'moon'    => array( 'Yeni Ay ve Dolunay', array( 'new_moon', 'full_moon' ) ),
	'eclipse' => array( 'Tutulmalar', array( 'solar_eclipse', 'lunar_eclipse' ) ),
	'retro'   => array( 'Retrolar', array( 'station_retro', 'station_direct' ) ),
	'ingress' => array( 'Burç geçişleri', array( 'ingress' ) ),
);
$group_of = static function ( string $type ) use ( $groups ): string {
	foreach ( $groups as $key => [ , $types ] ) {
		if ( in_array( $type, $types, true ) ) {
			return $key;
		}
	}
	return '';
};
$eclipses = array_filter( $events, static fn( $e ) => str_ends_with( $e['type'], 'eclipse' ) );
$base     = get_permalink();
?>
<div <?php echo get_block_wrapper_attributes( array( 'class' => 'sk-calendar' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?> data-filter-scope>
	<div class="sk-calendar__bar">
		<nav class="sk-yearnav" aria-label="Yıl seç">
			<a href="<?php echo esc_url( add_query_arg( 'yil', $year - 1, $base ) ); ?>" rel="nofollow"><?php echo Icons::svg( 'chevron-left', array( 'size' => 18 ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?><span class="sk-vh">Önceki yıl</span><?php echo (int) ( $year - 1 ); ?></a>
			<strong aria-current="page"><?php echo (int) $year; ?></strong>
			<a href="<?php echo esc_url( add_query_arg( 'yil', $year + 1, $base ) ); ?>" rel="nofollow"><?php echo (int) ( $year + 1 ); ?><span class="sk-vh">Sonraki yıl</span><?php echo Icons::svg( 'chevron-right', array( 'size' => 18 ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?></a>
		</nav>
		<div class="sk-chips" role="group" aria-label="Olay türüne göre filtrele" data-filter hidden>
			<button type="button" class="sk-chip" aria-pressed="true" data-value="">Tümü</button>
			<?php foreach ( $groups as $key => [ $label ] ) : ?>
			<button type="button" class="sk-chip" aria-pressed="false" data-value="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $label ); ?></button>
			<?php endforeach; ?>
		</div>
	</div>

	<?php if ( $eclipses ) : ?>
	<section class="sk-calendar__highlights" aria-labelledby="sk-ecl-h">
		<h2 id="sk-ecl-h"><?php echo esc_html( $year . ' tutulmaları' ); ?></h2>
		<ul role="list">
			<?php foreach ( $eclipses as $e ) : ?>
			<li class="sk-card<?php echo $e['unix'] < $now ? ' is-past' : ''; ?>">
				<p class="sk-label"><?php echo esc_html( Data::format( $e['unix'], 'j F Y' ) ); ?></p>
				<p class="sk-card__title"><?php echo esc_html( $e['title'] ); ?></p>
				<p class="sk-muted sk-small"><?php echo esc_html( sprintf( 'Tepe: %s TSİ · %s %s%s', Data::format( $e['unix'], 'H:i' ), View::sign_name( $e['sign'] ), $e['degree'], $e['unix'] < $now ? ' · geçti' : '' ) ); ?></p>
			</li>
			<?php endforeach; ?>
		</ul>
	</section>
	<?php endif; ?>

	<nav class="sk-monthjump" aria-label="Aya git">
		<?php for ( $m = 1; $m <= 12; $m++ ) : ?>
		<a href="#ay-<?php echo (int) $m; ?>"<?php echo ( $year === $current && (int) Data::format( $now, 'n' ) === $m ) ? ' aria-current="date"' : ''; ?>><?php echo esc_html( mb_substr( Zodiac::MONTHS[ $m ], 0, 3 ) ); ?></a>
		<?php endfor; ?>
	</nav>

	<?php for ( $m = 1; $m <= 12; $m++ ) : ?>
	<section class="sk-month" id="ay-<?php echo (int) $m; ?>" aria-labelledby="ay-<?php echo (int) $m; ?>-h">
		<h2 class="sk-month__title" id="ay-<?php echo (int) $m; ?>-h"><?php echo esc_html( Zodiac::MONTHS[ $m ] . ' ' . $year ); ?></h2>
		<?php if ( empty( $months[ $m ] ) ) : ?>
		<p class="sk-muted">Bu ay için listelenen olay yok.</p>
		<?php else : ?>
		<ol class="sk-timeline" role="list">
			<?php foreach ( $months[ $m ] as $e ) : ?>
			<li class="<?php echo $e['unix'] < $now ? 'is-past' : ''; ?>" data-filter-item="<?php echo esc_attr( $group_of( $e['type'] ) ); ?>">
				<?php echo View::event_card( $e ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				<?php if ( $e['unix'] < $now ) : ?><span class="sk-vh">Geçmiş olay</span><?php endif; ?>
			</li>
			<?php endforeach; ?>
		</ol>
		<?php endif; ?>
	</section>
	<?php endfor; ?>
	<p class="sk-footnote">Saatler Türkiye saatiyle (TSİ, UTC+3) verilir. Olay zamanları Skyra efemeris motoruyla hesaplanır; dakika düzeyinde küçük farklar olabilir. Çok zayıf yarıgölge Ay tutulmaları listelenmeyebilir.</p>
</div>
