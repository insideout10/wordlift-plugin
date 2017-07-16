<?php
/**
 * This file is part of the properties group of files which handle the duration
 * property of entities.
 *
 * @since   3.14.0
 * @package Wordlift
 */

/**
 * Process references to time durations, convert them into proper  ISO 8601 duration format.
 *
 * @since 3.14.0
 */
class Wordlift_Duration_Property_Service extends Wordlift_Simple_Property_Service {

	/**
	 * {@inheritdoc}
	 */
	function get( $post_id, $meta_key ) {

		/*
		 * Map the value in the meta
		 * The UI for the meta date enable two forms, a number of minutes
		 * or an h:mm format.
		 * Both needs to be adjusted to the iso format.
		 */
		return array_map( function ( $value ) {
			return 'PT' . str_replace( ':', 'H', $value ) . 'M';
		}, parent::get( $post_id, $meta_key ) );
	}
}
