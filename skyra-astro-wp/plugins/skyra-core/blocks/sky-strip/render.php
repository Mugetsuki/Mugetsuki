<?php
/**
 * Live data line under the hero: now, Sun, Moon, Moon phase.
 *
 * @package Skyra\Core
 */

use Skyra\Core\Data;
use Skyra\Core\Icons;
use Skyra\Core\View;

defined( 'ABSPATH' ) || exit;

$sky = Data::snapshot();
?>
<div <?php echo get_block_wrapper_attributes( array( 'class' => 'sk-strip' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>>
	<p class="sk-strip__time"><span class="sk-live" aria-hidden="true"></span>Şu an · <time datetime="<?php echo esc_attr( gmdate( 'c', $sky['unix'] ) ); ?>"><?php echo esc_html( Data::format( $sky['unix'], 'j F, H:i' ) ); ?></time></p>
	<ul class="sk-strip__list">
		<li><span class="sk-strip__label">Güneş</span><?php echo Icons::zodiac( $sky['sun']['sign'], array( 'size' => 18 ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?><span><?php echo esc_html( View::sign_name( $sky['sun']['sign'] ) . ' ' . $sky['sun']['degree'] ); ?></span></li>
		<li><span class="sk-strip__label">Ay</span><?php echo Icons::zodiac( $sky['moon']['sign'], array( 'size' => 18 ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?><span><?php echo esc_html( View::sign_name( $sky['moon']['sign'] ) . ' ' . $sky['moon']['degree'] ); ?></span></li>
		<li><?php echo View::moon( $sky['phase']['illumination'], $sky['phase']['waxing'], 18 ); // phpcs:ignore WordPress.Security.EscapeOutput ?><span><?php echo esc_html( $sky['phase']['name'] . ' · ' . Data::percent( $sky['phase']['illumination'] ) ); ?></span></li>
	</ul>
</div>
