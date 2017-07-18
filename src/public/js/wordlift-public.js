(
	function( $, settings ) {
		'use strict';

		$( function() {
			// Check that we have a post id or it's homepage, otherwise exit.
			if (
			    typeof( settings.postId ) === 'undefined' &&
			    typeof( settings.isHome ) === 'undefined'
			) {
				return;
			}

			var requestData = {
				action: 'wl_jsonld',
			};

			// Check that we have a post id, and add it to the requestData.
			if ( typeof( settings.postId ) !== 'undefined' ) {
				requestData.id = settings.postId;
			}

			// Check that we have param that indicates we are on homepage, and add it to the requestData.
			if ( typeof( settings.isHome ) !== 'undefined' ) {
				requestData.homepage = true;
			}

			// Request the JSON-LD data.
			$.post( settings.ajaxUrl, requestData, function( data ) {
				// Append the data in the page head.
				$('head').append( '<script type="application/ld+json">'+JSON.stringify(data)+'</s' + 'cript>' );

			} );

		} );

	}
)( jQuery, wlSettings );
