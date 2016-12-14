/**
 * Add support for `.wl-key` `input`s.
 *
 * @since 3.9.0
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
            $elem.data('timeout', setTimeout(fn), 500);

        };

        /**
         * Bind additional functions to DOM elements:
         * * `input.wl-key`s validation.
         *
         * @since 3.9.0
         */
        var bind = function () {

            // Key validation: attach to all the input with a `wl-key` class.
            $('input[data-wl-key]')
                .on('keyup', function () {

                    // Get a jQuery reference to the object.
                    var $this = $(this);

                    // Remove any preexisting states.
                    $this.removeClass('valid invalid');

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

            // Vocabulary path validation, only allow 'a-z', '0-9', '-' and '_'. Prevent non valid keys from being entered
            // and perform a validation while user types (we don't want '-' and '_' as last characters, we require at least
            // one valid character).
            $('input[data-wl-vocabulary]')
                .on('keypress', function (e) {

                    // If the pressed key is invalid, cancel it.
                    if (!/^[a-z0-9\-_]$/i.test(e.key)) return e.preventDefault();

                })
                .on('keyup', function () {

                    // Set a jQuery reference to the element.
                    var $this = $(this);

                    // Remove any preexisting states.
                    $this.removeClass('valid invalid');

                    // Delay the check for a valid path.
                    delay($this, function () {

                        // An empty value or a value starting/ending with an alphanumeric character.
                        if (0 === $this.val().length || /^[a-z0-9]+(?:[a-z0-9\-_]*[a-z0-9]+)?$/i.test($this.val()))
                            $this.addClass('valid');
                        else
                            $this.addClass('invalid');

                    });

                });


            // Check that a name has been provided.
            $('input[data-wl-name]')
                .on('keyup', function () {

                    // Set a jQuery reference to the element.
                    var $this = $(this);

                    // Remove any preexisting states.
                    $this.removeClass('valid invalid');

                    // Delay the check for a valid path.
                    delay($this, function () {

                        // An empty value or a value starting/ending with an alphanumeric character.
                        if (0 < $this.val().length)
                            $this.addClass('valid');
                        else
                            $this.addClass('invalid');

                    });

                });

            // Media upload.
            $('a[data-wl-add-logo]').on('click', function () {

                // Create a WP media uploader.
                var uploader = wp.media({
                    title: settings.media.title,
                    button: settings.media.button,
                    multiple: false
                });

                // Catch `select` events on the uploader.
                uploader
                    .on('select', function () {

                        var attachment = uploader.state().get('selection').first().toJSON();

                        $('#logo img')
                            .attr('src', attachment.url)
                            .data('id', attachment.id);

                        $('#logo').show();
                        $('#addlogo').hide();
                    })
                    .open();

            });

            // Catch form submits and cancel them if the name is not properly set.
            $('form').on('submit', function (e) {

                // Check that we have one valid name.
                if (1 !== $('input.valid[data-wl-name]').length) e.preventDefault();

            });

        };


        /**
         *
         * @param $container
         * @constructor
         */
        var Controller = function ($container) {

            // Create a stable reference to ourselves.
            var that = this;

            // Prepare the array of panes we manage.
            var panes = [];

            // Append ourselves to the container.
            this.$elem = $('<ul></ul>').appendTo($container);

            // Create a {@link PaneIndicator}.
            new PaneIndicator(this).$elem.insertBefore($container);

            // Get the viewport width.
            var width = that.$elem.width();

            /**
             * Add a new {@link Pane} to the list of managed {@link Pane}s.
             *
             * @since 3.9.0
             *
             * @param {string} html The pane html code.
             * @param {Function} validate The validation function for the {@link Pane}.
             */
            this.add = function (html, validate) {

                // Create the pane.
                var pane = new Pane(that, html, validate);

                // Append the pane within a list item.
                $('<li></li>').appendTo(that.$elem).append(pane.$elem);

                // Add the pane among the list of panes.
                panes.push(pane);

                // Trigger a pane added event with the added pane.
                that.$elem.trigger('paneAdd', pane);

            };

            /**
             * Goes to the pane at the specified index (zero-based).
             *
             * @since 3.9.0
             * @param {number} index The pane index (zero-based).
             */
            var goTo = function (index) {

                // If the index is in the range move the viewport to it.
                if (index >= 0 && index < panes.length) {
                    that.$elem.css('margin-left', -width * index);
                }

                // Trigger an pane change event with the index of the current pane.
                that.$elem.trigger('paneChange', index);

            };

            // Catch 'previous' events to move to the previous pane.
            this.$elem.on('previous', function (e, pane) {
                goTo(panes.indexOf(pane) - 1);
            });

            // Catch 'next' events to move to the next pane.
            this.$elem.on('next', function (e, pane) {
                goTo(panes.indexOf(pane) + 1);

            });

        };

        /**
         * Define a single {@link Pane}.
         *
         * @since 3.9.0
         *
         * @param {Controller} controller A {@link Pane}s {@link Controller}.
         * @param {string} html The raw html code.
         * @param {Function} validate The validation function for the {@link Pane}.
         * @constructor creates a {@link Pane} instance.
         */
        var Pane = function (controller, html, validate) {

            // A stable reference to ourselves.
            var that = this;

            // The pane element.
            this.$elem = $('<div></div>').append(html);

            // Hook to the next element and raise a 'next' event when clicked.
            $('.wl-next', that.$elem).on('click', function () {
                if (validate()) controller.$elem.trigger('next', that);
            });

            // Hook to the previous element raise a 'previous' event when clicked.
            $('.wl-previous', that.$elem).on('click', function () {
                if (validate()) controller.$elem.trigger('previous', that);
            });

        };

        /**
         * Add a {@link PaneIndicator}.
         *
         * @since 3.9.0
         *
         * @param {Controller} controller A {@link Controller} instance.
         * @constructor creates a {@link PaneIndicator}.
         */
        var PaneIndicator = function (controller) {

            // A stable reference to this.
            var that = this;

            // Create the jQuery instance.
            this.$elem = $('<ul class="wl-pane-indicator"></ul>');

            // Attach to the add and change elements.
            controller.$elem
                .on('paneAdd', function (e, pane) {
                    // Append a list item and add the 'active' class to the first element.
                    $('<li></li>')
                        .addClass(0 === $('li', that.$elem).length ? 'active' : '')
                        .appendTo(that.$elem);
                })
                .on('paneChange', function (e, index) {
                    $('li', that.$elem).removeClass('active');
                    $('li', that.$elem).eq(index).addClass('active');
                });
        };

        // Create a controller and attach it to the viewport.
        var controller = new Controller($('.viewport'));

        var alwaysValid = function () {
            return true;
        };

        var validations = [
            alwaysValid,
            function () {
                // The WL key is valid when we have one `.wl-key` marked as valid.
                return 1 === $('input.valid[data-wl-key]').length;
            },
            function () {
                // The vocabulary path is valid when the input is marked valid.
                return 1 === $('input.valid[data-wl-vocabulary]').length;
            },
            alwaysValid,
            // Validation for the last step happens with the form submit, see the beginning of this file.
            alwaysValid
        ];


        // Add the pages.
        for (var i = 0; i < 5; i++)
            controller.add($('#page-' + i).html(), validations[i]);

        // Finally bind additional functions.
        bind();

    });

})(jQuery, _wlAdminSetup);