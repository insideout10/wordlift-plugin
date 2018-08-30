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
	 * A {@link Wordlift_Log_Service} instance.
	 *
	 * @since  3.15.0
	 * @access private
	 * @var \Wordlift_Log_Service $log A {@link Wordlift_Log_Service} instance.
	 */
	private $log;

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

		$this->log = Wordlift_Log_Service::get_logger( 'Wordlift_Entity_Type_Adapter' );

		$this->entity_type_service = $entity_type_service;

	}

	/**
	 * Hook to `save_post`.
	 *
	 * This function is called when the `save_post` action is run. The
	 * function checks whether an Entity Type term (from the Entity Types
	 * taxonomy) is already assigned and, if not, it calls {@link Wordlift_Entity_Type_Service}
	 * to set the default taxonomy term.
	 *
	 * @since 3.15.0
	 *
	 * @param int     $post_id The {@link WP_Post}'s id.
	 * @param WP_Post $post The {@link WP_Post} instance.
	 * @param bool    $update Whether it's an update.
	 */
	public function save_post( $post_id, $post, $update ) {

		if ( ! Wordlift_Entity_Type_Service::is_valid_entity_post_type( $post->post_type ) ) {
			$this->log->debug( "Ignoring `{$post->post_type}` post type." );

			// Bail out if the post can not be an entity.
			return;
		}

		// Bail out if the post already has an entity type.
		if ( $this->entity_type_service->has_entity_type( $post_id ) ) {
			$this->log->debug( "Post $post_id has already an entity type." );

			return;
		}

		$this->log->debug( "Setting `Article` entity type for post $post_id." );

		// Finally set the default entity type.

		// For entities use a Thing
		if ( 'entity' === $post->post_type ) {
			$this->entity_type_service->set( $post_id, 'http://schema.org/Thing' );
		} else {
			$this->entity_type_service->set( $post_id, apply_filters( 'wl_default_entity_type_for_post_type', 'http://schema.org/Article', $post->post_type ) );
		}

	}

}
