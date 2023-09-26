<?php
/**
 * Properties: Double Property.
 *
 * @since      3.14.0
 * @package    Wordlift
 * @subpackage Wordlift/includes/properties
 */

/**
 * Define the {@link Wordlift_Double_Property_Service} class.
 *
 * @since      3.14.0
 * @package    Wordlift
 * @subpackage Wordlift/includes/properties
 */
class Wordlift_Double_Property_Service extends Wordlift_Simple_Property_Service {

	/**
	 * @inheritdoc
	 */
	public function get( $id, $meta_key, $type ) {

		// Map the result to a numeric value when possible.
		return array_map(
			function ( $value ) {
				return is_numeric( $value ) ? (float) $value : $value;
			},
			parent::get( $id, $meta_key, $type )
		);
	}

}
