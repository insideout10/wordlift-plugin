
(function ($) {
    
    $(document).ready( function() {
        
        var duplicatedEntityErrorDiv = $('<div class="error" id="wl-same-title-error" ></div>')
                .insertAfter('div.wrap h2:first')
                .hide();

        var titleInput = $('[name=post_title], [name=wl_alternative_label]');
        var latestTimeOut;
        var ajax_url = wlEntityDuplicatedTitlesLiveSearchParams.ajax_url + '?action=' + wlEntityDuplicatedTitlesLiveSearchParams.action;
        var currentPostId = wlEntityDuplicatedTitlesLiveSearchParams.post_id;

        // Check for duplicates at start up
        checkForDuplicateTitles();

        // And also when title is changed:
        titleInput.on('change paste keyup', function(){
            
            console.log(123);

            // Unbind previous timeOut
            clearTimeout( latestTimeOut );

            // Bind a new timeout and store its ID
            latestTimeOut = setTimeout( checkForDuplicateTitles, 500);
        });


        function checkForDuplicateTitles(){

            // AJAX call to search for entities with the same title
            $.getJSON( ajax_url + '&title=' + titleInput.val(), function(response){

                var thereAreDuplicates = false;

                // Write an error notice with a link for every duplicated entity            
                if( response && response.results.length > 0 ) {

                    duplicatedEntityErrorDiv.html( function(){
                        var html = '';

                        for( var i=0; i<response.results.length; i++ ){

                            // No error if the entity ID given from the AJAX endpoint is the same as the entity we are editing
                            if( response.results[i].id !== currentPostId ) {

                                thereAreDuplicates = true;

                                var title = response.results[i].title;;
                                var edit_link = response.edit_link.replace('%d', response.results[i].id);

                                html += 'Error: you already published an entity with the same name: ';
                                html += '<a target="_blank" href="' + edit_link + '">';
                                html += title;
                                html += '</a></br>';
                            }
                        }

                        return html;
                    });
                }

                if( thereAreDuplicates ) {
                    // Notify user he is creating a duplicate.
                    duplicatedEntityErrorDiv.show();
                } else {
                    // Hide notice
                    duplicatedEntityErrorDiv.hide();
                }
            });
        }
    });
})(jQuery);