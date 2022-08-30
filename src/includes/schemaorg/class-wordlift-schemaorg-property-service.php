<?php
/**
 * Services: Schema.org Property Service.
 *
 * Provides read functions to Schema.org properties stored with posts.
 *
 * @since 3.20.0
 * @package Wordlift
 * @subpackage Wordlift/includes/schemaorg
 */

/**
 * Define the Wordlift_Schemaorg_Property_Service class.
 *
 * @since 3.20.0
 */
class Wordlift_Schemaorg_Property_Service {

	/**
	 * The meta key prefix used to store properties. The `_` prefix makes these metas invisible in the
	 * edit screen custom fields metabox.
	 *
	 * @since 3.20.0
	 */
	const PREFIX = '_wl_prop_';

	/**
	 * Create a {@link Wordlift_Schemaorg_Property_Service} instance.
	 *
	 * @since 3.20.0
	 */
	protected function __construct() {

	}

	private static $instance = null;

	/**
	 * Get the singleton instance.
	 *
	 * @return \Wordlift_Schemaorg_Property_Service The singleton instance.
	 * @since 3.20.0
	 */
	public static function get_instance() {

		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Get all the properties bound to the specified post.
	 *
	 * @param int $post_id The post id.
	 *
	 * @return array An array of properties instances keyed by the property name. Each property contains a
	 *  `type` and a `value` and optionally a `language`.
	 * }
	 * @since 3.20.0
	 */
	public function get_all( $post_id ) {

		// Get all the post metas.
		$post_metas = get_post_meta( $post_id );

		// Cycle through them to get the Schema.org properties.
		$props = array();
		foreach ( $post_metas as $key => $values ) {
			$matches = array();

			// We're looking for `_wl_prop_propName_uuid_key`.
			if ( 1 === preg_match( '/' . self::PREFIX . '(\w+)_([\w-]+)_(\w+)/i', $key, $matches ) ) {
				$name = $matches[1];
				$uuid = $matches[2];
				$key  = $matches[3];

				// Record the value.
				$props[ $name ][ $uuid ][ $key ] = $values[0];
			}
		}

		// Remove the UUIDs.
		foreach ( $props as $name => $instance ) {
			foreach ( $instance as $uuid => $keys ) {
				// This way we remove the `uuid`s.
				$props[ $name ] = array_values( $instance );
			}
		}

		// Finally return the props.
		return $props;
	}

	/**
	 * Get the meta keys for Schema.org properties associated with the specified post.
	 *
	 * @param int $post_id The post id.
	 *
	 * @return array An array of meta keys.
	 * @since 3.20.0
	 */
	public function get_keys( $post_id ) {

		// Get all the post metas to remove the `_wl_prop` ones.
		$post_meta = get_post_meta( $post_id );

		// Get the keys.
		$post_meta_keys = array_unique( array_keys( $post_meta ) );

		// Get only the `_wl_prop` keys. `array_values` resets the indexes.
		$prop_keys = array_values(
			array_filter(
				$post_meta_keys,
				function ( $item ) {
					return 0 === strpos( $item, Wordlift_Schemaorg_Property_Service::PREFIX );
				}
			)
		);

		return $prop_keys;
	}

}
