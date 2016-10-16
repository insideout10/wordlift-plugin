<?php

class Wordlift_Location_Property_Service extends Wordlift_Simple_Property_Service {

	const META_KEY = 'wl_location';

	public function get( $post_id, $meta_key ) {

		$value = get_post_meta( $post_id, $meta_key );

		if ( 0 === count( $value ) ) {
			return NULL;
		}

		$value = array_map( function ( $item ) {

			return Wordlift_Location_Property_Service::expand( $item );
		}, $value );

		if ( 1 === count( $value ) ) {
			return $value[0];
		}

		return $value;
	}

	public function expand( $post_id ) {

		return Wordlift_Jsonld_Service::get_instance()->get_by_id( $post_id );
	}

}
