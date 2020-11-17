<?php

namespace Wordlift\Dataset;
/**
 * @since 3.28.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */

/**
 * This class adds `relation` property to entity jsonld by using `wl_entity_jsonld_array hook`.
 * Class Entity_Dct_Relation
 */
class Sync_Hooks_Entity_Relation {

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

		add_filter( 'wl_dataset__sync_service__sync_item__jsonld', array(
			$this,
			'wl_dataset__sync_service__sync_item__jsonld'
		), 10, 2 );
	}

	public function wl_dataset__sync_service__sync_item__jsonld( $jsonld, $post_id ) {

		// Choose the dcterm property according to the post type.
		$property = $this->entity_service->is_entity( $post_id )
			? 'http://purl.org/dc/terms/relation'
			: 'http://purl.org/dc/terms/references';

		$references = array_unique( $this->entity_service->get_related_entities( $post_id ) );

		// Bail out if there are no references.
		if ( empty( $references ) ) {
			return $jsonld;
		}

		$jsonld[0][ $property ] = array_values( array_map( function ( $item ) {
			return array( '@id' => $this->entity_service->get_uri( $item ) );
		}, $references ) );

		return $jsonld;
	}

}
