<?php
/**
 * Adapters: Entity Type Adapter.
 *
 * An adapter to route hooks to the {@link Wordlift_Entity_Type_Service}. Among
 * these hooks, when a post/page is saved and the entity type isn't set, then
 * the `http://schema.org/Article` class is assigned automatically.
 *
 * @since      3.15.0
 * @package    Wordlift
 * @subpackage Wordlift/includes
 */

/**
 * Define the {@link Wordlift_Entity_Type_Adapter} class.
 *
 * @since      3.15.0
 * @package    Wordlift
 * @subpackage Wordlift/includes
 */
class Wordlift_Entity_Type_Adapter {

	/**
	 * The {@link Wordlift_Entity_Type_Service} instance.
	 *
	 * @since  3.15.0
	 * @access private
	 * @var \Wordlift_Entity_Type_Service $entity_type_service The {@link Wordlift_Entity_Type_Service} instance.
	 */
	private $entity_type_service;

	/**
	 * Create a {@link Wordlift_Entity_Type_Adapter} instance.
	 *
	 * @since 3.15.0
	 *
	 * @param \Wordlift_Entity_Type_Service $entity_type_service The {@link Wordlift_Entity_Type_Service} instance.
	 */
	public function __construct( $entity_type_service ) {

		$this->entity_type_service = $entity_type_service;

	}

	/**
	 * Hook to `wp_insert_post`.
	 *
	 * This function is called when the `wp_insert_post` action is run. The
	 * function checks whether an Entity Type term (from the Entity Types
	 * taxonomy) is already assigned and, if not, it calls {@link Wordlift_Entity_Type_Service}
	 * to set the default taxonomy term.
	 *
	 * @since 3.15.0
	 *
	 * @param int     $post_id The {@link WP_Post}'s id.
	 * @param WP_Post $post    The {@link WP_Post} instance.
	 * @param boolean $update  Whether it's an update.
	 */
	public function insert_post( $post_id, $post, $update ) {

		// Bail out if this is an update.
		if ( $update ) {
			return;
		}

		// Bail out if the post isn't `page` or `post`.
		if ( ! in_array( $post->post_type, array( 'post', 'page' ) ) ) {
			return;
		}

		// Bail out if the post already has an entity type.
		if ( $this->entity_type_service->has_entity_type( $post_id ) ) {
			return;
		}

		// Finally set the default entity type.
		$this->entity_type_service->set( $post_id, 'http://schema.org/Article' );

	}

}
