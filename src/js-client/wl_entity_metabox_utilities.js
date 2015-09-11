/* 
 * This file contains utilities running on the entity editor page (i.e. AJAX autocomplete).
 * The main meeting points between the PHP backend and this script are:
 * <input 
 */

jQuery(document).ready(function($){
    
    var ajax_url = wlEntityMetaboxParams.ajax_url + '?action=' + wlEntityMetaboxParams.action;
    
    // Launch autocomplete on every <input> with class autocomplete
    $('.wl-autocomplete').each( attachAutocomplete );
    
    function attachAutocomplete( i, inputElement ){
        
        var metabox = $(inputElement).parents('.wl-metabox');
        var cardinality = $(metabox).data('cardinality');
        var expectedTypes = $(metabox).data('expected-types');
        var hiddenInput = $(inputElement).siblings('input');
        var latestResults = {};  // hash used to keep a reference to the entities (title => uri, id, type, ecc.)

        // Callback for every change in the main <input>.
        // We use it to synch the value maintained in the hidden <input> (which goes to the server)
        $(inputElement).keyup(function(s){
            var newValue = $(this).val();
            $(hiddenInput).val( newValue );
        });

        $(inputElement).autocomplete({
            source: function( request, response ) {
                // AJAX call to search for entities starting with the typed letters
                $.getJSON( ajax_url + '&autocomplete' + '&title=' + $(inputElement).val(), function( searchResults ){
                    // TODO: expected types???
                    // Populate suggestions
                    var suggestedTitles = [];
                    if( searchResults.results ){
                        for( var i=0; i<searchResults.results.length; i++ ) {
                            var entityName = searchResults.results[i].title;
                            // Keep hash table up to date
                            latestResults[entityName] = searchResults.results[i];
                            // refresh suggestions list
                            suggestedTitles.push( entityName );
                        }
                        response( suggestedTitles );
                    }
                });
            },
            // Callback that fires when a suggestion is taken.
            select: function(s){

                // Assign entity id to the hidden <input>
                var chosenEntity = $(inputElement).val();
                var chosenEntityObj = latestResults[chosenEntity];
                $(hiddenInput).val( chosenEntityObj.id );

                // Now we proceed to create a new <input> for eventual new values
                appendNewAutocomplete(metabox);
            }
        });
    }
    
    function appendNewAutocomplete(metabox){
        
        var cardinality = $(metabox).data('cardinality');
        var alreadyPresentInputs = $(metabox).find('.wl-autocomplete').size();
        var latestInput = $(metabox).find('.wl-autocomplete').last();
        var latestInputVal = $(latestInput).val();
                
        // Don't trasgress cardinality
        var canAddInput = (cardinality === 'n') || (alreadyPresentInputs < cardinality);
        
        if( canAddInput && latestInputVal !== '' ){

            // Build HTML of the new <input>
            var newInputDiv = $(latestInput).parent().clone();
            newInputDiv.appendTo( metabox );
            
            // Launch autocomplete on the new created <input>
            var newInputField = newInputDiv.find('.wl-autocomplete');
            attachAutocomplete( null, newInputField );
            newInputField.val('').focus();
        }
    }
});

