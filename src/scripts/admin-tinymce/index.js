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
import { $, _wlSettings } from '../common/wordpress';
import delay from '../common/delay';

// A handy function to load a script in the TinyMCE frame.
const script = ( editor, source ) => {
	// Create the script element.
	const element = document.createElement( 'script' );
	element.type = 'text/javascript';
	element.src = source;
	// Ensure scripts are executed in order.
	//
	// See note [2]:
	// https://developer.mozilla.org/en-US/docs/Web/HTML/Element/script#Browser_compatibility
	// See also: https://bugs.chromium.org/p/chromium/issues/detail?id=594444
	element.async = false;

	// Finaally append the `script` element to the head of TinyMCE's `iframe`.
	editor.getDoc().getElementsByTagName( 'head' )[ 0 ].appendChild( element );
};

// Get a reference to the scripts to load into TinyMCE.
const sources = parent._wlAdminEditPage.tinymce.scripts;

// Add our plugin.
tinymce.PluginManager.add( 'wl_tinymce', ( editor ) => {
	// When the editor is initialized add support for the Navigator
	// shortcode and configure the TinyMCE views.
	editor.on( 'init', function() {
		for ( const source of sources ) {
			script( editor, source + '?version=' + _wlSettings.version );
		}
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
