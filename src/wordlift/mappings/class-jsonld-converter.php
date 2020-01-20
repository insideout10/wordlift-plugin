<?php
/**
 * Define the Wordlift_Mapping_Jsonld_Converter class to add JSON-LD generated from mappings.
 *
 * @since   3.25.0
 * @package Wordlift
 * @subpackage Wordlift\Mappings
 */

namespace Wordlift\Mappings;

/**
 * This class takes the output from json-ld service and alter depends on the
 * rule and properties defined in sync mappings.
 *
 * @since 3.25.0
 */
class Jsonld_Converter {

	/**
	 * The {@link Mappings_Validator} instance to test.
	 *
	 * @since  3.25.0
	 * @access private
	 * @var Mappings_Validator $validator The {@link Mappings_Validator} instance.
	 */
	private $validator;

	/**
	 * The {@link Mappings_Transform_Functions_Registry} instance.
	 *
	 * @since  3.25.0
	 * @access private
	 * @var Mappings_Transform_Functions_Registry $transform_functions_registry The {@link Mappings_Transform_Functions_Registry} instance.
	 */
	private $transform_functions_registry;

	/**
	 * Initialize all dependencies required.
	 *
	 * @param Mappings_Validator $validator A {@link Mappings_Validator} instance.
	 * @param Mappings_Transform_Functions_Registry $transform_functions_registry
	 */
	public function __construct( $validator, $transform_functions_registry ) {

		$this->validator                    = $validator;
		$this->transform_functions_registry = $transform_functions_registry;

		// Hook to refactor the JSON-LD.
		add_filter( 'wl_post_jsonld', array( $this, 'wl_post_jsonld' ), 11, 3 );
		add_filter( 'wl_entity_jsonld', array( $this, 'wl_post_jsonld' ), 11, 3 );

	}

	/**
	 * Hook to `wl_post_jsonld` and `wl_entity_jsonld`.
	 *
	 * Returns JSON-LD data after applying transformation functions.
	 *
	 * @param array $jsonld The JSON-LD structure.
	 * @param int $post_id The {@link WP_Post} id.
	 * @param array $references An array of post references.
	 *
	 * @return array the new refactored array structure.
	 * @since 3.25.0
	 */
	public function wl_post_jsonld( $jsonld, $post_id, $references ) {

		// @@todo I think there's an issue here with the Validator, because you're changing the instance state and the
		// instance may be reused afterwards.

		$this->validator->validate( $post_id );
		$properties = $this->validator->get_valid_properties();

		foreach ( $properties as $property ) {
			$transform_instance = $this->transform_functions_registry->get_transform_function( $property['transform_function'] );
			if ( null !== $transform_instance ) {
				$data                                 = $this->get_data_from_data_source( $post_id, $property );
				$jsonld[ $property['property_name'] ] = $transform_instance->transform_data( $data );
			} else {
				$jsonld[ $property['property_name'] ] = $property['field_name'];
			}
		}

		return $jsonld;
	}

	/**
	 * Returns data from data source.
	 *
	 * @param int $post_id Id of the post.
	 * @param array $property_data The property data for the post_id.
	 *
	 * @return array Returns key, value array, if the value is not found, then it returns null.
	 */
	final public function get_data_from_data_source( $post_id, $property_data ) {
		$value = $property_data['field_name'];
		// @@todo 'ACF' shouldn't be here.
		// Do 1 to 1 mapping and return result.
		if ( 'acf' === $property_data['field_type'] && function_exists( 'get_field' ) ) {
			$value = get_field( $property_data['field_name'], $post_id );
			$value = ( null !== $value ) ? $value : '';
		}

		return $value;
	}

}
