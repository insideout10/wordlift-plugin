<?php

/**
 * @since 3.8.0
 */
class Wordlift_Url_Property_Service extends Wordlift_Simple_Property_Service {

	const META_KEY = 'wl_schema_url';

	public function get( $post_id, $meta_key ) {

		// Convert <permalink> in actual permalink values.
		return array_map( function ( $item ) use ( $post_id ) {

			return '<permalink>' === $item ? get_permalink( $post_id ) : $item;
		}, parent::get( $post_id, $meta_key ) );
	}

}
