<?php
/**
 * @since 3.32.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * This class handles save / get data for all the post fields.
 */

namespace Wordlift\Metabox\Field\Store;

class Post_Store implements Store {

	public static function get_data( $post_id, $meta_key ) {
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
			add_post_meta( $entity_id, $meta_key, $value, $single );
		}
	}

	public static function delete_meta( $id, $meta_key ) {
		delete_post_meta( $id, $meta_key );
	}

	public static function add_meta( $id, $meta_key, $meta_value, $is_unique ) {
		add_post_meta( $id, $meta_key, $meta_value, $is_unique );
	}
}
