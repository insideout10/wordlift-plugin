<?php
/**
 * Properties: Duration Property.
 *
 * This file is part of the properties group of files which handle the duration
 * property of entities.
 *
 * @since      3.14.0
 * @package    Wordlift
 * @subpackage Wordlift/includes/properties
 */

/**
 * Process references to time durations, convert them into proper  ISO 8601 duration format.
 *
 * @since      3.14.0
 * @package    Wordlift
 * @subpackage Wordlift/includes/properties
 */
class Wordlift_Duration_Property_Service extends Wordlift_Simple_Property_Service {

	/**
	 * {@inheritdoc}
	 */
	public function get( $id, $meta_key, $type ) {

		// Get the values and filter out the empty ones (or the ones with 00:00).
		$values = array_filter(
			parent::get( $id, $meta_key, $type ),
			function ( $item ) {
				return ! empty( $item ) && '00:00' !== $item;
			}
		);

		/*
		 * Map the value in the meta
		 * The UI for the meta date enable two forms, a number of minutes
		 * or an h:mm format.
		 * Both needs to be adjusted to the iso format.
		 */
		return array_map(
			function ( $value ) {
				return 'PT' . str_replace( ':', 'H', $value ) . 'M';
			},
			$values
		);
	}

}
