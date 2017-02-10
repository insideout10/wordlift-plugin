/**
 * UI interactions on the WordLift Settings page
 *
 * @since 3.11.0
 */
(function ($, settings) {

    $(function ($) {

		/**
         * Delay a function call by half a second.
         *
         * Any function can be delayed using `delay`. The timeout for the call is bound to the provided element. If another
         * function call is delayed on the same element, any previous timeout is cancelled.
         *
         * This function is used to validate in real-time inputs when the user presses a key, but allowing the user to
         * press more keys (hence the delay).
         *
         * @since 3.9.0
         *
         * @param {Object} $elem A jQuery element reference which will hold the timeout reference.
         * @param {Function} fn The function to call.
         */
        var delay = function ($elem, fn) {

            // Clear a validation timeout.
            clearTimeout($elem.data('timeout'));

            // Validate the key, after a delay, so that another key is pressed, this validation is cancelled.
            $elem.data('timeout', setTimeout(fn, 500));

        };

		/**
         * Bind additional functions to DOM elements:
         * * `input.wl-key`s validation.
         *
         * @since 3.9.0
         */
        var bind = function () {

            // Key validation: attach to all the input with a `wl-key` class.
            $('#wl-key')
                .on('keyup', function () {

                    // Get a jQuery reference to the object.
                    var $this = $(this);

                    // Remove any preexisting states, including the `untouched` class which is set initially to prevent
                    // displaying the `valid`/`invalid` indicator.
                    $this.removeClass('untouched valid invalid');

                    // Delay execution of the validation.
                    delay($this, function () {

                        // Post the validation request.
                        $.post(settings.ajaxUrl, {'action': settings.action, 'key': $this.val()},
                            function (data) {

                                // If the key is valid then set the process class.
                                if (data && data.valid)
                                    $this.addClass('valid');
                                else
                                    $this.addClass('invalid');

                            });

                    });

			});

			// tab switching between selection of existing entity for a publisher
			// and creation of a new one.
			// bind the event only if there are actually two tabs to switch between.

			if ( 'yes' == $( '#wl-publisher-section' ).data('tabing-enabled') ) {
				$('.nav-tab' )
					.on( 'click', function (event) {
						// switch the tab indicators.

						$('.nav-tab' ).removeClass( 'nav-tab-active' );
						$(this).addClass( 'nav-tab-active' );

						// switch panels.

						var panel = $(this).data( 'panel' );
						$( '#wl-publisher-section' ).attr( 'class', panel + '-active' );

						// set the current panel indicator for the server to know
						// which was the last active tab

						$('#wl-setting-panel').val(panel);
						event.preventDefault();
					});

					// handle switch between personal and company
					// new publisher type. Hide and show the logo selection
					// based on the current selection of the radio button.

					$('#wl-publisher-type input' )
						.on( 'click', function (event) {

							if ( $(this).val() == 'person' )
								$('#wl-publisher-logo').hide();
							else
								$('#wl-publisher-logo').show();
						});

				/**
				 * Add the thumbnail to the display entity in the dropdown
				 *
				 * Used as a callback to the select2 instance
				 *
				 * @return the query element to be inserted before the name of
				 *         the entity in the dropdown.
				 */
				function formatEntity (entity) {
				  if (!entity.id) { return entity.text; }


				  return $( '<span>mark + '+ entity.text + ' </span>' );
				};

				function htmlForElement(data) {
					var thumburl = $(data.element).data('thumb');
					var thumb = '';
					if ('' == thumburl) {
						thumb = '<span class="img-filler"></span>';
					} else {
						thumb = '<img src="' + thumburl + '">';
					}

					var type = $(data.element).data('type');
					return $( '<span class="wl-select2-type">' + type + '</span>' +
								'<span class="wl-select2">' + thumb + data.text  + ' </span>'
							);
				}

				/**
				 * Add the thumbnail to the display entity in the dropdown
				 *
				 * Used as a callback to the select2 instance
				 *
				 * @return the query element to be inserted before the name of
				 *         the entity in the dropdown.
				 */
				function formatEntity (data) {
					if (!data.id) { return data.text; }

					return htmlForElement( data );
				};

				function formatSelectedEntity(data,container) {
					return htmlForElement( data );
				}

				$('#wl-select-entity-panel select').select2( {
					templateResult: formatEntity,
					templateSelection: formatSelectedEntity,
				})
			}
		};

		// Finally bind additional functions.
        bind();

	});

})(jQuery, _wlAdminSettings);
