jQuery(document).ready(function ($) {
    //On click on refresh button then ajax call
    jQuery('#wl-refresh-summary').on('click', function(e) {
        e.preventDefault();
        let postId = jQuery(this).data('post-id');

        jQuery.ajax({
            type:'POST',
            dataType:'json',
            url: etsAdminAjaxUrl.wpadminajax,
            data:{ 'action': 'wl_refresh_excerpt_summary', 'post_id':postId },
            beforeSend: function(){
                // Show image container
                jQuery("#loader").show();
            },
            success:function(response){ 
            },
            complete:function(data){
                // Hide image container
                jQuery("#loader").hide();
           }
        });
    });
    
    //event on use button
    jQuery('#wl-use-summary').on('click', function(e){
        e.preventDefault();
        let excerpt_summary = jQuery('#excerpt_summary').val();
        jQuery('#excerpt').val(excerpt_summary);
    });

    //add bell icon when summeries API responce
    if (etsAdminAjaxUrl.summaryKey) {
        jQuery('#postexcerpt').find('.handlediv')   .prepend('<span id="wl-summary-notify"></span>');
        jQuery('#postexcerpt .handlediv').css('width', '100px');
    }
});