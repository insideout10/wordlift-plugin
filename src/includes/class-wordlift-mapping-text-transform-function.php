<?php
require_once 'class-wordlift-mapping-transform-function.php';
/**
 * Define the Wordlift_Mapping_Acf_Transform_Function Class
 *
 * @since   3.25.0
 * @package Wordlift
 */

/**
 * This class extends the interface { @link \Wordlift_Mapping_Transform_Function }
 * and creates transform function.
 *
 * @since 3.25.0
 */
class Wordlift_Mapping_Text_Transform_Function extends Wordlift_Mapping_Transform_Function {

	/**
	 * Returns Name.
	 *
	 * {@inheritdoc}
	 */
	public function get_name() {
		return 'text_transform_function';
	}

	/**
	 * Returns Label.
	 *
	 * {@inheritdoc}
	 */
	public function get_label() {
		return __( 'Text Transform function', 'wordlift' );
	}
	/**
	 * Returns data from data source.
	 *
	 * {@inheritdoc}
	 */
	public function get_data_from_data_source( $post_id, $property_data ) {
		// Do 1 to 1 mapping and return result.
		return array(
			'key'   => $property_data['property_name'],
			'value' => $property_data['field_name'],
		);
	}

	/**
	 * {@inheritdoc}
	 */
	public function map_data_to_schema_properties( $data ) {
		return $data;
	}
}

