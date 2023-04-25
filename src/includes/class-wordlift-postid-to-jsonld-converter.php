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

	private $entity_post_to_jsonld_converter;
	private $post_to_jsonld_converter;

	/**
	 * A {@link Wordlift_Log_Service} instance.
	 *
	 * @since  3.16.0
	 * @access private
	 * @var \Wordlift_Log_Service $log A {@link Wordlift_Log_Service} instance.
	 */
	private $log;

	/**
	 * Wordlift_Entity_To_Jsonld_Converter constructor.
	 *
	 * @param \Wordlift_Entity_Post_To_Jsonld_Converter $entity_post_to_jsonld_converter
	 * @param \Wordlift_Post_To_Jsonld_Converter        $post_to_jsonld_converter
	 *
	 * @since 3.8.0
	 */
	public function __construct( $entity_post_to_jsonld_converter, $post_to_jsonld_converter ) {

		$this->log = Wordlift_Log_Service::get_logger( get_class() );

		$this->entity_post_to_jsonld_converter = $entity_post_to_jsonld_converter;
		$this->post_to_jsonld_converter        = $post_to_jsonld_converter;

	}

	/**
	 * Convert a post to JSON-LD.
	 *
	 * @param string $post_id A post id (post or entity).
	 * @param array  $references
	 * @param array  $relations
	 *
	 * @return array|NULL A JSON-LD array representation of the post with the provided id, or NULL if not found.
	 * @since 3.8.0
	 */
	public function convert( $post_id, &$references = array(), &$references_infos = array(), $relations = null ) {

		$this->log->trace( "Converting post $post_id..." );

		$entity_service = Wordlift_Entity_Service::get_instance();

		return $entity_service->is_entity( $post_id )
			// Entity.
			? $this->entity_post_to_jsonld_converter->convert( $post_id, $references, $references_infos, $relations )
			// Post/Page.
			: $this->post_to_jsonld_converter->convert( $post_id, $references, $references_infos, $relations );
	}

}
