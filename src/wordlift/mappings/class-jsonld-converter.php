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
	 * Enumerations for the field types.
	 * Enumerations for the field types.
	 */
	const FIELD_TYPE_TEXT_FIELD = 'text';
	const FIELD_TYPE_CUSTOM_FIELD = 'custom_field';
	const FIELD_TYPE_ACF = 'acf';
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
		add_filter( 'wl_post_jsonld_array', array( $this, 'wl_post_jsonld_array' ), 11, 2 );
		add_filter( 'wl_entity_jsonld_array', array( $this, 'wl_post_jsonld_array' ), 11, 3 );

	}

	/**
	 * Hook to `wl_post_jsonld_array` and `wl_entity_jsonld_array`.
	 *
	 * Receive the JSON-LD and the references in the array along with the post ID and transform them according to
	 * the configuration.
	 *
	 * @param array $value {
	 *      The array containing the JSON-LD and the references.
	 *
	 * @type array $jsonld The JSON-LD array.
	 * @type int[] $references An array of post ID referenced by the JSON-LD (will be expanded by the converter).
	 * }
	 *
	 * @param int $post_id The post ID.
	 *
	 * @return array An array with the updated JSON-LD and references.
	 */
	public function wl_post_jsonld_array( $value, $post_id ) {

		$jsonld     = $value['jsonld'];
		$references = $value['references'];

		return array(
			'jsonld'     => $this->wl_post_jsonld( $jsonld, $post_id, $references ),
			'references' => $references,
		);
	}

	/**
	 * Returns JSON-LD data after applying transformation functions.
	 *
	 * @param array $jsonld The JSON-LD structure.
	 * @param int $post_id The {@link WP_Post} id.
	 * @param array $references An array of post references.
	 *
	 * @return array the new refactored array structure.
	 * @since 3.25.0
	 */
	private function wl_post_jsonld( $jsonld, $post_id, &$references ) {

		// @@todo I think there's an issue here with the Validator, because you're changing the instance state and the
		// instance may be reused afterwards.

		$properties = $this->validator->validate( $post_id );

		foreach ( $properties as $property ) {
			$transform_instance = $this->transform_functions_registry->get_transform_function( $property['transform_function'] );
			$data               = $this->get_data_from_data_source( $post_id, $property );
			if ( null !== $transform_instance ) {
				$transform_data = $transform_instance->transform_data( $data, $jsonld, $references, $post_id );
				if ( null !== $transform_data ) {
					$jsonld[ $property['property_name'] ] = $transform_data;
				}
			} else {
				$jsonld[ $property['property_name'] ] = $data;
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
		// Do 1 to 1 mapping and return result.
		if ( self::FIELD_TYPE_ACF === $property_data['field_type'] && function_exists( 'get_field' ) ) {
			$value = get_field( $property_data['field_name'], $post_id );
			$value = ( null !== $value ) ? $value : '';
		}

		return $value;
	}

}
