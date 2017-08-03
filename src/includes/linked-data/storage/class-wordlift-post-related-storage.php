<?php
/**
 * Storage: Post Related Storage.
 *
 * Provides access to {@link WP_Post} properties.
 *
 * @since      3.15.0
 * @package    Wordlift
 * @subpackage Wordlift/includes/linked-data/storage
 */

/**
 * Define the {@link Wordlift_Post_Related_Storage} class.
 *
 * @since      3.15.0
 * @package    Wordlift
 * @subpackage Wordlift/includes/linked-data/storage
 */
class Wordlift_Post_Related_Storage extends Wordlift_Storage {

	/**
	 * @var Wordlift_Entity_Service
	 */
	private $entity_service;

	/**
	 * Wordlift_Post_Related_Storage constructor.
	 *
	 * @param \Wordlift_Entity_Service $entity_service
	 */
	public function __construct( $entity_service ) {

		$this->entity_service = $entity_service;

	}

	/**
	 * Get the property value.
	 *
	 * @since 3.15.0
	 *
	 * @param int $post_id The {@link WP_Post}'s id.
	 *
	 * @return array|string|null A single string, or an array of values or null
	 *                           if the property isn't recognized.
	 */
	public function get( $post_id ) {

		// get related entities.
		$related = wl_core_get_related_entity_ids( $post_id );

		// A reference to the entity service.
		$entity_service = $this->entity_service;

		// Map the related posts' ids to URIs.
		return array_map( function ( $item ) use ( $entity_service ) {
			return $entity_service->get_uri( $item );
		}, $related );
	}

}
