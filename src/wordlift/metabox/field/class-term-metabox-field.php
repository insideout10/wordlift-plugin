<?php
/**
 * @since 3.31.6
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * This class handles save / get data for all the term fields.
 */
namespace Wordlift\Metabox\Field;

class Term_Metabox_Field  {

	public static function get_data( $term_id, $meta_key ) {
		return get_term_meta( $term_id, $meta_key );
	}

	public static function save_data( $term_id, $meta_key, $cardinality, $values ) {

		// Take away old values.
		delete_term_meta( $term_id, $meta_key );

		// insert new values, respecting cardinality.
		$single = ( 1 === $cardinality );
		foreach ( $values as $value ) {
			// To avoid duplicate values
			delete_term_meta( $term_id, $meta_key, $value );
			add_term_meta( $term_id, $meta_key, $value, $single );
		}

	}

}