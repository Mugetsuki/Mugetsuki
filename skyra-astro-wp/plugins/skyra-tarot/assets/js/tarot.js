/**
 * Skyra Tarot — interactive reading.
 *
 * Flow: choose a spread → shuffle → pick cards from the fan → cards fly
 * to their places → turn them over → reflective reading.
 * Everything runs in the browser: no question field, nothing is sent to
 * the server and nothing is stored.
 */
( function () {
	'use strict';

	var rootEl = document.querySelector( '[data-skyra-tarot]' );
	if ( ! rootEl || ! window.SkyraTarot ) { return; }
	var dataEl = rootEl.querySelector( 'script.skt-data' );
	var DATA;
	try { DATA = JSON.parse( dataEl.textContent ); } catch ( e ) { return; }
	var DECK = DATA.deck;
	var SPREADS = DATA.spreads;
	var CFG = DATA.config || {};
	var T = window.SkyraTarot;

	var reduced = window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches;
	var app = rootEl.querySelector( '.skt-app' );
	var chooser = app.querySelector( '.skt-spreads' );
	var stage = app.querySelector( '.skt-stage' );
	var board = app.querySelector( '.skt-board' );
	var deckEl = app.querySelector( '.skt-deck' );
	var statusEl = app.querySelector( '.skt-status' );
	var btnShuffle = app.querySelector( '[data-act="shuffle"]' );
	var btnAuto = app.querySelector( '[data-act="auto"]' );
	var btnReveal = app.querySelector( '[data-act="reveal"]' );
	var readingEl = app.querySelector( '.skt-reading' );

	var state = { spread: null, order: [], picks: [], phase: 'choose', busy: false };

	/* ------------------------------------------------------------ helpers */
	function h( tag, cls, html ) {
		var n = document.createElement( tag );
		if ( cls ) { n.className = cls; }
		if ( html !== undefined ) { n.innerHTML = html; }
		return n;
	}
	function esc( s ) {
		return String( s ).replace( /[&<>"']/g, function ( c ) { return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[ c ]; } );
	}
	function rand( n ) {
		var a = new Uint32Array( 1 );
		( window.crypto || window.msCrypto ).getRandomValues( a );
		return a[ 0 ] % n;
	}
	function shuffled( list ) {
		var a = list.slice();
		for ( var i = a.length - 1; i > 0; i-- ) { var j = rand( i + 1 ); var t = a[ i ]; a[ i ] = a[ j ]; a[ j ] = t; }
		return a;
	}
	function animate( el, frames, opts ) {
		if ( reduced || ! el.animate ) { return Promise.resolve(); }
		var a = el.animate( frames, opts );
		return a.finished.catch( function () {} );
	}
	function wait( ms ) { return new Promise( function ( r ) { window.setTimeout( r, reduced ? 0 : ms ); } ); }
	function say( text ) { statusEl.textContent = text; }

	/* ------------------------------------------------------------ card art */
	var ROMAN = [ '0', 'I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII', 'XIII', 'XIV', 'XV', 'XVI', 'XVII', 'XVIII', 'XIX', 'XX', 'XXI' ];
	var COURT = { 11: 'Uşak', 12: 'Şövalye', 13: 'Kraliçe', 14: 'Kral' };
	function pentagram() {
		var p = [];
		for ( var i = 0; i < 5; i++ ) { var a = ( -90 + i * 144 ) * Math.PI / 180; p.push( ( 12 + 6.4 * Math.cos( a ) ).toFixed( 2 ) + ' ' + ( 12 + 6.4 * Math.sin( a ) ).toFixed( 2 ) ); }
		return '<circle cx="12" cy="12" r="8.5"/><path d="M' + p.join( 'L' ) + 'Z"/>';
	}
	var SUIT_GLYPH = {
		asa: '<path d="M6.5 20.5 17.5 3.5"/><path d="M14.8 7.6c1.6-.4 2.8.1 3.3 1.2"/><path d="M10.6 13.9c-1.7.1-2.8-.6-3.1-1.7"/><circle cx="17.5" cy="3.5" r="1"/>',
		kupa: '<path d="M6 4h12v4.5a6 6 0 0 1-12 0z"/><path d="M12 14.5v5"/><path d="M8 20.5h8"/>',
		kilic: '<path d="M12 2.5 13.6 5.2v11.3h-3.2V5.2z"/><path d="M7.5 16.5h9"/><path d="M12 16.5v4"/><circle cx="12" cy="21.4" r=".9"/>',
		tilsim: pentagram()
	};
	var MAJOR_GLYPH = [
		'<circle cx="17" cy="6" r="2.6"/><path d="M3.5 20c4-.8 6-5 9.2-6.2 2.8-1 4.6.4 7.8-1.6"/><path d="M3.5 20h7"/>',
		'<path d="M12 12c-2-3.2-6.2-3.2-6.2 0s4.2 3.2 6.2 0 6.2-3.2 6.2 0-4.2 3.2-6.2 0"/><path d="M12 15.5v5.5"/><path d="M9.5 21h5"/>',
		'<path d="M5.5 3.5v17"/><path d="M18.5 3.5v17"/><path d="M14 8.5a3.6 3.6 0 1 0 0 7 4.6 4.6 0 0 1 0-7z"/>',
		'<circle cx="12" cy="9" r="4.6"/><path d="M12 13.6V21"/><path d="M8.8 18h6.4"/>',
		'<path d="M5 9.5l3.5 3L12 6.5l3.5 6L19 9.5v7.5H5z"/><path d="M5 20h14"/>',
		'<circle cx="8" cy="8" r="3.6"/><path d="M10.6 10.6 19.5 19.5"/><path d="M16.2 16.2l2-2"/><path d="M18.3 18.3l1.6-1.6"/>',
		'<circle cx="9.3" cy="12" r="5.2"/><circle cx="14.7" cy="12" r="5.2"/>',
		'<path d="M12 2.8l1 2.2 2.4.3-1.8 1.6.5 2.4L12 8.1 9.9 9.3l.5-2.4-1.8-1.6 2.4-.3z"/><path d="M5 12.5h14v4.5H5z"/><circle cx="8" cy="19.5" r="1.6"/><circle cx="16" cy="19.5" r="1.6"/>',
		'<path d="M12 5.2c-1.2-1.9-3.8-1.9-3.8 0s2.6 1.9 3.8 0 3.8-1.9 3.8 0-2.6 1.9-3.8 0"/><circle cx="12" cy="15" r="5.2"/><path d="M10 15.2h4"/>',
		'<path d="M9 8.5h6l-1 7.5h-4z"/><path d="M12 5v3.5"/><path d="M10 16v2.2h4V16"/><path d="M12 10.5v3"/><path d="M10.5 12h3"/>',
		'<circle cx="12" cy="12" r="8.4"/><circle cx="12" cy="12" r="2.4"/><path d="M12 3.6v6M12 14.4v6M3.6 12h6M14.4 12h6"/>',
		'<path d="M12 3.5v16.5"/><path d="M8 20.5h8"/><path d="M4.5 7h15"/><path d="M4.5 7 2.2 13h4.6z"/><path d="M19.5 7l-2.3 6h4.6z"/>',
		'<path d="M4 3.5h16"/><path d="M12 3.5v8"/><circle cx="12" cy="15.8" r="3"/><path d="M9 7.5l3 2.4 3-2.4"/>',
		'<path d="M3 17h18"/><path d="M7 17a5 5 0 0 1 10 0"/><path d="M12 7.5v2.2"/><path d="M6.3 10.2l1.5 1.5"/><path d="M17.7 10.2l-1.5 1.5"/><path d="M6 20.5h12"/>',
		'<path d="M3.8 5.5h5.2v3a2.6 2.6 0 0 1-5.2 0z"/><path d="M15 14.5h5.2v3a2.6 2.6 0 0 1-5.2 0z"/><path d="M8.2 10.2c3 .9 5.2 2.6 7.2 5"/>',
		'<rect x="4" y="6.5" width="10" height="5.2" rx="2.6"/><rect x="10" y="12.3" width="10" height="5.2" rx="2.6"/>',
		'<path d="M8 21V9.5h8V21"/><path d="M8 9.5 7 6.5h10l-1 3"/><path d="M11 21v-3h2v3"/><path d="M17.5 1.8l-2.8 4.4h2.8l-2.8 4.4"/>',
		'<path d="M12 2.8v18.4M2.8 12h18.4"/><path d="M6.5 6.5l11 11M17.5 6.5l-11 11"/><circle cx="12" cy="12" r="2.6"/>',
		'<path d="M15.5 3.5a8.5 8.5 0 1 0 0 17 7 7 0 0 1 0-17z"/>',
		'<circle cx="12" cy="12" r="4.2"/><path d="M12 2.5v3M12 18.5v3M2.5 12h3M18.5 12h3M5.3 5.3l2.1 2.1M16.6 16.6l2.1 2.1M18.7 5.3l-2.1 2.1M7.4 16.6l-2.1 2.1"/>',
		'<path d="M3 10.5v3l11 4.5V6z"/><path d="M14 6c3.4.2 6.2 2.6 7 6-.8 3.4-3.6 5.8-7 6"/><path d="M6 13.5v4.5"/>',
		'<ellipse cx="12" cy="12" rx="6.4" ry="9"/><circle cx="12" cy="12" r="1.8"/><circle cx="3.2" cy="3.2" r=".9"/><circle cx="20.8" cy="3.2" r=".9"/><circle cx="3.2" cy="20.8" r=".9"/><circle cx="20.8" cy="20.8" r=".9"/>'
	];
	/* Pip positions (0–100 × 0–160 card space) for ranks 2–10. */
	var PIPS = {
		2: [ [ 50, 48 ], [ 50, 104 ] ],
		3: [ [ 50, 44 ], [ 50, 76 ], [ 50, 108 ] ],
		4: [ [ 33, 50 ], [ 67, 50 ], [ 33, 102 ], [ 67, 102 ] ],
		5: [ [ 33, 46 ], [ 67, 46 ], [ 50, 76 ], [ 33, 106 ], [ 67, 106 ] ],
		6: [ [ 33, 44 ], [ 67, 44 ], [ 33, 76 ], [ 67, 76 ], [ 33, 108 ], [ 67, 108 ] ],
		7: [ [ 33, 42 ], [ 67, 42 ], [ 50, 58 ], [ 33, 76 ], [ 67, 76 ], [ 33, 110 ], [ 67, 110 ] ],
		8: [ [ 33, 40 ], [ 67, 40 ], [ 50, 56 ], [ 33, 76 ], [ 67, 76 ], [ 50, 96 ], [ 33, 112 ], [ 67, 112 ] ],
		9: [ [ 33, 40 ], [ 67, 40 ], [ 33, 64 ], [ 67, 64 ], [ 50, 76 ], [ 33, 88 ], [ 67, 88 ], [ 33, 112 ], [ 67, 112 ] ],
		10: [ [ 33, 38 ], [ 67, 38 ], [ 50, 52 ], [ 33, 66 ], [ 67, 66 ], [ 33, 86 ], [ 67, 86 ], [ 50, 100 ], [ 33, 114 ], [ 67, 114 ] ]
	};
	function glyph( inner, x, y, size, cls ) {
		var s = size / 24;
		return '<g class="' + ( cls || '' ) + '" transform="translate(' + ( x - size / 2 ) + ' ' + ( y - size / 2 ) + ') scale(' + s + ')" fill="none" stroke="currentColor" stroke-width="' + ( 1.4 / s ).toFixed( 2 ) + '" stroke-linecap="round" stroke-linejoin="round">' + inner + '</g>';
	}
	function nameLines( name ) {
		var parts = name.split( ' ' );
		if ( name.length <= 13 || parts.length === 1 ) { return [ name ]; }
		return [ parts[ 0 ], parts.slice( 1 ).join( ' ' ) ];
	}
	function face( card ) {
		var top;
		var art;
		var cls = card.s ? 'skt-el--' + card.s : 'skt-el--major';
		if ( card.a === 'major' ) {
			top = ROMAN[ card.r ];
			art = '<circle cx="50" cy="76" r="27" class="skt-face__halo"/>' + glyph( MAJOR_GLYPH[ card.r ], 50, 76, 40, 'skt-face__glyph' );
		} else if ( card.r === 1 ) {
			top = 'As';
			art = '<circle cx="50" cy="76" r="27" class="skt-face__halo"/>' + glyph( SUIT_GLYPH[ card.s ], 50, 76, 42, 'skt-face__glyph' );
		} else if ( card.r <= 10 ) {
			top = String( card.r );
			art = PIPS[ card.r ].map( function ( p ) { return glyph( SUIT_GLYPH[ card.s ], p[ 0 ], p[ 1 ], 17, 'skt-face__glyph' ); } ).join( '' );
		} else {
			top = COURT[ card.r ];
			var crown = { 11: '<path d="M40 40l4 4 6-5 6 5 4-4"/>', 12: '<path d="M38 44h24M44 38l6-4 6 4"/>', 13: '<path d="M54 36a6 6 0 1 1-6 9 7 7 0 0 0 6-9z"/>', 14: '<path d="M38 46l4-9 8 6 8-6 4 9z"/>' }[ card.r ];
			art = '<g class="skt-face__crown" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round" stroke-linecap="round">' + crown + '</g>' + glyph( SUIT_GLYPH[ card.s ], 50, 82, 40, 'skt-face__glyph' );
		}
		var lines = nameLines( card.n );
		var label = lines.map( function ( l, i ) { return '<tspan x="50" dy="' + ( i ? 9 : 0 ) + '">' + esc( l ) + '</tspan>'; } ).join( '' );
		return '<svg class="skt-face ' + cls + '" viewBox="0 0 100 160" role="img" aria-label="' + esc( card.n ) + '">' +
			'<rect x="1" y="1" width="98" height="158" rx="9" class="skt-face__bg"/>' +
			'<rect x="6" y="6" width="88" height="148" rx="6" class="skt-face__frame"/>' +
			'<text x="50" y="22" class="skt-face__top">' + esc( top ) + '</text>' +
			art +
			'<text x="50" y="' + ( lines.length > 1 ? 136 : 141 ) + '" class="skt-face__name">' + label + '</text>' +
			'</svg>';
	}
	function cardEl( card, reversed ) {
		var el = h( 'div', 'skt-card' + ( reversed ? ' is-reversed' : '' ) );
		el.innerHTML = '<div class="skt-card__inner"><div class="skt-card__back" aria-hidden="true"></div><div class="skt-card__front">' + ( card ? face( card ) : '' ) + '</div></div>';
		return el;
	}

	/* ------------------------------------------------------------ spreads */
	function buildChooser() {
		SPREADS.forEach( function ( sp ) {
			var b = h( 'button', 'skt-spread' );
			b.type = 'button';
			b.setAttribute( 'aria-pressed', 'false' );
			b.dataset.spread = sp.id;
			var dots = sp.positions.map( function ( p ) {
				var x = ( p.x / sp.board.w ) * 100, y = ( p.y / sp.board.h ) * 100;
				return '<i style="left:' + x + '%;top:' + y + '%' + ( p.rot ? ';transform:translate(-50%,-50%) rotate(90deg)' : '' ) + '"></i>';
			} ).join( '' );
			b.innerHTML = '<span class="skt-spread__map" style="aspect-ratio:' + sp.board.w + '/' + sp.board.h + '">' + dots + '</span>' +
				'<span class="skt-spread__name">' + esc( sp.name ) + '</span>' +
				'<span class="skt-spread__meta">' + sp.positions.length + ' kart · ' + esc( sp.short ) + '</span>';
			b.addEventListener( 'click', function () { if ( ! state.busy ) { choose( sp ); } } );
			chooser.appendChild( b );
		} );
	}

	function choose( sp ) {
		state.spread = sp;
		state.picks = [];
		state.phase = 'ready';
		Array.prototype.forEach.call( chooser.children, function ( b ) { b.setAttribute( 'aria-pressed', b.dataset.spread === sp.id ? 'true' : 'false' ); } );
		readingEl.hidden = true;
		readingEl.innerHTML = '';
		stage.hidden = false;
		stage.dataset.spread = sp.id;
		buildBoard();
		deckEl.hidden = false;
		deckEl.getAnimations && deckEl.getAnimations().forEach( function ( an ) { an.cancel(); } );
		buildStack();
		btnShuffle.hidden = false;
		btnShuffle.disabled = false;
		btnAuto.hidden = true;
		btnReveal.hidden = true;
		say( sp.name + ' seçildi. Sorunu aklında tut ve kartları karıştır.' );
		var note = stage.querySelector( '.skt-note' );
		note.textContent = sp.note || '';
		note.hidden = ! sp.note;
		animate( board, [ { opacity: 0, transform: 'translateY(16px) scale(.98)' }, { opacity: 1, transform: 'none' } ], { duration: 500, easing: 'cubic-bezier(.22,.8,.24,1)' } );
		stage.scrollIntoView( { behavior: reduced ? 'auto' : 'smooth', block: 'start' } );
	}

	function buildBoard() {
		var sp = state.spread;
		board.innerHTML = '';
		board.style.aspectRatio = sp.board.w + ' / ' + sp.board.h;
		board.style.setProperty( '--cw', ( 100 / sp.board.w ) + '%' );
		sp.positions.forEach( function ( p, i ) {
			var slot = h( 'div', 'skt-slot' + ( p.rot ? ' is-cross' : '' ) );
			slot.style.left = ( ( p.x - 0.5 ) / sp.board.w ) * 100 + '%';
			slot.style.top = ( ( p.y - 0.8 ) / sp.board.h ) * 100 + '%';
			slot.style.width = ( 100 / sp.board.w ) + '%';
			slot.dataset.index = i;
			slot.setAttribute( 'aria-label', ( i + 1 ) + '. pozisyon: ' + p.label );
			slot.title = ( i + 1 ) + ' · ' + p.label;
			if ( sp.positions[ i + 1 ] && sp.positions[ i + 1 ].rot ) { slot.classList.add( 'is-under' ); }
			slot.innerHTML = '<span class="skt-slot__num" aria-hidden="true">' + ( i + 1 ) + '</span>';
			board.appendChild( slot );
		} );
	}

	/* ------------------------------------------------------------ deck */
	var STACK = 14;
	function buildStack() {
		deckEl.innerHTML = '';
		deckEl.className = 'skt-deck is-stack';
		deckEl.style.height = '';
		for ( var i = 0; i < STACK; i++ ) {
			var c = cardEl( null, false );
			c.classList.add( 'skt-stack-card' );
			c.style.setProperty( '--i', i );
			deckEl.appendChild( c );
		}
	}

	function shuffleAnim() {
		var cards = Array.prototype.slice.call( deckEl.querySelectorAll( '.skt-stack-card' ) );
		var ease = 'cubic-bezier(.22,.8,.24,1)';
		var split = cards.map( function ( c, i ) {
			var left = i % 2 === 0;
			return animate( c, [
				{ transform: 'translate(-50%,-50%)' },
				{ transform: 'translate(calc(-50% + ' + ( left ? -1 : 1 ) * ( 70 + i * 3 ) + 'px), calc(-50% - ' + ( i * 2 ) + 'px)) rotate(' + ( left ? -12 : 12 ) + 'deg)', offset: 0.35 },
				{ transform: 'translate(calc(-50% + ' + ( left ? -1 : 1 ) * 18 + 'px), calc(-50% - 40px)) rotate(' + ( left ? -4 : 4 ) + 'deg)', offset: 0.7 },
				{ transform: 'translate(-50%,-50%)' }
			], { duration: 900, delay: i * 28, easing: ease } );
		} );
		return Promise.all( split ).then( function () {
			var burst = cards.map( function ( c, i ) {
				var a = ( i / cards.length ) * Math.PI * 2;
				var r = 110;
				return animate( c, [
					{ transform: 'translate(-50%,-50%) rotate(0deg)' },
					{ transform: 'translate(calc(-50% + ' + ( Math.cos( a ) * r ).toFixed( 1 ) + 'px), calc(-50% + ' + ( Math.sin( a ) * r * 0.55 ).toFixed( 1 ) + 'px)) rotate(' + ( a * 57.3 + 90 ).toFixed( 0 ) + 'deg)', offset: 0.5 },
					{ transform: 'translate(-50%,-50%) rotate(360deg)' }
				], { duration: 1000, delay: i * 18, easing: 'cubic-bezier(.65,0,.35,1)' } );
			} );
			return Promise.all( burst );
		} );
	}

	function fanGeometry( n ) {
		var W = deckEl.clientWidth;
		var wide = W >= 900;
		if ( wide ) {
			var cw = 84, ch = 134, perRow = Math.ceil( n / 2 ), A = 22 * Math.PI / 180;
			var R = ( W - cw - 16 ) / ( 2 * Math.sin( A ) );
			var drop = R * ( 1 - Math.cos( A ) );
			var pos = [];
			for ( var i = 0; i < n; i++ ) {
				var row = i < perRow ? 0 : 1, k = row ? i - perRow : i, count = row ? n - perRow : perRow;
				var t = count > 1 ? ( k / ( count - 1 ) ) * 2 - 1 : 0;
				var th = t * A;
				pos.push( { x: W / 2 + R * Math.sin( th ) - cw / 2, y: row * 158 + R * ( 1 - Math.cos( th ) ), r: th * 57.2958 } );
			}
			return { pos: pos, cw: cw, ch: ch, height: 158 + ch + drop + 24, scroll: false };
		}
		var mcw = 64, mch = 102, step = 30;
		var p2 = [];
		for ( var j = 0; j < n; j++ ) { p2.push( { x: 12 + j * step, y: 18 + Math.sin( ( j / ( n - 1 ) ) * Math.PI ) * -10 + 10, r: 0 } ); }
		return { pos: p2, cw: mcw, ch: mch, height: mch + 48, scroll: true, inner: 24 + ( n - 1 ) * step + mcw };
	}

	function spreadFan() {
		state.order = shuffled( DECK.map( function ( c, i ) { return i; } ) );
		var g = fanGeometry( state.order.length );
		var stackRect = deckEl.getBoundingClientRect();
		deckEl.innerHTML = '';
		deckEl.className = 'skt-deck is-fan' + ( g.scroll ? ' is-scroll' : '' );
		deckEl.style.height = g.height + 'px';
		deckEl.style.setProperty( '--cw', g.cw + 'px' );
		deckEl.style.setProperty( '--ch', g.ch + 'px' );
		var track = h( 'div', 'skt-fan' );
		if ( g.scroll ) { track.style.width = g.inner + 'px'; }
		deckEl.appendChild( track );
		var jobs = state.order.map( function ( deckIndex, k ) {
			var c = cardEl( null, false );
			var btn = h( 'button', 'skt-fan-card' );
			btn.type = 'button';
			btn.dataset.k = k;
			btn.setAttribute( 'aria-label', 'Kart ' + ( k + 1 ) + ' / ' + state.order.length );
			btn.appendChild( c );
			var p = g.pos[ k ];
			btn.style.left = p.x + 'px';
			btn.style.top = p.y + 'px';
			btn.style.setProperty( '--r', p.r.toFixed( 2 ) + 'deg' );
			btn.addEventListener( 'click', function () { pick( btn ); } );
			track.appendChild( btn );
			var dx = stackRect.width / 2 - p.x - g.cw / 2;
			var dy = Math.min( stackRect.height, g.height ) / 2 - p.y - g.ch / 2;
			return animate( btn, [
				{ transform: 'translate(' + dx + 'px,' + dy + 'px) rotate(0deg) scale(.9)', opacity: 0 },
				{ opacity: 1, offset: 0.2 },
				{ transform: 'translate(0,0) rotate(var(--r)) scale(1)', opacity: 1 }
			], { duration: 700, delay: k * 7, easing: 'cubic-bezier(.22,.8,.24,1)' } );
		} );
		return Promise.all( jobs );
	}

	/* ------------------------------------------------------------ picking */
	function pick( btn ) {
		if ( state.phase !== 'picking' || btn.classList.contains( 'is-taken' ) ) { return Promise.resolve(); }
		var sp = state.spread;
		var i = state.picks.length;
		if ( i >= sp.positions.length ) { return Promise.resolve(); }
		var deckIndex = state.order[ +btn.dataset.k ];
		var reversed = rand( 3 ) === 0;
		var pickRec = { card: DECK[ deckIndex ], reversed: reversed, position: sp.positions[ i ] };
		state.picks.push( pickRec );
		btn.classList.add( 'is-taken' );
		btn.disabled = true;
		var slot = board.querySelector( '.skt-slot[data-index="' + i + '"]' );
		var placed = cardEl( pickRec.card, reversed );
		placed.classList.add( 'skt-placed' );
		pickRec.el = placed;
		var from = btn.getBoundingClientRect();
		slot.appendChild( placed );
		slot.classList.add( 'is-filled' );
		var to = placed.getBoundingClientRect();
		var left = sp.positions.length - state.picks.length;
		say( state.picks.length + '. kart “' + sp.positions[ i ].label + '” pozisyonuna yerleşti.' + ( left ? ' ' + left + ' kart kaldı.' : ' Tüm kartlar yerinde.' ) );
		var fly = Promise.resolve();
		if ( ! reduced ) {
			var rot = sp.positions[ i ].rot ? 90 : 0;
			var sx = from.width / ( rot ? to.height : to.width );
			var dx = ( from.left + from.width / 2 ) - ( to.left + to.width / 2 );
			var dy = ( from.top + from.height / 2 ) - ( to.top + to.height / 2 );
			/* The cross slot is rotated 90°: map screen offsets into its frame. */
			var local = function ( x, y ) { return rot ? [ y, -x ] : [ x, y ]; };
			var p0 = local( dx, dy );
			var p1 = local( dx * 0.45, dy * 0.45 - 70 );
			fly = animate( placed, [
				{ transform: 'translate(' + p0[ 0 ] + 'px,' + p0[ 1 ] + 'px) rotate(' + ( -rot ) + 'deg) scale(' + sx + ')' },
				{ transform: 'translate(' + p1[ 0 ] + 'px,' + p1[ 1 ] + 'px) rotate(' + ( -rot * 0.4 + ( dx > 0 ? -8 : 8 ) ) + 'deg) scale(1.12)', offset: 0.55 },
				{ transform: 'none' }
			], { duration: 720, easing: 'cubic-bezier(.22,.8,.24,1)' } );
			animate( btn, [ { opacity: 1, transform: 'rotate(var(--r)) scale(1)' }, { opacity: 0, transform: 'rotate(var(--r)) scale(.6)' } ], { duration: 260, fill: 'forwards' } );
		}
		if ( ! left ) {
			state.phase = 'placed';
			btnAuto.hidden = true;
			fly.then( function () {
				/* The rest of the deck leaves the stage. */
				animate( deckEl, [ { opacity: 1, transform: 'none' }, { opacity: 0, transform: 'translateY(40px)' } ], { duration: 420, easing: 'cubic-bezier(.4,0,1,1)', fill: 'forwards' } ).then( function () { deckEl.hidden = true; } );
				btnReveal.hidden = false;
				btnReveal.focus( { preventScroll: true } );
				animate( btnReveal, [ { transform: 'scale(.9)', opacity: 0 }, { transform: 'scale(1)', opacity: 1 } ], { duration: 400, easing: 'cubic-bezier(.22,.8,.24,1)' } );
			} );
		}
		return fly;
	}

	function autoPick() {
		if ( state.phase !== 'picking' || state.busy ) { return; }
		state.busy = true;
		var free = Array.prototype.filter.call( deckEl.querySelectorAll( '.skt-fan-card' ), function ( b ) { return ! b.classList.contains( 'is-taken' ); } );
		var need = state.spread.positions.length - state.picks.length;
		var chosen = shuffled( free ).slice( 0, need );
		var chain = Promise.resolve();
		chosen.forEach( function ( b ) { chain = chain.then( function () { pick( b ); return wait( 230 ); } ); } );
		chain.then( function () { state.busy = false; } );
	}

	/* ------------------------------------------------------------ reveal */
	function reveal() {
		if ( state.phase !== 'placed' || state.busy ) { return; }
		state.busy = true;
		state.phase = 'revealing';
		btnReveal.hidden = true;
		say( 'Kartlar açılıyor.' );
		var chain = Promise.resolve();
		state.picks.forEach( function ( p, i ) {
			chain = chain.then( function () {
				p.el.classList.add( 'is-open' );
				var inner = p.el.querySelector( '.skt-card__inner' );
				animate( inner, [
					{ transform: 'rotateY(0deg) translateZ(0) scale(1)' },
					{ transform: 'rotateY(95deg) translateZ(40px) scale(1.12)', offset: 0.5 },
					{ transform: 'rotateY(180deg) translateZ(0) scale(1)' }
				], { duration: 760, easing: 'cubic-bezier(.45,.05,.25,1)' } );
				animate( p.el, [ { filter: 'drop-shadow(0 0 0 rgba(205,184,231,0))' }, { filter: 'drop-shadow(0 0 18px rgba(205,184,231,.9))', offset: 0.55 }, { filter: 'drop-shadow(0 0 0 rgba(205,184,231,0))' } ], { duration: 900 } );
				return wait( i < 3 ? 320 : 200 );
			} );
		} );
		chain.then( function () { return wait( 600 ); } ).then( function () {
			return T.interpret( { spread: state.spread, cards: state.picks }, CFG.interpreter );
		} ).then( function ( result ) {
			renderReading( result );
			state.phase = 'done';
			state.busy = false;
			say( 'Açılımın hazır. Yorum aşağıda.' );
		} );
	}

	function renderReading( r ) {
		var html = '<header class="skt-reading__head"><p class="skt-eyebrow">Açılımın</p><h2 class="skt-reading__title">' + esc( state.spread.name ) + '</h2><p class="skt-reading__intro">' + esc( r.intro ) + '</p>' + ( r.note ? '<p class="skt-reading__note">' + esc( r.note ) + '</p>' : '' ) + '</header>';
		html += '<ol class="skt-items">' + r.items.map( function ( it ) {
			return '<li class="skt-item" data-in>' +
				'<div class="skt-item__thumb' + ( it.reversed ? ' is-reversed' : '' ) + '">' + face( it.card ) + '</div>' +
				'<div class="skt-item__body">' +
					'<p class="skt-item__pos"><span>' + it.index + '</span> ' + esc( it.label ) + '</p>' +
					'<h3 class="skt-item__card">' + esc( it.card.n ) + ' <span class="skt-item__orient">' + it.orientation + '</span></h3>' +
					'<p class="skt-item__role">' + esc( it.role ) + '</p>' +
					'<ul class="skt-chips">' + it.keywords.map( function ( k ) { return '<li>' + esc( k ) + '</li>'; } ).join( '' ) + '</ul>' +
					'<p class="skt-item__text">' + esc( it.text ) + '</p>' +
					'<p class="skt-item__q"><span>Kendine sor:</span> ' + esc( it.question ) + '</p>' +
				'</div></li>';
		} ).join( '' ) + '</ol>';
		if ( r.themes.length ) {
			html += '<section class="skt-themes" data-in><h3 class="skt-section-title">Bütünsel bakış</h3>' + r.themes.map( function ( t ) {
				return '<div class="skt-theme"><h4>' + esc( t.title ) + '</h4><p>' + esc( t.text ) + '</p></div>';
			} ).join( '' ) + '</section>';
		}
		html += '<section class="skt-questions" data-in><h3 class="skt-section-title">Üzerine düşünebileceğin sorular</h3><ul>' + r.questions.map( function ( q ) { return '<li>' + esc( q ) + '</li>'; } ).join( '' ) + '</ul></section>';
		html += '<p class="skt-reading__disclaimer" data-in>' + esc( CFG.disclaimer || '' ) + '</p>';
		html += '<div class="skt-reading__actions" data-in><button type="button" class="skt-btn skt-btn--primary" data-act="again">Yeni açılım</button></div>';
		readingEl.innerHTML = html;
		readingEl.hidden = false;
		readingEl.querySelector( '[data-act="again"]' ).addEventListener( 'click', function () {
			choose( state.spread );
		} );
		readingEl.scrollIntoView( { behavior: reduced ? 'auto' : 'smooth', block: 'start' } );
		Array.prototype.forEach.call( readingEl.querySelectorAll( '[data-in]' ), function ( el, i ) {
			animate( el, [ { opacity: 0, transform: 'translateY(28px)' }, { opacity: 1, transform: 'none' } ], { duration: 640, delay: 120 + i * 110, easing: 'cubic-bezier(.22,.8,.24,1)', fill: 'backwards' } );
		} );
	}

	/* ------------------------------------------------------------ wiring */
	btnShuffle.addEventListener( 'click', function () {
		if ( state.busy || ( state.phase !== 'ready' ) ) { return; }
		state.busy = true;
		btnShuffle.disabled = true;
		say( 'Kartlar karışıyor…' );
		shuffleAnim().then( function () { return spreadFan(); } ).then( function () {
			state.phase = 'picking';
			state.busy = false;
			btnShuffle.hidden = true;
			btnAuto.hidden = false;
			say( 'Kartları açtım. İçinden gelen ' + state.spread.positions.length + ' kartı seç.' + ( deckEl.classList.contains( 'is-scroll' ) ? ' Desteyi yana kaydırarak tüm kartları görebilirsin.' : '' ) );
		} );
	} );
	btnAuto.addEventListener( 'click', autoPick );
	btnReveal.addEventListener( 'click', reveal );

	buildChooser();
	app.hidden = false;
	rootEl.classList.add( 'is-ready' );
	window.addEventListener( 'resize', function () {
		if ( state.phase !== 'picking' ) { return; }
		var g = fanGeometry( state.order.length );
		deckEl.style.height = g.height + 'px';
		deckEl.classList.toggle( 'is-scroll', g.scroll );
		deckEl.style.setProperty( '--cw', g.cw + 'px' );
		deckEl.style.setProperty( '--ch', g.ch + 'px' );
		deckEl.querySelector( '.skt-fan' ).style.width = g.scroll ? g.inner + 'px' : '';
		Array.prototype.forEach.call( deckEl.querySelectorAll( '.skt-fan-card' ), function ( b ) {
			var p = g.pos[ +b.dataset.k ];
			b.style.left = p.x + 'px';
			b.style.top = p.y + 'px';
			b.style.setProperty( '--r', p.r.toFixed( 2 ) + 'deg' );
		} );
	} );
}() );
