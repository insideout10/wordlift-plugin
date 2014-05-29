<?php

/**
 * This file contains methods that intercept the *publish* box and add the display as option for entities.
 */


function wl_admin_post_publish_meta_box() {

    // Don't do anything if the post type isn't entity.
    if ( WL_ENTITY_TYPE_NAME !== get_post_type() ) {
        return;
    }

    // Get the current post.
    $post = get_post();
    $display_as = wl_get_entity_display_as( $post->ID );

?>
    <div class="misc-pub-section wl-pub-entity-display-as"><label for="wl_entity_display_as"><?php _e('Display as:', 'wordlift') ?></label>
<span id="wl-entity-display-as">
<?php
    _e( 'index' === $display_as ? 'Index' : 'Page', 'wordlift' );
?>
</span>

    <a href="#wl_entity_display_as" class="wl-edit-entity-display-as hide-if-no-js"><?php _e('Edit') ?></a>

    <div id="wl-entity-display-as-select" class="hide-if-js">
        <input type="hidden" name="hidden_wl_entity_display_as" id="hidden_wl_entity_display_as" value="<?php echo esc_attr( $display_as ); ?>" />
        <select name='wl_entity_display_as' id='wl_entity_display_as'>
            <option<?php selected( $display_as, 'index' ); ?> value='index'><?php _e('Index', 'wordlift') ?></option>
            <option<?php selected( $display_as, 'page' ); ?> value='page'><?php _e('Page', 'wordlift') ?></option>
        </select>
        <a href="#wl_entity_display_as" class="wl-save-entity-display-as hide-if-no-js button"><?php _e('OK'); ?></a>
        <a href="#wl_entity_display_as" class="wl-cancel-entity-display-as hide-if-no-js button-cancel"><?php _e('Cancel'); ?></a>
    </div>
</div>

<?php

}
add_action( 'post_submitbox_misc_actions', 'wl_admin_post_publish_meta_box', 10, 0 );

/**
 * Intercept the post updates and set the display as for entities.
 *
 * @uses wl_set_entity_display_as to set the entity display as parameter.
 *
 * @param int $post_id The post ID.
 */
function wl_admin_pre_post_update( $post_id ) {

    // Don't do anything if the post type isn't entity.
    if ( ! isset( $_POST['hidden_wl_entity_display_as'] ) || WL_ENTITY_TYPE_NAME !== get_post_type( $post_id ) ) {
        return;
    }

    wl_set_entity_display_as( $post_id, $_POST['hidden_wl_entity_display_as'] );

}
add_action( 'pre_post_update', 'wl_admin_pre_post_update', 10, 1 );