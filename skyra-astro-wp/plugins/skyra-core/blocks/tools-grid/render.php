<?php
/**
 * Editorial grid of the seven astrology tools.
 *
 * @package Skyra\Core
 */

use Skyra\Core\Blocks;
use Skyra\Core\Data;
use Skyra\Core\Icons;
use Skyra\Core\View;

defined( 'ABSPATH' ) || exit;

$tools = apply_filters(
	'skyra_tools',
	array(
		array( 'dogum-haritasi', 'Doğum Haritası', 'Doğduğun anın gökyüzü: gezegenler, evler ve açılar tek haritada.', 'chart', 'Haritanı oluştur' ),
		array( 'yukselen-burc-hesaplama', 'Yükselen Burç', 'İlk izlenimini ve dünyaya bakışını anlatan burç; doğum saatinle hesaplanır.', 'rising', 'Hesapla' ),
		array( 'ay-burcu-hesaplama', 'Ay Burcu', 'Duygusal ihtiyaçlarını anlatan burç. Saatini bilmesen de yanıt verir.', 'moon-sign', 'Hesapla' ),
		array( 'burc-uyumu', 'Burç Uyumu', 'İki burç arasındaki açıyı ve element ilişkisini oku.', 'compat', 'Uyuma bak' ),
		array( 'transitler', 'Transitler', 'Gezegenlerin şu anki konumları ve aralarındaki açılar.', 'transit', 'Gökyüzüne bak' ),
		array( 'retro-takvimi', 'Retro Takvimi', 'Merkür’den Plüton’a yılın bütün retro dönemleri.', 'retro', 'Takvimi aç' ),
		array( 'astroloji-takvimi', 'Astroloji Takvimi', 'Yeni aylar, dolunaylar, tutulmalar ve burç geçişleri.', 'calendar', 'Takvimi aç' ),
	)
);
$next    = Data::upcoming( 3 );
$head_id = View::uid( 'tools' );
?>
<section <?php echo get_block_wrapper_attributes( array( 'class' => 'sk-section sk-tools', 'aria-labelledby' => $head_id ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>>
	<div class="sk-container">
		<?php
		echo View::head( // phpcs:ignore WordPress.Security.EscapeOutput
			array(
				'eyebrow' => Blocks::attr( $attributes, 'eyebrow' ),
				'heading' => Blocks::attr( $attributes, 'heading', 'Kendini keşfetmenin yolları' ),
				'intro'   => Blocks::attr( $attributes, 'intro' ),
				'id'      => $head_id,
			)
		);
		?>
		<ul class="sk-tools__grid" role="list">
			<?php foreach ( $tools as $i => [ $slug, $title, $desc, $icon, $cta ] ) : ?>
			<li class="sk-tool-card sk-tool-card--<?php echo esc_attr( $slug ); ?>" data-reveal>
				<div class="sk-tool-card__top">
					<span class="sk-tool-card__index"><?php echo esc_html( sprintf( '%02d', $i + 1 ) ); ?></span>
					<span class="sk-tool-card__icon"><?php echo Icons::svg( $icon, array( 'size' => 24 ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
				</div>
				<?php if ( 0 === $i ) : ?>
				<svg class="sk-tool-card__art" viewBox="0 0 200 200" aria-hidden="true" focusable="false"><circle cx="100" cy="100" r="92"/><circle cx="100" cy="100" r="72"/><circle cx="100" cy="100" r="40"/><path d="M8 100h184M100 8v184M35 35l130 130M165 35 35 165"/><path class="is-accent" d="M62 76 138 88 96 150z"/></svg>
				<?php endif; ?>
				<h3 class="sk-tool-card__title"><a class="sk-stretch" href="<?php echo esc_url( Data::page_url( $slug ) ); ?>"><?php echo esc_html( $title ); ?></a></h3>
				<p class="sk-tool-card__desc"><?php echo esc_html( $desc ); ?></p>
				<?php if ( 'astroloji-takvimi' === $slug && $next ) : ?>
				<ol class="sk-tool-card__mini" aria-label="Sıradaki olaylar">
					<?php foreach ( $next as $e ) : ?>
					<li><time datetime="<?php echo esc_attr( gmdate( 'c', $e['unix'] ) ); ?>"><?php echo esc_html( Data::format( $e['unix'], 'j M' ) ); ?></time><span><?php echo esc_html( $e['title'] ); ?></span></li>
					<?php endforeach; ?>
				</ol>
				<?php endif; ?>
				<p class="sk-tool-card__cta" aria-hidden="true"><?php echo esc_html( $cta ); ?><?php echo Icons::svg( 'arrow-right', array( 'size' => 16 ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?></p>
			</li>
			<?php endforeach; ?>
		</ul>
	</div>
</section>
