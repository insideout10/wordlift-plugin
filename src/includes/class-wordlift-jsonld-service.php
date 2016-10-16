<?php

require_once( 'properties/class-wordlift-property-service.php' );
require_once( 'properties/class-wordlift-simple-property-service.php' );
require_once( 'properties/class-wordlift-entity-property-service.php' );
require_once( 'properties/class-wordlift-url-property-service.php' );
require_once( 'properties/class-wordlift-double-property-service.php' );

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

	const CONTEXT = 'http://schema.org';

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
	 * @var \Wordlift_Property_Service_2
	 */
	private $property_service;

	private static $instance;

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

		$this->property_service = new Wordlift_Property_Service_2( new Wordlift_Simple_Property_Service() );
		$this->property_service->register( new Wordlift_Entity_Property_Service(), array(
			Wordlift_Schema_Service::FIELD_LOCATION,
			Wordlift_Schema_Service::FIELD_FOUNDER
		) );
		$this->property_service->register( new Wordlift_Url_Property_Service(), array( Wordlift_Url_Property_Service::META_KEY ) );
		$this->property_service->register( new Wordlift_Double_Property_Service(), array(
			Wordlift_Schema_Service::FIELD_GEO_LATITUDE,
			Wordlift_Schema_Service::FIELD_GEO_LONGITUDE
		) );

		self::$instance = $this;
	}

	public static function get_instance() {

		return self::$instance;
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


		$jsonld = array( '@context' => self::CONTEXT ) + $this->get_by_post( $post );

		wp_send_json( $jsonld );

	}

	public function get_by_id( $post_id ) {


		return $this->get_by_post( get_post( $post_id ) );
	}

	public function get_by_post( $post ) {

		$type   = $this->entity_type_service->get( $post->ID );
		$id     = $this->entity_service->get_uri( $post->ID );
		$name   = $post->post_title;
		$fields = $type['custom_fields'];

		$jsonld = array(
			'@id'   => $id,
			'@type' => $this->relative_to_context( $type['uri'] ),
			'name'  => $name,
		);

		foreach ( $fields as $key => $value ) {
			$name  = $this->relative_to_context( $value['predicate'] );
			$value = $this->property_service->get( $post->ID, $key );

			if ( NULL === $value ) {
				continue;
			}

			$jsonld[ $name ] = $this->relative_to_context( $value );
		}


		return $jsonld;

	}

	private function relative_to_context( $value ) {

		return ( 0 === strpos( $value, self::CONTEXT . '/' ) ? substr( $value, strlen( self::CONTEXT ) + 1 ) : $value );
	}

}
