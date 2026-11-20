/**
 * Light/dark toggle for non-Skyra themes. The head script has already set
 * data-theme; this only wires the button and follows the system setting
 * until the visitor makes an explicit choice.
 */
( function () {
	'use strict';

	var doc = document.documentElement;
	var btn = document.querySelector( '[data-mode-toggle]' );
	var system = window.matchMedia ? window.matchMedia( '(prefers-color-scheme: dark)' ) : null;

	function saved() {
		try { return localStorage.getItem( 'skyra-theme' ); } catch ( e ) { return null; }
	}
	function label() {
		if ( btn ) { btn.setAttribute( 'aria-label', doc.getAttribute( 'data-theme' ) === 'dark' ? 'Açık temaya geç' : 'Koyu temaya geç' ); }
	}

	if ( system ) {
		system.addEventListener( 'change', function ( m ) {
			if ( ! saved() ) {
				doc.setAttribute( 'data-theme', m.matches ? 'dark' : 'light' );
				label();
			}
		} );
	}

	if ( ! btn ) { return; }
	label();
	btn.hidden = false;
	btn.addEventListener( 'click', function () {
		var next = doc.getAttribute( 'data-theme' ) === 'dark' ? 'light' : 'dark';
		doc.setAttribute( 'data-theme', next );
		try { localStorage.setItem( 'skyra-theme', next ); } catch ( e ) {}
		label();
	} );
}() );
