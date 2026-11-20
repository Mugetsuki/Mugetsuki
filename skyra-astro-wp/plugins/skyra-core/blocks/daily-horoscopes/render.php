<?php
/**
 * Daily readings for all 12 signs.
 *
 * @package Skyra\Core
 */

use Skyra\Astro\Zodiac;
use Skyra\Core\Blocks;
use Skyra\Core\Data;
use Skyra\Core\Icons;
use Skyra\Core\View;

defined( 'ABSPATH' ) || exit;

$today    = Data::today();
$carousel = 'grid' !== ( $attributes['layout'] ?? 'carousel' );
$head_id  = View::uid( 'horo' );
$list_id  = View::uid( 'horo-list' );
?>
<section <?php echo get_block_wrapper_attributes( array( 'class' => 'sk-section sk-horoscopes' . ( $carousel ? ' is-carousel' : ' is-grid' ), 'aria-labelledby' => $head_id ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>>
	<div class="sk-container">
		<?php
		echo View::head( // phpcs:ignore WordPress.Security.EscapeOutput
			array(
				'eyebrow' => Blocks::attr( $attributes, 'eyebrow' ),
				'heading' => Blocks::attr( $attributes, 'heading', 'Bugün burcunda ne var?' ),
				'intro'   => Blocks::attr( $attributes, 'intro' ),
				'meta'    => '<time datetime="' . esc_attr( $today ) . '">' . esc_html( Data::format_date( $today, 'j F Y, l' ) ) . '</time>',
				'id'      => $head_id,
				'link'    => $carousel ? array( Data::page_url( 'gunluk-burc-yorumlari' ), '12 burcun yorumu' ) : null,
			)
		);
		?>
		<div class="sk-carousel" data-carousel>
			<div class="sk-carousel__track" id="<?php echo esc_attr( $list_id ); ?>" <?php echo $carousel ? 'tabindex="0" role="region" aria-label="12 burcun günlük yorumu"' : ''; // phpcs:ignore WordPress.Security.EscapeOutput ?>>
				<ul class="sk-hlist" role="list">
					<?php foreach ( Zodiac::SIGNS as $sign ) : $r = Data::reading( $sign['slug'], $today ); ?>
					<li class="sk-hcard" data-sign="<?php echo esc_attr( $sign['slug'] ); ?>">
						<div class="sk-hcard__top">
							<?php echo View::sign_badge( $sign['slug'] ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
							<div>
								<h3 class="sk-hcard__name"><a class="sk-stretch" href="<?php echo esc_url( Data::daily_url( $sign['slug'] ) ); ?>"><?php echo esc_html( $sign['name'] ); ?><span class="sk-vh"> burcu günlük yorumu</span></a></h3>
								<p class="sk-hcard__dates"><?php echo esc_html( Zodiac::date_range( $sign ) ); ?></p>
							</div>
							<span class="sk-hcard__mine" hidden>Senin burcun</span>
						</div>
						<p class="sk-hcard__theme"><?php echo esc_html( $r['theme'] ); ?></p>
						<p class="sk-hcard__teaser"><?php echo esc_html( $r['teaser'] ); ?></p>
						<p class="sk-hcard__cta" aria-hidden="true">Yorumunu Oku<?php echo Icons::svg( 'arrow-right', array( 'size' => 16 ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?></p>
					</li>
					<?php endforeach; ?>
				</ul>
			</div>
			<?php if ( $carousel ) : ?>
			<div class="sk-carousel__controls" data-carousel-controls hidden>
				<button type="button" class="sk-iconbtn" data-carousel-prev aria-controls="<?php echo esc_attr( $list_id ); ?>" aria-label="Önceki burçlar"><?php echo Icons::svg( 'arrow-left', array( 'size' => 20 ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?></button>
				<span class="sk-carousel__progress" aria-hidden="true"><span data-carousel-bar></span></span>
				<button type="button" class="sk-iconbtn" data-carousel-next aria-controls="<?php echo esc_attr( $list_id ); ?>" aria-label="Sonraki burçlar"><?php echo Icons::svg( 'arrow-right', array( 'size' => 20 ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?></button>
			</div>
			<?php endif; ?>
		</div>
		<p class="sk-footnote">Yorumlar, Ay'ın her burca göre bulunduğu eve ve günün öne çıkan gezegen açısına dayanır. Editörün yazdığı yorum olduğunda o gösterilir.</p>
	</div>
</section>
