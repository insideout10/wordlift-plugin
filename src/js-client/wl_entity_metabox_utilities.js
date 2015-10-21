/* 
 * This file contains utilities running on the entity editor page (e.g. AJAX autocomplete).
 * The main meeting points between the PHP backend and this script are:
 * <input 
 */

jQuery(document).ready(function($){
    
    // Remove button
    $('.wl-remove-input').click( function(event){
        
        var button = $(event.target);
        var inputWrapper = button.parent('.wl-input-wrapper');
        
        // Leave at least one <input>
        if( inputWrapper.parent('.wl-metabox').children('.wl-input-wrapper').size() > 1 ){
            // Delete the <div> containing the <input> tags and the "Remove" button
            inputWrapper.remove();
        } else {
            inputWrapper.find('input').val('');
        }
    });
    
    // Add button
    $('.wl-add-input').click( function(event){
        
        var button = $(event.target);
        var field = button.parent('.wl-metabox');
        var cardinality = field.data('cardinality');
        
        // Take previous, delete values and copy it at the end
        var alreadyPresentInputs = field.find('.wl-input-wrapper').size();
        var latestInput = field.find('.wl-input-wrapper').last();
        
        // Don't trasgress cardinality
        var canAddInput = (cardinality === 'INF') || (alreadyPresentInputs < cardinality);
        if( canAddInput ){

            // Build HTML of the new <input>
            var newInputDiv = latestInput.clone();    // .clone(true) would clone also the event callbacks, but messes up with autocomplete
            $(this).before( newInputDiv );
            
            // Impose default new values
            newInputDiv.find('input').val('');
            
            // Move focus to the empty new <input>
            newInputDiv.find('input:visible').focus();

            // If necessary, launch autocomplete on the new created <input>
            var newInputField = newInputDiv.find('.wl-autocomplete')[0];
            if( newInputField ){
                attachAutocomplete( null, newInputField );
            }
        }
    });
    
    
    var ajax_url = wlEntityMetaboxParams.ajax_url + '?action=' + wlEntityMetaboxParams.action;

    // Launch autocomplete on every <input> with class autocomplete
    $('.wl-autocomplete').each( attachAutocomplete );
    
    function attachAutocomplete( i, inputElement ){
        
        var metabox = $(inputElement).parents('.wl-metabox');
        var cardinality = $(metabox).data('cardinality');
        var expectedTypes = $(metabox).data('expected-types');
        if( expectedTypes ) {
            expectedTypes = expectedTypes.split(',');
        }
        console.log(inputElement, cardinality, expectedTypes);
        var hiddenInput = $(inputElement).siblings('input');
        var latestResults = {};  // hash used to keep a reference to the entities (title => uri, id, type, ecc.)
        
        // Callback for every change in the main <input>.
        // We use it to synch the value maintained in the hidden <input> (which goes to the server)
        // The visible <input> contains the label
        // while the hidden <input> contains:
        //    - already saved entity ID or
        //    - any Url or
        //    - new entity name.
        function synchInputValueWithAutocompleteResults(){
            var newValue = $(inputElement).val();
            
            // If the typed name is in the autocomplete list, put the id in the value field
            if( latestResults[newValue] ) {
                newValue = latestResults[newValue].id;
            }
            
            // Update hidden <input> value
            $(hiddenInput).val( newValue );
        }

        $(inputElement).keyup(function(s){
            // Keep <input>s in synch
            synchInputValueWithAutocompleteResults();
        });

        // Launch autocomplete
        $(inputElement).autocomplete({
            minLength: 2,   // Fire an AJAX call only when at least two chars are typed
            source: function( request, response ) {
                // AJAX call to search for entities starting with the typed letters
                $.getJSON( ajax_url + '&autocomplete' + '&title=' + $(inputElement).val(), function( searchResults ){
                    
                    // Populate suggestions
                    var suggestedTitles = [];
                    if( searchResults.results ){
                        for( var i=0; i<searchResults.results.length; i++ ) {
                            var entity = searchResults.results[i];
                            var entityName = entity.title;
                            var entityType = entity.schema_type_name;
                            
                            // Verify accepted schema.org type
                            if( !expectedTypes || ( entityType && expectedTypes.indexOf(entityType) !== -1 ) ) {

                                // Keep hash table up to date
                                latestResults[entityName] = searchResults.results[i];
                                // refresh suggestions list
                                suggestedTitles.push( entityName );
                            }
                        }
                        response( suggestedTitles );
                    }
                    
                    // In case the user already typed an entity name, we must match it
                    synchInputValueWithAutocompleteResults();
                });
            },
            // Callback that fires when a suggestion is approved.
            select: function(s){

                // Assign entity id to the hidden <input>
                var chosenEntity = $(inputElement).val();
                var chosenEntityObj = latestResults[chosenEntity];
                $(hiddenInput).val( chosenEntityObj.id );
            }
        });
    }
});

