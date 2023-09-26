/* 
 * This file contains utilities running on the entity editor page (e.g. AJAX autocomplete).
 * The main meeting points between the PHP backend and this script are:
 * <input 
 */

jQuery( document ).ready( function ( $ ) {

	// Show Add button when already some links
	$( '#wl-input-container' ).children( '.wl-input-wrapper' ).size() > 0 ? $( '.wl-add-input--sameas' ).removeClass('hide') : null;

	// Remove button
	$( '.wl-remove-input' ).click( removeButton );

	function removeButton( event ) {
		event.preventDefault();
		var button = $( event.target );
		var inputWrapper = button.parent( '.wl-input-wrapper' );

		// Leave at least one <input>
		if ( inputWrapper.parent().children( '.wl-input-wrapper' ).size() > 1 ) {
			// Delete the <div> containing the <input> tags and the "Remove" button
			inputWrapper.remove();
		} else {
			if ( $(this).is('.wl-remove-input--sameas') ) {
				inputWrapper.remove();
				$( '.wl-add-input--sameas' ).addClass('hide');
			} else {
				inputWrapper.find( 'input' ).val( '' );
			}
		}
	}

	// Add button
	$( '.wl-add-input' ).click( addButton );

	function addButton( event ) {
		event.preventDefault();
		var button = $( event.target );
		var field = button.parent( '.wl-field' );
		var cardinality = field.data( 'cardinality' );

		// Take previous, delete values and copy it at the end
		var alreadyPresentInputs = field.find( '.wl-input-wrapper' ).size();
		var latestInput = field.find( '.wl-input-wrapper:not(.wl-input-wrapper-readonly)' ).last();

		// Don't trasgress cardinality
		var canAddInput = (
			cardinality === 'INF'
		) || (
			alreadyPresentInputs < cardinality
		);
		if ( canAddInput ) {

			var isAutocomplete = (
				latestInput.find( '.wl-autocomplete' ).size() > 0
			);

			// Build HTML of the new <input>
			var newInputDiv = latestInput.clone( ! isAutocomplete );
			// .clone(true) clones also the event callbacks, but messes up with the autocomplete. See below**

			// Insert cloned element in page
			if ( $( this ).is( '.wl-add-input--sameas, .wl-add-input--link' ) ) {
				$( '#wl-input-container' ).append( newInputDiv );
				$( '.wl-add-input--sameas' ).removeClass( 'hide' )
			} else {
				$( this ).before( newInputDiv );
			}

			// Impose default new values
			newInputDiv.find( 'input' ).val( '' );

			newInputDiv.find( '.wl-input-notice' ).text( '' );

			// Move focus to the empty new <input>
			newInputDiv.find( 'input:visible' ).focus();

			// If necessary, launch autocomplete on the new created <input>
			if ( isAutocomplete ) {
				var newInputField = newInputDiv.find( '.wl-autocomplete' )[0];

				// **Since we could not use .clone(true) with the autocomplete, we attach the events manually.
				newInputDiv.find( '.wl-remove-input' ).click( removeButton );
				attachAutocomplete( null, newInputField );
			}
		}
	}

	var ajax_url = wlEntityMetaboxParams.ajax_url + '?action=' + wlEntityMetaboxParams.action;

	// Launch autocomplete on every <input> with class autocomplete
	$( '.wl-autocomplete' ).each( attachAutocomplete );

	function attachAutocomplete( i, inputElement ) {

		var metabox = $( inputElement ).parents( '.wl-field' );
		var cardinality = $( metabox ).data( 'cardinality' );
		var expectedTypes = $( metabox ).data( 'expected-types' );
		if ( expectedTypes ) {
			expectedTypes = expectedTypes.split( ',' );
		}

		var hiddenInput = $( inputElement ).siblings( 'input' );
		var latestResults = {};  // hash used to keep a reference to the entities (title => uri, id, type, ecc.)

		// Callback for every change in the main <input>.
		// We use it to synch the value maintained in the hidden <input> (which goes to the server)
		// The visible <input> contains the label
		// while the hidden <input> contains:
		//    - already saved entity ID or
		//    - any Url or
		//    - new entity name.
		function synchInputValueWithAutocompleteResults() {
			var newValue = $( inputElement ).val();
			var noticeText = '';

			// If the typed name is in the autocomplete list, put the id in the value field
			if ( latestResults[newValue] ) {
				newValue = latestResults[newValue].id;
			} else {
				if ( newValue !== '' ) {
					// If we are creating a new entity, notify the user
					noticeText = newValue + ' will be created.';
				}
			}

			// Update the notice
			$( inputElement ).siblings( '.wl-input-notice' ).text( noticeText );

			// Update hidden <input> value
			$( hiddenInput ).val( newValue );
		}

		$( inputElement ).keyup( function ( s ) {
			// Keep <input>s in synch
			synchInputValueWithAutocompleteResults();
		} );

		// Launch autocomplete
		$( inputElement ).autocomplete( {
			minLength: 2,   // Fire an AJAX call only when at least two chars are typed
			source: function ( request, callback ) {
				// AJAX call to search for entities starting with the typed letters
				$.getJSON( ajax_url + '&autocomplete&alias&title=' + $( inputElement ).val(), function ( response ) {

					const searchResults = response.data;

					// Populate suggestions
					const suggestedTitles = [];

					if ( searchResults.results ) {
						for ( var i = 0; i < searchResults.results.length; i ++ ) {
							var entity = searchResults.results[i];
							var entityName = entity.title;
							var entityType = entity.schema_type_name;

							// Verify accepted schema.org type
							if ( ! expectedTypes || (
								entityType && expectedTypes.indexOf( entityType ) !== - 1
							) ) {

								// Keep hash table up to date
								latestResults[entityName] = searchResults.results[i];
								// refresh suggestions list
								suggestedTitles.push( entityName );
							}
						}
						callback( suggestedTitles );
					}

					// In case the user already typed an entity name, we must match it
					synchInputValueWithAutocompleteResults();
				} );
			},

			// Callback that fires when a suggestion is approved.
			close: function ( event, ui ) {
				synchInputValueWithAutocompleteResults();
			}
		} );
	}

} );

