<?php
/**
 * Converters: Abstract cached Post to JSON-LD Converter.
 *
 * An abstract converter which provides basic post conversion.
 *
 * @since      3.16.0
 * @package    Wordlift
 * @subpackage Wordlift/includes
 */

/**
 * Define the {@link Wordlift_Abstract_Cached_Post_To_Jsonld_Converter} class.
 *
 * @since      3.16.0
 * @package    Wordlift
 * @subpackage Wordlift/includes
 */
abstract class Wordlift_Abstract_Cached_Post_To_Jsonld_Converter extends Wordlift_Abstract_Post_To_Jsonld_Converter {

	/**
	 * Convert the provided {@link WP_Post} to a JSON-LD array. Any entity reference
	 * found while processing the post is set in the $references array. Uses a caching
	 * layer to retrieve previously computed json-ld and populate the cache with new values when needed.
	 *
	 * @since 3.16.0
	 *
	 * @param int   $post_id    The post id.
	 * @param array $references An array of entity references.
	 *
	 * @return array A JSON-LD array.
	 */
	public function convert( $post_id, &$references = array() ) {

		$cache = Wordlift_Jsonld_Cache_Service::get_instance();
		$values = $cache->get( $post_id );
		if ( false === $values ) { // Nothing in the cache? calculate it and cache.
			$values = array();
			$values['references'] = array();
			$values['jsonld'] = parent::convert( $post_id, $values['references'] );
			if ( null === $values['jsonld'] ) {
				return null;
			}
			
			// If we have referrers which might use this data, we should invalidate them.
			// This comes before saving the cache to avoid problems with circular references.
			$cache->invalidate_referrers( $post_id );

			$cache->set( $post_id, $values, 0 );
		}
		$references = $values['references'];
		return $values['jsonld'];
	}
}
