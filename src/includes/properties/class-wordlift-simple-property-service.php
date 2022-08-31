<?php

use Wordlift\Object_Type_Enum;

require_once 'class-wordlift-property-not-found.php';
require_once 'class-wordlift-property-entity-reference.php';

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
	 * @param int                   $id The post id.
	 * @param string                $meta_key The meta key.
	 *
	 * @param $type int Post or Term
	 *
	 * @return mixed|null The property value.
	 * @since 3.8.0
	 */
	public function get( $id, $meta_key, $type ) {

		if ( Object_Type_Enum::POST === $type ) {
			// Get the value stored in WP.
			return get_post_meta( $id, $meta_key );
		} elseif ( Object_Type_Enum::TERM === $type ) {
			return get_term_meta( $id, $meta_key );
		}
		return null;
	}

}
