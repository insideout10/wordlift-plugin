/* 
 * This file contains utilities for uninstall feedback popup.
 */

jQuery( document ).ready( function ( $ ) {
	// Add uninstall listenter.
	var deactivationLink;
	var reasonParent;
	var reason;

	$( '#the-list .active[data-plugin="wordlift/wordlift.php"] .deactivate a' ).click(function (e) {
		e.preventDefault();

		$( '.wl-modal-deactivation-feedback' ).addClass( 'visible' );

		deactivationLink = $(this).attr( 'href' );
	})

	$('.wl-reason').on('change', function () {
		reasonParent = $(this).parents('li');
		reason       = $(this).val();

		$( '.additional-info' ).removeClass( 'visible' );
		reasonParent.find( '.additional-info' ).addClass( 'visible' );
	})

	$( '.wl-modal-button-deactivate' ).on( 'click', function (e) {
		e.preventDefault();

		// Deactivate if the user hasn't selected any reason.
		if ( typeof( reason ) === 'undefined' ) {
			return window.location.href = deactivationLink;
		}

		$.ajax({
			url       : settings.ajaxUrl,
			method    : 'POST',
			data      : {
				'action'		  : 'wl_uninstall_feedback',
				'reason_id'		  : reason,
				'additional_info' : reasonParent.find( '.wl-reason-info' ).val(),
				'bws_ajax_nonce'  : $('.wl_feedback_nonce').val()
			},
			beforeSend: function() {
				// Add indicator that the request going to be made.
				$( '.wl-modal-button-deactivate' )
					.addClass( 'disabled' )
					.text( 'Processing ...' );
			},
			success   : function( response ) {
				// Redirect if we have success state.
				if ( response.success ) {
					return window.location.href = deactivationLink;
				}

				// Display the errors to the user.
				$( '.wl-errors' ).html( '<p>' + response.data + '</p>' );
			},
			error     : function ( xhr,status,error ) {
				if ( error.length ) {
					// Display the errors to the user.
					$( '.wl-errors' ).html( '<p>' + error + '</p>' );
				}
			},
			complete  : function () {
				// Return the button to initial state.
				$( '.wl-modal-button-deactivate' )
					.removeClass( 'disabled' )
					.text( 'Deactivate' );
			}
		});

	})

	// Close the popup by clicking outside of the body
	// or on "Cancel" button.
	$( '.wl-modal-deactivation-feedback, .wl-modal-button-close' ).on( 'click', function (e) {
		e.preventDefault();

		$( this ).removeClass( 'visible' );
	})

	// Prevent the popup from bubbling.
	$( '.wl-modal-body, .wl-modal-button-deactivate' ).on( 'click', function (e) {
		e.stopPropagation();
	})
} );