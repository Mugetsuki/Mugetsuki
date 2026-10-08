<?php
/**
 * Newsletter sign-up (double opt-in). Panel for pages, compact for the footer.
 *
 * @package Skyra\Core
 */

use Skyra\Core\Blocks;
use Skyra\Core\Data;
use Skyra\Core\Forms;
use Skyra\Core\Icons;
use Skyra\Core\View;

defined( 'ABSPATH' ) || exit;

$compact = 'compact' === ( $attributes['variant'] ?? 'panel' );
$id      = View::uid( 'nl' );
// phpcs:disable WordPress.Security.NonceVerification.Recommended -- display-only status flags.
$status = '';
if ( isset( $_GET['skyra_form'], $_GET['skyra_status'] ) && 'newsletter' === $_GET['skyra_form'] ) {
	$status = Forms::MESSAGES[ sanitize_key( $_GET['skyra_status'] ) ] ?? '';
}
if ( ! $compact && isset( $_GET['bulten'] ) ) {
	$status = array(
		'onaylandi' => 'Kaydın onaylandı. İlk not pazartesi sabahı e-postanda olacak.',
		'ayrildi'   => 'Kaydını sildik. Seni yeniden görmek isteriz.',
		'gecersiz'  => 'Bu bağlantı geçersiz ya da süresi dolmuş. Formdan yeniden kaydolabilirsin.',
	)[ sanitize_key( $_GET['bulten'] ) ] ?? '';
}
// phpcs:enable
$form = sprintf(
	'<form class="sk-newsletter__form" method="post" action="%1$s" data-ajax-form="newsletter" novalidate>
		<input type="hidden" name="action" value="skyra_newsletter"><input type="hidden" name="started" value="%2$d">
		<div class="sk-hp" aria-hidden="true"><label for="%3$s-web">Web sitesi</label><input id="%3$s-web" type="text" name="website" tabindex="-1" autocomplete="off"></div>
		<div class="sk-field sk-newsletter__field"><label for="%3$s-email">E-posta adresin</label><div class="sk-inline"><input id="%3$s-email" type="email" name="email" required autocomplete="email" inputmode="email" placeholder="ad@alanadi.com" aria-describedby="%3$s-err"><button type="submit" class="sk-btn sk-btn--primary"><span>Kaydol</span>%4$s</button></div><p class="sk-error" id="%3$s-err" hidden></p></div>
		<label class="sk-check sk-check--small"><input type="checkbox" name="consent" value="1" required><span>E-posta adresimin bülten gönderimi için işlenmesine ilişkin <a href="%5$s">aydınlatma metnini</a> okudum.</span></label>
		<p class="sk-status%6$s" role="status" aria-live="polite" data-status>%7$s</p>
	</form>',
	esc_url( admin_url( 'admin-post.php' ) ),
	time(),
	esc_attr( $id ),
	Icons::svg( 'arrow-right', array( 'size' => 18 ) ),
	esc_url( Data::page_url( 'kvkk' ) ),
	$status ? ' is-visible' : '',
	esc_html( $status )
);

if ( $compact ) :
	?>
<div <?php echo get_block_wrapper_attributes( array( 'class' => 'sk-newsletter is-compact' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>>
	<p class="sk-newsletter__title"><?php echo esc_html( Blocks::attr( $attributes, 'heading', 'Gökyüzünden haftalık bir not.' ) ); ?></p>
	<?php echo $form; // phpcs:ignore WordPress.Security.EscapeOutput ?>
</div>
<?php else : ?>
<section <?php echo get_block_wrapper_attributes( array( 'class' => 'sk-section sk-newsletter', 'aria-labelledby' => $id . '-h' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>>
	<div class="sk-container">
		<div class="sk-newsletter__panel" data-reveal>
			<svg class="sk-newsletter__art" viewBox="0 0 400 400" aria-hidden="true" focusable="false"><ellipse cx="200" cy="200" rx="190" ry="80" transform="rotate(-20 200 200)" pathLength="1"/><ellipse cx="200" cy="200" rx="120" ry="120" pathLength="1"/><circle cx="200" cy="200" r="36" class="is-moon"/><circle cx="352" cy="130" r="6" class="is-dot"/></svg>
			<div class="sk-newsletter__copy">
				<p class="sk-eyebrow"><?php echo esc_html( Blocks::attr( $attributes, 'eyebrow', 'Bülten' ) ); ?></p>
				<h2 class="sk-head__title" id="<?php echo esc_attr( $id . '-h' ); ?>"><?php echo esc_html( Blocks::attr( $attributes, 'heading', 'Gökyüzünden haftalık bir not.' ) ); ?></h2>
				<p class="sk-head__intro"><?php echo esc_html( Blocks::attr( $attributes, 'intro' ) ); ?></p>
			</div>
			<?php echo $form; // phpcs:ignore WordPress.Security.EscapeOutput ?>
		</div>
	</div>
</section>
<?php endif; ?>
