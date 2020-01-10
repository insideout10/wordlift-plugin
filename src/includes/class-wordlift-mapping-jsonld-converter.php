<?php
/**
 * Define the Wordlift_Mapping_Jsonld_Converter class to add JSON-LD generated from mappings.
 *
 * @since   3.25.0
 * @package Wordlift
 */

/**
 * This class takes the output from json-ld service and alter depends on the
 * rule and properties defined in sync mappings.
 *
 * @since 3.25.0
 */
class Wordlift_Mapping_Jsonld_Converter {
	/**
	 * The {@link Wordlift_Mapping_Validator} instance to test.
	 *
	 * @since  3.25.0
	 * @access private
	 * @var \Wordlift_Mapping_Validator $validator The {@link Wordlift_Mapping_Validator} instance.
	 */
	private $validator;
	/**
	 * The {@link Wordlift_Mapping_Transform_Function_Registry} instance.
	 *
	 * @since  3.25.0
	 * @access private
	 * @var \Wordlift_Mapping_Transform_Function_Registry $transform_functions_registry The {@link Wordlift_Mapping_Transform_Function_Registry} instance.
	 */
	private $transform_functions_registry;

	/**
	 * The variable to hold the json-ld data
	 *
	 * @since  3.25.0
	 * @access private
	 * @var Array $jsonld_data The array of json-ld data
	 */
	private $jsonld_data;

	/**
	 * The variable to hold the post_id
	 *
	 * @since  3.25.0
	 * @access private
	 * @var Int $post_id The id of the post.
	 */
	private $post_id;

	/**
	 * Initialize all dependancies required.
	 *
	 * @param Int   $post_id The Id of the post where the mappings should be validated.
	 * @param Array $jsonld_data An Array of json-ld data from {@link \Wordlift_Jsonld_Service} class.
	 */
	public function __construct( $post_id, $jsonld_data ) {
		$this->validator                    = new Wordlift_Mapping_Validator();
		$this->transform_functions_registry = new Wordlift_Mapping_Transform_Function_Registry();
		$this->jsonld_data                  = $jsonld_data;
		$this->post_id                      = $post_id;
	}

	/**
	 * Check if the key and value matches some criteria, if it does
	 * then it should be replaced only on first element of the array
	 *
	 * @param String            $key The key of the property.
	 * @param String|Array|NULL $value The Value obtained after the transformation method.
	 * @return Boolean
	 */
	private static function should_be_replaced_on_one_entity( $key, $value ) {
		$how_to_fields = array( 'step', 'tool', 'supply' );
		return ( '@type' === $key && 'HowTo' === $value ) || in_array( $key, $how_to_fields );
	}
	/**
	 * Either only one item is replaced with key and value or the entire array
	 * is replaced depends on the condition.
	 *
	 * @param Array             $json_ld_data_array Array of json ld items.
	 * @param String            $key The key of the property.
	 * @param String|Array|NULL $value The Value obtained after the transformation method.
	 * @return Array            $json_ld_data_array replaced with key and value.
	 */
	private static function replace_jsonld_based_on_type( $json_ld_data_array, $key, $value ) {
		if ( self::should_be_replaced_on_one_entity( $key, $value ) ) {
			// Replace it only on first element.
			if ( count( $json_ld_data_array ) > 0 ) {
				$json_ld_data_array[0][ $key ] = $value;
			}
			return $json_ld_data_array;
		}
		else {
			return self::iterate_through_items_and_replace( $json_ld_data_array, $key, $value );
		}
	}
	/**
	 * Iterate through all the json_ld_data_array and replace the key and value
	 *
	 * @param Array             $json_ld_data_array Array of json ld items.
	 * @param String            $key The key of the property.
	 * @param String|Array|NULL $value The Value obtained after the transformation method.
	 * @return Array            $json_ld_data_array replaced with key and value.
	 */
	private static function iterate_through_items_and_replace( $json_ld_data_array, $key, $value ) {
		foreach ( $json_ld_data_array as &$jsonld_data ) {
			$jsonld_data[ $key ] = $value;
		}
		return $json_ld_data_array;
	}
	/**
	 * Returns Json-LD data after applying transformation functions.
	 *
	 * @return Array Array of json-ld data.
	 */
	public function get_jsonld_data() {
		// Validate the post id here.
		$this->validator->validate( $this->post_id );
		$json_ld_data_array = $this->jsonld_data;
		$properties         = $this->validator->get_valid_properties();
		foreach ( $properties as $property ) {
			$transform_instance = $this->transform_functions_registry->get_transform_function( $property['transform_function'] );
			if ( null !== $transform_instance ) {
				$transformed_data   = $transform_instance->get_transformed_data( $this->post_id, $property );
				$json_ld_data_array = self::replace_jsonld_based_on_type(
					$json_ld_data_array,
					$transformed_data['key'],
					$transformed_data['value']
				);
			}
			else {
				$json_ld_data_array = self::replace_jsonld_based_on_type(
					$json_ld_data_array,
					$property['property_name'],
					$property['field_name']
				);
			}
		}
		return $json_ld_data_array;
	}

}
