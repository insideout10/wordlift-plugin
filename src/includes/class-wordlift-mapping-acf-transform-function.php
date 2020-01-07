<?php
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
class Wordlift_Mapping_Acf_Transform_Function extends Wordlift_Mapping_Transform_Function {

	/**
	 * Returns Name.
	 *
	 * @inheritdoc
	 */
	public function get_name() {
		return 'acf-transform-function';
	}

	/**
	 * Returns Label.
	 *
	 * @inheritdoc
	 */
	public function get_label() {
		return __( 'ACF Transform function', 'wordlift' );
	}
	/**
	 * Returns transformed data.
	 *
	 * @param Array $property_data Property Data.
	 * @inheritdoc
	 */
	public function transform_data( $property_data ) {

	}
}

