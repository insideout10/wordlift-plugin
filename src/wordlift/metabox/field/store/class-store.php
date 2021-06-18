<?php
namespace Wordlift\Metabox\Field\Store;

interface Store {

	public static function get_data( $post_id, $meta_key );

	public static function save_data( $post_id, $meta_key, $cardinality, $values );

}