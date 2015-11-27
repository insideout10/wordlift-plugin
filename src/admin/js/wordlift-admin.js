(function ( $ ) {
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
    $( function () {

        // The Entity Types Taxonomy is exclusive, one cannot choose more than a type. Therefore from the PHP code
        // we provide a Walker that changes checkboxes into radios. However the quickedit on the client side is applied
        // only to checkboxes, so we override the function here to apply the selection also to radios.

        // Do not hook, if we're not on a page with the inlineEditPost.
        if ( 'undefined' === typeof inlineEditPost || null === inlineEditPost )
            return;

        var fnEdit = inlineEditPost.edit; // Create a reference to the original function.

        // Override the edit function.
        inlineEditPost.edit = function ( id ) {

            // Call the original function.
            fnEdit.apply( this, arguments );

            // Get the id (this is a copy of what happens in the original edit function).
            if ( typeof(id) === 'object' ) {
                id = this.getId( id );
            }

            // Get a reference to the row data (holding the post data) and to the newly displayed inline edit row.
            var rowData = $( '#inline_' + id ),
                editRow = $( '#edit-' + id );

            // Select the terms for the taxonomy (this is a copy of the original lines in the edit function but we're
            // targeting radios instead of checkboxes).
            $( '.post_category', rowData ).each( function () {
                var taxname,
                    term_ids = $( this ).text();

                if ( term_ids ) {
                    taxname = $( this ).attr( 'id' ).replace( '_' + id, '' );
                    // Target radios (instead of checkboxes).
                    $( 'ul.' + taxname + '-checklist :radio', editRow ).val( term_ids.split( ',' ) );
                }
            } );

        };

    } );


    $( function () {

        $( '.wl-alternative-label > .wl-delete-button' ).on( 'click', function ( event ) {

            $( event.delegateTarget ).parent().remove();

        } );

        $( '#wl-add-alternative-labels-button' ).on( 'click', function ( event ) {

            $( event.delegateTarget ).before( function () {
                var $element = $( $( '#wl-tmpl-alternative-label-input' ).html() );
                $element.children( '.wl-delete-button' ).on( 'click', function () {
                    $element.remove();
                } );
                return $element;
            } );

        } );

    } );

})( jQuery );
