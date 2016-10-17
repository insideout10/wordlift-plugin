<?php

class Wordlift_Simple_Property_Service {

	const META_KEY = '*';

	public function get( $post_id, $meta_key, $expand = TRUE ) {

		$value = get_post_meta( $post_id, $meta_key );

		if ( 0 === count( $value ) ) {
			return NULL;
		}

		if ( 1 === count( $value ) ) {
			return $value[0];
		}

		return $value;
	}

}
