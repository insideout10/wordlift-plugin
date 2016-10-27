<?php

/**
 *
 */
class Wordlift_Entity_Uri_To_Jsonld_Converter extends Wordlift_Entity_Post_To_Jsonld_Converter {

//	/**
//	 * Wordlift_Entity_To_Jsonld_Converter constructor.
//	 *
//	 * @since 3.8.0
//	 *
//	 * @param \Wordlift_Entity_Type_Service $entity_type_service
//	 * @param \Wordlift_Entity_Service $entity_service
//	 * @param \Wordlift_Property_Getter $property_getter
//	 */
//	public function __construct( $entity_type_service, $entity_service, $property_getter ) {
//
//		$this->entity_type_service = $entity_type_service;
//		$this->entity_service      = $entity_service;
//		$this->property_getter     = $property_getter;
//	}

	/**
	 * Convert an entity URI to JSON-LD.
	 *
	 * @since 3.8.0
	 *
	 * @param string $uri An entity uri.
	 *
	 * @param array $references
	 *
	 * @return array|NULL A JSON-LD array representation of the entity with the provided URI or NULL if an entity is not found.
	 */
	public function convert( $uri, &$references = array() ) {

		if ( NULL === ( $post = $this->entity_service->get_entity_post_by_uri( $uri ) ) ) {
			return NULL;
		}

		return parent::convert( $post, $references );

	}

}
