(
	function( $, settings ) {
		'use strict';

		$( function() {

			// Check that we have a post id, otherwise exit.
			if ( typeof settings.postId === 'undefined' ) {
				return;
			}

			// Request the JSON-LD data.
			$.post( settings.ajaxUrl, {
				action: 'wl_jsonld',
				id: settings.postId
			}, function( data ) {
				console.log( data );

			} );

		} );

	}
)( jQuery, wlSettings );
