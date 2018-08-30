<?php
/**
 * Created by PhpStorm.
 * User: david
 * Date: 16.08.18
 * Time: 16:13
 */

class Wordlift_Schemaorg_Property_Service {

	const PREFIX = "_wl_prop_";

	private static $instance;

	public function __construct() {

		self::$instance = $this;

	}

	public static function get_instance() {

		return self::$instance;
	}

	public function get_all( $post_id ) {

		$post_metas = get_post_meta( $post_id );

		$props = array();
		foreach ( $post_metas as $key => $values ) {
			$matches = array();
			if ( 1 === preg_match( '/' . self::PREFIX . '(\w+)_([\w-]+)_(\w+)/i', $key, $matches ) ) {
				$name = $matches[1];
				$uuid = $matches[2];
				$key  = $matches[3];

				$props[ $name ][ $uuid ][ $key ] = $values[0];
			}
		}

		// Remove the UUIDs.
		foreach ( $props as $name => $instance ) {
			foreach ( $instance as $uuid => $keys ) {
				$props[ $name ] = array_values( $instance );
			}
		}

		return $props;
	}

	/**
	 * Get the meta keys for Schema.org properties associated with the specified post.
	 *
	 * @since 3.20.0
	 *
	 * @param int $post_id The post id.
	 *
	 * @return array An array of meta keys.
	 */
	public function get_keys( $post_id ) {

		// Get all the post metas to remove the `_wl_prop` ones.
		$post_meta = get_post_meta( $post_id );

		// Get the keys.
		$post_meta_keys = array_unique( array_keys( $post_meta ) );

		// Get only the `_wl_prop` keys.
		//
		// Keep PHP 5.3 compatibility, `self` in closures doesn't exist.
		$prefix    = self::PREFIX;
		$prop_keys = array_filter( $post_meta_keys, function ( $item ) use ( $prefix ) {
			return 0 === strpos( $item, $prefix );
		} );

		return $prop_keys;
	}


}