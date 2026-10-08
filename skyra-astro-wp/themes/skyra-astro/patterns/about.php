<?php
/**
 * Title: Skyra yaklaşımı
 * Slug: skyra/about
 * Categories: text, featured
 * Keywords: hakkında, yaklaşım, güven
 * Description: "Skyra Astro nedir?" bölümü: kısa tanım ve üç ilke kartı.
 *
 * @package Skyra\Theme
 */

if ( class_exists( '\Skyra\Core\Setup' ) ) {
	echo \Skyra\Core\Setup::about_pattern(); // phpcs:ignore WordPress.Security.EscapeOutput
}
