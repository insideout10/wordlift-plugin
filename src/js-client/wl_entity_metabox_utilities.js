/* 
 * This file contains utilities running on the entity editor page (i.e. AJAX autocomplete).
 * The main meeting points between the PHP backend and this script are:
 * <input 
 */

jQuery(document).ready(function($){
    
    var ajax_url = wlEntityMetaboxParams.ajax_url + '?action=' + wlEntityMetaboxParams.action;
    
    // TODO: Add and remove buttons should be independent of the autocomplete
    
    // Launch autocomplete on every <input> with class autocomplete
    $('.wl-autocomplete').each( attachAutocomplete );
    
    function attachAutocomplete( i, inputElement ){
        
        var metabox = $(inputElement).parents('.wl-metabox');
        var cardinality = $(metabox).data('cardinality');
        var expectedTypes = $(metabox).data('expected-types');
        if( expectedTypes ) {
            expectedTypes = expectedTypes.split(',');
        }
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
        
        metabox.find('button').click(function(){
            // Create a new <input> for eventual new values
            appendNewAutocomplete(metabox);
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
                            
                            console.log(entityType, expectedTypes);
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
                
                // Move focus to the empty new <input>
                $(metabox).find('.wl-autocomplete').last().focus();
            }
        });
    
        function appendNewAutocomplete(){

            var alreadyPresentInputs = $(metabox).find('.wl-autocomplete').size();
            var latestInput = $(metabox).find('.wl-autocomplete').last();
            var latestInputVal = $(latestInput).val();

            // Don't trasgress cardinality
            var canAddInput = (cardinality === 'INF') || (alreadyPresentInputs < cardinality);

            if( canAddInput && latestInputVal !== '' ){

                // Build HTML of the new <input>
                var newInputDiv = $(latestInput).parent().clone();
                //newInputDiv.appendTo( metabox );
                metabox.children('button').before( newInputDiv );

                // Launch autocomplete on the new created <input>
                var newInputField = newInputDiv.find('.wl-autocomplete');
                attachAutocomplete( null, newInputField );
                // Impose default new values
                newInputField.val('');
                newInputField.siblings('input').val('');
            }
        }
    }
});

