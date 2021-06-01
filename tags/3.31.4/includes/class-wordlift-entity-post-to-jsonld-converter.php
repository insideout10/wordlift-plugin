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
class Wordlift_Entity_Post_To_Jsonld_Converter extends Wordlift_Abstract_Post_To_Jsonld_Converter {

	/**
	 * A {@link Wordlift_Property_Getter} instance.
	 *
	 * @since  3.8.0
	 * @access private
	 * @var \Wordlift_Property_Getter $property_getter A {@link Wordlift_Property_Getter} instance.
	 */
	private $property_getter;

	/**
	 * The {@link Wordlift_Schemaorg_Property_Service} or null if not provided.
	 *
	 * @since 3.20.0
	 * @access private
	 * @var null|Wordlift_Schemaorg_Property_Service The {@link Wordlift_Schemaorg_Property_Service} or null if not provided.
	 */
	private $schemaorg_property_service;

	/**
	 * The {@link Wordlift_Post_To_Jsonld_Converter} is used to convert entities that also have the `article` term.
	 *
	 * @since 3.25.2
	 * @var \Wordlift_Post_To_Jsonld_Converter $post_to_jsonld_converter The {@link Wordlift_Post_To_Jsonld_Converter} instance.
	 */
	private $post_to_jsonld_converter;

	private $references_infos;

	/**
	 * Wordlift_Entity_To_Jsonld_Converter constructor.
	 *
	 * @param \Wordlift_Entity_Type_Service $entity_type_service A {@link Wordlift_Entity_Type_Service} instance.
	 * @param \Wordlift_Entity_Service $entity_service A {@link Wordlift_Entity_Service} instance.
	 * @param \Wordlift_User_Service $user_service A {@link Wordlift_User_Service} instance.
	 * @param \Wordlift_Attachment_Service $attachment_service A {@link Wordlift_Attachment_Service} instance.
	 * @param \Wordlift_Property_Getter $property_getter A {@link Wordlift_Property_Getter} instance.
	 * @param \Wordlift_Schemaorg_Property_Service $schemaorg_property_service A {@link Wordlift_Schemaorg_Property_Service} instance.
	 * @param \Wordlift_Post_To_Jsonld_Converter $post_to_jsonld_converter The {@link Wordlift_Post_To_Jsonld_Converter} instance.
	 *
	 * @since 3.8.0
	 */
	public function __construct( $entity_type_service, $entity_service, $user_service, $attachment_service, $property_getter, $schemaorg_property_service, $post_to_jsonld_converter ) {
		parent::__construct( $entity_type_service, $entity_service, $user_service, $attachment_service );

		$this->property_getter            = $property_getter;
		$this->schemaorg_property_service = $schemaorg_property_service;
		$this->post_to_jsonld_converter   = $post_to_jsonld_converter;

	}

	/**
	 * Convert the provided {@link WP_Post} to a JSON-LD array. Any entity reference
	 * found while processing the post is set in the $references array.
	 *
	 * @param int $post_id The {@link WP_Post} id.
	 * @param array $references An array of entity references.
	 * @param array $references_infos
	 *
	 * @return array A JSON-LD array.
	 * @since 3.8.0
	 */
	public function convert( $post_id, &$references = array(), &$references_infos = array() ) {

		// Get the post instance.
		$post = get_post( $post_id );
		if ( null === $post ) {
			// Post not found.
			return null;
		}

		// Get the base JSON-LD and the list of entities referenced by this entity.
		if ( has_term( 'article', Wordlift_Entity_Type_Taxonomy_Service::TAXONOMY_NAME, $post_id ) ) {
			$jsonld = $this->post_to_jsonld_converter->convert( $post_id, $references, $references_infos );
		} else {
			$jsonld = parent::convert( $post_id, $references, $references_infos );
		}

		// Get the entity name.
		$jsonld['name'] = html_entity_decode( $post->post_title );

		// 3.13.0, add alternate names.
		$alternative_labels = $this->entity_service->get_alternative_labels( $post_id );
		if ( 0 < count( $alternative_labels ) ) {
			$jsonld['alternateName'] = $alternative_labels;
		}

		// Get the entity `@type` with custom fields set by the Wordlift_Schema_Service.
		//
		// This allows us to gather the basic properties as defined by the `Thing` entity type.
		// Get the configured type custom fields.
		$custom_fields = $this->entity_type_service->get_custom_fields( $post_id );

		if ( isset( $custom_fields ) ) {
			$this->process_type_custom_fields( $jsonld, $custom_fields, $post, $references, $references_infos );
		}

		/*
		 * Get the properties attached to the post.
		 *
		 * @since 3.20.0 We attach properties directly to the posts.
		 *
		 * @see https://github.com/insideout10/wordlift-plugin/issues/835
		 */
		if ( WL_ALL_ENTITY_TYPES ) {
			$this->process_post_properties( $jsonld, $post_id );
		}

		/**
		 * Call the `wl_post_jsonld_array` filter. This filter allows 3rd parties to also modify the references.
		 *
		 * @param array $value {
		 *
		 * @type array $jsonld The JSON-LD structure.
		 * @type int[] $references An array of post IDs.
		 * }
		 * @since 3.25.0
		 *
		 * @see https://www.geeklab.info/2010/04/wordpress-pass-variables-by-reference-with-apply_filter/
		 *
		 * @api
		 */
		$ret_val    = apply_filters( 'wl_entity_jsonld_array', array(
			'jsonld'     => $jsonld,
			'references' => $references,
		), $post_id );
		$jsonld     = $ret_val['jsonld'];
		$references = $ret_val['references'];

		/**
		 * Call the `wl_entity_jsonld` filter.
		 *
		 * @param array $jsonld The JSON-LD structure.
		 * @param int $post_id The {@link WP_Post} `id`.
		 * @param array $references The array of referenced entities.
		 *
		 * @since 3.20.0
		 *
		 * @api
		 */
		return apply_filters( 'wl_entity_jsonld', $this->post_process( $jsonld ), $post_id, $references );
	}

	/**
	 * Add data to the JSON-LD using the `custom_fields` array which contains the definitions of property
	 * for the post entity type.
	 *
	 * @param array $jsonld The JSON-LD array.
	 * @param array $fields The entity types field array.
	 * @param WP_Post $post The target {@link WP_Post} instance.
	 * @param array $references The references array.
	 *
	 * @since 3.20.0 This code moved from the above function `convert`, used for entity types defined in
	 *  the {@link Wordlift_Schema_Service} class.
	 *
	 */
	private function process_type_custom_fields( &$jsonld, $fields, $post, &$references, &$references_infos ) {

		// Set a reference to use in closures.
		$converter = $this;

		// Try each field on the entity.
		foreach ( $fields as $key => $value ) {

			// Get the predicate.
			$name = $this->relative_to_context( $value['predicate'] );

			// Get the value, the property service will get the right extractor
			// for that property.
			$value = $this->property_getter->get( $post->ID, $key );

			if ( empty( $value ) ) {
				continue;
			}

			// Map the value to the property name.
			// If we got an array with just one value, we return that one value.
			// If we got a Wordlift_Property_Entity_Reference we get the URL.
			$jsonld[ $name ] = self::make_one( array_map( function ( $item ) use ( $converter, &$references, &$references_infos ) {

				if ( $item instanceof Wordlift_Property_Entity_Reference ) {

					$url = $item->get_url();

					// The refactored converters require the entity id.
					$references[] = $item->get_id();

					$references_infos[] = array( 'reference' => $item );

					return array(
						'@id' => $url,
					);
				}

				return $converter->relative_to_context( $item );
			}, $value ) );

		}

	}

	/**
	 * Process the properties attached to the {@link WP_Post}.
	 *
	 * @param array $jsonld The JSON-LD array.
	 * @param int $post_id The target {@link WP_Post} id.
	 *
	 * @since 3.20.0
	 *
	 */
	private function process_post_properties( &$jsonld, $post_id ) {

		// Get all the props.
		$props = $this->schemaorg_property_service->get_all( $post_id );

		// Process all the props.
		foreach ( $props as $name => $instances ) {

			// Get the values.
			$values = array_map( function ( $instance ) {
				return $instance['value'];
			}, $instances );

			// We might receive empty values, remove them.
			$non_empty_values = array_filter( $values, function ( $value ) {
				return ! empty( $value );
			} );

			// Skip empty properties.
			if ( empty( $non_empty_values ) ) {
				continue;
			}

			// @@todo: need to handle maybe Numbers and URLs differently.
			// Make an array a single value when possible.
			$jsonld[ $name ] = self::make_one( $non_empty_values );
		}

	}

	/**
	 * Post process the generated JSON to reorganize values which are stored as 1st
	 * level in WP but are really 2nd level.
	 *
	 * @param array $jsonld An array of JSON-LD properties and values.
	 *
	 * @return array The array remapped.
	 * @since 3.8.0
	 *
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

			if ( 'calories' === $key ) {
				$jsonld['nutrition']['@type'] = 'NutritionInformation';
				$jsonld['nutrition'][ $key ]  = $value;
				unset( $jsonld[ $key ] );
			}
		}

		return $jsonld;
	}

}
