<?php
/**
 * Social profiles from Settings → Skyra Astro. Nothing is shown for
 * visitors when no profile is configured.
 *
 * @package Skyra\Core
 */

use Skyra\Core\Blocks;
use Skyra\Core\Icons;
use Skyra\Core\Settings;
use Skyra\Core\View;

defined( 'ABSPATH' ) || exit;

$socials = Settings::socials();
if ( ! $socials ) {
	if ( current_user_can( 'manage_options' ) ) {
		echo '<p class="sk-notice">Sosyal medya adresi tanımlı değil. Ayarlar → Skyra Astro sayfasından ekleyebilirsin.</p>';
	}
	return;
}
$handle = static function ( string $url ): string {
	$path = trim( (string) wp_parse_url( $url, PHP_URL_PATH ), '/' );
	return $path ? '@' . ltrim( $path, '@' ) : (string) wp_parse_url( $url, PHP_URL_HOST );
};
$notes = array(
	'instagram' => 'Günlük Ay notları ve görsel anlatımlar',
	'tiktok'    => 'Kısa gökyüzü açıklamaları',
	'youtube'   => 'Uzun anlatımlar ve takvim özetleri',
);

if ( 'inline' === ( $attributes['variant'] ?? 'community' ) ) :
	?>
<ul <?php echo get_block_wrapper_attributes( array( 'class' => 'sk-social-inline' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>>
	<?php foreach ( $socials as $key => $s ) : ?>
	<li><a class="sk-iconbtn" href="<?php echo esc_url( $s['url'] ); ?>" rel="me noopener" target="_blank"><?php echo Icons::svg( $key, array( 'size' => 20 ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?><span class="sk-vh"><?php echo esc_html( $s['label'] ); ?> (yeni sekmede açılır)</span></a></li>
	<?php endforeach; ?>
</ul>
<?php else : $head_id = View::uid( 'social' ); ?>
<section <?php echo get_block_wrapper_attributes( array( 'class' => 'sk-section sk-community', 'aria-labelledby' => $head_id ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>>
	<div class="sk-container sk-community__grid">
		<?php
		echo View::head( // phpcs:ignore WordPress.Security.EscapeOutput
			array(
				'eyebrow' => Blocks::attr( $attributes, 'eyebrow' ),
				'heading' => Blocks::attr( $attributes, 'heading', 'Gökyüzünü birlikte takip edelim' ),
				'intro'   => Blocks::attr( $attributes, 'intro' ),
				'id'      => $head_id,
			)
		);
		?>
		<ul class="sk-community__list" role="list">
			<?php foreach ( $socials as $key => $s ) : ?>
			<li class="sk-community__item" data-reveal>
				<span class="sk-community__icon"><?php echo Icons::svg( $key, array( 'size' => 26 ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
				<div>
					<h3 class="sk-community__name"><a class="sk-stretch" href="<?php echo esc_url( $s['url'] ); ?>" rel="me noopener" target="_blank"><?php echo esc_html( $s['label'] ); ?><span class="sk-vh"> (yeni sekmede açılır)</span></a></h3>
					<p class="sk-community__handle"><?php echo esc_html( $handle( $s['url'] ) ); ?></p>
					<p class="sk-community__note"><?php echo esc_html( $notes[ $key ] ?? '' ); ?></p>
				</div>
				<?php echo Icons::svg( 'arrow-up-right', array( 'size' => 20, 'class' => 'sk-community__arrow' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
			</li>
			<?php endforeach; ?>
		</ul>
	</div>
</section>
<?php endif; ?>
