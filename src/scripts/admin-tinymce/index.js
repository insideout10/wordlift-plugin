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
import { $ } from '../common/wordpress';
import delay from '../common/delay';

const pluginURL = '/../../../../../wp-content/plugins/wordlift/';

// A handy function to load a script in the TinyMCE frame.
const script = ( editor, source ) => {
	const elementId = editor.dom.uniqueId();

	const element = editor.dom.create( 'script', {
		id: elementId,
		type: 'text/javascript',
		src: source
	} );

	editor.getDoc().getElementsByTagName( 'head' )[ 0 ].appendChild( element );
};

// Add our plugin.
tinymce.PluginManager.add( 'wl_tinymce', ( editor, url ) => {
	// When the editor is initialized add support for the Navigator
	// shortcode and configure the TinyMCE views.
	editor.on( 'init', function() {
		script( editor, url + pluginURL + 'public/js/wordlift-navigator.bundle.js' );
		script( editor, url + pluginURL + 'admin/js/wordlift-admin-tinymce-views.bundle.js' );
	} );

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
} );
