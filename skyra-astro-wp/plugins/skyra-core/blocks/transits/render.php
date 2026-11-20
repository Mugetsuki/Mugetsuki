<?php
/**
 * Current (or chosen) planetary positions, retrogrades and aspects.
 *
 * @package Skyra\Core
 */

use Skyra\Astro\Readings;
use Skyra\Astro\Sky;
use Skyra\Astro\Zodiac;
use Skyra\Core\Data;
use Skyra\Core\Icons;
use Skyra\Core\Post_Types;
use Skyra\Core\View;
use Skyra\Core\Wheel;

defined( 'ABSPATH' ) || exit;

// phpcs:disable WordPress.Security.NonceVerification.Recommended -- public, read-only date switch.
$date = isset( $_GET['tarih'] ) ? sanitize_text_field( wp_unslash( $_GET['tarih'] ) ) : '';
$time = isset( $_GET['saat'] ) ? sanitize_text_field( wp_unslash( $_GET['saat'] ) ) : '12:00';
// phpcs:enable
$custom = (bool) preg_match( '/^(19|20)\d{2}-\d{2}-\d{2}$/', $date ) && preg_match( '/^\d{2}:\d{2}$/', $time );
if ( $custom ) {
	try {
		$unix = ( new DateTimeImmutable( "$date $time", Data::zone() ) )->getTimestamp();
		$sky  = Sky::snapshot( $unix, Data::tz() );
	} catch ( Exception $e ) {
		$custom = false;
	}
}
if ( ! $custom ) {
	$sky = Data::snapshot();
}
$notes = get_posts(
	array(
		'post_type'      => Post_Types::TRANSIT,
		'post_status'    => 'publish',
		'posts_per_page' => 20,
		'no_found_rows'  => true,
	)
);
$active_notes = array_filter(
	$notes,
	static function ( $p ) use ( $sky ) {
		$planet = get_post_meta( $p->ID, 'skyra_planet', true );
		return isset( $sky['bodies'][ $planet ] ) && get_post_meta( $p->ID, 'skyra_sign', true ) === $sky['bodies'][ $planet ]['sign'];
	}
);
$form_id = View::uid( 'transit' );
?>
<div <?php echo get_block_wrapper_attributes( array( 'class' => 'sk-transits' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>>
	<form class="sk-transits__form sk-card" method="get" action="<?php echo esc_url( get_permalink() ); ?>">
		<div class="sk-field"><label for="<?php echo esc_attr( $form_id ); ?>-d">Tarih</label><input id="<?php echo esc_attr( $form_id ); ?>-d" type="date" name="tarih" min="1900-01-01" max="2100-12-31" value="<?php echo esc_attr( Data::format( $sky['unix'], 'Y-m-d' ) ); ?>"></div>
		<div class="sk-field"><label for="<?php echo esc_attr( $form_id ); ?>-t">Saat (TSİ)</label><input id="<?php echo esc_attr( $form_id ); ?>-t" type="time" name="saat" value="<?php echo esc_attr( Data::format( $sky['unix'], 'H:i' ) ); ?>"></div>
		<button type="submit" class="sk-btn sk-btn--secondary">Göster</button>
		<?php if ( $custom ) : ?><a class="sk-link" href="<?php echo esc_url( get_permalink() ); ?>">Şimdiye dön</a><?php endif; ?>
	</form>
	<p class="sk-transits__when"><?php echo esc_html( ( $custom ? 'Seçilen an: ' : 'Şu an: ' ) . Data::format( $sky['unix'], 'j F Y, l · H:i' ) . ' TSİ' ); ?></p>

	<div class="sk-transits__grid">
		<figure class="sk-chart-figure sk-transits__wheel" data-draw>
			<?php
			echo Wheel::svg( // phpcs:ignore WordPress.Security.EscapeOutput
				$sky['bodies'],
				array(
					'aspects' => $sky['aspects'],
					'title'   => 'Transit haritası',
					'desc'    => 'Gezegenlerin seçilen andaki konumları ve aralarındaki sıkı açılar.',
				)
			);
			?>
			<figcaption class="sk-legend"><span class="sk-legend__item is-soft">Uyumlu açı</span><span class="sk-legend__item is-hard">Gerilimli açı</span></figcaption>
		</figure>
		<div class="sk-table-wrap" tabindex="0" role="region" aria-label="Gezegen konumları">
			<table class="sk-table">
				<caption>Gezegen konumları</caption>
				<thead><tr><th scope="col">Gezegen</th><th scope="col">Burç</th><th scope="col">Derece</th><th scope="col">Günlük hareket</th></tr></thead>
				<tbody>
				<?php foreach ( $sky['bodies'] as $key => $b ) : ?>
					<tr<?php echo $b['retro'] ? ' class="is-retro"' : ''; ?>>
						<th scope="row"><?php echo Icons::planet( $key, array( 'size' => 16 ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?> <?php echo esc_html( $b['name'] ); ?></th>
						<td><?php echo Icons::zodiac( $b['sign'], array( 'size' => 16 ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?> <?php echo esc_html( View::sign_name( $b['sign'] ) ); ?></td>
						<td><?php echo esc_html( $b['degree'] ); ?></td>
						<td><?php echo esc_html( number_format( $b['speed'], 3, ',', '' ) . '°' ); ?><?php echo $b['retro'] ? ' <abbr title="Retro (geri hareket)">R</abbr>' : ''; ?></td>
					</tr>
				<?php endforeach; ?>
				</tbody>
			</table>
		</div>
	</div>

	<section class="sk-transits__aspects" aria-labelledby="<?php echo esc_attr( $form_id ); ?>-ah">
		<h2 id="<?php echo esc_attr( $form_id ); ?>-ah">Gezegenler arası açılar</h2>
		<?php if ( $sky['aspects'] ) : ?>
		<ul class="sk-aspect-list">
			<?php foreach ( $sky['aspects'] as $a ) : $asp = Zodiac::ASPECTS[ $a['type'] ]; ?>
			<li class="is-<?php echo esc_attr( $asp['kind'] ); ?>">
				<p class="sk-aspect-list__title"><?php echo esc_html( Zodiac::PLANETS[ $a['a'] ]['name'] ); ?> <?php echo Icons::svg( 'aspect-' . $a['type'], array( 'size' => 16, 'title' => $asp['name'] ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?> <?php echo esc_html( Zodiac::PLANETS[ $a['b'] ]['name'] ); ?> <span class="sk-aspect-list__name"><?php echo esc_html( $asp['name'] ); ?></span> <span><?php echo esc_html( 'orb ' . number_format( $a['orb'], 1, ',', '' ) . '° · ' . ( $a['applying'] ? 'yaklaşıyor' : 'ayrılıyor' ) ); ?></span></p>
				<p><?php echo esc_html( Readings::sky_aspect( $a ) ); ?></p>
			</li>
			<?php endforeach; ?>
		</ul>
		<?php else : ?>
		<p class="sk-muted">Bu anda gezegenler arasında 3° içinde bir ana açı yok.</p>
		<?php endif; ?>
		<p class="sk-footnote">Hızlı hareket ettiği için Ay açı listesine alınmaz. Orb: açının tam değerden sapması.</p>
	</section>

	<?php if ( $active_notes ) : ?>
	<section class="sk-transits__notes" aria-labelledby="<?php echo esc_attr( $form_id ); ?>-nh">
		<h2 id="<?php echo esc_attr( $form_id ); ?>-nh">Uzun transitler</h2>
		<ul class="sk-related__list" role="list">
			<?php foreach ( $active_notes as $p ) : ?>
			<li class="sk-card"><?php echo View::planet_badge( (string) get_post_meta( $p->ID, 'skyra_planet', true ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?><div><h3><a class="sk-stretch" href="<?php echo esc_url( get_permalink( $p ) ); ?>"><?php echo esc_html( get_the_title( $p ) ); ?></a></h3><p class="sk-muted"><?php echo esc_html( get_the_excerpt( $p ) ); ?></p></div></li>
			<?php endforeach; ?>
		</ul>
	</section>
	<?php endif; ?>
</div>
