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

// Add our plugin.
tinymce.PluginManager.add( 'wl_tinymce', ( editor ) => {
	// Listen for `KeyPress` events.
	//
	// See https://www.tinymce.com/docs/api/tinymce/tinymce.editor/#on
	editor.on( 'KeyDown', () => {
		// Set a reference to the container. We cannot do it before since the
		// Area Container isn't set yet.
		const $body = $( editor.getBody() );

		// Add the typing class.
		$body.addClass( 'wl-tinymce-typing' );

		// Delay a timer in 3 secs to remove the class.
		delay( $body, () => {
			$body.removeClass( 'wl-tinymce-typing' );
		}, 3000 );
	} );
} );
