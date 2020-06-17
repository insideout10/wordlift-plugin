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
		add_filter( 'wl_term_jsonld_array', array( $this, 'wl_post_jsonld_array' ), 11, 3 );
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

		$properties        = $this->validator->validate( $post_id );
		$nested_properties = array();

		foreach ( $properties as $property ) {
			// If the property has the character '/' in the property name then it is a nested property.
			if ( strpos( $property['property_name'], '/' ) !== false ) {
				$nested_properties[] = $property;
				continue;
			}
			$property_transformed_data = $this->get_property_data( $property, $jsonld, $post_id, $references );
			if ( false !== $property_transformed_data ) {
				$jsonld[ $property['property_name'] ] = $property_transformed_data;
			}
		}

		$jsonld = $this->process_nested_properties( $nested_properties, $jsonld, $post_id, $references );

		return $jsonld;
	}

	/**
	 * Get the property data by applying the transformation function
	 *
	 * @param $property
	 * @param $jsonld
	 * @param $post_id
	 * @param $references
	 *
	 * @return array|bool|null
	 */
	public function get_property_data( $property, $jsonld, $post_id, &$references ) {
		$transform_instance = $this->transform_functions_registry->get_transform_function( $property['transform_function'] );
		$data               = $this->get_data_from_data_source( $post_id, $property );
		if ( null !== $transform_instance ) {
			$transform_data = $transform_instance->transform_data( $data, $jsonld, $references, $post_id );
			if ( null !== $transform_data ) {
				return $this->make_single( $transform_data );
			}
		} else {
			return $this->make_single( $data );
		}

		return false;
	}

	/**
	 * Process all the nested properties.
	 *
	 * @param $nested_properties array
	 * @param $jsonld array
	 *
	 * @return array
	 */
	public function process_nested_properties( $nested_properties, $jsonld, $post_id, &$references ) {
		foreach ( $nested_properties as $property ) {
			$property_data = $this->get_property_data( $property, $jsonld, $post_id, $references );
			if ( false === $property_data ) {
				// No need to create nested levels.
				continue;
			}

			$keys = explode( '/', $property['property_name'] );
			// end is the last level of the nested property.
			$end                      = array_pop( $keys );
			$current_property_pointer = &$jsonld;

			/**
			 * Once we find all the nested levels from the property name
			 * loop through it and create associative array if the levels
			 * didnt exist.
			 */
			while ( count( $keys ) > 0 ) {
				$key = array_shift( $keys );
				if ( $key === "" ) {
					continue;
				}
				if ( ! array_key_exists( $key, $current_property_pointer ) ) {
					$current_property_pointer[ $key ] = array();
				}
				// We are setting the pointer to the current key, so that at the end
				// we can add the data at last level.
				$current_property_pointer = &$current_property_pointer[ $key ];
			}
			$current_property_pointer[ $end ] = $property_data;
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
		switch ( $property_data['field_type'] ) {
			case self::FIELD_TYPE_ACF:
				if ( ! function_exists( 'get_field' ) || ! function_exists( 'get_field_object' ) ) {
					return array();
				}

				return $this->get_data_for_acf_field( $property_data['field_name'], $post_id );

			case self::FIELD_TYPE_CUSTOM_FIELD:
				if ( get_queried_object() instanceof \WP_Term ) {
					return array_map( 'wp_strip_all_tags', get_term_meta( get_queried_object_id(), $value ) );
				}
				else {
					return array_map( 'wp_strip_all_tags', get_post_meta( $post_id, $value ) );
				}
			default:
				return $value;
		}

	}

	/**
	 * Gets data from acf, format the data if it is a repeater field.
	 *
	 * @param $field_name
	 * @param $post_id
	 *
	 * @return array|mixed
	 */
	public function get_data_for_acf_field( $field_name, $post_id ) {
		if ( get_queried_object() instanceof \WP_Term ) {
			// Data fetching method for term is different.
			$term = get_queried_object();
			$field_data = get_field_object( $field_name, $term );
			$data       = get_field( $field_name, $term );
		}
		else {
			$field_data = get_field_object( $field_name, $post_id );
			$data       = get_field( $field_name, $post_id );
		}
		// only process if it is a repeater field, else return the data.
		if ( is_array( $field_data ) && array_key_exists( 'type', $field_data )
		     && $field_data['type'] === 'repeater' ) {
			/**
			 * check if we have only one sub field, currently we only support one subfield,
			 * so each repeater item should be checked if there is a single sub field.
			 */
			if ( is_array( $data ) &&
			     count( $data ) > 0 &&
			     count( array_keys( $data[0] ) ) === 1 ) {
				$repeater_formatted_data = array();
				foreach ( $data as $item ) {
					$repeater_formatted_data = array_merge( $repeater_formatted_data, array_values( $item ) );
				}
				// Remove non unique values.
				$repeater_formatted_data = array_unique( $repeater_formatted_data );
				// Remove empty values
				$repeater_formatted_data = array_filter( $repeater_formatted_data, 'strlen' );

				// re-index all the values.
				return array_values( $repeater_formatted_data );
			}
		}

		// Return normal acf data if it is not a repeater field.
		return $data;
	}

	private function make_single( $value ) {

		$values = (array) $value;

		if ( empty( $values ) ) {
			return false;
		}

		if ( 1 === count( $values ) && 0 === key( $values ) ) {
			return current( $values );
		}

		return $values;
	}

}
