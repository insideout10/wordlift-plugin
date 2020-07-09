<?php
/**
 * Services: URL Property.
 *
 * @since 3.8.0
 * @package Wordlift
 * @subpackage Wordlift/includes/properties
 */

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
	 * @param int    $post_id The post id.
	 * @param string $meta_key The meta key.
	 *
	 * @return array An array of URLs.
	 */
	public function get( $post_id, $meta_key ) {

		// Get the meta values and push the <permalink> to
		// ensure that default url will be added to the schema:url's.
		/*
		 * Do not add `<permalink>` if one or more URLs have been provided by the editor.
		 *
		 * @see https://github.com/insideout10/wordlift-plugin/issues/913
		 *
		 * @since 3.21.1
		 */
		$urls = parent::get( $post_id, $meta_key ) ?: array( '<permalink>' );

		// Convert <permalink> in actual permalink values.
		return array_map( function ( $item ) use ( $post_id ) {
			/*
			 * If `<permalink>` get the production permalink.
			 *
			 * @since 3.20.0
			 *
			 * @see https://github.com/insideout10/wordlift-plugin/issues/850.
			 */
			return '<permalink>' === $item ? Wordlift_Post_Adapter::get_production_permalink( $post_id ) : $item;
		}, array_unique( $urls ) );
	}

}
