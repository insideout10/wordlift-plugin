<?php

class Wordlift_Double_Property_Service extends Wordlift_Simple_Property_Service {

	function get( $post_id, $meta_key, $expand = TRUE ) {

		$value = array_map( function ( $value ) {
			return is_numeric( $value ) ? (double) $value : $value;
		}, get_post_meta( $post_id, $meta_key ) );

		if ( 0 === count( $value ) ) {
			return NULL;
		}

		if ( 1 === count( $value ) ) {
			return (double) $value[0];
		}

		return $value;

	}


}