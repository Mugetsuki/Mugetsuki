<?php
/**
 * Title: Skyra hero
 * Slug: skyra/hero
 * Categories: featured, banner
 * Keywords: hero, skyra, gökyüzü
 * Description: Başlık, açıklama, iki çağrı, canlı gökyüzü şeridi ve gerçek konumlarla çizilen gökyüzü kompozisyonu.
 *
 * @package Skyra\Theme
 */

if ( class_exists( '\Skyra\Core\Setup' ) ) {
	echo \Skyra\Core\Setup::hero_pattern(); // phpcs:ignore WordPress.Security.EscapeOutput
}
