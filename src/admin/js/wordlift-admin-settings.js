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

				/*
				 * Tab switching between selection of existing entity for a publisher
				 * and creation of a new one.
				 * Bind the event only if there are actually two tabs to switch between.
				 */

//				if ( 'yes' == $( '#wl-publisher-section' ).data(
//						'tabing-enabled' ) ) {
//					$( '.nav-tab' ).on( 'click', function( event ) { //
//						Switch
//						the
//						tab
//						indicators.$( '.nav-tab' ).removeClass(
// 'nav-tab-active' ); $( this ).addClass( 'nav-tab-active' );  // Switch //
// panels. // var panel = // $( this ).data( 'panel' ); $(
// '#wl-publisher-section' ).attr( 'class', panel + '-active' ); /* * Set the
// current panel indicator for the server to know * which was the last active
// tab. */ $( '#wl-setting-panel' ).val( panel ); event.preventDefault(); } );
// /* * Handle switch between personal and company * new publisher type. Hide
// and show the logo selection * based on the current selection of the radio
// button. */ $( '#wl-publisher-type input' ).on( 'click', function( event ) {
// if ( $( this ).val() == 'person' ) { $( '#wl-publisher-logo' ).hide(); }
// else { $( '#wl-publisher-logo' ).show(); } } ); /** * Format the data
// related to the entity into HTML to be * used in the select2 UI. * * There
// can be two sources of data, the HTML itself, in * which case the data
// parameter indicates its DOM element, * or an AJAX response in which case the
// data is in the * data parameter itself * * @since 3.11 * * @param data An
// object that have the following properties * id - The post id of the entity *
//             text - The title of the entity *             element - The DOM
// option element which is *     being processed thumburl - If from AJAX, the
// url of *     the entity's thumburl type - The type of the entity *
// company/personal * * @return string The HTML to be used to display the
// option */ function htmlForElement( data ) { var thumburl, type; if (
// data.type === undefined ) { thumburl = $( data.element ).data( 'thumb' );
// type = $( data.element ).data( 'type' ); } else { thumburl = data.thumburl;
// type = data.type; } var thumb = ''; if ( '' == thumburl ) { thumb = '<span
// class="img-filler"></span>'; } else { thumb = '<img src="' + thumburl +
// '">'; } return $( '<span class = "wl-select2-type" > ' + type + ' < / span >
// ' + ' < span class = "wl-select2" > ' + thumb + data.text + '</span>' ) ; }
// /** * Add the thumbnail to the display entity in the dropdown * * Used as a
// callback to the select2 instance * * @return the query element to be
// inserted before the name *     of the entity in the dropdown. */ function
// formatEntity( data ) { if ( ! data.id ) { return data.text; } return
// htmlForElement( data ); }; function formatSelectedEntity( data, container )
// { return htmlForElement( data ); }  var select2_options = { templateResult:
// formatEntity, templateSelection: formatSelectedEntity, }  // If AJAX is not
// done, check if we should even bother with // a search box.  if ( ! $(
// '#wl-select-entity-panel select' ).data( 'ajax--url' ) ) {  // turn off
// search if we know that it is a small number // of results if ( $(
// '#wl-select-entity-panel select' ).data( 'nosearch' ) ) {
// select2_options.minimumResultsForSearch = 'Infinity'; } } else { // Set
// delay to start the ajax. select2_options.minimumInputLength = 3; /* * Due to
// the way select2 combines parameters to the URL * it is saner to use POST
// over get. */ select2_options.ajax = { type: 'POST', dataType: 'json', data:
// function( params ) { return { q: params.term, // search term action:
// 'wl_possible_publisher', // Ajax // action. }; }, // processResults:
// function( data, params ) { // Parse the results into the format expected //
// by Select2.return { results: data, pagination : { more: false, } } ; },
// templateResult: formatEntity, templateSelection: formatEntity, } }  $(
// '#wl-select-entity-panel select' ). select2( select2_options ); }

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
						$( '#wl-publisher-logo-id' ).val( attachment.id );

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
