<?php
/**
 * Title: Skyra ana sayfa
 * Slug: skyra/home
 * Categories: featured
 * Keywords: ana sayfa, home
 * Description: Ana sayfanın tüm bölümleri, önerilen sırayla. Yeni bir ana sayfa kurarken kullan.
 *
 * @package Skyra\Theme
 */

if ( class_exists( '\Skyra\Core\Setup' ) ) {
	echo \Skyra\Core\Setup::home_content(); // phpcs:ignore WordPress.Security.EscapeOutput
}
