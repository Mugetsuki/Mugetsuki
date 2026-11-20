<?php
/**
 * Skyra icon set: 24 × 24 grid, 1.5 px stroke, round caps and joins,
 * currentColor. Zodiac and planet glyphs share one optical weight.
 *
 * @package Skyra\Core
 */

namespace Skyra\Core;

defined( 'ABSPATH' ) || exit;

final class Icons {

	/** Inner SVG markup per icon. */
	public const PATHS = array(
		// Zodiac.
		'zodiac-koc'     => '<path d="M12 20.5V10"/><path d="M12 10c0-3.6-1.9-6.5-4.6-6.5-2.2 0-3.9 1.8-3.9 4.1 0 1.7 1 3.1 2.4 3.6"/><path d="M12 10c0-3.6 1.9-6.5 4.6-6.5 2.2 0 3.9 1.8 3.9 4.1 0 1.7-1 3.1-2.4 3.6"/>',
		'zodiac-boga'    => '<circle cx="12" cy="15" r="5"/><path d="M4 4c.6 3.5 3.8 6 8 6s7.4-2.5 8-6"/>',
		'zodiac-ikizler' => '<path d="M4.5 4c4.8 1.6 10.2 1.6 15 0"/><path d="M4.5 20c4.8-1.6 10.2-1.6 15 0"/><path d="M9 5.1v13.8"/><path d="M15 5.1v13.8"/>',
		'zodiac-yengec'  => '<circle cx="7" cy="9.5" r="2.5"/><path d="M7 7c3.5-2.6 9.4-2.3 13.5 1.2"/><circle cx="17" cy="14.5" r="2.5"/><path d="M17 17c-3.5 2.6-9.4 2.3-13.5-1.2"/>',
		'zodiac-aslan'   => '<circle cx="7" cy="15.5" r="2.8"/><path d="M9.6 14.4C9 12.5 8.4 10.6 8.4 9c0-3 2.4-5.5 5.4-5.5s5.4 2.4 5.4 5.3c0 3.6-3.6 6.1-3.6 9.3 0 1.6 1.1 2.4 2.3 2.4 1 0 1.9-.6 2.3-1.5"/>',
		'zodiac-basak'   => '<path d="M3.5 5v13"/><path d="M3.5 7.5a2 2 0 0 1 4 0V18"/><path d="M7.5 7.5a2 2 0 0 1 4 0V16c0 2.5 1.7 4 4 4"/><path d="M11.5 13.5c.8-2.3 3-3.8 5.3-3.3 2.2.5 2.9 3.2 1.1 5.4-1.5 1.8-4 2.9-6.4 3.2"/>',
		'zodiac-terazi'  => '<path d="M3.5 19.5h17"/><path d="M3.5 15.5h4.8a5.5 5.5 0 1 1 7.4 0h4.8"/>',
		'zodiac-akrep'   => '<path d="M3.5 5v13"/><path d="M3.5 7.5a2 2 0 0 1 4 0V18"/><path d="M7.5 7.5a2 2 0 0 1 4 0V17c0 1.4 1.1 2.5 2.5 2.5h6"/><path d="M17.5 17l2.5 2.5-2.5 2.5"/>',
		'zodiac-yay'     => '<path d="M4.5 19.5l15-15"/><path d="M12.5 4.5h7v7"/><path d="M7 10.5l6.5 6.5"/>',
		'zodiac-oglak'   => '<path d="M3.5 5.5L7 16l3.5-9.5c.9-2.2 3.5-2 3.5.5v9c0 2.3 1.6 3.8 3.6 3.8 1.9 0 3.4-1.4 3.4-3.3 0-1.8-1.4-3.2-3.2-3.2-1.9 0-3.3 1.4-3.8 3.3"/>',
		'zodiac-kova'    => '<path d="M3 10l3-2.5 3 2.5 3-2.5 3 2.5 3-2.5 3 2.5"/><path d="M3 16.5L6 14l3 2.5 3-2.5 3 2.5 3-2.5 3 2.5"/>',
		'zodiac-balik'   => '<path d="M6 3.5c3.8 4.2 3.8 12.8 0 17"/><path d="M18 3.5c-3.8 4.2-3.8 12.8 0 17"/><path d="M4.5 12h15"/>',
		// Planets.
		'planet-sun'     => '<circle cx="12" cy="12" r="8.5"/><circle cx="12" cy="12" r="1.3" fill="currentColor" stroke="none"/>',
		'planet-moon'    => '<path d="M15.5 3.5a9 9 0 1 0 0 17 10 10 0 0 1 0-17z"/>',
		'planet-mercury' => '<circle cx="12" cy="11.5" r="4"/><path d="M8 3.5c.7 1.8 2.2 3 4 3s3.3-1.2 4-3"/><path d="M12 15.5v6"/><path d="M9.5 18.8h5"/>',
		'planet-venus'   => '<circle cx="12" cy="8.5" r="5"/><path d="M12 13.5v7"/><path d="M9 17.5h6"/>',
		'planet-mars'    => '<circle cx="10" cy="14" r="5.5"/><path d="M14 10l6-6"/><path d="M15 4h5v5"/>',
		'planet-jupiter' => '<path d="M4.5 8.5c0-2.8 2-5 4.6-5 2.7 0 4.5 2.1 4.1 4.7-.4 2.9-3.6 5.7-8.7 8.3h15"/><path d="M16 3.5v17"/>',
		'planet-saturn'  => '<path d="M7.5 3.5v14"/><path d="M4.5 6.5h6"/><path d="M7.5 12c1.2-1.9 3-3 5.1-3 2.6 0 4.4 1.9 4.4 4.3 0 2.2-1.5 3.6-2.7 4.9-.9 1-1 2.3-.1 3 .9.7 2.1.4 2.8-.4"/>',
		'planet-uranus'  => '<path d="M6 3.5V14"/><path d="M18 3.5V14"/><path d="M6 8.8h12"/><path d="M12 3.5v12.3"/><circle cx="12" cy="18.5" r="2.2"/>',
		'planet-neptune' => '<path d="M5 4v4.5c0 4 3 6.8 7 6.8s7-2.8 7-6.8V4"/><path d="M12 3v18"/><path d="M8.5 18h7"/>',
		'planet-pluto'   => '<circle cx="12" cy="7" r="3"/><path d="M6 5.5c0 4.5 2.6 7.5 6 7.5s6-3 6-7.5"/><path d="M12 13v7.5"/><path d="M9 17.5h6"/>',
		'planet-node'    => '<path d="M7 16c-2.4-1.4-3.5-3.7-3.5-6.1C3.5 5.9 7.3 3 12 3s8.5 2.9 8.5 6.9c0 2.4-1.1 4.7-3.5 6.1"/><circle cx="7" cy="18.3" r="2.2"/><circle cx="17" cy="18.3" r="2.2"/>',
		// Aspects.
		'aspect-conjunction' => '<circle cx="9" cy="15" r="4"/><path d="M11.8 12.2L18 6"/>',
		'aspect-sextile'     => '<path d="M12 4v16"/><path d="M5 8l14 8"/><path d="M5 16l14-8"/>',
		'aspect-square'      => '<rect x="5" y="5" width="14" height="14" rx="1"/>',
		'aspect-trine'       => '<path d="M12 4.5L20 19H4z"/>',
		'aspect-opposition'  => '<circle cx="7" cy="17" r="3"/><circle cx="17" cy="7" r="3"/><path d="M9.1 14.9l5.8-5.8"/>',
		// Elements.
		'element-ates'   => '<path d="M12 4l8.5 15h-17z"/>',
		'element-toprak' => '<path d="M12 20L3.5 5h17z"/><path d="M6.5 10.5h11"/>',
		'element-hava'   => '<path d="M12 4l8.5 15h-17z"/><path d="M6.5 13.5h11"/>',
		'element-su'     => '<path d="M12 20L3.5 5h17z"/>',
		// Interface.
		'arrow-right'    => '<path d="M5 12h14"/><path d="M13 6l6 6-6 6"/>',
		'arrow-left'     => '<path d="M19 12H5"/><path d="M11 6l-6 6 6 6"/>',
		'arrow-up-right' => '<path d="M7 17L17 7"/><path d="M8 7h9v9"/>',
		'chevron-down'   => '<path d="M6 9l6 6 6-6"/>',
		'chevron-left'   => '<path d="M15 6l-6 6 6 6"/>',
		'chevron-right'  => '<path d="M9 6l6 6-6 6"/>',
		'menu'           => '<path d="M4 7h16"/><path d="M4 12h16"/><path d="M4 17h10"/>',
		'close'          => '<path d="M6 6l12 12"/><path d="M18 6L6 18"/>',
		'theme-light'    => '<circle cx="12" cy="12" r="4"/><path d="M12 2.5v2M12 19.5v2M4.6 4.6 6 6M18 18l1.4 1.4M2.5 12h2M19.5 12h2M4.6 19.4 6 18M18 6l1.4-1.4"/>',
		'theme-dark'     => '<path d="M19.5 14.6A8 8 0 0 1 9.4 4.5a8 8 0 1 0 10.1 10.1z"/>',
		'calendar'       => '<rect x="3.5" y="5" width="17" height="15.5" rx="2.5"/><path d="M3.5 10h17"/><path d="M8 3v4"/><path d="M16 3v4"/>',
		'clock'          => '<circle cx="12" cy="12" r="8.5"/><path d="M12 7.5V12l3 2"/>',
		'pin'            => '<path d="M12 21s-6.5-5.6-6.5-11a6.5 6.5 0 0 1 13 0c0 5.4-6.5 11-6.5 11z"/><circle cx="12" cy="10" r="2.3"/>',
		'chart'          => '<circle cx="12" cy="12" r="8.5"/><circle cx="12" cy="12" r="4.5"/><path d="M12 3.5v4M12 16.5v4M3.5 12h4M16.5 12h4"/>',
		'orbit'          => '<ellipse cx="12" cy="12" rx="9" ry="4.5" transform="rotate(-25 12 12)"/><circle cx="12" cy="12" r="2.5"/><circle cx="19.2" cy="8.2" r="1" fill="currentColor" stroke="none"/>',
		'compat'         => '<circle cx="9" cy="12" r="5.5"/><circle cx="15" cy="12" r="5.5"/>',
		'transit'        => '<circle cx="12" cy="12" r="3"/><path d="M12 3a9 9 0 0 1 9 9"/><path d="M3 12a9 9 0 0 1 4.2-7.6"/><circle cx="12" cy="21" r="1.2" fill="currentColor" stroke="none"/>',
		'retro'          => '<path d="M20 12a8 8 0 1 1-2.3-5.7"/><path d="M20 4.5v4h-4"/>',
		'rising'         => '<path d="M3 18.5h18"/><path d="M6.5 18.5a5.5 5.5 0 0 1 11 0"/><path d="M12 3.5v6"/><path d="M9.5 6L12 3.5 14.5 6"/>',
		'moon-sign'      => '<path d="M14.5 4a8 8 0 1 0 0 16 9 9 0 0 1 0-16z"/><circle cx="18" cy="7" r="1" fill="currentColor" stroke="none"/>',
		'spark'          => '<path d="M12 3v5M12 16v5M3 12h5M16 12h5"/>',
		'check'          => '<path d="M5 12.5l4.5 4.5L19 7"/>',
		'info'           => '<circle cx="12" cy="12" r="8.5"/><path d="M12 11v5"/><circle cx="12" cy="8" r=".9" fill="currentColor" stroke="none"/>',
		'alert'          => '<path d="M12 4 21 19.5H3z"/><path d="M12 10v4"/><circle cx="12" cy="16.8" r=".9" fill="currentColor" stroke="none"/>',
		'search'         => '<circle cx="10.5" cy="10.5" r="6"/><path d="M15 15l5 5"/>',
		'mail'           => '<rect x="3.5" y="5.5" width="17" height="13" rx="2.5"/><path d="M4 7l8 6 8-6"/>',
		'instagram'      => '<rect x="3.5" y="3.5" width="17" height="17" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.2" cy="6.8" r=".9" fill="currentColor" stroke="none"/>',
		'tiktok'         => '<path d="M14 3.5v11.2a3.8 3.8 0 1 1-3.8-3.8"/><path d="M14 3.5c.4 2.6 2.3 4.4 5 4.6"/>',
		'youtube'        => '<rect x="2.5" y="5.5" width="19" height="13" rx="4"/><path d="M10.5 9.5v5l4.5-2.5z"/>',
	);

	/**
	 * @param string $name  Icon key.
	 * @param array  $attrs class, width, height, title (adds role=img + <title>), stroke-width.
	 */
	public static function svg( string $name, array $attrs = array() ): string {
		if ( ! isset( self::PATHS[ $name ] ) ) {
			return '';
		}
		$size  = (int) ( $attrs['size'] ?? 24 );
		$class = trim( 'skyra-icon skyra-icon--' . $name . ' ' . ( $attrs['class'] ?? '' ) );
		$title = $attrs['title'] ?? '';
		$a11y  = $title
			? sprintf( ' role="img"><title>%s</title', esc_html( $title ) )
			: ' aria-hidden="true" focusable="false"';
		return sprintf(
			'<svg class="%s" width="%d" height="%d" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="%s" stroke-linecap="round" stroke-linejoin="round"%s>%s</svg>',
			esc_attr( $class ),
			$size,
			$size,
			esc_attr( (string) ( $attrs['stroke'] ?? '1.5' ) ),
			$a11y,
			self::PATHS[ $name ]
		);
	}

	public static function zodiac( string $slug, array $attrs = array() ): string {
		return self::svg( 'zodiac-' . $slug, $attrs );
	}

	public static function planet( string $key, array $attrs = array() ): string {
		return self::svg( 'planet-' . $key, $attrs );
	}
}
