<?php
/**
 * Breadcrumb trail (matches the BreadcrumbList structured data).
 *
 * @package Skyra\Core
 */

use Skyra\Core\Seo;

defined( 'ABSPATH' ) || exit;

$trail = Seo::breadcrumbs();
if ( count( $trail ) < 2 ) {
	return;
}
$last = count( $trail ) - 1;
?>
<nav <?php echo get_block_wrapper_attributes( array( 'class' => 'sk-crumbs' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?> aria-label="İçerik yolu">
	<ol>
		<?php foreach ( $trail as $i => $c ) : ?>
		<li><?php if ( $i === $last ) : ?><span aria-current="page"><?php echo esc_html( $c['name'] ); ?></span><?php else : ?><a href="<?php echo esc_url( $c['url'] ); ?>"><?php echo esc_html( $c['name'] ); ?></a><?php endif; ?></li>
		<?php endforeach; ?>
	</ol>
</nav>
