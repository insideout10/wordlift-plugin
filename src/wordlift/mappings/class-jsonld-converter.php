<?php
/**
 * Define the Wordlift_Mapping_Jsonld_Converter class to add JSON-LD generated from mappings.
 *
 * @since   3.25.0
 * @package Wordlift
 * @subpackage Wordlift\Mappings
 */

namespace Wordlift\Mappings;

use Wordlift\Mappings\Data_Source\Data_Source_Factory;

/**
 * This class takes the output from json-ld service and alter depends on the
 * rule and properties defined in sync mappings.
 *
 * @since 3.25.0
 */
class Jsonld_Converter {
	/**
	 * Enumerations for the field types.
	 */
	const FIELD_TYPE_TEXT_FIELD   = 'text';
	const FIELD_TYPE_CUSTOM_FIELD = 'custom_field';
	const FIELD_TYPE_ACF          = 'acf';

	/**
	 * Mappings can be applied to either post
	 * or term, the below is used to specify whether it is a post or a term.
	 */
	const POST = 'post';
	const TERM = 'term';
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
	 * @param Mappings_Validator                    $validator A {@link Mappings_Validator} instance.
	 * @param Mappings_Transform_Functions_Registry $transform_functions_registry
	 */
	public function __construct( $validator, $transform_functions_registry ) {

		$this->validator                    = $validator;
		$this->transform_functions_registry = $transform_functions_registry;

		// Hook to refactor the JSON-LD.
		add_filter( 'wl_post_jsonld_array', array( $this, 'wl_post_jsonld_array' ), 11, 2 );
		add_filter( 'wl_entity_jsonld_array', array( $this, 'wl_post_jsonld_array' ), 11, 2 );

		// Hook at add term jsonld.
		add_filter( 'wl_term_jsonld_array', array( $this, 'wl_term_jsonld_array' ), 11, 2 );
	}

	/**
	 * Hook to `wl_term_jsonld_array`.
	 *
	 * Receive the JSON-LD and the references in the array along with the term ID and transform them according to
	 * the configuration.
	 *
	 * @param array $value {
	 *      The array containing the JSON-LD and the references.
	 *
	 * @type array $jsonld The JSON-LD array.
	 * @type int[] $references An array of post ID referenced by the JSON-LD (will be expanded by the converter).
	 * }
	 *
	 * @param int   $term_id The Term ID.
	 *
	 * @return array An array with the updated JSON-LD and references.
	 */
	public function wl_term_jsonld_array( $value, $term_id ) {
		$jsonld     = $value['jsonld'];
		$references = $value['references'];

		return array(
			'jsonld'     => $this->build_jsonld( $jsonld, $term_id, $references, self::TERM ),
			'references' => $references,
		);
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
	 * @param int   $post_id The post ID.
	 *
	 * @return array An array with the updated JSON-LD and references.
	 */
	public function wl_post_jsonld_array( $value, $post_id ) {

		$jsonld     = $value['jsonld'];
		$references = $value['references'];

		return array(
			'jsonld'     => $this->build_jsonld( $jsonld, $post_id, $references, self::POST ),
			'references' => $references,
		);
	}

	/**
	 * Returns JSON-LD data after applying transformation functions.
	 *
	 * @param array  $jsonld The JSON-LD structure.
	 * @param int    $identifier The {@link WP_Post} id or {@link \WP_Term} id.
	 * @param array  $references An array of post references.
	 *
	 * @param string $type Post or term.
	 *
	 * @return array the new refactored array structure.
	 * @since 3.25.0
	 */
	private function build_jsonld( $jsonld, $identifier, &$references, $type ) {
		// @@todo I think there's an issue here with the Validator, because you're changing the instance state and the
		// instance may be reused afterwards.

		$properties        = $this->validator->validate( $identifier, $type );
		$nested_properties = array();

		foreach ( $properties as $property ) {
			// If the property has the character '/' in the property name then it is a nested property.
			if ( strpos( $property['property_name'], '/' ) !== false ) {
				$nested_properties[] = $property;
				continue;
			}
			$property_transformed_data = $this->get_property_data( $property, $jsonld, $identifier, $references, $type );
			if ( false !== $property_transformed_data ) {
				$jsonld[ $property['property_name'] ] = $property_transformed_data;
			}
		}

		$jsonld = $this->process_nested_properties( $nested_properties, $jsonld, $identifier, $references, $type );

		return $jsonld;
	}

	/**
	 * Get the property data by applying the transformation function
	 *
	 * @param $property
	 * @param $jsonld
	 * @param $identifier
	 * @param $references
	 *
	 * @param $type
	 *
	 * @return array|bool|null
	 */
	public function get_property_data( $property, $jsonld, $identifier, &$references, $type ) {
		$transform_instance = $this->transform_functions_registry->get_transform_function( $property['transform_function'] );
		$data               = Data_Source_Factory::get_instance()->get_data( $identifier, $property, $type );
		if ( null !== $transform_instance ) {
			$transform_data = $transform_instance->transform_data( $data, $jsonld, $references, $identifier );
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
	 * @param $identifier
	 * @param $references
	 * @param string                  $type Post or term.
	 *
	 * @return array
	 */
	public function process_nested_properties( $nested_properties, $jsonld, $identifier, &$references, $type ) {
		foreach ( $nested_properties as $property ) {
			$property_data = $this->get_property_data( $property, $jsonld, $identifier, $references, $type );
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
			 * didn't exist.
			 */
			foreach ( $keys as $key ) {
				if ( '' === $key ) {
					continue;
				}
				if ( ! array_key_exists( $key, $current_property_pointer ) ) {
					$current_property_pointer[ $key ] = array();
				}
				// We are setting the pointer to the current key, so that at the end
				// we can add the data at last level.
				$current_property_pointer = &$current_property_pointer[ $key ];
			}

			// We overwrite the existing value if it's not an array.
			if ( ! is_array( $current_property_pointer ) ) {
				$current_property_pointer = array();
			}

			$current_property_pointer[ $end ] = $property_data;
		}

		return $jsonld;
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
