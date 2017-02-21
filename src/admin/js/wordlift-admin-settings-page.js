/**
 * UI interactions on the WordLift Settings page
 *
 * @since 3.11.0
 */
(
	function( $, settings ) {

		$( function( $ ) {

			/**
			 * Delay a function call by half a second.
			 *
			 * Any function can be delayed using `delay`. The timeout for the
			 * call is bound to the provided element. If another function call
			 * is delayed on the same element, any previous timeout is
			 * cancelled.
			 *
			 * This function is used to validate in real-time inputs when the
			 * user presses a key, but allowing the user to press more keys
			 * (hence the delay).
			 *
			 * @since 3.9.0
			 *
			 * @param {Object} $elem A jQuery element reference which will hold
			 *     the timeout reference.
			 * @param {Function} fn The function to call.
			 */
			var delay = function( $elem, fn ) {

				// Clear a validation timeout.
				clearTimeout( $elem.data( 'timeout' ) );

				// Validate the key, after a delay, so that another key is
				// pressed, this validation is cancelled.
				$elem.data( 'timeout', setTimeout( fn, 500 ) );

			};

			/**
			 * Bind additional functions to DOM elements:
			 * * `input.wl-key`s validation.
			 *
			 * @since 3.9.0
			 */
			var bind = function() {

				// Key validation: attach to all the input with a `wl-key`
				// class.
				$( '#wl-key' )
					.on( 'keyup', function() {

						// Get a jQuery reference to the object.
						var $this = $( this );

						// Remove any preexisting states, including the
						// `untouched` class which is set initially to prevent
						// displaying the `valid`/`invalid` indicator.
						$this.removeClass( 'untouched valid invalid' );

						// Delay execution of the validation.
						delay( $this, function() {

							// Post the validation request.
							wp.ajax.post( 'wl_validate_key', {
								'key': $this.val(),
							} ).done( function( data ) {

								// If the key is valid then set the process
								// class.
								if ( data && data.valid ) {
									$this.addClass( 'valid' );
								} else {
									$this.addClass( 'invalid' );
								}

							} );

						} );

					} );

			};

			// Add logo.
			$( '#wl-publisher-logo input' ).on( 'click', function() {

				// Create a WP media uploader.
				var uploader = wp.media( {
											 title: settings.l10n.logo_selection_title,
											 button: settings.l10n.logo_selection_button,
											 multiple: false,

											 // Tell the modal to show only
											 // images.
											 library: {
												 type: 'image',
											 },
										 } );

				// Catch `select` events on the uploader.
				uploader
					.on( 'select', function() {

						// Get the selected attachment.
						var attachment = uploader.state().get( 'selection' ).first().toJSON();

						// Set the selected image as the preview image
						$( '#wl-publisher-logo-preview' ).attr( 'src', attachment.url ).show();

						// Set the logo id.
						$( '#wl-publisher-thumbnail-id' ).val( attachment.id );

					} )
					.open();

			} );

			// Finally bind additional functions.
			bind();

			// Although in jQuery UI 1.12 it's possible to configure the css
			// classes, WP 4.2 uses jQuery 1.11.
			$( '.wl-tabs-element' ).each( function() {

				//
				const $this = $( this );

				// Create the tabs and set the default active element.
				$this.tabs( { active: $this.data( 'active' ) } );

			} );

			$( '.wl-select2-element' ).each( function() {

				//
				const $this = $( this );

				// Create the tabs and set the default active element.
				$this.select2(
					{
						width: '100%',
						data: $this.data( 'wl-select2-data' ),
						escapeMarkup: function( markup ) {
							return markup;
						},
						templateResult: _.template( $this.data( 'wl-select2-template-result' ) ),
						templateSelection: _.template( $this.data( 'wl-select2-template-selection' ) )
					}
				);

			} );

		} );

	}
)( jQuery, wlSettings );
