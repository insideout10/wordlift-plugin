<?php
/**
 * Converters: Post Id to JSON-LD converter.
 *
 * Define the {@link Wordlift_Postid_To_Jsonld_Converter} class.
 *
 * @since   3.8.0
 * @package Wordlift
 */

/**
 * Define the {@link Wordlift_Postid_To_Jsonld_Converter} class to convert
 * post ids to JSON-LD arrays.
 *
 * @since 3.8.0
 */
class Wordlift_Postid_To_Jsonld_Converter implements Wordlift_Post_Converter {

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
	 * Convert a post to JSON-LD.
	 *
	 * @since 3.8.0
	 *
	 * @param string $id A post id (post or entity).
	 *
	 * @param array  $references
	 *
	 * @return array|NULL A JSON-LD array representation of the post with the provided id, or NULL if not found.
	 */
	public function convert( $id, &$references = array() ) {

		// Convert an entity.
//		if ( null !== $entity = $this->entity_service->get_entity_post_by_uri( $id ) ) {
//			return $this->entity_post_to_jsonld_converter->convert( $entity );
//		}

		// Convert a post.
		return $this->post_to_jsonld_converter->convert( $id );
	}

}
