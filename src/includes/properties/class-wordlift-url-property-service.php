<?php

class Wordlift_Url_Property_Service {

	const META_KEY = 'wl_schema_url';

	public function get( $post_id, $meta_key ) {

		$value = get_post_meta( $post_id, $meta_key );

		if ( 0 === count( $value ) ) {
			return NULL;
		}

		$processed = array_map( function ( $item ) use ( $post_id ) {

			return '<permalink>' === $item ? get_permalink( $post_id ) : $item;
		}, $value );

		if ( 1 === count( $processed ) ) {
			return $processed[0];
		}

		return $processed;
	}

}
