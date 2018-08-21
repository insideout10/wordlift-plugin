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

		$meta = get_post_meta( $post_id );

//		var_dump( $meta );

		$wl_meta = array_reduce( array_keys( $meta ), function ( $carry, $key ) use ( $meta ) {

			if ( - 1 === strpos( $key, self::PREFIX ) ) {
				return $carry;
			}
			$matches = array();
			preg_match( $key, "/_wl_prop_(\w+)_(\d+)_(\w+)/i", $matches );
//			var_dump( $matches );

			return $carry;
		}, array() );

		return $meta;
	}

	public function get_keys( $post_id ) {

		$meta = get_post_meta( $post_id );

		$keys = array_filter( array_keys( $meta ), function ( $key ) {
			return 0 === strpos( $key, self::PREFIX );
		} );

		return $keys;
	}


}