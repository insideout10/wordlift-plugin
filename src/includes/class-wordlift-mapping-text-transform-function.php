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
	 * {@inheritdoc}
	 */
	public function map_data_to_schema_properties( $data ) {
		return $data;
	}
}

