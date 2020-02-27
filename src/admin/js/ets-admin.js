document.addEventListener( "DOMContentLoaded", function() {

   //event on use button
   document.querySelector( "#wl-use-summary" ).addEventListener( 'click', function (e) {
        e.preventDefault();
        var excerptSummary = document.getElementById( "wl-excerpt-summary" ).value;
        document.getElementById("excerpt").value = excerptSummary;
    });

   //Ajax call to refresh summarize API
    document.querySelector( "#wl-refresh-summary" ).addEventListener( 'click', function(e) {
        e.preventDefault();
        var postId = document.getElementById( "wl-refresh-summary" ).getAttribute( "data-post-id" );
        document.querySelector( "#wl-loader" ).style.display = "block";
        wp.ajax.post( "wl_refresh_excerpt_summary", { post_id: postId } )
        .done(function (data) {
            if (data) {
                var summarizeVal = data;
                document.getElementById( "wl-excerpt-summary" ).value = summarizeVal;
                document.querySelector( "#wl-loader" ).style.display = "none";
            }
        });
    });

    //add bell icon when summeries API responce
    if ( etsAdminAjaxUrl.summaryKey ) {
        var postEx = document.querySelector( "#postexcerpt" );
        var handleDiv = postEx.querySelector( ".handlediv" );
        var theChild = document.createElement( "span" );
        theChild.classList.add( "wl-summary-notify" );
        handleDiv.prepend(theChild);
        document.querySelector( "#postexcerpt .handlediv" ).style.width = "100px";
    }
});
