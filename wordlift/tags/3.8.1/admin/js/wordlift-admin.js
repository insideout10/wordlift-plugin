(function ($) {
    'use strict';

    /**
     * All of the code for your admin-specific JavaScript source
     * should reside in this file.
     *
     * Note that this assume you're going to use jQuery, so it prepares
     * the $ function reference to be used within the scope of this
     * function.
     *
     * From here, you're able to define handlers for when the DOM is
     * ready:
     *
     * $(function() {
	 *
	 * });
     *
     * Or when the window is loaded:
     *
     * $( window ).load(function() {
	 *
	 * });
     *
     * ...and so on.
     *
     * Remember that ideally, we should not attach any more than a single DOM-ready or window-load handler
     * for any particular page. Though other scripts in WordPress core, other plugins, and other themes may
     * be doing this, we should try to minimize doing that in our own work.
     */

    /**
     * Execute when the document is ready.
     *
     * @since 3.1.0
     */
    $(function () {

        // The Entity Types Taxonomy is exclusive, one cannot choose more than a type. Therefore from the PHP code
        // we provide a Walker that changes checkboxes into radios. However the quickedit on the client side is applied
        // only to checkboxes, so we override the function here to apply the selection also to radios.

        // Do not hook, if we're not on a page with the inlineEditPost.
        if ('undefined' === typeof inlineEditPost || null === inlineEditPost)
            return;

        var fnEdit = inlineEditPost.edit; // Create a reference to the original function.

        // Override the edit function.
        inlineEditPost.edit = function (id) {

            // Call the original function.
            fnEdit.apply(this, arguments);

            // Get the id (this is a copy of what happens in the original edit function).
            if (typeof(id) === 'object') {
                id = this.getId(id);
            }

            // Get a reference to the row data (holding the post data) and to the newly displayed inline edit row.
            var rowData = $('#inline_' + id),
                editRow = $('#edit-' + id);

            // Select the terms for the taxonomy (this is a copy of the original lines in the edit function but we're
            // targeting radios instead of checkboxes).
            $('.post_category', rowData).each(function () {
                var taxname,
                    term_ids = $(this).text();

                if (term_ids) {
                    taxname = $(this).attr('id').replace('_' + id, '');
                    // Target radios (instead of checkboxes).
                    $('ul.' + taxname + '-checklist :radio', editRow).val(term_ids.split(','));
                }
            });

        };

    });


    /**
     * Handle the alternative labels, by providing an 'Add more titles' button and input texts where to add the labels.
     *
     * @since 3.2.0
     */
    $(function () {

        // Add the delete button to the existing input texts.
        $('.wl-alternative-label > .wl-delete-button').on('click', function (event) {

            $(event.delegateTarget).parent().remove();

        });

        // Handle the click on the 'Add more titles' button and bind the event of the (new) delete button.
        $('#wl-add-alternative-labels-button').on('click', function (event) {

            $(event.delegateTarget).before(function () {
                var $element = $($('#wl-tmpl-alternative-label-input').html());
                $element.children('.wl-delete-button').on('click', function () {
                    $element.remove();
                });
                return $element;
            });

        });

    });

    /**
     * Check for duplicate title/labels via AJAX while the user is typing.
     *
     * @since 3.2.0
     */
    $(function () {

        // return if we are not in the entity editor page (the *wlEntityTitleLiveSearchParams* json is only enqueued there)
        if (typeof wlEntityTitleLiveSearchParams === 'undefined') {
            return;
        }

        // AJAX environment
        var ajax_url = wlEntityTitleLiveSearchParams.ajax_url + '?action=' + wlEntityTitleLiveSearchParams.action;
        var currentPostId = wlEntityTitleLiveSearchParams.post_id;

        // Print error message in page and hide it.
        var duplicatedEntityErrorDiv = $('<div class="wl-notice notice wl-suggestion" id="wl-same-title-error" ><p></p></div>')
            .insertBefore('div.wrap [name=post]')
            .hide();

        // Check duplicates at startup
        titleInputChecker($('[name=post_title]'));

        // Whenever something happens in the entity title...
        $('[name=post_title]').on('change paste keyup', function (event) {
            // ... check duplicated titles in the interested input
            titleInputChecker($(event.target));
        });

        function titleInputChecker(titleInput) {

            var thereAreDuplicates = false;

            // AJAX call to search for entities with the same title
            $.getJSON(ajax_url + '&title=' + titleInput.val(), function (response) {

                // Write an error notice with a link for every duplicated entity            
                if (response && response.results.length > 0) {

                    $('#wl-same-title-error p').html(function () {
                        var html = '';

                        for (var i = 0; i < response.results.length; i++) {

                            // No error if the entity ID given from the AJAX endpoint is the same as the entity we are editing
                            if (response.results[i].id !== currentPostId) {

                                thereAreDuplicates = true;

                                var title = response.results[i].title;
                                var edit_link = response.edit_link.replace('%d', response.results[i].id);

                                html += 'You already published an entity with the same name: ';
                                html += '<a target="_blank" href="' + edit_link + '">';
                                html += title;
                                html += '</a><br />';
                            }
                        }

                        return html;
                    });
                }

                if (thereAreDuplicates) {
                    // Notify user he is creating a duplicate.
                    duplicatedEntityErrorDiv.show();
                } else {
                    // Hide notice
                    duplicatedEntityErrorDiv.hide();
                }
            });
        }

    });

    /**
     * Draw dashboard if needed
     *
     * @since 3.4.0
     */
    $(function () {

        // return if not needed
        if (!$('#wl-dashboard-widget-inner-wrapper').length) {
            return;
        }

        $.getJSON(ajaxurl + '?action=wordlift_get_stats', function (stats) {

            // Get the triples, 0 by default if triples is not a number.
            var triples = isNaN(stats.triples) ? 0 : stats.triples;

            // Calculate wikidata ratio
            // TODO percentage should be added via css
            stats.wikidata = ( ( triples * 100 ) / 947690143 ).toFixed(5) + '%';
            // Calculate annotated posts ratio
            stats.annotated_posts_percentage = ( ( stats.annotated_posts * 100 ) / stats.posts ).toFixed(1);
            // Convert NaN to zero if needed
            // See https://github.com/insideout10/wordlift-plugin/issues/269
            stats.annotated_posts_percentage = +stats.annotated_posts_percentage || 0;
            // TODO percentage should be added via css
            stats.annotated_posts_percentage = stats.annotated_posts_percentage + '%';

            // Populate annotated posts pie chart
            $('#wl-posts-pie-chart circle').css(
                'stroke-dasharray',
                ( ( stats.annotated_posts * 100 ) / stats.posts ) + ' 100'
            );
            // Populate avarage entity ratings gauge chart
            $('#wl-entities-gauge-chart .stat').css(
                'stroke-dasharray',
                ( stats.rating / 2 ) + ' 100'
            );

            // TODO percentage should be added via css
            stats.rating = stats.rating + '%';
            // populate value placeholders
            for (var property in stats) {
                $('#wl-dashboard-widget-' + property).text(stats[property]);
            }

            // Finally show the widget
            $('#wl-dashboard-widget-inner-wrapper').show();

            // Set the same height for stat graph wrappers
            // Links not working with css alternatives
            var minHeight = 0;
            $('.wl-stat-graph-wrapper').each(function (index) {
                var stat = $(this);
                if (stat.height() > minHeight) {
                    minHeight = stat.height();
                }
            });

            $('.wl-stat-graph-wrapper').css('min-height', minHeight);

        });

    });

})(jQuery);
