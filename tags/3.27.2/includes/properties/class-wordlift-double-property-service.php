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
	function get( $post_id, $meta_key ) {

		// Map the result to a numeric value when possible.
		return array_map( function ( $value ) {
			return is_numeric( $value ) ? (double) $value : $value;
		}, parent::get( $post_id, $meta_key ) );
	}

}
