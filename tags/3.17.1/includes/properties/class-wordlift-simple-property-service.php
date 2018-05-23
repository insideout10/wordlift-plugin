<?php

// Require the Wordlift_Property_Not_Found class.
require_once( "class-wordlift-property-not-found.php" );
require_once( "class-wordlift-property-entity-reference.php" );

/**
 * A property service that just returns the value stored in WP's meta.
 *
 * @since 3.8.0
 */
class Wordlift_Simple_Property_Service {

	/**
	 * The meta key for this property service.
	 *
	 * @since 3.8.0
	 */
	const META_KEY = '*';

	/**
	 * Get the property value for the specified post id and meta with the specified key.
	 *
	 * @since 3.8.0
	 *
	 * @param int $post_id The post id.
	 * @param string $meta_key The meta key.
	 *
	 * @return mixed|null The property value.
	 */
	public function get( $post_id, $meta_key ) {

		// Get the value stored in WP.
		return get_post_meta( $post_id, $meta_key );
	}

}
