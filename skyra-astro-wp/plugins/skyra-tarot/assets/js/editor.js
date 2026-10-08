/**
 * Block editor: a simple placeholder. The reading itself is interactive
 * and only runs on the front end.
 */
( function ( wp ) {
	if ( ! wp || ! wp.blocks || ! wp.element ) { return; }
	var el = wp.element.createElement;
	var useBlockProps = wp.blockEditor && wp.blockEditor.useBlockProps;
	wp.blocks.registerBlockType( 'skyra-tarot/reading', {
		edit: function () {
			var props = useBlockProps ? useBlockProps( { style: { padding: '24px', border: '1px dashed #887790', borderRadius: '16px', textAlign: 'center' } } ) : {};
			return el( 'div', props,
				el( 'strong', null, 'Skyra Tarot açılımı' ),
				el( 'p', { style: { margin: '8px 0 0' } }, 'Etkileşimli açılım sitede görüntülenir. Test modundayken yalnızca yöneticilere görünür.' )
			);
		},
		save: function () { return null; }
	} );
}( window.wp ) );
