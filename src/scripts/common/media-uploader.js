/**
 * Media Uploader.
 *
 * Provide a function to use WordPress' Media Uploader by binding a button's
 * click event.
 *
 * @since 3.11.0
 */

// Set a reference to jQuery.
const $ = jQuery;

/**
 * Hook WordPress' Media Uploader.
 *
 * @since 3.11.0
 * @param {string} selector The button's selector.
 * @param {Object} options The Media Uploader's options.
 * @param {Function} callback A callback function which will receive the
 *     selected attachment.
 * @constructor
 */
const MediaUploader = ( selector, options, callback ) => {
	// Create a WP media uploader.
	const uploader = wp.media( options );

	// Catch `select` events on the uploader.
	uploader.on( 'select', function() {
		// Get the selected attachment.
		callback( uploader.state().get( 'selection' ).first().toJSON() );
	} );

	// Add logo.
	$( selector ).on( 'click', function() {
		uploader.open();
	} );
};

// Finally export the `MediaUploader`.
export default MediaUploader;
