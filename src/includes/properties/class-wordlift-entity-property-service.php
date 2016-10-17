<?php

class Wordlift_Entity_Property_Service extends Wordlift_Simple_Property_Service {

	public function get( $post_id, $meta_key, $expand = TRUE ) {

		if ( ! $expand ) {
			return NULL;
		}

		$value = array_map( function ( $item ) {
			return Wordlift_Entity_Property_Service::expand( $item );
		}, get_post_meta( $post_id, $meta_key ) );

		if ( 0 === count( $value ) ) {
			return NULL;
		}

		if ( 1 === count( $value ) ) {
			return $value[0];
		}

		return $value;
	}

	public function expand( $post_id ) {

		return Wordlift_Jsonld_Service::get_instance()
		                              ->get_by_id( $post_id, FALSE );
	}

}
