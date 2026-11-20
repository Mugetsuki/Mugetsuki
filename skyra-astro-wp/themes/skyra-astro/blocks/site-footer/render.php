<?php
/**
 * Site footer: brand closure with menus, socials and the newsletter.
 *
 * @package Skyra\Theme
 */

use function Skyra\Theme\logo;
use function Skyra\Theme\menu;
use function Skyra\Theme\setting;

defined( 'ABSPATH' ) || exit;

$registry = WP_Block_Type_Registry::get_instance();
?>
<footer class="sk-footer">
	<svg class="sk-footer__sky" viewBox="0 0 560 320" aria-hidden="true" focusable="false" fill="none" stroke="currentColor" stroke-width="1">
		<path d="M40 210 L120 160 L200 190 L262 118 L340 140 L402 72 L500 96"/>
		<path d="M262 118 L300 230 L380 262"/>
		<circle cx="40" cy="210" r="3" fill="currentColor"/><circle cx="120" cy="160" r="2.5" fill="currentColor"/><circle cx="200" cy="190" r="2" fill="currentColor"/><circle cx="262" cy="118" r="3.5" fill="currentColor"/><circle cx="340" cy="140" r="2" fill="currentColor"/><circle cx="402" cy="72" r="3" fill="currentColor"/><circle cx="500" cy="96" r="2.5" fill="currentColor"/><circle cx="300" cy="230" r="2" fill="currentColor"/><circle cx="380" cy="262" r="2.5" fill="currentColor"/>
		<ellipse cx="420" cy="170" rx="210" ry="70" transform="rotate(-14 420 170)" opacity=".5"/>
	</svg>
	<div class="sk-container sk-footer__grid">
		<div class="sk-footer__brand">
			<?php echo logo(); // phpcs:ignore WordPress.Security.EscapeOutput ?>
			<p class="sk-footer__tagline"><?php echo esc_html( setting( 'tagline', 'Gökyüzünü anla. Kendine alan aç.' ) ); ?></p>
			<?php
			if ( $registry->is_registered( 'skyra/social-links' ) ) {
				echo render_block( // phpcs:ignore WordPress.Security.EscapeOutput
					array(
						'blockName' => 'skyra/social-links',
						'attrs'     => array( 'variant' => 'inline' ),
					)
				);
			}
			?>
		</div>
		<nav aria-labelledby="sk-f-explore">
			<h2 class="sk-footer__title" id="sk-f-explore">Keşfet</h2>
			<?php echo menu( 'footer-explore', 'Keşfet', 'sk-footer__menu' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
		</nav>
		<nav aria-labelledby="sk-f-company">
			<h2 class="sk-footer__title" id="sk-f-company">Kurumsal</h2>
			<?php echo menu( 'footer-company', 'Kurumsal', 'sk-footer__menu' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
		</nav>
		<div class="sk-footer__news">
			<?php
			if ( $registry->is_registered( 'skyra/newsletter' ) ) {
				echo render_block( // phpcs:ignore WordPress.Security.EscapeOutput
					array(
						'blockName' => 'skyra/newsletter',
						'attrs'     => array( 'variant' => 'compact' ),
					)
				);
			}
			?>
		</div>
	</div>
	<div class="sk-container sk-footer__base">
		<p>© <?php echo esc_html( gmdate( 'Y' ) ); ?> Skyra Astro</p>
		<p>Astrolojik yorumlar sembolik okumalardır; sağlık, hukuk ya da finans danışmanlığının yerini tutmaz.</p>
		<p>Konum verisi: <a href="https://www.geonames.org/" rel="noopener">GeoNames</a> (CC BY 4.0)</p>
	</div>
</footer>
