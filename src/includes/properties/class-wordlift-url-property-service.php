<?php
/**
 * Services: URL Property.
 *
 * @since 3.8.0
 * @package Wordlift
 * @subpackage Wordlift/includes/properties
 */

use Wordlift\Object_Type_Enum;

/**
 * Define the Wordlift_Url_Property_Service class.
 *
 * @since 3.8.0
 */
class Wordlift_Url_Property_Service extends Wordlift_Simple_Property_Service {

	/**
	 * The meta key for the schema:url property.
	 */
	const META_KEY = 'wl_schema_url';

	/**
	 * Get the URLs associated with the post.
	 *
	 * @param int    $id The post id.
	 * @param string $meta_key The meta key.
	 *
	 * @return array An array of URLs.
	 */
	public function get( $id, $meta_key, $type ) {

		// Get the meta values and push the <permalink> to
		// ensure that default url will be added to the schema:url's.
		/*
		 * Do not add `<permalink>` if one or more URLs have been provided by the editor.
		 *
		 * @see https://github.com/insideout10/wordlift-plugin/issues/913
		 *
		 * @since 3.21.1
		 */
		$urls = parent::get( $id, $meta_key, $type );
		$urls = array_filter( $urls ? $urls : array( '<permalink>' ) );

		// Convert <permalink> in actual permalink values.
		return array_map(
			function ( $item ) use ( $id, $type ) {
				/*
					* If `<permalink>` get the production permalink.
					*
					* @since 3.20.0
					*
					* @see https://github.com/insideout10/wordlift-plugin/issues/850.
					*/

				if ( '<permalink>' !== $item && 'http://<permalink>/' !== $item ) {
					  return $item;
				}

				// Permalinks.
				switch ( $type ) {
					case Object_Type_Enum::POST:
						return Wordlift_Post_Adapter::get_production_permalink( $id );
					case Object_Type_Enum::TERM:
						return get_term_link( $id );
					default:
						return $item;
				}
			},
			array_unique( $urls )
		);
	}

}
