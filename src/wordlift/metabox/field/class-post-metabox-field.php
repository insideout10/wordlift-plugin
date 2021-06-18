<?php
/**
 * @since 3.31.6
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * This class handles save / get data for all the post fields.
 */

namespace Wordlift\Metabox\Field;

class Post_Metabox_Field {

	public function get_data( $post_id, $meta_key ) {
		return get_post_meta( $post_id, $meta_key );
	}

	public static function save_data( $post_id, $meta_key, $cardinality, $values ) {
		$entity_id = $post_id;
		// Take away old values.
		delete_post_meta( $entity_id, $meta_key );
		// insert new values, respecting cardinality.
		$single = ( 1 === $cardinality );
		foreach ( $values as $value ) {
			// To avoid duplicate values
			delete_post_meta( $entity_id, $meta_key, $value );
			$meta_id = add_post_meta( $entity_id, $meta_key, $value, $single );
		}
	}


}