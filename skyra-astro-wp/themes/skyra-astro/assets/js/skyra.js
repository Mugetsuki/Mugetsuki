/**
 * Skyra theme: header state, mobile menu, theme toggle and scroll reveals.
 * Content never depends on this file; it only adds motion and convenience.
 */
( function () {
	'use strict';

	var doc = document.documentElement;
	var header = document.querySelector( '[data-header]' );
	var reduced = window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches;
	var hasIO = 'IntersectionObserver' in window;

	/* Header: solid surface once the page scrolls (transparent over the home hero). */
	if ( header ) {
		var onScroll = function () { header.classList.toggle( 'is-scrolled', window.scrollY > 8 ); };
		onScroll();
		window.addEventListener( 'scroll', onScroll, { passive: true } );
	}

	/* Mobile menu: in-page panel, Escape closes and returns focus. */
	var toggle = document.querySelector( '[data-menu-toggle]' );
	if ( toggle && header ) {
		var setOpen = function ( open, focusBack ) {
			header.classList.toggle( 'is-open', open );
			toggle.setAttribute( 'aria-expanded', open ? 'true' : 'false' );
			toggle.setAttribute( 'aria-label', open ? 'Menüyü kapat' : 'Menüyü aç' );
			if ( ! open && focusBack ) { toggle.focus(); }
		};
		toggle.addEventListener( 'click', function () { setOpen( ! header.classList.contains( 'is-open' ), false ); } );
		document.addEventListener( 'keydown', function ( e ) {
			if ( e.key === 'Escape' && header.classList.contains( 'is-open' ) ) { setOpen( false, true ); }
		} );
		document.addEventListener( 'click', function ( e ) {
			if ( header.classList.contains( 'is-open' ) && ! header.contains( e.target ) ) { setOpen( false, false ); }
		} );
		header.addEventListener( 'click', function ( e ) {
			if ( e.target.closest( '.sk-nav a' ) ) { setOpen( false, false ); }
		} );
		window.matchMedia( '(min-width: 1024px)' ).addEventListener( 'change', function ( m ) { if ( m.matches ) { setOpen( false, false ); } } );
	}

	/* Theme toggle: explicit choice overrides the system preference. */
	var themeBtn = document.querySelector( '[data-theme-toggle]' );
	if ( themeBtn ) {
		var system = window.matchMedia( '(prefers-color-scheme: dark)' );
		var current = function () { return doc.getAttribute( 'data-theme' ) || ( system.matches ? 'dark' : 'light' ); };
		var label = function () { themeBtn.setAttribute( 'aria-label', current() === 'dark' ? 'Açık temaya geç' : 'Koyu temaya geç' ); };
		label();
		themeBtn.addEventListener( 'click', function () {
			var next = current() === 'dark' ? 'light' : 'dark';
			doc.setAttribute( 'data-theme', next );
			try { localStorage.setItem( 'skyra-theme', next ); } catch ( e ) {}
			label();
		} );
		system.addEventListener( 'change', label );
	}

	/* Reveal: only elements below the fold are hidden, so nothing flashes. */
	if ( hasIO && ! reduced ) {
		var pending = [];
		document.querySelectorAll( '[data-reveal], .sk-newsletter__panel' ).forEach( function ( el ) {
			if ( el.getBoundingClientRect().top > window.innerHeight * 0.92 ) {
				el.classList.add( 'is-pending' );
				pending.push( el );
			}
		} );
		var io = new IntersectionObserver( function ( entries ) {
			entries.forEach( function ( entry ) {
				if ( entry.isIntersecting ) {
					entry.target.classList.remove( 'is-pending' );
					entry.target.classList.add( 'is-revealed' );
					io.unobserve( entry.target );
				}
			} );
		}, { rootMargin: '0px 0px -8% 0px' } );
		pending.forEach( function ( el ) { io.observe( el ); } );
	}

	/* Chart drawings trace in when they enter the viewport. */
	var draws = document.querySelectorAll( '[data-draw]' );
	if ( hasIO && ! reduced ) {
		var drawIO = new IntersectionObserver( function ( entries ) {
			entries.forEach( function ( entry ) {
				if ( entry.isIntersecting ) {
					entry.target.classList.add( 'is-drawn' );
					drawIO.unobserve( entry.target );
				}
			} );
		}, { threshold: 0.25 } );
		draws.forEach( function ( el ) { drawIO.observe( el ); } );
	} else {
		draws.forEach( function ( el ) { el.classList.add( 'is-drawn' ); } );
	}

	/* Hero orbits pause when off screen (no work for invisible motion). */
	var hero = document.querySelector( '[data-hero-visual]' );
	if ( hero && hasIO ) {
		new IntersectionObserver( function ( entries ) {
			hero.classList.toggle( 'is-paused', ! entries[ 0 ].isIntersecting );
		} ).observe( hero );
	}
} )();
