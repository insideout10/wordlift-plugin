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

				// Append the data in the page head.
				$('head').append( '<script type="application/ld+json">'+JSON.stringify(data)+'</s' + 'cript>' );

			} );

		} );

	}
)( jQuery, wlSettings );
