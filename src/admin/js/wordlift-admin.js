/**
 * Define our global hooks.
 *
 * @since 3.0.0
 */

/**
 * Internal dependencies
 */
// eslint-disable-next-line no-unused-vars
import wordlift from './modules/wordlift';
import delay from './modules/delay';
import check from './modules/check';

(
	function( $ ) {
		/**
		 * Execute when the document is ready.
		 *
		 * @since 3.1.0
		 */
		$( function() {
			// The Entity Types Taxonomy is exclusive, one cannot choose more
			// than a type. Therefore from the PHP code we provide a Walker
			// that changes checkboxes into radios. However the quickedit on the
			// client side is applied only to checkboxes, so we override the
			// function here to apply the selection also to radios.

			// Do not hook, if we're not on a page with the inlineEditPost.
			if ( 'undefined' === typeof inlineEditPost || null === inlineEditPost ) {
				return;
			}

			// Create a reference to the original function.
			const fnEdit = inlineEditPost.edit;

			// Override the edit function.
			inlineEditPost.edit = function( id ) {
				// Call the original function.
				fnEdit.apply( this, arguments );

				// Get the id (this is a copy of what happens in the original
				// edit function).
				if ( typeof(
						id
					) === 'object' ) {
					id = this.getId( id );
				}

				// Get a reference to the row data (holding the post data) and
				// to the newly displayed inline edit row.
				const rowData = $( '#inline_' + id );
				const editRow = $( '#edit-' + id );

				// Select the terms for the taxonomy (this is a copy of the
				// original lines in the edit function but we're targeting
				// radios instead of checkboxes).
				$( '.post_category', rowData ).each( function() {
					const terms = $( this ).text();

					if ( terms ) {
						const taxname = $( this ).attr( 'id' ).replace( '_' + id, '' );
						// Target radios (instead of checkboxes).
						$( 'ul.' + taxname + '-checklist :radio', editRow )
							.val( terms.split( ',' ) );
					}
				} );
			};
		} );

		/**
		 * Handle the alternative labels, by providing an 'Add more titles'
		 * button and input texts where to add the labels.
		 *
		 * @since 3.2.0
		 */
		$( function() {
			// Add the delete button to the existing input texts.
			$( '.wl-alternative-label > .wl-delete-button' ).on( 'click', function( event ) {
				$( event.delegateTarget ).parent().remove();
			} );

			// Handle the click on the 'Add more titles' button and bind the
			// event of the (new) delete button.
			$( '#wl-add-alternative-labels-button' ).on( 'click', function( event ) {
				$( event.delegateTarget ).before( function() {
					const $element = $( $( '#wl-tmpl-alternative-label-input' ).html() );
					$element.children( '.wl-delete-button' ).on( 'click', function() {
						$element.remove();
					} );
					return $element;
				} );
			} );
		} );

		/**
		 * Check for duplicate title/labels via AJAX while the user is typing.
		 *
		 * @since 3.2.0
		 */
		$( function() {
			// return if we are not in the entity editor page (the *wlSettings*
			// json is only enqueued there) wlSettings.entityBeingEdited comes
			// from `wp_localize_script`, so '1' (true) or '' (false).
			if ( typeof wlSettings === 'undefined' || '1' !== wlSettings.entityBeingEdited ) {
				return;
			}

			// Print error message in page and hide it.
			const duplicatedEntityErrorDiv = $( '<div class="wl-notice notice wl-suggestion"' +
												' id="wl-same-title-error" ><p></p></div>' )
				.insertBefore( 'div.wrap [name=post]' )
				.hide();

			/**
			 * Check whether the specified title is already used by other
			 * entities.
			 *
			 * @since 3.10.0
			 */
			const callback = function() {
				// A jQuery reference to the element firing the event.
				const $this = $( this );

				// Delay execution of the check.
				delay( $this, check, $, wp.ajax, $this.val(), wlSettings.post_id,
					   wlSettings.l10n[ 'You already published an entity with the same name' ],
					   function( html ) {
						   // Set the error div content.
						   $( '#wl-same-title-error p' ).html( html );

						   // If the html code isn't empty then show the error.
						   if ( '' !== html ) {
							   duplicatedEntityErrorDiv.show();
						   } else {
							   // If the html code is empty, hide the error div.
							   duplicatedEntityErrorDiv.hide();
						   }
					   } );
			};

			// Whenever something happens in the entity title...
			$( '[name=post_title]' )
				.on( 'change paste keyup', callback )
				.each( callback );
		} );

		/**
		 * Draw dashboard if needed
		 *
		 * @since 3.4.0
		 */
		$( function() {
			// return if not needed
			if ( ! $( '#wl-dashboard-widget-inner-wrapper' ).length ) {
				return;
			}

			$.getJSON( ajaxurl + '?action=wordlift_get_stats', function( stats ) {
				// Get the triples, 0 by default if triples is not a number.
				const triples = isNaN( stats.triples ) ? 0 : stats.triples;

				// Calculate wikidata ratio
				// TODO percentage should be added via css
				const percent = triples * 100 / 947690143;
				stats.wikidata = percent.toFixed( 5 ) + '%';
				// Calculate annotated posts ratio
				const annotated = stats.annotated_posts * 100 / stats.posts;
				stats.annotatedPostsPercentage = annotated.toFixed( 1 );
				// Convert NaN to zero if needed
				//
				// See https://github.com/insideout10/wordlift-plugin/issues/269
				stats.annotatedPostsPercentage = stats.annotatedPostsPercentage || 0;
				// TODO percentage should be added via css
				stats.annotatedPostsPercentage = stats.annotatedPostsPercentage + '%';

				// Populate annotated posts pie chart
				$( '#wl-posts-pie-chart circle' ).css(
					'stroke-dasharray',
					(
						(
							stats.annotated_posts * 100
						) / stats.posts
					) + ' 100'
				);
				// Populate avarage entity ratings gauge chart
				$( '#wl-entities-gauge-chart .stat' ).css(
					'stroke-dasharray',
					(
						stats.rating / 2
					) + ' 100'
				);

				// TODO percentage should be added via css
				stats.rating = stats.rating + '%';
				// populate value placeholders
				for ( const property in stats ) {
					$( '#wl-dashboard-widget-' + property ).text( stats[ property ] );
				}

				// Finally show the widget
				$( '#wl-dashboard-widget-inner-wrapper' ).show();

				// Set the same height for stat graph wrappers
				// Links not working with css alternatives
				let minHeight = 0;
				$( '.wl-stat-graph-wrapper' ).each( function() {
					const stat = $( this );
					if ( stat.height() > minHeight ) {
						minHeight = stat.height();
					}
				} );

				$( '.wl-stat-graph-wrapper' ).css( 'min-height', minHeight );
			} );
		} );
	}
)( jQuery );
