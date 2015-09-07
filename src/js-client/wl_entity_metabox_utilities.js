/* 
 * This file contains utilities running on the entity editor page (i.e. AJAX autocomplete).
 */

jQuery(document).ready(function($){
    
    var ajax_url = wlEntityMetaboxParams.ajax_url + '?action=' + wlEntityMetaboxParams.action;
    $('.wl-metabox').each( function( i, metabox ){
        
        var meta_cardinality = $(metabox).data('cardinality');
        console.log( $(metabox).children('.wl-autocomplete') );
        
        // If there are no value, put empty <input> tag
        $(metabox).find('.wl-autocomplete').each( attachAutocomplete );
        
        function attachAutocomplete( i, input ){
          
            $(input).autocomplete({
                source: function( request, response ) {
                    // AJAX call to search for entities starting with the typed letters
                    $.getJSON( ajax_url + '&autocomplete' + '&title=' + $(input).val(), function( searchResults ){
                        // Populate suggestions
                        var suggestedTitles = [];
                        if( searchResults.results ){
                            for( var i=0; i<searchResults.results.length; i++ ) {
                                suggestedTitles.push(searchResults.results[i].title);
                            }
                            response( suggestedTitles );
                        }
                    });
                },
                select: function(s){
                    
                    // TODO: refresh the attributes of the new <inputs>
                    
                    var newInputDiv = $(input).parent().clone();
                    newInputDiv.appendTo( metabox ); // check for cardinality, clone latest inputs, add index and append to page
                    newInputField = newInputDiv.find('.wl-autocomplete');
                    attachAutocomplete( 99999, newInputField );
                    newInputField.focus();
                }
            });
        };
    });
    
});

