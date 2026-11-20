/**
 * Consent bar and preferences. Optional scripts arrive as
 * <script type="text/plain" data-skyra-consent="analytics"> (see
 * class-consent.php) and only run after the visitor allows that category.
 * The choice is kept in localStorage ("skyra-consent"); no cookie is set.
 */
( function () {
	'use strict';

	var cfg = window.skyraConsentConfig || {};
	var KEY = 'skyra-consent';
	var VERSION = cfg.version || 1;
	var reduced = window.matchMedia && window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches;
	var started = {};
	var bar = null;
	var dialog = null;

	function gated( category ) {
		return document.querySelectorAll( 'script[type="text/plain"][data-skyra-consent' + ( category ? '="' + category + '"' : '' ) + ']' );
	}

	function read() {
		try {
			var v = JSON.parse( window.localStorage.getItem( KEY ) || 'null' );
			return v && v.v === VERSION ? v : null;
		} catch ( e ) { return null; }
	}

	function write( analytics ) {
		var state = { v: VERSION, analytics: !! analytics, at: new Date().toISOString() };
		try { window.localStorage.setItem( KEY, JSON.stringify( state ) ); } catch ( e ) {}
		return state;
	}

	/* Replace each gated tag with a real script, keeping document order. */
	function start( category ) {
		if ( started[ category ] ) { return; }
		started[ category ] = true;
		Array.prototype.forEach.call( gated( category ), function ( old ) {
			var s = document.createElement( 'script' );
			Array.prototype.forEach.call( old.attributes, function ( a ) {
				if ( a.name !== 'type' && a.name !== 'data-skyra-consent' ) { s.setAttribute( a.name, a.value ); }
			} );
			if ( old.getAttribute( 'src' ) ) {
				s.async = false;
			} else {
				s.text = old.text;
			}
			old.parentNode.replaceChild( s, old );
		} );
	}

	function apply( state, wasRunning ) {
		if ( state.analytics ) {
			start( 'analytics' );
		} else if ( wasRunning ) {
			// Scripts that already ran cannot be unloaded; a reload leaves them gated.
			window.location.reload();
		}
	}

	function decide( analytics ) {
		var before = !! started.analytics;
		var state = write( analytics );
		hideBar();
		closeDialog();
		apply( state, before && ! analytics );
	}

	function el( tag, attrs, html ) {
		var n = document.createElement( tag );
		Object.keys( attrs || {} ).forEach( function ( k ) { n.setAttribute( k, attrs[ k ] ); } );
		if ( html ) { n.innerHTML = html; }
		return n;
	}

	function links() {
		var out = [];
		if ( cfg.cookies ) { out.push( '<a href="' + cfg.cookies + '">Çerez Politikası</a>' ); }
		if ( cfg.privacy ) { out.push( '<a href="' + cfg.privacy + '">KVKK Aydınlatma Metni</a>' ); }
		return out.join( ' · ' );
	}

	function showBar() {
		if ( bar ) { return; }
		bar = el( 'section', { class: 'sk-consent', role: 'region', 'aria-label': 'Çerez tercihleri' },
			'<p class="sk-consent__title">Ölçüm için izin</p>' +
			'<p class="sk-consent__text">Sitenin çalışması için gereken ayarlar her zaman açıktır. İzin verirsen ziyaret istatistikleri ve sayfa hızı ölçümü için WordPress.com (Automattic) ölçüm araçları çalışır; bu araçlar IP adresini ve görüntülediğin sayfayı yurt dışındaki sunuculara iletir. İzin vermezsen site aynı şekilde çalışır.</p>' +
			'<p class="sk-consent__links">' + links() + '</p>' +
			'<div class="sk-consent__actions">' +
				'<button type="button" class="sk-consent__btn" data-consent="reject">Tümünü Reddet</button>' +
				'<button type="button" class="sk-consent__btn sk-consent__btn--text" data-consent="manage">Tercihleri Yönet</button>' +
				'<button type="button" class="sk-consent__btn" data-consent="accept">Tümünü Kabul Et</button>' +
			'</div>'
		);
		document.body.appendChild( bar );
		window.requestAnimationFrame( function () { bar.classList.add( 'is-in' ); } );
	}

	function hideBar() {
		if ( ! bar ) { return; }
		var b = bar;
		bar = null;
		b.classList.remove( 'is-in' );
		window.setTimeout( function () { b.remove(); }, reduced ? 0 : 280 );
	}

	function buildDialog() {
		dialog = el( 'dialog', { class: 'sk-consent-dialog', 'aria-labelledby': 'sk-consent-h' },
			'<form method="dialog" class="sk-consent-dialog__inner">' +
				'<h2 id="sk-consent-h" class="sk-consent-dialog__title">Çerez ve ölçüm tercihleri</h2>' +
				'<p class="sk-consent__text">Tercihini istediğin zaman sayfanın altındaki “Çerez tercihleri” bağlantısından değiştirebilirsin. Seçimin yalnızca bu tarayıcıda saklanır.</p>' +
				'<div class="sk-consent-cat">' +
					'<div class="sk-consent-cat__head"><span class="sk-consent-cat__name" id="sk-cc-1">Gerekli</span><span class="sk-consent-cat__always">Her zaman açık</span></div>' +
					'<p class="sk-consent-cat__desc">Tema tercihin, seçtiğin burç ve araçlar arası geçiş için tarayıcında tutulan kayıtlar. İstediğin işlevin çalışması için gerekir; ziyaretçiye çerez yazılmaz.</p>' +
				'</div>' +
				'<div class="sk-consent-cat">' +
					'<div class="sk-consent-cat__head"><label class="sk-consent-cat__name" for="sk-cc-analytics">Analitik ve performans</label>' +
					'<input type="checkbox" class="sk-consent-switch" id="sk-cc-analytics" role="switch"></div>' +
					'<p class="sk-consent-cat__desc">Jetpack İstatistik (ziyaret sayıları) ve WordPress.com performans ölçümü. Sağlayıcı: Automattic Inc. IP adresi, görüntülenen sayfa, yönlendiren adres ve tarayıcı bilgisi yurt dışındaki sunuculara iletilir.</p>' +
				'</div>' +
				'<p class="sk-consent__links">' + links() + '</p>' +
				'<div class="sk-consent__actions">' +
					'<button type="button" class="sk-consent__btn" data-consent="reject">Tümünü Reddet</button>' +
					'<button type="button" class="sk-consent__btn sk-consent__btn--text" data-consent="save">Seçimleri Kaydet</button>' +
					'<button type="button" class="sk-consent__btn" data-consent="accept">Tümünü Kabul Et</button>' +
				'</div>' +
			'</form>'
		);
		document.body.appendChild( dialog );
		dialog.addEventListener( 'click', function ( e ) {
			if ( e.target === dialog ) { closeDialog(); }
		} );
	}

	function openDialog() {
		if ( ! dialog ) { buildDialog(); }
		var state = read();
		dialog.querySelector( '#sk-cc-analytics' ).checked = !! ( state && state.analytics );
		if ( typeof dialog.showModal === 'function' ) {
			if ( ! dialog.open ) { dialog.showModal(); }
		} else {
			dialog.setAttribute( 'open', '' );
		}
		window.requestAnimationFrame( function () { dialog.classList.add( 'is-in' ); } );
	}

	function closeDialog() {
		if ( ! dialog || ! dialog.open ) { return; }
		dialog.classList.remove( 'is-in' );
		if ( typeof dialog.close === 'function' ) { dialog.close(); } else { dialog.removeAttribute( 'open' ); }
	}

	document.addEventListener( 'click', function ( e ) {
		var btn = e.target.closest( '[data-consent]' );
		if ( btn ) {
			var act = btn.getAttribute( 'data-consent' );
			if ( act === 'accept' ) { decide( true ); }
			if ( act === 'reject' ) { decide( false ); }
			if ( act === 'manage' ) { openDialog(); }
			if ( act === 'save' ) { decide( dialog.querySelector( '#sk-cc-analytics' ).checked ); }
			return;
		}
		var open = e.target.closest( '[data-skyra-consent-open], a[href$="#cerez-tercihleri"]' );
		if ( open ) {
			e.preventDefault();
			openDialog();
		}
	} );

	var state = read();
	if ( state ) {
		apply( state, false );
	} else if ( gated().length ) {
		showBar();
	}
	if ( window.location.hash === '#cerez-tercihleri' ) { openDialog(); }
}() );
