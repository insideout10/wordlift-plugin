/* 
 * This file contains utilities running on the entity editor page (i.e. AJAX autocomplete).
 * The main meeting points between the PHP backend and this script are:
 * <input 
 */

jQuery(document).ready(function($){
    
    var ajax_url = wlEntityMetaboxParams.ajax_url + '?action=' + wlEntityMetaboxParams.action;
    $('.wl-metabox').each( function( i, metabox ){
        
        var cardinality = $(metabox).data('cardinality');
        var expectedTypes = $(metabox).data('expected-types');
        
        // Launch autocomplete on every <input> with class autocomplete
        $(metabox).find('.wl-autocomplete').each( attachAutocomplete );
        
        function attachAutocomplete( inputIndex, inputElement ){
            
            var hiddenInput = $(inputElement).siblings('input');
            var latestResults = {};  // hash used to keep a reference to the entities (title => uri, id, type, ecc.)
            
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
                    var newInputIndex = inputIndex + 1;
                    
                    // Check for cardinality
                    if( cardinality === 'n' || newInputIndex <= cardinality ){
                        
                        // Build HTML of the new <input>
                        var newInputDiv = $(inputElement).parent().clone();
                        newInputDiv.appendTo( metabox ); // check for cardinality, clone latest inputs, add index and append to page
                        var newInputField = newInputDiv.find('.wl-autocomplete');

                        // Launch autocomplete on the new created <input>
                        attachAutocomplete( newInputIndex, newInputField );
                        newInputField.val('').focus();
                    }
                }
            });
        };
    });
    
});

