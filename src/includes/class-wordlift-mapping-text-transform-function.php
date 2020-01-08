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
class Wordlift_Mapping_Text_Transform_Function implements Wordlift_Mapping_Transform_Function {

	/**
	 * Returns Name.
	 *
	 * @inheritdoc
	 */
	public function get_name() {
		return 'text-transform-function';
	}

	/**
	 * Returns Label.
	 *
	 * @inheritdoc
	 */
	public function get_label() {
		return __( 'Text Transform function', 'wordlift' );
	}
	/**
	 * Returns transformed data.
	 *
	 * @inheritdoc
	 */
	public function transform_data( $post_id, $property_data ) {
		// Do 1 to 1 mapping and return result.
		return array(
			'key'   => $property_data['property_name'],
			'value' => $property_data['field_text'],
		);
	}
}

