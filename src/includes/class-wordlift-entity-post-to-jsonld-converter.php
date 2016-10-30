<?php
/**
 * This file defines a converter from an entity {@link WP_Post} to a JSON-LD array.
 *
 * @since   3.8.0
 * @package Wordlift
 */

/**
 * Define the {@link Wordlift_Entity_Post_To_Jsonld_Converter} class.
 *
 * @since 3.8.0
 */
class Wordlift_Entity_Post_To_Jsonld_Converter {

	/**
	 * The JSON-LD context.
	 *
	 * @since 3.8.0
	 */
	const CONTEXT = 'http://schema.org';

	/**
	 * A {@link Wordlift_Entity_Type_Service} instance.
	 *
	 * @since  3.8.0
	 * @access protected
	 * @var \Wordlift_Entity_Type_Service $entity_type_service A {@link Wordlift_Entity_Type_Service} instance.
	 */
	protected $entity_type_service;

	/**
	 * A {@link Wordlift_Entity_Service} instance.
	 *
	 * @since  3.8.0
	 * @access protected
	 * @var \Wordlift_Entity_Service $entity_type_service A {@link Wordlift_Entity_Service} instance.
	 */
	protected $entity_service;

	/**
	 * A {@link Wordlift_Property_Getter} instance.
	 *
	 * @since  3.8.0
	 * @access private
	 * @var \Wordlift_Property_Getter $property_getter A {@link Wordlift_Property_Getter} instance.
	 */
	private $property_getter;

	/**
	 * Wordlift_Entity_To_Jsonld_Converter constructor.
	 *
	 * @since 3.8.0
	 *
	 * @param \Wordlift_Entity_Type_Service $entity_type_service
	 * @param \Wordlift_Entity_Service      $entity_service
	 * @param \Wordlift_Property_Getter     $property_getter
	 */
	public function __construct( $entity_type_service, $entity_service, $property_getter ) {

		$this->entity_type_service = $entity_type_service;
		$this->entity_service      = $entity_service;
		$this->property_getter     = $property_getter;
	}

	/**
	 * Convert the provided {@link WP_Post} to a JSON-LD array. Any entity reference
	 * found while processing the post is set in the $references array.
	 *
	 * @since 3.8.0
	 *
	 * @param WP_Post $post       The {@link WP_Post} to convert.
	 *
	 * @param array   $references An array of entity references.
	 *
	 * @return array A JSON-LD array.
	 */
	public function convert( $post, &$references = array() ) {


		// Get the entity @type.
		$type = $this->entity_type_service->get( $post->ID );

		// Get the entity @id.
		$id = $this->entity_service->get_uri( $post->ID );

		// Get the entity name.
		$name = $post->post_title;

		// Get the configured type custom fields.
		$fields = $type['custom_fields'];

		// Prepare the response.
		$jsonld = array(
			'@context' => self::CONTEXT,
			'@id'      => $id,
			'@type'    => $this->relative_to_context( $type['uri'] ),
			'name'     => $name,
		);


		// Set a reference to use in closures.
		$converter = $this;

		// Try each field on the entity.
		foreach ( $fields as $key => $value ) {

			// Get the predicate.
			$name = $this->relative_to_context( $value['predicate'] );

			// Get the value, the property service will get the right extractor
			// for that property.
			$value = $this->property_getter->get( $post->ID, $key );

			if ( 0 === count( $value ) ) {
				continue;
			}

			// Map the value to the property name.
			// If we got an array with just one value, we return that one value.
			// If we got a Wordlift_Property_Entity_Reference we get the URL.
			$jsonld[ $name ] = $this->make_one( array_map( function ( $item ) use ( $converter, &$references ) {

				if ( $item instanceof Wordlift_Property_Entity_Reference ) {

					$url          = $item->getURL();
					$references[] = $url;

					return array( "@id" => $url );
				}

				return $converter->relative_to_context( $item );
			}, $value ) );

		}

		return $this->post_process( $jsonld );
	}

	/**
	 * If the provided value starts with the schema.org context, we remove the schema.org
	 * part since it is set with the '@context'.
	 *
	 * @since 3.8.0
	 *
	 * @param string $value The property value.
	 *
	 * @return string The property value without the context.
	 */
	public function relative_to_context( $value ) {

		return 0 === strpos( $value, self::CONTEXT . '/' ) ? substr( $value, strlen( self::CONTEXT ) + 1 ) : $value;
	}

	/**
	 * If the provided array of values contains only one value, then one single
	 * value is returned, otherwise the original array is returned.
	 *
	 * @since  3.8.0
	 * @access private
	 *
	 * @param array $value An array of values.
	 *
	 * @return mixed|array A single value or the original array.
	 */
	private function make_one( $value ) {

		return 1 === count( $value ) ? $value[0] : $value;
	}

	/**
	 * Post process the generated JSON to reorganize values which are stored as 1st
	 * level in WP but are really 2nd level.
	 *
	 * @since 3.8.0
	 *
	 * @param array $jsonld An array of JSON-LD properties and values.
	 *
	 * @return array The array remapped.
	 */
	private function post_process( $jsonld ) {

		foreach ( $jsonld as $key => $value ) {
			if ( 'streetAddress' === $key || 'postalCode' === $key || 'addressLocality' === $key || 'addressRegion' === $key || 'addressCountry' === $key || 'postOfficeBoxNumber' === $key ) {
				$jsonld['address']['@type'] = 'PostalAddress';
				$jsonld['address'][ $key ]  = $value;
				unset( $jsonld[ $key ] );
			}

			if ( 'latitude' === $key || 'longitude' === $key ) {
				$jsonld['geo']['@type'] = 'GeoCoordinates';
				$jsonld['geo'][ $key ]  = $value;
				unset( $jsonld[ $key ] );
			}
		}

		return $jsonld;
	}

}
