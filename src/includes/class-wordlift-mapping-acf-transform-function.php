<?php
require_once 'intf-wordlift-mapping-transform-function.php';
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
class Wordlift_Mapping_Acf_Transform_Function implements Wordlift_Mapping_Transform_Function {

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
	 * Returns transformed data, returns null in the value if
	 * the ACF field doesn't exist.
	 *
	 * @inheritdoc
	 */
	public function transform_data( $post_id, $property_data ) {
		$key   = $property_data['property_name'];
		$value = null;
		// Check ACF is loaded.
		if ( function_exists( 'get_field_object' ) ) {
			$value = get_field( $key, $post_id );
		}
		return array(
			'key'   => $key,
			'value' => $value,
		);
	}
}

