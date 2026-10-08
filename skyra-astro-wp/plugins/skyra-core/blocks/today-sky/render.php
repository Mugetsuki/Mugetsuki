<?php
/**
 * "Bugün Gökyüzünde": Moon, Sun, retrogrades, the day's aspect and theme.
 *
 * @package Skyra\Core
 */

use Skyra\Astro\Readings;
use Skyra\Astro\Zodiac;
use Skyra\Core\Blocks;
use Skyra\Core\Data;
use Skyra\Core\Icons;
use Skyra\Core\View;

defined( 'ABSPATH' ) || exit;

$sky     = Data::snapshot();
$moon    = Zodiac::by_slug( $sky['moon']['sign'] );
$sun     = Zodiac::by_slug( $sky['sun']['sign'] );
$aspect  = $sky['aspects'][0] ?? null;
$sun_end = null;
foreach ( Data::upcoming( 40, array( 'ingress' ) ) as $e ) {
	if ( 'sun' === $e['body'] ) {
		$sun_end = $e;
		break;
	}
}
$head_id = View::uid( 'today' );
$meta    = sprintf(
	'<time datetime="%s">%s</time> · Europe/Istanbul · Güncelleme %s',
	esc_attr( gmdate( 'c', $sky['unix'] ) ),
	esc_html( Data::format( $sky['unix'], 'j F Y, l' ) ),
	esc_html( Data::format( $sky['unix'], 'H:i' ) )
);
$wrapper = get_block_wrapper_attributes(
	array(
		'class'           => 'sk-section sk-today',
		'aria-labelledby' => $head_id,
	)
);
if ( ! str_contains( $wrapper, ' id=' ) ) {
	$wrapper .= ' id="bugun"';
}
?>
<section <?php echo $wrapper; // phpcs:ignore WordPress.Security.EscapeOutput ?>>
	<div class="sk-container">
		<?php
		echo View::head( // phpcs:ignore WordPress.Security.EscapeOutput
			array(
				'eyebrow' => Blocks::attr( $attributes, 'eyebrow' ),
				'heading' => Blocks::attr( $attributes, 'heading', 'Bugün Gökyüzünde' ),
				'intro'   => Blocks::attr( $attributes, 'intro' ),
				'meta'    => $meta,
				'id'      => $head_id,
			)
		);
		?>
		<div class="sk-today__grid">
			<article class="sk-card sk-today__moon" data-reveal>
				<div class="sk-card__top"><p class="sk-label">Ay</p><?php echo View::tag( 'data' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
				<div class="sk-today__moon-main">
					<?php echo View::sign_badge( $moon['slug'], 'lg' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
					<div>
						<h3 class="sk-today__value"><?php echo esc_html( $moon['name'] ); ?> <span><?php echo esc_html( $sky['moon']['degree'] ); ?></span></h3>
						<?php if ( $sky['moon']['next'] ) : ?>
						<p class="sk-muted"><?php echo esc_html( sprintf( '%s %s geçiyor', Data::format( $sky['moon']['next']['unix'], 'j F H:i' ), Zodiac::by_slug( $sky['moon']['next']['to'] )['dat'] ) ); ?></p>
						<?php endif; ?>
					</div>
				</div>
				<div class="sk-today__phase">
					<?php echo View::moon( $sky['phase']['illumination'], $sky['phase']['waxing'], 56 ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
					<div>
						<p class="sk-today__phase-name"><?php echo esc_html( $sky['phase']['name'] ); ?></p>
						<p class="sk-muted"><?php echo esc_html( Data::percent( $sky['phase']['illumination'] ) . ' aydınlık · ' . ( $sky['phase']['waxing'] ? 'büyüyor' : 'küçülüyor' ) ); ?></p>
					</div>
				</div>
				<dl class="sk-facts sk-today__next">
					<div><dt>Sonraki Yeni Ay</dt><dd><?php echo esc_html( Data::format( $sky['phase']['next']['new'], 'j F, H:i' ) ); ?></dd></div>
					<div><dt>Sonraki Dolunay</dt><dd><?php echo esc_html( Data::format( $sky['phase']['next']['full'], 'j F, H:i' ) ); ?></dd></div>
					<div><dt>Ay'ın bu burçtaki kalan süresi</dt><dd><?php echo esc_html( $sky['moon']['next'] ? sprintf( '%s saat', max( 1, (int) round( ( $sky['moon']['next']['unix'] - $sky['unix'] ) / 3600 ) ) ) : '—' ); ?></dd></div>
				</dl>
			</article>

			<article class="sk-card sk-today__sun" data-reveal>
				<div class="sk-card__top"><p class="sk-label">Güneş</p></div>
				<div class="sk-today__row"><?php echo View::sign_badge( $sun['slug'] ); // phpcs:ignore WordPress.Security.EscapeOutput ?><h3 class="sk-today__value"><?php echo esc_html( $sun['name'] ); ?> <span><?php echo esc_html( $sky['sun']['degree'] ); ?></span></h3></div>
				<?php if ( $sun_end ) : ?>
				<p class="sk-muted"><?php echo esc_html( sprintf( '%s sezonu · %s %s geçiyor', $sun['name'], Data::format( $sun_end['unix'], 'j F' ), Zodiac::by_slug( $sun_end['sign'] )['dat'] ) ); ?></p>
				<?php endif; ?>
			</article>

			<article class="sk-card sk-today__retro" data-reveal>
				<div class="sk-card__top"><p class="sk-label">Retro gezegenler</p></div>
				<?php if ( $sky['retro'] ) : ?>
				<ul class="sk-planet-list">
					<?php foreach ( $sky['retro'] as $key ) : ?>
					<li><?php echo Icons::planet( $key, array( 'size' => 18 ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?><span><?php echo esc_html( Zodiac::PLANETS[ $key ]['name'] ); ?></span><?php if ( isset( $sky['retro_until'][ $key ] ) ) : ?><small><?php echo esc_html( Data::until( $sky['retro_until'][ $key ] ) ); ?></small><?php endif; ?></li>
					<?php endforeach; ?>
				</ul>
				<?php else : ?>
				<p class="sk-muted">Şu an geri harekette gezegen yok.</p>
				<?php endif; ?>
			</article>

			<article class="sk-card sk-today__aspect" data-reveal>
				<div class="sk-card__top"><p class="sk-label">Günün açısı</p><?php echo View::tag( 'data' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
				<?php if ( $aspect ) : $asp = Zodiac::ASPECTS[ $aspect['type'] ]; ?>
				<h3 class="sk-today__value sk-today__aspect-title">
					<?php echo Icons::planet( $aspect['a'], array( 'size' => 20 ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
					<?php echo Icons::svg( 'aspect-' . $aspect['type'], array( 'size' => 16, 'class' => 'is-' . $asp['kind'] ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
					<?php echo Icons::planet( $aspect['b'], array( 'size' => 20 ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
					<span><?php echo esc_html( Zodiac::PLANETS[ $aspect['a'] ]['name'] . ' – ' . Zodiac::PLANETS[ $aspect['b'] ]['name'] ); ?></span>
				</h3>
				<p class="sk-muted"><?php echo esc_html( sprintf( '%s (%d°) · orb %s° · %s', $asp['name'], $asp['angle'], number_format( $aspect['orb'], 1, ',', '' ), $aspect['applying'] ? 'yaklaşıyor' : 'ayrılıyor' ) ); ?></p>
				<p><?php echo esc_html( Readings::sky_aspect( $aspect ) ); ?></p>
				<?php else : ?>
				<p class="sk-muted">Bugün gezegenler arasında sıkı (3° içinde) bir ana açı yok.</p>
				<?php endif; ?>
			</article>

			<article class="sk-card sk-today__theme" data-reveal>
				<div class="sk-card__top"><p class="sk-label">Bugünün teması</p><?php echo View::tag( 'reading' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
				<h3 class="sk-today__quote"><?php echo esc_html( $sky['theme'][0] ); ?></h3>
				<p><?php echo esc_html( $sky['theme'][1] ); ?></p>
				<p class="sk-muted sk-small"><?php echo esc_html( sprintf( "Ay'ın %s burcundaki konumundan okunur.", $moon['name'] ) ); ?></p>
			</article>
		</div>
		<p class="sk-footnote">Konumlar Skyra efemeris motoruyla hesaplanır (geosentrik, tropikal zodyak). Yorumlar kesin sonuç değil, sembolik okumadır.</p>
	</div>
</section>
