<?php
/**
 * Skyra Tarot reading. Renders nothing for visitors while in test mode.
 *
 * @package Skyra\Tarot
 */

use function Skyra\Tarot\can_view;
use function Skyra\Tarot\data;
use function Skyra\Tarot\is_public;

defined( 'ABSPATH' ) || exit;

if ( ! can_view() ) {
	return;
}
?>
<div <?php echo get_block_wrapper_attributes( array( 'class' => 'skt' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?> data-skyra-tarot>
	<?php if ( ! is_public() ) : ?>
		<p class="skt-testmode">Test modu · Bu sayfayı yalnızca giriş yapmış yöneticiler görebilir.</p>
	<?php endif; ?>

	<div class="skt-intro">
		<p>Tarot kartları burada geleceği söyleyen bir araç olarak değil, semboller üzerinden düşünmeye alan açan bir ayna olarak kullanılır. Bir açılım seç, kartları karıştır ve içinden gelen kartları kendin seç.</p>
		<ol class="skt-steps">
			<li><span>1</span>Açılımı seç</li>
			<li><span>2</span>Kartları karıştır</li>
			<li><span>3</span>Kartlarını seç</li>
			<li><span>4</span>Çevir ve oku</li>
		</ol>
		<p class="skt-callout">Sorunu aklında tutman yeterli, bir yere yazmana gerek yok. Kart seçimlerin yalnızca bu tarayıcıda işlenir; kaydedilmez ve sunucuya gönderilmez.</p>
	</div>

	<div class="skt-app" hidden>
		<h2 class="skt-app__title">Açılımını seç</h2>
		<div class="skt-spreads" role="group" aria-label="Açılım türleri"></div>

		<div class="skt-stage" hidden>
			<div class="skt-board" aria-label="Açılım düzeni"></div>
			<p class="skt-note" hidden></p>
			<div class="skt-controls">
				<p class="skt-status" role="status" aria-live="polite"></p>
				<div class="skt-buttons">
					<button type="button" class="skt-btn skt-btn--primary" data-act="shuffle">Kartları karıştır</button>
					<button type="button" class="skt-btn" data-act="auto" hidden>Benim yerime seç</button>
					<button type="button" class="skt-btn skt-btn--primary" data-act="reveal" hidden>Kartları çevir</button>
				</div>
			</div>
			<div class="skt-deck"></div>
		</div>

		<div class="skt-reading" hidden aria-live="polite"></div>
	</div>

	<noscript><p class="skt-callout">Etkileşimli tarot açılımı için tarayıcında JavaScript’in açık olması gerekir.</p></noscript>

	<details class="skt-legal">
		<summary>Bu sayfa hakkında</summary>
		<p>Kartlar geleceği öngörmez ve olaylar hakkında kesin bilgi vermez. Okumalar, kişisel düşünme için kültürel ve sembolik bir çerçeve sunar; başka birinin duygularını ya da düşüncelerini bildirmez.</p>
		<p>Sağlık, psikolojik destek, hukuk ya da finans konularında karar verirken ilgili uzmana başvur. Skyra Astro bu sayfada ücretli fal, tarot ya da danışmanlık hizmeti sunmaz.</p>
		<p>Kart tasarımları ve yorum metinleri Skyra Astro için özgün olarak hazırlanmıştır. Açılımın tarayıcında oluşturulur; seçtiğin kartlar ve yorumun herhangi bir yere kaydedilmez.</p>
	</details>

	<script type="application/json" class="skt-data"><?php echo wp_json_encode( data(), JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP ); ?></script>
</div>
