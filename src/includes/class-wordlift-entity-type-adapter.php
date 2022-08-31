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
	 * @param \Wordlift_Entity_Type_Service $entity_type_service The {@link Wordlift_Entity_Type_Service} instance.
	 *
	 * @since 3.15.0
	 */
	public function __construct( $entity_type_service ) {

		$this->log = Wordlift_Log_Service::get_logger( 'Wordlift_Entity_Type_Adapter' );

		$this->entity_type_service = $entity_type_service;

		add_filter(
			'wl_default_entity_type_for_post_type',
			array(
				$this,
				'default_entity_type_callback',
			),
			10,
			2
		);

	}

	/**
	 * @param $entity_type string Entity type
	 * @param $post_type string The post type
	 *
	 * @return string The default entity type depending on the post type.
	 */
	public function default_entity_type_callback( $entity_type, $post_type ) {
		if ( 'product' === $post_type ) {
			return 'http://schema.org/Product';
		}

		return $entity_type;
	}

	/**
	 * Hook to `save_post`.
	 *
	 * This function is called when the `save_post` action is run. The
	 * function checks whether an Entity Type term (from the Entity Types
	 * taxonomy) is already assigned and, if not, it calls {@link Wordlift_Entity_Type_Service}
	 * to set the default taxonomy term.
	 *
	 * @param int     $post_id The {@link WP_Post}'s id.
	 * @param WP_Post $post The {@link WP_Post} instance.
	 *
	 * @since 3.15.0
	 */
	public function save_post( $post_id, $post ) {

		if ( ! Wordlift_Entity_Type_Service::is_valid_entity_post_type( $post->post_type ) ) {
			$this->log->debug( "Ignoring `{$post->post_type}` post type." );

			// Bail out if the post can not be an entity.
			return;
		}

		// Bail out if the post already has an entity type.
		if ( apply_filters( 'wl_entity_type_adapter__save_post__has_entity_type', $this->entity_type_service->has_entity_type( $post_id ) ) ) {
			$this->log->debug( "Post $post_id has already an entity type." );

			return;
		}

		$this->log->debug( "Setting `Article` entity type for post $post_id." );

		// Finally set the default entity type.

		// For entities use a Thing
		if ( 'entity' === $post->post_type ) {
			$this->entity_type_service->set( $post_id, 'http://schema.org/Thing' );
		} else {
			// Get the entity types.
			$entity_types = self::get_entity_types( $post->post_type );

			// Set the entity type.
			foreach ( $entity_types as $entity_type ) {
				$this->entity_type_service->set( $post_id, $entity_type, false );
			}
		}

	}

	/**
	 * Get the entity types for a post type.
	 *
	 * @param string $post_type The post type.
	 *
	 * @return array An array of entity types.
	 * @since 3.20.0
	 */
	public static function get_entity_types( $post_type ) {

		/**
		 * Get the default entity type.
		 *
		 * @param string $entity_type The preset entity type.
		 * @param string $post_type The post type.
		 *
		 * @since 3.20.0
		 */
		$default_entity_type = apply_filters( 'wl_default_entity_type_for_post_type', 'http://schema.org/Article', $post_type );

		/**
		 * Get the default entity types.
		 *
		 * Adding support to assign more than one entity type.
		 *
		 * @param array $entity_types The default entity types.
		 * @param string $post_type The post type.
		 *
		 * @since 3.20.0
		 *
		 * @see Wordlift_Mapping_Service
		 */
		$entity_types = apply_filters( 'wl_default_entity_types_for_post_type', array( $default_entity_type ), $post_type );

		return $entity_types;
	}

}
