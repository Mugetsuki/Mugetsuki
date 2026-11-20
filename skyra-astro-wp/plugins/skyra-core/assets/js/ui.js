/**
 * Skyra UI: carousel controls, filters, tabs, "my sign", Moon phase
 * animation and the newsletter/contact forms. Everything is optional:
 * without this file the markup stays complete and usable.
 */
( function () {
	'use strict';

	var cfg = window.skyraConfig || {};
	var reduced = window.matchMedia && window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches;
	function $( sel, root ) { return ( root || document ).querySelector( sel ); }
	function $$( sel, root ) { return Array.prototype.slice.call( ( root || document ).querySelectorAll( sel ) ); }
	function local() {
		try { return window.localStorage; } catch ( e ) { return null; }
	}

	/* Carousel: buttons + progress for the horizontal list (mobile). */
	$$( '[data-carousel]' ).forEach( function ( root ) {
		var track = $( '.sk-carousel__track', root );
		var controls = $( '[data-carousel-controls]', root );
		if ( ! controls ) { return; }
		var prev = $( '[data-carousel-prev]', root );
		var next = $( '[data-carousel-next]', root );
		var bar = $( '[data-carousel-bar]', root );
		controls.hidden = false;
		function update() {
			var max = track.scrollWidth - track.clientWidth;
			var p = max > 0 ? track.scrollLeft / max : 0;
			bar.style.transform = 'scaleX(' + Math.max( 0.08, Math.min( 1, ( track.clientWidth + track.scrollLeft ) / track.scrollWidth ) ) + ')';
			prev.disabled = track.scrollLeft < 4;
			next.disabled = p > 0.99;
			controls.classList.toggle( 'is-static', max <= 4 );
		}
		function go( dir ) {
			track.scrollBy( { left: dir * track.clientWidth * 0.85, behavior: reduced ? 'auto' : 'smooth' } );
		}
		prev.addEventListener( 'click', function () { go( -1 ); } );
		next.addEventListener( 'click', function () { go( 1 ); } );
		track.addEventListener( 'scroll', function () { window.requestAnimationFrame( update ); }, { passive: true } );
		window.addEventListener( 'resize', update );
		update();
	} );

	/* Filters: chips inside [data-filter-scope] toggle [data-filter-item]. */
	$$( '[data-filter-scope]' ).forEach( function ( scope ) {
		var group = $( '[data-filter]', scope );
		if ( ! group ) { return; }
		group.hidden = false;
		group.addEventListener( 'click', function ( e ) {
			var chip = e.target.closest( '[data-value]' );
			if ( ! chip ) { return; }
			var value = chip.dataset.value;
			$$( '[data-value]', group ).forEach( function ( c ) { c.setAttribute( 'aria-pressed', c === chip ? 'true' : 'false' ); } );
			$$( '[data-filter-item]', scope ).forEach( function ( item ) {
				item.hidden = !! value && item.dataset.filterItem !== value;
			} );
			$$( '.sk-month', scope ).forEach( function ( m ) {
				var items = $$( '[data-filter-item]', m );
				m.classList.toggle( 'is-filtered-empty', items.length > 0 && items.every( function ( i ) { return i.hidden; } ) );
			} );
		} );
	} );

	/* Tabs (today / tomorrow). */
	$$( '[data-tabs]' ).forEach( function ( list ) {
		var tabs = $$( '[role=tab]', list );
		list.hidden = false;
		function select( tab, focus ) {
			tabs.forEach( function ( t ) {
				var on = t === tab;
				t.setAttribute( 'aria-selected', on ? 'true' : 'false' );
				t.tabIndex = on ? 0 : -1;
				document.getElementById( t.getAttribute( 'aria-controls' ) ).hidden = ! on;
			} );
			if ( focus ) { tab.focus(); }
		}
		tabs.forEach( function ( t, i ) {
			t.addEventListener( 'click', function () { select( t, false ); } );
			t.addEventListener( 'keydown', function ( e ) {
				if ( e.key === 'ArrowRight' || e.key === 'ArrowLeft' ) {
					e.preventDefault();
					select( tabs[ ( i + ( e.key === 'ArrowRight' ? 1 : tabs.length - 1 ) ) % tabs.length ], true );
				}
			} );
		} );
		select( tabs[ 0 ], false );
	} );

	/* "My sign": highlight in daily lists; explicit toggle on reading pages. */
	var ls = local();
	var mine = null;
	try { mine = ls && ls.getItem( 'skyra-sign' ); } catch ( e ) {}
	function markMine() {
		$$( '.sk-hcard' ).forEach( function ( card ) {
			var on = card.dataset.sign === mine;
			card.classList.toggle( 'is-mine', on );
			var badge = $( '.sk-hcard__mine', card );
			if ( badge ) { badge.hidden = ! on; }
		} );
	}
	markMine();
	$$( '.sk-reading.is-page[data-sign-page]' ).forEach( function ( root ) {
		var slug = root.dataset.signPage;
		var btn = document.createElement( 'button' );
		btn.type = 'button';
		btn.className = 'sk-btn sk-btn--ghost sk-mine-toggle';
		function paint() {
			var on = mine === slug;
			btn.setAttribute( 'aria-pressed', on ? 'true' : 'false' );
			btn.textContent = on ? 'Burcun olarak işaretli' : 'Burcum olarak işaretle';
		}
		btn.addEventListener( 'click', function () {
			mine = mine === slug ? null : slug;
			try {
				if ( mine ) { ls.setItem( 'skyra-sign', mine ); } else { ls.removeItem( 'skyra-sign' ); }
			} catch ( e ) {}
			paint();
		} );
		paint();
		var head = $( '.sk-reading__head', root );
		if ( head && ls ) { head.appendChild( btn ); }
	} );

	/* Moon phase: grow the lit part from new to today's phase once visible. */
	function moonPath( k, waxing, r ) {
		k = Math.max( 0, Math.min( 1, k ) );
		var rx = r * Math.abs( 1 - 2 * k );
		var limb = waxing ? 'M0 ' + ( -r ) + ' A' + r + ' ' + r + ' 0 0 1 0 ' + r : 'M0 ' + ( -r ) + ' A' + r + ' ' + r + ' 0 0 0 0 ' + r;
		var sweep = waxing ? ( k < 0.5 ? 0 : 1 ) : ( k < 0.5 ? 1 : 0 );
		return limb + ' A' + rx.toFixed( 2 ) + ' ' + r + ' 0 0 ' + sweep + ' 0 ' + ( -r ) + ' Z';
	}
	if ( ! reduced && 'IntersectionObserver' in window ) {
		$$( '[data-moon-anim]' ).forEach( function ( box ) {
			var svg = $( '.sk-moon', box );
			var lit = svg && $( '.sk-moon__lit', svg );
			if ( ! lit ) { return; }
			var target = parseFloat( svg.dataset.illum );
			var waxing = svg.dataset.waxing === '1';
			var io = new IntersectionObserver( function ( entries ) {
				if ( ! entries[ 0 ].isIntersecting ) { return; }
				io.disconnect();
				var start = null;
				function frame( t ) {
					start = start || t;
					var p = Math.min( 1, ( t - start ) / 1400 );
					var e = 1 - Math.pow( 1 - p, 3 );
					// Waning phases animate from full light down to today's value.
					var k = waxing ? target * e : 1 - ( 1 - target ) * e;
					lit.setAttribute( 'd', moonPath( k, waxing, 48 ) );
					if ( p < 1 ) { window.requestAnimationFrame( frame ); }
				}
				lit.setAttribute( 'd', moonPath( waxing ? 0 : 1, waxing, 48 ) );
				window.requestAnimationFrame( frame );
			}, { threshold: 0.35 } );
			io.observe( box );
		} );
	}

	/* Newsletter and contact: submit to REST, keep the no-JS fallback. */
	$$( 'form[data-ajax-form]' ).forEach( function ( form ) {
		var kind = form.dataset.ajaxForm;
		var status = $( '[data-status]', form );
		form.addEventListener( 'submit', function ( e ) {
			e.preventDefault();
			var btn = $( 'button[type=submit]', form );
			var data = {};
			new FormData( form ).forEach( function ( v, k ) { data[ k ] = v; } );
			$$( '.sk-error', form ).forEach( function ( er ) { er.hidden = true; } );
			$$( '[aria-invalid]', form ).forEach( function ( c ) { c.removeAttribute( 'aria-invalid' ); } );
			btn.disabled = true;
			btn.setAttribute( 'aria-busy', 'true' );
			fetch( cfg.rest + kind, {
				method: 'POST',
				headers: { 'Content-Type': 'application/json' },
				body: JSON.stringify( data )
			} ).then( function ( r ) { return r.json(); } ).then( function ( res ) {
				btn.disabled = false;
				btn.removeAttribute( 'aria-busy' );
				status.textContent = res.message || '';
				status.classList.add( 'is-visible' );
				status.classList.toggle( 'is-error', ! res.ok );
				if ( res.field ) {
					var ctl = form.querySelector( '[name="' + res.field + '"]' );
					if ( ctl ) {
						ctl.setAttribute( 'aria-invalid', 'true' );
						var err = ctl.closest( '.sk-field' ) && $( '.sk-error', ctl.closest( '.sk-field' ) );
						if ( err ) { err.textContent = res.message; err.hidden = false; }
						ctl.focus();
					}
				} else if ( res.ok ) {
					form.reset();
				}
			} ).catch( function () {
				btn.disabled = false;
				btn.removeAttribute( 'aria-busy' );
				status.textContent = 'Bağlantı kurulamadı. Yeniden dene.';
				status.classList.add( 'is-visible', 'is-error' );
			} );
		} );
	} );
} )();
