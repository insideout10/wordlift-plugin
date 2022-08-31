<?php
/**
 * Services: Entity Type Admin Service.
 *
 * Customization of the entity type display in the various admin pages.
 *
 * @since      3.15.0
 * @package    Wordlift
 * @subpackage Wordlift/admin
 */

/**
 * Define the {@link Wordlift_Entity_Type_Admin_Service} class.
 *
 * Handles the various display of entity type related info in the admin.
 *
 * @since      3.15.0
 * @package    Wordlift
 * @subpackage Wordlift/admin
 */
class Wordlift_Entity_Type_Admin_Service {

	/**
	 * Create a {@link Wordlift_Entity_List_Service} instance.
	 *
	 * Set up the relevant filters and actions.
	 *
	 * @since 3.15.0
	 */
	public function __construct() {

		add_action( 'admin_init', array( $this, 'hide_entity_type_metabox' ) );
		add_action(
			'admin_init',
			array(
				$this,
				'set_filters_to_hide_entity_type_from_lists',
			)
		);
	}

	/**
	 * Hide the entity type metabox from non entity edit screens if user is not
	 * allowed to configure entity types.
	 *
	 * @since 3.15.0
	 */
	public function hide_entity_type_metabox() {

		// Bail out if the user can edit entities.
		if ( current_user_can( 'edit_wordlift_entity' ) ) {
			return;
		}

		// Loop over all the non entity post types which support entities and turn off the metabox.
		foreach ( $this->get_types_no_entity() as $type ) {
			remove_meta_box( Wordlift_Entity_Type_Taxonomy_Service::TAXONOMY_NAME . 'div', $type, 'side' );
		}

	}

	/**
	 * Hide the entity type metabox from non entity list screens if user is not
	 * allowed to configure entity types.
	 *
	 * @since 3.15.0
	 */
	public function set_filters_to_hide_entity_type_from_lists() {

		// Bail out if the user can edit entities.
		if ( current_user_can( 'edit_wordlift_entity' ) ) {
			return;
		}

		// Loop over all the non entity post types which support entities and turn off the taxonomy column.
		foreach ( $this->get_types_no_entity() as $type ) {
			add_filter(
				'manage_taxonomies_for_' . $type . '_columns',
				function ( $taxonomies ) {
					unset( $taxonomies[ Wordlift_Entity_Type_Taxonomy_Service::TAXONOMY_NAME ] );

					return $taxonomies;
				}
			);
		}

	}

	/**
	 * Get the types which are not the entity post type.
	 *
	 * @since 3.15.0
	 *
	 * @return array An array of types' names.
	 */
	private function get_types_no_entity() {

		return array_diff( Wordlift_Entity_Service::valid_entity_post_types(), (array) Wordlift_Entity_Service::TYPE_NAME );
	}

}
