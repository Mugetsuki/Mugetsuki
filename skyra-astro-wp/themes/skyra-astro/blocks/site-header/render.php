<?php
/**
 * Site header. Mobile: logo · harita · tema · menü (in-page panel, not modal).
 * Desktop: logo · menu · theme · CTA. Transparent over the home hero until scroll.
 *
 * @package Skyra\Theme
 */

use function Skyra\Theme\icon;
use function Skyra\Theme\logo;
use function Skyra\Theme\menu;
use function Skyra\Theme\setting;

defined( 'ABSPATH' ) || exit;

$chart = home_url( '/dogum-haritasi/' );
?>
<header class="sk-header" data-header>
	<div class="sk-container sk-header__bar">
		<?php echo logo(); // phpcs:ignore WordPress.Security.EscapeOutput ?>
		<nav class="sk-nav" id="sk-nav" aria-label="Ana menü">
			<?php echo menu( 'primary', 'Ana menü', 'sk-nav__list' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
			<div class="sk-nav__extra">
				<svg class="sk-nav__orbit" viewBox="0 0 120 60" aria-hidden="true" focusable="false"><ellipse cx="60" cy="30" rx="56" ry="18" fill="none" stroke="currentColor"/><circle cx="60" cy="30" r="6" fill="none" stroke="currentColor"/><circle cx="112" cy="26" r="2.5" fill="currentColor"/></svg>
				<a class="sk-btn sk-btn--primary sk-btn--lg" href="<?php echo esc_url( $chart ); ?>"><span><?php echo esc_html( setting( 'cta_label', 'Haritanı Keşfet' ) ); ?></span><?php echo icon( 'arrow-right', 18 ); // phpcs:ignore WordPress.Security.EscapeOutput ?></a>
			</div>
		</nav>
		<div class="sk-header__actions">
			<a class="sk-btn sk-btn--secondary sk-header__cta sk-header__cta--short" href="<?php echo esc_url( $chart ); ?>"><?php echo esc_html( setting( 'cta_short', 'Harita' ) ); ?></a>
			<button type="button" class="sk-iconbtn sk-theme-toggle" data-theme-toggle aria-label="Koyu temaya geç">
				<?php echo icon( 'theme-light', 20 ) . icon( 'theme-dark', 20 ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
			</button>
			<a class="sk-btn sk-btn--primary sk-header__cta sk-header__cta--full" href="<?php echo esc_url( $chart ); ?>"><span><?php echo esc_html( setting( 'cta_label', 'Haritanı Keşfet' ) ); ?></span><?php echo icon( 'arrow-right', 18 ); // phpcs:ignore WordPress.Security.EscapeOutput ?></a>
			<button type="button" class="sk-iconbtn sk-menu-toggle" data-menu-toggle aria-controls="sk-nav" aria-expanded="false" aria-label="Menüyü aç">
				<?php echo icon( 'menu', 22 ) . icon( 'close', 22 ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
			</button>
		</div>
	</div>
</header>
