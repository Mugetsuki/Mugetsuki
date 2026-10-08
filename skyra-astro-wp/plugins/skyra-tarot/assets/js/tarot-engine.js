/**
 * Skyra Tarot — interpretation engine.
 *
 * Turns a reading (spread + drawn cards) into a structured, reflective
 * text built only from the pre-written card and position copy. No network,
 * no randomness beyond the draw itself.
 *
 * Extension point for later (e.g. an AI service): register another
 * interpreter with SkyraTarot.registerInterpreter( 'name', fn ) where fn
 * receives the same reading and returns (a promise of) the same result
 * shape. SkyraTarot.interpret() falls back to "local" on any failure.
 */
( function ( root ) {
	'use strict';

	var SUITS = {
		asa: { plural: 'Asalar', element: 'ateş', theme: 'istek, yaratıcılık ve harekete geçme' },
		kupa: { plural: 'Kupalar', element: 'su', theme: 'duygular, ilişkiler ve sezgi' },
		kilic: { plural: 'Kılıçlar', element: 'hava', theme: 'düşünce, iletişim ve kararlar' },
		tilsim: { plural: 'Tılsımlar', element: 'toprak', theme: 'beden, emek ve somut adımlar' }
	};
	var RANKS = {
		1: [ 'As', 'başlangıçlar ve yeni tohumlar' ],
		2: [ 'İkili', 'denge ve seçimler' ],
		3: [ 'Üçlü', 'büyüme ve iş birliği' ],
		4: [ 'Dörtlü', 'istikrar ve duraklama' ],
		5: [ 'Beşli', 'sarsıntı ve uyum arayışı' ],
		6: [ 'Altılı', 'uyum ve paylaşım' ],
		7: [ 'Yedili', 'değerlendirme ve sınanma' ],
		8: [ 'Sekizli', 'hareket ve ustalaşma' ],
		9: [ 'Dokuzlu', 'olgunlaşma ve son aşamalar' ],
		10: [ 'Onlu', 'bir döngünün tamamlanması' ]
	};
	/* Element pairs: same, supportive (fire–air, water–earth), contrasting. */
	function elementBond( a, b ) {
		if ( a === b ) { return 'aynı dili konuşuyor gibi; ortak bir ritim kolay kurulabilir, ama aynı kör noktaları da paylaşabilirsiniz'; }
		var pair = [ a, b ].sort().join( '-' );
		if ( pair === 'ateş-hava' || pair === 'su-toprak' ) { return 'birbirini besleyebilecek elementlerden geliyor; biri diğerinin eksik kalan yanını tamamlayabilir'; }
		if ( pair === 'ateş-su' || pair === 'hava-toprak' ) { return 'farklı ritimlerde akıyor; bu fark hem çekici hem zaman zaman yorucu olabilir'; }
		return 'farklı ama birbirini tamamlayabilecek ihtiyaçlara işaret ediyor; ortak bir dil bulmak emek isteyebilir';
	}

	function kw( item ) { return item.card.k[ 0 ]; }
	function nameOf( item ) { return item.card.n + ( item.reversed ? ' (ters)' : '' ); }
	function count( list, fn ) { return list.filter( fn ).length; }

	function local( reading ) {
		var spread = reading.spread;
		var items = reading.cards;
		var n = items.length;
		var themes = [];

		var out = {
			intro: 'Kartların yerinde. Onları birer ayna gibi düşünebilirsin: geleceği bildirmezler, ama üzerine düşünmen için semboller sunarlar.',
			note: spread.note || '',
			items: items.map( function ( it, i ) {
				return {
					index: i + 1,
					label: it.position.label,
					role: it.position.role,
					card: it.card,
					reversed: it.reversed,
					orientation: it.reversed ? 'Ters' : 'Düz',
					keywords: it.card.k,
					text: it.reversed ? it.card.rev : it.card.up,
					question: it.card.q
				};
			} ),
			themes: themes,
			questions: []
		};

		/* Major Arcana weight. */
		var majors = count( items, function ( it ) { return it.card.a === 'major'; } );
		if ( n === 1 ) {
			var only = items[ 0 ];
			if ( only.card.a === 'major' ) {
				themes.push( { title: 'Büyük Arkana', text: only.card.n + ' bir Büyük Arkana kartı. Gündelik bir ayrıntıdan çok, hayatındaki daha geniş bir temaya işaret ediyor olabilir.' } );
			} else {
				var s1 = SUITS[ only.card.s ];
				themes.push( { title: s1.plural + ' (' + s1.element + ')', text: 'Bu kart ' + s1.plural.toLowerCase() + ' ailesinden; ' + s1.theme + ' alanı bugün odağında olabilir.' } );
			}
		} else if ( majors / n >= 0.5 ) {
			themes.push( { title: 'Büyük temalar', text: 'Açılımda Büyük Arkana kartları ağırlıkta (' + majors + '/' + n + '). Bu, konunun gündelik ayrıntılardan çok daha geniş bir dönemeçle ilgili olabileceğini düşündürüyor.' } );
		} else if ( majors === 0 && n >= 3 ) {
			themes.push( { title: 'Gündelik akış', text: 'Açılımda hiç Büyük Arkana kartı yok. Konunun daha çok gündelik seçimlerle ve senin elindeki küçük adımlarla şekillendiğini düşünebilirsin.' } );
		}

		/* Suit balance. */
		if ( n >= 3 ) {
			var tally = { asa: 0, kupa: 0, kilic: 0, tilsim: 0 };
			items.forEach( function ( it ) { if ( it.card.s ) { tally[ it.card.s ]++; } } );
			var keys = Object.keys( tally );
			var top = Math.max.apply( null, keys.map( function ( k ) { return tally[ k ]; } ) );
			var leaders = keys.filter( function ( k ) { return tally[ k ] === top; } );
			if ( top >= 2 && leaders.length === 1 ) {
				var s = SUITS[ leaders[ 0 ] ];
				themes.push( { title: s.plural + ' öne çıkıyor', text: s.plural + ' (' + s.element + ' elementi) bu açılımda ' + top + ' kartla en güçlü aile. ' + s.theme.charAt( 0 ).toUpperCase() + s.theme.slice( 1 ) + ' konunun merkezinde olabilir.' } );
			}
			if ( n >= 7 ) {
				var missing = keys.filter( function ( k ) { return tally[ k ] === 0; } );
				if ( missing.length && missing.length < 4 ) {
					themes.push( { title: 'Arka planda kalanlar', text: missing.map( function ( k ) { return SUITS[ k ].plural; } ).join( ' ve ' ) + ' hiç çıkmadı. ' + missing.map( function ( k ) { return SUITS[ k ].theme; } ).join( '; ' ) + ' şu an arka planda kalmış olabilir; buna bilerek mi alan tanımadığını sorabilirsin.' } );
				}
			}
		}

		/* Reversed cards. */
		var rev = count( items, function ( it ) { return it.reversed; } );
		if ( n >= 3 && rev / n >= 0.5 ) {
			themes.push( { title: 'İçe dönen enerji', text: 'Kartların ' + rev + ' tanesi ters geldi. Bu, enerjinin içe döndüğü, bazı konuların ertelendiği ya da yeniden değerlendirilmeyi beklediği bir döneme işaret ediyor olabilir.' } );
		} else if ( n >= 3 && rev === 0 ) {
			themes.push( { title: 'Açık temalar', text: 'Hiç ters kart yok; temalar açıkça görünür ve dolaysız biçimde kendini ifade ediyor olabilir.' } );
		}

		/* Court cards. */
		var courts = count( items, function ( it ) { return it.card.a === 'minor' && it.card.r > 10; } );
		if ( courts >= 2 ) {
			themes.push( { title: 'İnsanlar ve roller', text: 'Açılımda ' + courts + ' saray kartı var. Saray kartları çoğu zaman çevrendeki insanları, üstlendiğin rolleri ya da kendi kişiliğinin farklı yüzlerini temsil eder.' } );
		}

		/* Repeated numbers. */
		var byRank = {};
		items.forEach( function ( it ) { if ( it.card.a === 'minor' && it.card.r <= 10 ) { byRank[ it.card.r ] = ( byRank[ it.card.r ] || 0 ) + 1; } } );
		Object.keys( byRank ).forEach( function ( r ) {
			if ( byRank[ r ] >= 2 ) {
				themes.push( { title: 'Tekrar eden sayı', text: ( [ '', '', 'İki', 'Üç', 'Dört' ][ byRank[ r ] ] || byRank[ r ] ) + ' ' + RANKS[ r ][ 0 ] + ' çıktı: ' + RANKS[ r ][ 1 ] + ' teması belirginleşiyor.' } );
			}
		} );

		/* Spread-specific arcs. */
		var it = items;
		if ( spread.id === 'uc' ) {
			themes.unshift( { title: 'Akış', text: 'Kökler’deki “' + kw( it[ 0 ] ) + '” temasından Şimdi’deki “' + kw( it[ 1 ] ) + '” vurgusuna uzanan bir hat görünüyor. Olası yön kartı “' + kw( it[ 2 ] ) + '” temasını öne çıkarıyor; bu bir sonuç değil, bugünkü eğilimin sana düşündürdüğü bir yön.' } );
		}
		if ( spread.id === 'kelt' ) {
			themes.unshift(
				{ title: 'Merkez', text: 'Merkezde ' + nameOf( it[ 0 ] ) + ' ile ' + nameOf( it[ 1 ] ) + ' kesişiyor. “' + kw( it[ 0 ] ) + '” ile “' + kw( it[ 1 ] ) + '” arasındaki gerilim ya da denge konunun kalbinde olabilir.' },
				{ title: 'Derinlik ve hedef', text: 'Temel’deki ' + nameOf( it[ 2 ] ) + ' ile Bilinçli hedef’teki ' + nameOf( it[ 4 ] ) + ' yan yana: içinde taşıdığın ile ulaşmak istediğin arasındaki mesafe üzerine düşünebilirsin.' },
				{ title: 'Zaman ekseni', text: 'Yakın geçmişteki “' + kw( it[ 3 ] ) + '” teması, yerini yavaş yavaş “' + kw( it[ 5 ] ) + '” temasına bırakıyor olabilir.' },
				{ title: 'İç ve dış', text: 'Sen pozisyonundaki ' + nameOf( it[ 6 ] ) + ' tutumunu, Çevren pozisyonundaki ' + nameOf( it[ 7 ] ) + ' dış koşullara dair gözlemini anlatıyor. ' + ( it[ 6 ].card.s && it[ 6 ].card.s === it[ 7 ].card.s ? 'İkisi aynı aileden; içindeki ile çevrendeki arasında bir uyum olabilir.' : 'İkisi farklı yerlerden konuşuyor; içsel ihtiyacınla çevrenin beklentisi arasındaki farkı görmek yol gösterici olabilir.' ) },
				{ title: 'Beklenti ve yön', text: 'Umutlar ve kaygılar’daki ' + nameOf( it[ 8 ] ) + ' ile Olası yön’deki ' + nameOf( it[ 9 ] ) + ' birlikte okunduğunda, beklentilerinin bu temayı nasıl şekillendirdiğini sorabilirsin. Olası yön bir kesinlik değil; bugünkü eğilimin bir yansımasıdır.' }
			);
		}
		if ( spread.id === 'iliski' ) {
			var a = it[ 0 ].card.s ? SUITS[ it[ 0 ].card.s ].element : null;
			var b = it[ 2 ].card.s ? SUITS[ it[ 2 ].card.s ].element : null;
			var bond = a && b ? 'Sen ve diğer kişiye bakışın ' + elementBond( a, b ) + '.' : 'Sen ya da diğer kişiye bakışın pozisyonunda bir Büyük Arkana kartı var; bu ilişki şu an senin için daha büyük bir temayı temsil ediyor olabilir.';
			themes.unshift(
				{ title: 'İki bakış', text: bond },
				{ title: 'Bağ ve zorluk', text: 'Aranızdaki bağ “' + kw( it[ 1 ] ) + '” teması etrafında görünürken, zorlayan tema “' + kw( it[ 4 ] ) + '” üzerinde duruyor. Bu ikisini birlikte konuşmak ilişkiye iyi gelebilir.' },
				{ title: 'İhtiyaçlar', text: 'Senin ihtiyacın (“' + kw( it[ 3 ] ) + '”) ile onun ihtiyacına dair tahminin (“' + kw( it[ 5 ] ) + '”) yan yana. Tahminini onunla konuşarak sınamak, varsayımların yerine gerçek bir anlayış koyabilir.' },
				{ title: 'Olası yön', text: nameOf( it[ 6 ] ) + ', bugünkü eğilim sürerse ilişkide “' + kw( it[ 6 ] ) + '” temasının öne çıkabileceğini düşündürüyor. Bu bir kehanet değil; ilişkiyi birlikte nasıl şekillendirmek istediğinizi sormak için bir davet.' }
			);
		}

		/* Closing questions: up to three, distinct. */
		var picks = n <= 3 ? items : [ items[ 0 ], items[ Math.floor( n / 2 ) ], items[ n - 1 ] ];
		picks.forEach( function ( p ) {
			if ( out.questions.indexOf( p.card.q ) === -1 ) { out.questions.push( p.card.q ); }
		} );
		return out;
	}

	var interpreters = { local: local };

	var api = root.SkyraTarot = root.SkyraTarot || {};
	api.SUITS = SUITS;
	api.registerInterpreter = function ( name, fn ) { interpreters[ name ] = fn; };
	api.interpret = function ( reading, name ) {
		var fn = interpreters[ name || 'local' ] || local;
		return Promise.resolve()
			.then( function () { return fn( reading ); } )
			.catch( function () { return local( reading ); } );
	};
}( window ) );
