<?php

namespace Wordlift\Jsonld;

/**
 * @since 3.27.9
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */
class Jsonld_Homepage {
	/**
	 * @var \Wordlift_Relation_Service
	 */
	private $relation_service;
	/**
	 * @var \Wordlift_Entity_Service
	 */
	private $entity_service;

	/**
	 * Jsonld_Homepage constructor.
	 *
	 * @param $relation_service \Wordlift_Relation_Service
	 * @param $entity_service \Wordlift_Entity_Service
	 */
	public function __construct( $relation_service, $entity_service ) {

		$this->relation_service = $relation_service;

		$this->entity_service = $entity_service;

		add_filter( 'wl_website_jsonld', array( $this, 'add_mentions_if_singular' ), 10, 2 );

	}


	private function entity_ids_to_jsonld( $entity_ids ) {
		return array_map( function ( $entity_id ) {
			return array( '@id' => $this->entity_service->get_uri( $entity_id ) );
		}, $entity_ids );
	}


	public function add_mentions_if_singular( $jsonld, $post_id ) {

		$mentions = array();
		if ( is_singular() && get_post_type( $post_id ) !== 'entity' ) {
			$entity_ids = $this->relation_service->get_objects( $post_id, 'ids', null, 'publish' );
			$mentions   = $this->entity_ids_to_jsonld( $entity_ids );
		}

		if ( $mentions ) {
			$jsonld['mentions'] = $mentions;
		}

		return $jsonld;
	}

}