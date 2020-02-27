<?php
/**
 * This file gathers functions to execute when the post (any type) is saved or updated.
 *
 * @since      3.0.0
 * @package    Wordlift
 * @subpackage Wordlift/admin
 */

/**
 * Receive events when a post (any type) status changes. We need to handle here the following cases:
 *  1. *published* to any other status:
 *      a) delete from the triple store.
 *      b) all the referenced entities that are not referenced by any other published post, are to be un-published.
 *  2. any other status to *published*: all referenced entities (only posts of type *entity*) must be published.
 *
 * Note that any status to *published* is handled by the save post routines.
 *
 * @see  http://codex.wordpress.org/Post_Status_Transitions about WordPress post transitions.
 *
 * @param string $new_status The new post status
 * @param string $old_status The old post status
 * @param array  $post       An array with the post data
 */
function wl_transition_post_status( $new_status, $old_status, $post ) {

	// wl_write_log( "wl_transition_post_status [ new status :: $new_status ][ old status :: $old_status ][ post ID :: $post->ID ]" );

	// transition from *published* to any other status: delete the post.
	if ( 'publish' === $old_status && 'publish' !== $new_status ) {
		// Delete the post from the triple store.
		rl_delete_post( $post );

		// Remove all relation instances for the current post from `wl_relation_instances`.
		wl_core_delete_relation_instances( $post->ID );
	}

	// when a post is published, then all the referenced entities must be published.
	if ( 'publish' !== $old_status && 'publish' === $new_status ) {
		foreach ( wl_core_get_related_entity_ids( $post->ID ) as $entity_id ) {
			wl_update_post_status( $entity_id, 'publish' );
		}
	}
}

// hook save events.
add_action( 'transition_post_status', 'wl_transition_post_status', 10, 3 );


/**
 * Delete the specified post from the triple store.
 *
 * @param array|int $post An array of post data
 */
function rl_delete_post( $post ) {

	$post_id = ( is_numeric( $post ) ? $post : $post->ID );

	// Remove the post.
	Wordlift_Linked_Data_Service::get_instance()->remove( $post_id );

}

/**
 * Update the status of a post.
 *
 * @param int    $post_id The post ID
 * @param string $status  The new status
 */
function wl_update_post_status( $post_id, $status ) {

	wl_write_log( "wl_update_post_status [ post ID :: $post_id ][ status :: $status ]" );

	global $wpdb;

	if ( ! $post = get_post( $post_id ) ) {
		return;
	}

	if ( $status === $post->post_status ) {
		return;
	}

	wl_write_log( "wl_update_post_status, old and new post status do not match [ post ID :: $post_id ][ new status :: $status ][ old status :: $post->post_status ]." );

	$wpdb->update( $wpdb->posts, array( 'post_status' => $status ), array( 'ID' => $post->ID ) );

	clean_post_cache( $post->ID );

	$old_status        = $post->post_status;
	$post->post_status = $status;

	wp_transition_post_status( $status, $old_status, $post );

	/** This action is documented in wp-includes/post.php */
	do_action( 'edit_post', $post->ID, $post );
	/** This action is documented in wp-includes/post.php */
	do_action( "save_post_{$post->post_type}", $post->ID, $post, true );
	/** This action is documented in wp-includes/post.php */
	do_action( 'wl_linked_data_save_post', $post->ID );
	/** This action is documented in wp-includes/post.php */
	do_action( 'wp_insert_post', $post->ID, $post, true );
}

/** This action to disable block editor*/
add_filter( 'use_block_editor_for_post', '__return_false', 10 );

/**
 * Update the excerpt summary and create wl_summaries-excerpt meta key to hold API responce.
 *
 * @param string $location The location of post after save
 * @param int $post_id post ID
 */
function update_post_excerpt_summary( $location, $post_id ) {
    $rest = get_excerpt_summary( $post_id );
    $res = json_decode( wp_remote_retrieve_body( $rest ), true );
    if ( $res ) {
        if ( array_key_exists( 'summary', $res ) ) {
            $summary = strip_tags( $res['summary'] );
            $postKey = update_post_meta( $post_id, "_wl-summaries-excerpt", $summary );
        }
    } 
    return $location;
}
add_filter( 'redirect_post_location', 'update_post_excerpt_summary', 10, 2 );

/**
 * Call summarize API.
 *
 * @param int $post_id The post ID
 *
 * @return JSON
 */
function get_excerpt_summary( $post_id ) {
    $post = get_post( $post_id );
    $post_content = $post->post_content;
    $summary_api_url = 'https://api.wordlift.io/summarize?min_length=60&ratio=0.005';
    $args =  array(
        'method'      => 'POST',
        'headers'     => array(
                "Authorization" => "Key ". WL_SUMMARIZE_API_KEY,
                "Content-Type"  => "text/plain"
            ),
        'timeout'     => 60,
        'redirection' => 5,
        'blocking'    => true,
        'httpversion' => '1.0',
        'body'        => $post_content
    );
    $response = wp_remote_post( $summary_api_url, $args ); 
    return $response;   
}

/**
 * Add the excerpt meta box
 *
 * @param  string $post_type
 * @return null
 */
function wl_add_excerpt_meta_box() {
    add_meta_box(
        'postexcerpt',
        __( 'Excerpt' ),
        'wl_add_field_excerpt_meta_boxes',
        'post',
        'normal',
        'default'
    ); 
}
add_action( 'admin_menu', 'wl_add_excerpt_meta_box' );
 
/**
 * Create fields in excerpt meta box.
 *
 * @param none
 */
function wl_add_field_excerpt_meta_boxes() {
    global $post;
    $post_id = $post->ID;
    $wlSummariesExcerpt = get_post_meta( $post_id, '_wl-summaries-excerpt', true );
    ?>
        <label class="screen-reader-text" for="excerpt"><?php echo esc_html__( 'Excerpt', 'wordlift' );?></label>
        <textarea rows="1" cols="40" name="excerpt" id="excerpt"><?php echo $post->post_excerpt;?></textarea>

        <p><?php echo esc_html__( 'Excerpts are optional hand-crafted summaries of your content that can be used in your theme.', 'wordlift' );?><a href="https://codex.wordpress.org/Excerpt"><?php echo esc_html__( 'Learn more about manual excerpts.', 'wordlift' );?></a></p>

        <label class="screen-reader-text" for="wl-excerpt-summary"><?php echo esc_html__( 'Excerpt', 'wordlift' );?></label>

        <textarea rows="1" cols="40" name="excerpt_summary" id="wl-excerpt-summary"><?php 
            if ( $wlSummariesExcerpt ) { 
                echo strip_tags( $wlSummariesExcerpt );
            } ?></textarea>

        <p><?php echo esc_html__( 'WordLift generated excerpt.', 'wordlift' );?></p>

        <div class="wl-use-ref">
            <button class="btn" id="wl-use-summary"><?php echo esc_html__( 'Use', 'wordlift' );?></button>
        
            <button class="btn" id="wl-refresh-summary" data-post-id="<?php echo $post_id;?>"><?php echo esc_html__( 'Refresh', 'wordlift' );?></button>
       
            <img id='wl-loader' src="<?php echo plugin_dir_url(__FILE__).'../images/loading.gif';?>">
        </div>    
    <?php
}

/**
 * Refresh call to summarize API.
 *
 * @param int    $post_id The post ID
 * @param string $status  The new status
 */
function wl_refresh_excerpt_summary() {
    $post_id = intval( $_POST['post_id'] );
    if ( isset( $post_id ) ) {
        $rest = get_excerpt_summary( $post_id );
        $res = json_decode( wp_remote_retrieve_body( $rest ), true );
        if ( $res ) {
            if ( array_key_exists( 'summary', $res ) ) {
                $summary = strip_tags( $res['summary'] );
                update_post_meta( $postid, "_wl-summaries-excerpt", $summary );
            }
        }
        wp_send_json_success( $summary, 1 );
    }
}
add_action( 'wp_ajax_wl_refresh_excerpt_summary', 'wl_refresh_excerpt_summary' );
add_action( 'wp_ajax_nopriv_wl_refresh_excerpt_summary', 'wl_refresh_excerpt_summary' );

/**
 * Add backend script.
 */
function wl_admin_backend_script_style()
{
    global $post;
    if ( $post ) {
        $wl_summaries_excerpt = get_post_meta( $post->ID, '_wl-summaries-excerpt', true );
    } else {
        $wl_summaries_excerpt = false;
    }

    $js_var = array(
            'wpadminajax'   => admin_url( 'admin-ajax.php' ),
            'summaryKey'    => $wl_summaries_excerpt
        );
    wp_register_script( 'ets_admin_script', plugin_dir_url(__FILE__).'js/ets-admin.js' ,array(), '1.0' );
    wp_enqueue_script( 'ets_admin_script' );
    wp_localize_script( 'ets_admin_script', 'etsAdminAjaxUrl', $js_var );
}
add_action( 'admin_enqueue_scripts', 'wl_admin_backend_script_style' );
