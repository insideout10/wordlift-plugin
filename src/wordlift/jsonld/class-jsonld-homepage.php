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
	 * @var \Wordlift_Entity_Post_To_Jsonld_Converter
	 */
	private $entity_post_to_jsonld_service;

	/**
	 * Jsonld_Homepage constructor.
	 *
	 * @param $relation_service \Wordlift_Relation_Service
	 * @param $entity_service \Wordlift_Entity_Service
	 * @param $entity_post_to_jsonld_service \Wordlift_Entity_Post_To_Jsonld_Converter
	 */
	public function __construct( $relation_service, $entity_service, $entity_post_to_jsonld_service ) {

		$this->relation_service = $relation_service;

		$this->entity_service = $entity_service;

		$this->entity_post_to_jsonld_service = $entity_post_to_jsonld_service;

		add_filter( 'wl_website_jsonld', array( $this, 'add_mentions_if_singular' ), 10, 2 );

	}


	private function entity_ids_to_jsonld_references( $entity_ids ) {
		$that = $this;

		return array_map( function ( $entity_id ) use ( $that ) {
			return array( '@id' => $that->entity_service->get_uri( $entity_id ) );
		}, $entity_ids );
	}

	private function entity_ids_to_jsonld( $entity_ids ) {
		$that = $this;

		return array_map( function ( $entity_id ) use ( $that ) {
			return $that->entity_post_to_jsonld_service->convert( $entity_id );
		}, $entity_ids );
	}


	public function add_mentions_if_singular( $jsonld, $post_id ) {

		$mentions                 = array();
		$referenced_entities_data = array();

		$jsonld['isPartOf'] = array(
			'@id' => get_home_url('', '#website')
		);

		if ( get_post_type( $post_id ) === 'entity' ) {
			$jsonld['@type'] = 'WebPage';

			$jsonld['mainEntity'] = array(
				'@id' => \Wordlift_Entity_Service::get_instance()->get_uri( $post_id )
		);

			return $jsonld;
		}


		if ( is_singular() ) {
			$jsonld['@type']          = 'WebPage';
			$entity_ids               = $this->relation_service->get_objects( $post_id, 'ids', null, 'publish' );
			$mentions                 = $this->entity_ids_to_jsonld_references( $entity_ids );
			$referenced_entities_data = $this->entity_ids_to_jsonld( $entity_ids );
		}

		if ( $mentions ) {
			$jsonld['mentions'] = $mentions;
		}

		if ( $referenced_entities_data ) {
			$jsonld = array( $jsonld );
			// Merge the homepage jsonld with annotated entities data.
			$jsonld = array_merge( $jsonld, $referenced_entities_data );
		}

		return $jsonld;
	}

}