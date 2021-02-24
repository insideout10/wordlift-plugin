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

		add_filter( 'wl_website_jsonld_array', array( $this, 'add_webpage_and_mentions' ), 10, 2 );

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


	public function add_webpage_and_mentions( $jsonld_array, $post_id ) {

		$website_jsonld           = $jsonld_array[0];
		$mentions                 = array();
		$referenced_entities_data = array();

//		if ( get_post_type( $post_id ) === 'entity' ) {
//			$jsonld['@type'] = 'WebPage';
//
//			$jsonld['mainEntity'] = array(
//				'@id' => \Wordlift_Entity_Service::get_instance()->get_uri( $post_id )
//		);
//
//			return $jsonld;
//		}


		if ( is_singular() ) {
			$entity_ids               = $this->relation_service->get_objects( $post_id, 'ids', null, 'publish' );
			$mentions                 = $this->entity_ids_to_jsonld_references( $entity_ids );
			$referenced_entities_data = $this->entity_ids_to_jsonld( $entity_ids );
		}


		$webpage_schema = $this->get_webpage_schema( $website_jsonld, $post_id, $mentions );

		if ( $referenced_entities_data ) {
			// Merge the homepage jsonld with annotated entities data.
			$jsonld_array = array_merge( $jsonld_array, array_merge( array( $webpage_schema ), $referenced_entities_data ) );
		}

		return $jsonld_array;
	}


	/**
	 * @param $website_schema array
	 * @param $page_id int
	 *
	 * @return array
	 */
	public function get_webpage_schema( $website_schema, $page_id, $mentions ) {

		$webpage = array(
			'@context' => 'http://schema.org',
			'@type'    => 'WebPage',

			'@id'             => get_home_url( '', '#webpage' ),
			'name'            => get_the_title( $page_id ),
			'description'     => get_post_field( 'post_content', $page_id ),
			'isPartOf'        => array(
				'@id' => $website_schema['@id']
			),
			'potentialAction' => array(
				'@type'  => 'ReadAction',
				'target' => array( get_home_url() )
			)
		);

		if ( $mentions ) {
			$webpage['mentions'] = $mentions;
		}

		return $webpage;

	}


}