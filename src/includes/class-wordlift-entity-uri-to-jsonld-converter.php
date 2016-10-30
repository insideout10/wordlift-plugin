<?php
/**
 * Define the {@link Wordlift_Entity_Uri_To_Jsonld_Converter} class which extends
 * {@link Wordlift_Entity_Post_To_Jsonld_Converter}.
 *
 * @since   3.8.0
 * @package Wordlift
 */

/**
 * Define the {@link Wordlift_Entity_Uri_To_Jsonld_Converter} class to convert
 * entity URIs to JSON-LD arrays.
 *
 * @since 3.8.0
 */
class Wordlift_Entity_Uri_To_Jsonld_Converter extends Wordlift_Entity_Post_To_Jsonld_Converter {
	
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

		if ( NULL === ( $post = $this->entity_service->get_entity_post_by_uri( $uri ) ) ) {
			return NULL;
		}

		return parent::convert( $post, $references );

	}

}
