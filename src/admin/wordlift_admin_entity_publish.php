<?php 

/**
 * Receive notifications when an entity is created and set its default *display as* value.
 *
 * @param int $post_id The entity post ID.
 */
function wl_admin_set_entity_display_as_default( $post_id ) {

    wl_set_entity_display_as( $post_id, wl_configuration_get_entity_display_as() );
}
add_action( 'wl_save_entity', 'wl_admin_set_entity_display_as_default', 10, 1 );
