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

		};

		// Finally bind additional functions.
        bind();

	});

})(jQuery, _wlAdminSettings);
