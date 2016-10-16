<?php

/**
 * Define the Wordlift_Jsonld_Service class to support JSON-LD.
 *
 * @since 3.7.0
 */

/**
 * This class exports an entity using JSON-LD.
 *
 * @since 3.7.0
 */
class Wordlift_Jsonld_Service {

	/**
	 * @since 3.7.0
	 * @var Wordlift_Entity_Service $entity_service
	 */
	private $entity_service;

	/**
	 * @var Wordlift_Entity_Type_Service $entity_type_service
	 */
	private $entity_type_service;

	/**
	 * @var Wordlift_Schema_Service $schema_service
	 */
	private $schema_service;

	/**
	 * Wordlift_Jsonld_Service constructor.
	 *
	 * @param $entity_service
	 * @param $entity_type_service
	 * @param $schema_service
	 */
	public function __construct( $entity_service, $entity_type_service, $schema_service ) {

		$this->entity_service      = $entity_service;
		$this->schema_service      = $schema_service;
		$this->entity_type_service = $entity_type_service;
	}

	/**
	 * @since 3.7.0
	 */
	public function get() {

		$uri = $_GET['uri'];

		$post = $this->entity_service->get_entity_post_by_uri( $uri );

		if ( NULL === $post ) {
			wp_send_json_error( 'Entity not found' );
		}

		$post_meta    = get_post_meta( $post->ID );
		$schema_class = $this->schema_service->get_narrowest_schema( $this->get_type( $post->ID ) );
		$fields       = $schema_class['custom_fields'];

		foreach ( $fields as $key => $value ) {

			if ( Wordlift_Schema_Url_Property_Service::META_KEY === $key
			     || ! isset( $post_meta[ $key ] )
			) {
				continue;
			}

			echo( $key . ' :: -' . $post_meta[ $key ][0] . '-<br/>' );
		}

	}

	private function get_type( $id ) {

		$types = get_post_meta( $id, 'wl_entity_type_uri' );

		return $types;
	}

	private function get_schema( $types ) {
	}

}
