<?php
/**
 * Contact form: stored in wp-admin and e-mailed to the configured address.
 *
 * @package Skyra\Core
 */

use Skyra\Core\Data;
use Skyra\Core\Forms;
use Skyra\Core\View;

defined( 'ABSPATH' ) || exit;

$id = View::uid( 'contact' );
// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- display-only status flag.
$status = ( isset( $_GET['skyra_form'], $_GET['skyra_status'] ) && 'contact' === $_GET['skyra_form'] ) ? ( Forms::MESSAGES[ sanitize_key( $_GET['skyra_status'] ) ] ?? '' ) : '';
$field  = static function ( string $name, string $label, string $control ) use ( $id ) {
	return sprintf( '<div class="sk-field"><label for="%1$s-%2$s">%3$s <span class="sk-req">gerekli</span></label>%4$s<p class="sk-error" id="%1$s-%2$s-err" hidden></p></div>', esc_attr( $id ), esc_attr( $name ), esc_html( $label ), $control );
};
?>
<form <?php echo get_block_wrapper_attributes( array( 'class' => 'sk-form sk-contact sk-card' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?> method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" data-ajax-form="contact" novalidate>
	<input type="hidden" name="action" value="skyra_contact">
	<input type="hidden" name="started" value="<?php echo (int) time(); ?>">
	<div class="sk-hp" aria-hidden="true"><label for="<?php echo esc_attr( $id ); ?>-web">Web sitesi</label><input id="<?php echo esc_attr( $id ); ?>-web" type="text" name="website" tabindex="-1" autocomplete="off"></div>
	<div class="sk-field">
		<label for="<?php echo esc_attr( $id ); ?>-topic">Konu</label>
		<select id="<?php echo esc_attr( $id ); ?>-topic" name="topic"><option>Genel soru</option><option>İçerik önerisi</option><option>İş birliği</option><option>Teknik sorun</option></select>
	</div>
	<?php echo $field( 'name', 'Adın', sprintf( '<input id="%1$s-name" type="text" name="name" required autocomplete="name" aria-describedby="%1$s-name-err">', esc_attr( $id ) ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
	<?php echo $field( 'email', 'E-posta adresin', sprintf( '<input id="%1$s-email" type="email" name="email" required autocomplete="email" inputmode="email" aria-describedby="%1$s-email-err">', esc_attr( $id ) ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
	<?php echo $field( 'message', 'Mesajın', sprintf( '<textarea id="%1$s-message" name="message" rows="6" required minlength="10" aria-describedby="%1$s-message-err"></textarea>', esc_attr( $id ) ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
	<label class="sk-check"><input type="checkbox" name="consent" value="1" required><span>Mesajıma yanıt verilebilmesi için bilgilerimin işlenmesine ilişkin <a href="<?php echo esc_url( Data::page_url( 'kvkk' ) ); ?>">aydınlatma metnini</a> okudum.</span></label>
	<button type="submit" class="sk-btn sk-btn--primary sk-btn--lg"><span>Mesajı Gönder</span></button>
	<p class="sk-status<?php echo $status ? ' is-visible' : ''; ?>" role="status" aria-live="polite" data-status><?php echo esc_html( $status ); ?></p>
</form>
