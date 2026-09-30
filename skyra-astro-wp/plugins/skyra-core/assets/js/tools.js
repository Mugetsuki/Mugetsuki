/**
 * Skyra tools: birth data forms (chart, rising, moon sign) and compatibility.
 * Progressive enhancement over plain HTML forms that already work without JS.
 */
( function () {
	'use strict';

	var cfg = window.skyraConfig || {};
	var HANDOFF = 'skyra-birth';
	var MSG = {
		date: 'Doğum tarihini gün, ay ve yıl olarak gir.',
		time: 'Doğum saatini saat:dakika olarak gir (ör. 14:30).',
		place: 'Doğum yerini yazıp listeden seç.',
		network: 'Bağlantı kurulamadı. İnternetini kontrol edip yeniden dene.',
		busy: 'Hesaplanıyor…'
	};
	var reduced = window.matchMedia && window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches;

	function $( sel, root ) { return ( root || document ).querySelector( sel ); }
	function $$( sel, root ) { return Array.prototype.slice.call( ( root || document ).querySelectorAll( sel ) ); }
	function store( kind ) {
		try { return window[ kind ]; } catch ( e ) { return null; }
	}

	/* ---------------------------------------------------------------- Combobox */

	function combobox( wrap ) {
		var input = $( '[role="combobox"]', wrap );
		var hidden = $( 'input[name="place"]', wrap );
		var list = $( '[role="listbox"]', wrap );
		var items = [];
		var active = -1;
		var timer = null;
		var seq = 0;

		function close() {
			list.hidden = true;
			input.setAttribute( 'aria-expanded', 'false' );
			input.removeAttribute( 'aria-activedescendant' );
			active = -1;
		}
		function highlight( i ) {
			var opts = $$( '[role="option"]', list );
			opts.forEach( function ( o, n ) { o.setAttribute( 'aria-selected', n === i ? 'true' : 'false' ); } );
			active = i;
			if ( opts[ i ] ) {
				input.setAttribute( 'aria-activedescendant', opts[ i ].id );
				opts[ i ].scrollIntoView( { block: 'nearest' } );
			}
		}
		function choose( i ) {
			var p = items[ i ];
			if ( ! p ) { return; }
			input.value = p.label;
			hidden.value = p.id;
			wrap.dataset.tz = p.tz;
			close();
			clearError( wrap.closest( '.sk-field' ) );
			input.dispatchEvent( new CustomEvent( 'skyra:place', { bubbles: true } ) );
		}
		function render( results, q ) {
			items = results;
			list.innerHTML = '';
			if ( ! results.length ) {
				var none = document.createElement( 'li' );
				none.className = 'is-empty';
				none.setAttribute( 'role', 'option' );
				none.setAttribute( 'aria-disabled', 'true' );
				none.id = list.id + '-none';
				none.textContent = '“' + q + '” için sonuç bulunamadı. Bağlı olduğu ili ya da yakın bir şehri dene.';
				list.appendChild( none );
			}
			results.forEach( function ( p, i ) {
				var li = document.createElement( 'li' );
				li.id = list.id + '-' + i;
				li.setAttribute( 'role', 'option' );
				li.setAttribute( 'aria-selected', 'false' );
				li.innerHTML = '<span></span><small></small>';
				li.firstChild.textContent = p.name;
				li.lastChild.textContent = p.sub || p.country;
				li.addEventListener( 'mousedown', function ( e ) { e.preventDefault(); choose( i ); } );
				list.appendChild( li );
			} );
			list.hidden = false;
			input.setAttribute( 'aria-expanded', 'true' );
			active = -1;
		}
		function search() {
			var q = input.value.trim();
			if ( q.length < 2 ) { close(); return; }
			var mine = ++seq;
			fetch( cfg.rest + 'places?q=' + encodeURIComponent( q ) )
				.then( function ( r ) { return r.json(); } )
				.then( function ( data ) { if ( mine === seq && document.activeElement === input ) { render( Array.isArray( data ) ? data : [], q ); } } )
				.catch( function () {} );
		}

		input.addEventListener( 'input', function () {
			hidden.value = '';
			clearTimeout( timer );
			timer = setTimeout( search, 180 );
		} );
		input.addEventListener( 'keydown', function ( e ) {
			var count = items.length;
			if ( e.key === 'ArrowDown' ) {
				e.preventDefault();
				if ( list.hidden ) { search(); return; }
				highlight( count ? ( active + 1 ) % count : -1 );
			} else if ( e.key === 'ArrowUp' ) {
				e.preventDefault();
				highlight( count ? ( active - 1 + count ) % count : -1 );
			} else if ( e.key === 'Enter' && ! list.hidden && active > -1 ) {
				e.preventDefault();
				choose( active );
			} else if ( e.key === 'Escape' ) {
				close();
			}
		} );
		input.addEventListener( 'blur', function () { setTimeout( close, 120 ); } );
		wrap.skyraSet = function ( place ) {
			input.value = place.label;
			hidden.value = place.id;
		};
	}

	/* --------------------------------------------------------------- Errors */

	function setError( field, text ) {
		if ( ! field ) { return; }
		var err = $( '.sk-error', field );
		var ctl = $( 'input:not([type=hidden]):not([type=checkbox]), select, textarea', field );
		if ( err ) { err.textContent = text; err.hidden = false; }
		if ( ctl ) { ctl.setAttribute( 'aria-invalid', 'true' ); }
		field.classList.add( 'has-error' );
	}
	function clearError( field ) {
		if ( ! field ) { return; }
		var err = $( '.sk-error', field );
		if ( err ) { err.hidden = true; err.textContent = ''; }
		$$( '[aria-invalid]', field ).forEach( function ( c ) { c.removeAttribute( 'aria-invalid' ); } );
		field.classList.remove( 'has-error' );
	}

	function validate( form, scope ) {
		var ok = true;
		var first = null;
		$$( '.sk-field[data-field]', scope || form ).forEach( function ( field ) {
			var name = field.dataset.field;
			var bad = false;
			if ( name === 'date' ) {
				var v = $( 'input', field ).value;
				bad = ! /^\d{4}-\d{2}-\d{2}$/.test( v ) || v < '1900-01-01' || v > '2100-12-31';
			} else if ( name === 'time' ) {
				var t = $( 'input[type=time]', field );
				var unknown = $( '[data-time-unknown]', field );
				bad = ! ( unknown && unknown.checked ) && ( t.required || t.value ) && ! /^\d{2}:\d{2}/.test( t.value );
			} else if ( name === 'place' ) {
				bad = ! $( 'input[name=place]', field ).value;
			}
			if ( bad ) {
				setError( field, MSG[ name ] );
				ok = false;
				first = first || field;
			} else {
				clearError( field );
			}
		} );
		if ( first ) {
			var ctl = $( 'input:not([type=hidden])', first );
			if ( ctl ) { ctl.focus(); }
		}
		return ok;
	}

	/* -------------------------------------------------------------- Stepper */

	function stepper( form ) {
		var steps = $$( '[data-step]', form );
		var current = 0;
		var indicator = $( '[data-steps]', form );
		indicator.hidden = false;
		$$( '[data-step-nav], [data-prev]', form ).forEach( function ( n ) { n.hidden = false; } );
		function show( i, focus ) {
			current = i;
			steps.forEach( function ( s, n ) { s.hidden = n !== i; } );
			$( '[data-step-no]', form ).textContent = String( i + 1 );
			$$( '.sk-steps__dots li', form ).forEach( function ( d, n ) { d.classList.toggle( 'is-active', n <= i ); } );
			if ( focus ) {
				var ctl = $( 'input:not([type=hidden]):not([disabled])', steps[ i ] );
				if ( ctl ) { ctl.focus(); }
			}
		}
		form.addEventListener( 'click', function ( e ) {
			if ( e.target.closest( '[data-next]' ) ) {
				if ( validate( form, steps[ current ] ) ) { show( current + 1, true ); }
			} else if ( e.target.closest( '[data-prev]' ) ) {
				show( Math.max( 0, current - 1 ), true );
			}
		} );
		form.addEventListener( 'keydown', function ( e ) {
			if ( e.key === 'Enter' && e.target.matches( 'input' ) && current < steps.length - 1 && ! e.target.closest( '[data-combo]' ) ) {
				e.preventDefault();
				if ( validate( form, steps[ current ] ) ) { show( current + 1, true ); }
			}
		} );
		form.skyraStep = show;
		show( 0, false );
	}

	/* ------------------------------------------------------------ Submitting */

	function values( form ) {
		var fd = new FormData( form );
		var out = {};
		fd.forEach( function ( v, k ) { out[ k ] = v; } );
		if ( out.time_unknown ) { out.time = ''; }
		return out;
	}

	function busy( form, on ) {
		var btn = $( '[data-submit]', form );
		var label = btn && $( 'span', btn );
		if ( ! btn ) { return; }
		if ( on ) {
			btn.dataset.label = label.textContent;
			label.textContent = MSG.busy;
			btn.setAttribute( 'aria-busy', 'true' );
			btn.disabled = true;
		} else {
			label.textContent = btn.dataset.label || label.textContent;
			btn.removeAttribute( 'aria-busy' );
			btn.disabled = false;
		}
	}

	function showResult( form, html ) {
		var host = form.closest( '.sk-tool, .sk-chartcta__panel' ) || form.parentNode;
		var slot = $( '[data-result-slot]', host.closest( '.sk-tool' ) || host );
		if ( ! slot ) { return; }
		slot.innerHTML = html;
		var result = $( '[data-result]', slot );
		if ( form.dataset.variant === 'compact' ) {
			form.hidden = true;
		}
		if ( result ) {
			result.focus( { preventScroll: true } );
			var top = slot.getBoundingClientRect().top + window.scrollY - 96;
			window.scrollTo( { top: top, behavior: reduced ? 'auto' : 'smooth' } );
			$$( '[data-draw]', result ).forEach( function ( f ) {
				requestAnimationFrame( function () { f.classList.add( 'is-drawn' ); } );
			} );
		}
	}

	function submit( form ) {
		var tool = form.dataset.tool;
		var status = $( '[data-status]', form );
		var data = values( form );
		if ( status ) { status.textContent = ''; status.classList.remove( 'is-visible', 'is-error' ); }

		var req;
		if ( tool === 'compatibility' ) {
			req = fetch( cfg.rest + 'compatibility?a=' + encodeURIComponent( data.a ) + '&b=' + encodeURIComponent( data.b ) + '&level=' + ( form.dataset.level || 2 ) );
		} else {
			if ( ! validate( form ) ) { return; }
			var body = { date: data.date, time: data.time || '', place: parseInt( data.place, 10 ), variant: data.variant, level: parseInt( form.dataset.level || 2, 10 ) };
			form.skyraLast = body;
			req = fetch( cfg.rest + ( tool === 'moon-sign' ? 'moon-sign' : 'chart' ), {
				method: 'POST',
				headers: { 'Content-Type': 'application/json' },
				body: JSON.stringify( body ),
				cache: 'no-store'
			} );
		}
		busy( form, true );
		req.then( function ( r ) {
			return r.json().then( function ( j ) { return { ok: r.ok, body: j }; } );
		} ).then( function ( res ) {
			busy( form, false );
			if ( ! res.ok ) {
				var field = res.body && res.body.data && res.body.data.field;
				var target = field && $( '.sk-field[data-field="' + field + '"]', form );
				if ( target ) {
					if ( form.skyraStep ) { form.skyraStep( $$( '[data-step]', form ).indexOf( target.closest( '[data-step]' ) ), false ); }
					setError( target, res.body.message );
					var ctl = $( 'input:not([type=hidden])', target );
					if ( ctl ) { ctl.focus(); }
				} else if ( status ) {
					status.textContent = ( res.body && res.body.message ) || MSG.network;
					status.classList.add( 'is-visible', 'is-error' );
				}
				return;
			}
			if ( res.body.big3 && res.body.big3.sun ) {
				var ls = store( 'localStorage' );
				try { if ( ls ) { ls.setItem( 'skyra-sign', res.body.big3.sun.sign ); } } catch ( e ) {}
			}
			showResult( form, res.body.html );
		} ).catch( function () {
			busy( form, false );
			if ( status ) {
				status.textContent = MSG.network;
				status.classList.add( 'is-visible', 'is-error' );
			}
		} );
	}

	/* ------------------------------------------------------------ Wiring */

	$$( 'form[data-tool]' ).forEach( function ( form ) {
		$$( '[data-combo]', form ).forEach( combobox );
		if ( form.hasAttribute( 'data-stepper' ) ) { stepper( form ); }

		var unknown = $( '[data-time-unknown]', form );
		if ( unknown ) {
			unknown.addEventListener( 'change', function () {
				var t = $( 'input[type=time]', form );
				t.disabled = unknown.checked;
				if ( unknown.checked ) { t.value = ''; clearError( t.closest( '.sk-field' ) ); }
			} );
		}
		form.addEventListener( 'submit', function ( e ) {
			e.preventDefault();
			submit( form );
		} );
		if ( form.dataset.tool === 'compatibility' ) {
			$$( 'select', form ).forEach( function ( s ) { s.addEventListener( 'change', function () { submit( form ); } ); } );
		}
	} );

	document.addEventListener( 'click', function ( e ) {
		var handoff = e.target.closest( '[data-handoff]' );
		if ( handoff ) {
			var form = document.querySelector( 'form[data-tool="chart"]' );
			var ss = store( 'sessionStorage' );
			if ( form && form.skyraLast && ss ) {
				var place = $( '[role=combobox]', form );
				try { ss.setItem( HANDOFF, JSON.stringify( Object.assign( { label: place ? place.value : '' }, form.skyraLast ) ) ); } catch ( err ) {}
			}
			return;
		}
		var reset = e.target.closest( '[data-reset]' );
		if ( reset ) {
			var slot = reset.closest( '[data-result-slot]' );
			var scope = slot.closest( '.sk-tool, .sk-chartcta__panel' );
			var f = $( 'form[data-tool]', scope );
			slot.innerHTML = '';
			if ( f ) {
				f.hidden = false;
				if ( f.skyraStep ) { f.skyraStep( 0, true ); } else {
					var first = $( 'input:not([type=hidden])', f );
					if ( first ) { first.focus(); }
				}
				f.scrollIntoView( { block: 'start', behavior: reduced ? 'auto' : 'smooth' } );
			}
		}
	} );

	// Arriving from the home page summary: fill the full form and run it once.
	var ss = store( 'sessionStorage' );
	var saved = null;
	try { saved = ss && JSON.parse( ss.getItem( HANDOFF ) || 'null' ); } catch ( e ) {}
	var full = document.querySelector( 'form[data-tool="chart"][data-variant="full"], form[data-tool="chart"][data-variant="rising"]' );
	if ( saved && full ) {
		try { ss.removeItem( HANDOFF ); } catch ( e ) {}
		$( 'input[name=date]', full ).value = saved.date;
		var time = $( 'input[name=time]', full );
		if ( saved.time ) { time.value = saved.time; } else {
			var unk = $( '[data-time-unknown]', full );
			if ( unk ) { unk.checked = true; time.disabled = true; }
		}
		var combo = $( '[data-combo]', full );
		if ( combo && combo.skyraSet ) { combo.skyraSet( { id: saved.place, label: saved.label } ); }
		submit( full );
	}
} )();
