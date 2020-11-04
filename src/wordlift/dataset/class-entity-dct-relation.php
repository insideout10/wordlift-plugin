<?php

namespace Wordlift\Dataset;
/**
 * @since 3.28.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */

/**
 * This class adds `relation` property to entity jsonld by using `wl_entity_jsonld_array hook`.
 * Class Entity_Dct_Relation
 * @package Wordlift\Internal_Hooks
 */
class Entity_Dct_Relation {
	/**
	 * @var \Wordlift_Entity_Service
	 */
	private $entity_service;

	/**
	 * Entity_Dct_Relation constructor.
	 *
	 * @param $entity_service \Wordlift_Entity_Service
	 */
	public function __construct( $entity_service ) {
		$this->entity_service = $entity_service;
//		add_filter( 'wl_entity_jsonld_array', array( $this, 'wl_entity_jsonld_array' ), 10, 2 );
	}

	/**
	 * @param $data array The array containing jsonld and references.
	 * @param $post_id int The entity id
	 * @return array
	 */
	public function wl_entity_jsonld_array( $data, $post_id ) {
		$single_entity_jsonld = $data['jsonld'];
		$references           = array_unique( $this->entity_service->get_related_entities( $post_id ) );

		if ( 0 === count( $references ) ) {
			return $data;
		}

		$single_entity_jsonld['http://purl.org/dc/terms/relation'] = array_map( function ( $item ) {
			return array( '@id' => $this->entity_service->get_uri( $item ) );
		}, $references );


		// Re assign the reference.
		$data['jsonld'] = $single_entity_jsonld;

		return $data;
	}
}
