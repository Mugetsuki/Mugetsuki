/**
 * Block editor UI for Skyra's dynamic blocks: text/select settings in the
 * sidebar, live server-rendered preview in the canvas. No build step.
 */
( function ( wp ) {
	'use strict';
	var el = wp.element.createElement;
	var InspectorControls = wp.blockEditor.InspectorControls;
	var useBlockProps = wp.blockEditor.useBlockProps;
	var C = wp.components;
	var ServerSideRender = wp.serverSideRender;

	var LABELS = {
		eyebrow: 'Üst etiket',
		heading: 'Başlık',
		intro: 'Açıklama',
		link: 'Bağlantı',
		caption: 'Veri notunu göster',
		count: 'Olay sayısı',
		year: 'Yıl (0 = bu yıl)',
		category: 'Kategori kısa adı (boş = tümü)',
		sign: 'Burç (boş = sayfanın burcu)',
		a: 'Varsayılan birinci burç',
		b: 'Varsayılan ikinci burç'
	};
	var SIGNS = [ [ '', '—' ], [ 'koc', 'Koç' ], [ 'boga', 'Boğa' ], [ 'ikizler', 'İkizler' ], [ 'yengec', 'Yengeç' ], [ 'aslan', 'Aslan' ], [ 'basak', 'Başak' ], [ 'terazi', 'Terazi' ], [ 'akrep', 'Akrep' ], [ 'yay', 'Yay' ], [ 'oglak', 'Oğlak' ], [ 'kova', 'Kova' ], [ 'balik', 'Balık' ] ];
	var CHOICES = {
		variant: {
			'skyra/birth-chart': [ [ 'full', 'Tam araç' ], [ 'home', 'Ana sayfa (adım adım)' ] ],
			'skyra/newsletter': [ [ 'panel', 'Bölüm' ], [ 'compact', 'Kompakt' ] ],
			'skyra/social-links': [ [ 'community', 'Topluluk bölümü' ], [ 'inline', 'İkon satırı' ] ],
			'skyra/sign-reading': [ [ 'page', 'Sayfa (burç seçicili)' ], [ 'card', 'Kart' ] ]
		},
		layout: { 'skyra/daily-horoscopes': [ [ 'carousel', 'Kaydırmalı (mobil)' ], [ 'grid', 'Izgara' ] ] }
	};

	function options( list ) {
		return list.map( function ( o ) {
			return { value: o[ 0 ], label: o[ 1 ] };
		} );
	}

	function control( name, key, def, props ) {
		var value = props.attributes[ key ];
		var set = function ( v ) {
			var next = {};
			next[ key ] = v;
			props.setAttributes( next );
		};
		var label = LABELS[ key ] || key;
		if ( CHOICES[ key ] && CHOICES[ key ][ name ] ) {
			return el( C.SelectControl, { key: key, label: key === 'variant' ? 'Çeşit' : 'Düzen', value: value, options: options( CHOICES[ key ][ name ] ), onChange: set } );
		}
		if ( key === 'sign' || key === 'a' || key === 'b' ) {
			return el( C.SelectControl, { key: key, label: label, value: value, options: options( SIGNS ), onChange: set } );
		}
		if ( def.type === 'boolean' ) {
			return el( C.ToggleControl, { key: key, label: label, checked: !! value, onChange: set } );
		}
		if ( def.type === 'integer' ) {
			return el( C.TextControl, { key: key, label: label, type: 'number', value: value, onChange: function ( v ) { set( parseInt( v, 10 ) || 0 ); } } );
		}
		if ( key === 'intro' ) {
			return el( C.TextareaControl, { key: key, label: label, value: value, onChange: set } );
		}
		return el( C.TextControl, { key: key, label: label, value: value, onChange: set } );
	}

	// Server-registered block names; attributes come from block.json via the
	// server-side definitions WordPress bootstraps into the editor.
	( window.skyraBlocks || [] ).forEach( function ( name ) {
		wp.blocks.registerBlockType( name, {
			edit: function ( props ) {
				var attrs = ( wp.blocks.getBlockType( name ) || {} ).attributes || {};
				var fields = Object.keys( attrs ).filter( function ( k ) {
					return [ 'align', 'anchor', 'className', 'style', 'lock', 'metadata' ].indexOf( k ) === -1;
				} );
				return el(
					'div',
					useBlockProps(),
					fields.length ? el( InspectorControls, {}, el( C.PanelBody, { title: 'Ayarlar', initialOpen: true }, fields.map( function ( k ) { return control( name, k, attrs[ k ], props ); } ) ) ) : null,
					el( ServerSideRender, { block: name, attributes: props.attributes, httpMethod: 'POST' } )
				);
			},
			save: function () {
				return null;
			}
		} );
	} );
} )( window.wp );
