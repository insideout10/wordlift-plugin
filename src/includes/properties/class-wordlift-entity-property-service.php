<?php

/**
 * Process references to other entities, local or remote, by returning a
 * {@link Wordlift_Property_Entity_Reference} with the URL of the referenced entity.
 *
 * @since 3.8.0
 */
class Wordlift_Entity_Property_Service extends Wordlift_Simple_Property_Service {
	/**
	 * @var \Wordlift_Entity_Service $entity_service
	 */
	private $entity_service;

	/**
	 * Wordlift_Entity_Property_Service constructor.
	 *
	 * @param \Wordlift_Entity_Service $entity_service
	 */
	public function __construct( $entity_service ) {

		$this->entity_service = $entity_service;

	}

	/**
	 * {@inheritdoc}
	 */
	public function get( $post_id, $meta_key ) {

		$entity_service = $this->entity_service;

		// Map each returned value to a Wordlift_Property_Entity_Reference.
		return array_map( function ( $item ) use ( $entity_service ) {
			return new Wordlift_Property_Entity_Reference( $entity_service->get_uri( $item ) );
		}, get_post_meta( $post_id, $meta_key ) );
	}

}
