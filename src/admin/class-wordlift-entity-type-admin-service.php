<?php
/**
 * Services: Entity Type Admin Service.
 *
 * Customization of the entity type display in the various admin pages.
 *
 * @since      3.15.0
 * @package    WordLift
 * @subpackage WordLift/admin
 */

/**
 * Define the {@link Wordlift_Entity_Type_Admin_Service} class.
 *
 * Handles the various display of entity type related info in the admin.
 *
 * @since      3.15.0
 * @package    WordLift
 * @subpackage WordLift/admin
 */
class Wordlift_Entity_Type_Admin_Service {

	/**
	 * Create a Wordlift_Entity_List_Service.
	 * Set up the relevant filters and actions.
	 *
	 * @since 3.15.0
	 *
	 */
	public function __construct() {

		add_action( 'admin_init', array( $this, 'hide_entity_type_metabox' ) );
		add_action( 'admin_init', array( $this, 'set_filters_to_hide_entity_type_from_lists' ) );
	}

	/**
	 * Hide the entity type metabox from non entity edit screens if user is not
	 * allowed to configure entity types.
	 *
	 * @since 3.15.0
	 */
	public function hide_entity_type_metabox() {

		if ( ! current_user_can( 'edit_wordlift_entity' ) ) {

			// loop over all the non entity post types which support entities and turn off the metabox.

			foreach ( Wordlift_Entity_Service::valid_entity_post_types() as $screen ) {
				if ( Wordlift_Entity_Service::TYPE_NAME !== $screen ) {
					remove_meta_box( Wordlift_Entity_Types_Taxonomy_Service::TAXONOMY_NAME . 'div', $screen, 'side' );
				}
			}
		}
	}

	/**
	 * Hide the entity type metabox from non entity list screens if user is not
	 * allowed to configure entity types.
	 *
	 * @since 3.a5.0
	 *
	 */
	function set_filters_to_hide_entity_type_from_lists() {

		if ( ! current_user_can( 'edit_wordlift_entity' ) ) {

			// loop over all the non entity post types which support entities and turn off the taxonomy column.

			foreach ( Wordlift_Entity_Service::valid_entity_post_types() as $post_type ) {
				if ( Wordlift_Entity_Service::TYPE_NAME !== $post_type ) {
					add_filter( 'manage_taxonomies_for_' . $post_type . '_columns', function ( $taxonomies ) {
						unset( $taxonomies[ Wordlift_Entity_Types_Taxonomy_Service::TAXONOMY_NAME ] );
						return $taxonomies;
					});
				}
			}
		}

	}
}
