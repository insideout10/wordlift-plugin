<?php
/**
 * Define the {@link Wordlift_Uri_To_Jsonld_Converter} class which extends
 * {@link Wordlift_Entity_Post_To_Jsonld_Converter}.
 *
 * @since   3.8.0
 * @package Wordlift
 */

/**
 * Define the {@link Wordlift_Uri_To_Jsonld_Converter} class to convert
 * entity URIs to JSON-LD arrays.
 *
 * @since 3.8.0
 */
class Wordlift_Uri_To_Jsonld_Converter {

	private $entity_service;
	private $post_service;
	private $entity_post_to_jsonld_converter;
	private $post_to_jsonld_converter;

	/**
	 * Wordlift_Entity_To_Jsonld_Converter constructor.
	 *
	 * @since 3.8.0
	 *
	 * @param \Wordlift_Entity_Service                  $entity_service
	 * @param \Wordlift_Post_Service                    $post_service
	 * @param \Wordlift_Entity_Post_To_Jsonld_Converter $entity_post_to_jsonld_converter
	 * @param \Wordlift_Post_To_Jsonld_Converter        $post_to_jsonld_converter
	 */
	public function __construct( $entity_service, $post_service, $entity_post_to_jsonld_converter, $post_to_jsonld_converter ) {

		$this->entity_service                  = $entity_service;
		$this->post_service                    = $post_service;
		$this->entity_post_to_jsonld_converter = $entity_post_to_jsonld_converter;
		$this->post_to_jsonld_converter        = $post_to_jsonld_converter;

	}


	/**
	 * Convert an entity URI to JSON-LD.
	 *
	 * @since 3.8.0
	 *
	 * @param string $uri An entity uri.
	 *
	 * @param array  $references
	 *
	 * @return array|NULL A JSON-LD array representation of the entity with the provided URI or NULL if an entity is not found.
	 */
	public function convert( $uri, &$references = array() ) {

		if ( null !== $entity = $this->entity_service->get_entity_post_by_uri( $uri ) ) {
			return $this->entity_post_to_jsonld_converter->convert( $entity );
		}

		return $this->post_to_jsonld_converter->convert( $this->post_service->get_by_uri( $uri ) );
	}

}
