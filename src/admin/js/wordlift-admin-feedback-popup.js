/* 
 * This file contains utilities for uninstall feedback popup.
 */

jQuery( document ).ready( function ( $ ) {
	// Add uninstall listenter.
	var deactivationLink;

	$( '#the-list .active[data-plugin="wordlift/wordlift.php"] .deactivate a' ).click(function (e) {
		e.preventDefault();

		$( '.wl-modal-deactivation-feedback' ).addClass( 'visible' );

		deactivationLink = $(this).attr( 'href' );

	})

	$('.wl-reason').on('change', function () {
		var parent = $(this).parents('li');
		$( '.additional-info' ).removeClass( 'visible' );
		parent.find( '.additional-info' ).addClass( 'visible' );
	})

	$( '.wl-modal-button-deactivate' ).on( 'click', function (e) {
		e.preventDefault();

		// TODO: Finish the ajax call.
		// $.ajax({
		// 	url       : settings.ajaxUrl,
		// 	method    : 'POST',
		// 	data      : {
		// 		'action'			: 'wl_uninstall_feedback',
		// 		'reason_id'			: $('input[type="radio"][name="wl-reason"]:checked').val(),
		// 		// 'reason_info'		: userReason,
		// 		'bws_ajax_nonce'	: $('.wl_feedback_nonce').val()
		// 	},
		// 	beforeSend: function() {
		// 		$( '.wl-modal-button-deactivate' )
		// 			.addClass( 'disabled' )
		// 			.text( 'Processing' + '...' );
		// 	},
		// 	complete  : function( message ) {
		// 		window.location.href = deactivationLink;
		// 	}
		// });

	})

	// Close the popup by clicking outside of the body
	// or on "Cancel" button.
	$( '.wl-modal-deactivation-feedback, .wl-modal-button-close' ).on( 'click', function (e) {
		e.preventDefault();

		$( this ).removeClass( 'visible' );
	})

	// Prevent the popup from bubbling.
	$( '.wl-modal-body' ).on( 'click', function (e) {
		e.stopPropagation();
	})
} );