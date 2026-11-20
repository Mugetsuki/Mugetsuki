<?php
/**
 * Hero visual: soft celestial sphere, orbital lines and the real sky wheel
 * for this moment, with a small data note.
 *
 * @package Skyra\Core
 */

use Skyra\Core\Data;
use Skyra\Core\View;
use Skyra\Core\Wheel;

defined( 'ABSPATH' ) || exit;

$sky    = Data::snapshot();
$wheel  = Wheel::svg(
	$sky['bodies'],
	array(
		'aspects' => $sky['aspects'],
		'title'   => 'Gökyüzü şu an',
		'desc'    => sprintf( 'Gezegenlerin %s itibarıyla burç çemberindeki konumları.', Data::format( $sky['unix'], 'j F Y H:i' ) ),
		'class'   => 'is-hero',
	)
);
$notes  = array( 'sun', 'moon', 'mercury', 'venus' );
?>
<figure <?php echo get_block_wrapper_attributes( array( 'class' => 'sk-skywheel' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?> data-hero-visual>
	<div class="sk-skywheel__stage">
		<span class="sk-skywheel__sphere" aria-hidden="true"></span>
		<svg class="sk-skywheel__orbits" viewBox="0 0 600 600" aria-hidden="true" focusable="false">
			<g class="sk-orbit sk-orbit--a"><ellipse cx="300" cy="300" rx="292" ry="118" pathLength="1"/><circle class="sk-orbit__body" cx="592" cy="300" r="7"/></g>
			<g class="sk-orbit sk-orbit--b"><ellipse cx="300" cy="300" rx="250" ry="250" pathLength="1"/><circle class="sk-orbit__body is-small" cx="300" cy="50" r="4"/></g>
			<g class="sk-orbit sk-orbit--c"><ellipse cx="300" cy="300" rx="286" ry="178" pathLength="1"/></g>
			<g class="sk-constellation">
				<path d="M88 118 L132 86 L176 104 L214 70 L248 92" pathLength="1"/>
				<circle cx="88" cy="118" r="2.4" style="--i:0"/><circle cx="132" cy="86" r="2" style="--i:1"/><circle cx="176" cy="104" r="2.6" style="--i:2"/><circle cx="214" cy="70" r="1.8" style="--i:3"/><circle cx="248" cy="92" r="2.2" style="--i:4"/>
			</g>
		</svg>
		<div class="sk-skywheel__wheel"><?php echo $wheel; // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
	</div>
	<?php if ( ! empty( $attributes['caption'] ) ) : ?>
	<figcaption class="sk-skywheel__note">
		<p class="sk-label">Gökyüzü şu an</p>
		<p class="sk-skywheel__time"><time datetime="<?php echo esc_attr( gmdate( 'c', $sky['unix'] ) ); ?>"><?php echo esc_html( Data::format( $sky['unix'], 'j F · H:i' ) ); ?> TSİ</time></p>
		<dl>
			<?php foreach ( $notes as $key ) : $b = $sky['bodies'][ $key ]; ?>
			<div><dt><?php echo esc_html( $b['name'] ); ?></dt><dd><?php echo esc_html( View::sign_name( $b['sign'] ) . ' ' . $b['degree'] . ( $b['retro'] ? ' R' : '' ) ); ?></dd></div>
			<?php endforeach; ?>
		</dl>
		<?php echo View::arrow_link( Data::page_url( 'transitler' ), 'Tüm konumlar' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
	</figcaption>
	<?php endif; ?>
</figure>
