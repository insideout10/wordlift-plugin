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
	 * @var \Wordlift_Property_Factory
	 */
	private $property_factory;

	/**
	 * Create a JSON-LD service.
	 *
	 * @since 3.7.0
	 *
	 * @param \Wordlift_Entity_Service $entity_service A {@link Wordlift_Entity_Service} instance.
	 * @param \Wordlift_Entity_Type_Service $entity_type_service A {@link Wordlift_Entity_Type_Service} instance.
	 * @param \Wordlift_Schema_Service $schema_service A {@link Wordlift_Schema_Service} instance.
	 * @param \Wordlift_Property_Factory $property_factory
	 */
	public function __construct( $entity_service, $entity_type_service, $schema_service, $property_factory ) {

		$this->entity_service      = $entity_service;
		$this->schema_service      = $schema_service;
		$this->entity_type_service = $entity_type_service;
		$this->property_factory    = $property_factory;
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


		$jsonld = $this->get_by_post( $post );

		var_dump( $jsonld );

		return;

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

	private function get_by_id( $post_id ) {


		return $this->get_by_post( get_post( $post_id ) );
	}

	private function get_by_post( $post ) {

		$post_meta = get_post_meta( $post->ID );

		$type   = $this->entity_type_service->get( $post->ID );
		$id     = $this->entity_service->get_uri( $post->ID );
		$name   = $post->post_title;
		$fields = $type['custom_fields'];

		$jsonld = array(
			'@id'   => $id,
			'@type' => substr( $type['uri'], strlen( 'http://schema.org/' ) ),
			'name'  => $name,
		);

		var_dump( $fields );

		foreach ( $fields as $key => $value ) {
			$name            = substr( $value['predicate'], strlen( 'http://schema.org/' ) );
			$value           = is_numeric( $post_meta[ $key ] ) ? $this->get_by_id( $post_meta[ $key ] ) : $post_meta[ $key ];
			$jsonld[ $name ] = 1 === count( $value ) ? $value[0] : $value;
		}


		return $jsonld;

	}

}
