jQuery(function ($) {

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
        this.$elem = $('<ul></ul>')
            .appendTo($container);

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
            that.$elem.trigger('add', pane);

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
            that.$elem.trigger('change', index);

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
     * @param {object} $container A jQuery reference to the container element.
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
            .on('add', function (e, pane) {
                // Append a list item and add the 'active' class to the first element.
                $('<li></li>')
                    .addClass(0 === $('li', that.$elem).length ? 'active' : '')
                    .appendTo(that.$elem);
            })
            .on('change', function (e, index) {
                $('li', that.$elem).removeClass('active');
                $('li', that.$elem).eq(index).addClass('active');
            });
    };

    // Create a controller and attach it to the viewport.
    var controller = new Controller($('.viewport'));

    // Add the pages.
    for (var i = 1; i < 6; i++) {
        controller.add($('#page-' + i).html(), function () {
            return true;
        });
    }

});