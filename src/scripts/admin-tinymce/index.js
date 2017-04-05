/**
 * TinyMCE Plugins: manage annotations visibility.
 *
 * This plugin will fade out/in annotations according on the user actions on the
 * TinyMCE editor.
 *
 * @since 3.12.0
 */

/**
 * Internal dependencies
 */
import delay from '../common/delay';

// Set a reference to jQuery.
const $ = jQuery;

(
	function( tinymce ) {
		// Add our plugin.
		tinymce.PluginManager.add( 'wl_tinymce', ( editor, url ) => {
			// Listen for `KeyPress` events.
			//
			// See https://www.tinymce.com/docs/api/tinymce/tinymce.editor/#on
			editor.on( 'KeyDown', () => {
				// Set a reference to the container. We cannot do it before
				// since the Area Container isn't set yet.
				const $body = $( editor.getBody() );

				// Add the typing class.
				$body.addClass( 'wl-tinymce-typing' );

				// Delay a timer in 3 secs to remove the class.
				delay( $body, () => {
					$body.removeClass( 'wl-tinymce-typing' );
				}, 3000 );
			} );
//		} );
//
//		tinymce.PluginManager.add( 'wl_shortcode_previews', function( editor,
// url ) {
			editor.on( 'init', function() {
				console.log( { 'mce here': wp.mce, 'doc': editor.getDoc() } );
				const scriptId = editor.dom.uniqueId();

				const scriptElm = editor.dom.create( 'script', {
					id: scriptId,
					type: 'text/javascript',
					src: url + '/../../../../../wp-content/plugins/wordlift/admin/js/wordlift-admin-tinymce-views.bundle.js'
				} );

				editor.getDoc().getElementsByTagName( 'head' )[ 0 ].appendChild( scriptElm );
			} );
		} );
	}
)( window.tinymce );
