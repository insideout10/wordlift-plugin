<?php
/**
 * @since 3.32.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */

namespace Wordlift\Metabox\Field\Store;

interface Store {

	public static function get_data( $post_id, $meta_key );

	public static function save_data( $post_id, $meta_key, $cardinality, $values );

	public static function delete_meta( $id, $meta_key );

	public static function add_meta( $id, $meta_key, $meta_value, $is_unique );

}
