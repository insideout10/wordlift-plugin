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
			if ( 1 === preg_match( "/_wl_prop_(\w+)_([\w-]+)_(\w+)/i", $key, $matches ) ) {
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

	public function get_keys( $post_id ) {

		$meta = get_post_meta( $post_id );

		$keys = array_filter( array_keys( $meta ), function ( $key ) {
			return 0 === strpos( $key, self::PREFIX );
		} );

		return $keys;
	}


}