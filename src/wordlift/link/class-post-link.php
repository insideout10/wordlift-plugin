<?php
/**
 * @since 3.32.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * This class is created to handle the post link.
 */

namespace Wordlift\Link;


use Wordlift_Entity_Service;
use Wordlift_Schema_Service;

class Post_Link extends Default_Link {

	/**
	 * @var \Wordlift_Entity_Service
	 */
	private $entity_service;
	/**
	 * @var \Wordlift_Entity_Uri_Service
	 */
	private $entity_uri_service;


	public function __construct() {
		parent::__construct();
		$this->entity_service     = Wordlift_Entity_Service::get_instance();
		$this->entity_uri_service = \Wordlift_Entity_Uri_Service::get_instance();
	}


	public function get_same_as_uris( $id ) {

		return array_merge(
			(array) $this->entity_service->get_uri( $id ),
			get_post_meta( $id, Wordlift_Schema_Service::FIELD_SAME_AS )
		);

	}

	public function get_id( $uri ) {
		$entity = $this->entity_uri_service->get_entity( $uri );
		if ( ! $entity ) {
			return false;
		}

		return $entity->ID;
	}

	public function get_synonyms( $id ) {
		// Get possible alternative entity_labels we can select from.
		$entity_labels = $this->entity_service->get_alternative_labels( $id );

		/*
		 * Since the original text might use an alternative entity_label than the
		 * Entity title, add the title itself which is not returned by the api.
		 */
		$entity_labels[] = get_the_title( $id );

		// Add some randomness to the entity_label selection.
		shuffle( $entity_labels );

		return $entity_labels;
	}

	public function get_permalink( $id ) {
		return get_permalink( $id );
	}
}
