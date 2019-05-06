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

	const META_KEY = 'wl_schema_url';

	public function get( $post_id, $meta_key ) {

		// Get the meta values and push the <permalink> to
		// ensure that default url will be added to the schema:url's.
		$urls = array_unique( // We need to avoid duplicates.
			array_merge(
				parent::get( $post_id, $meta_key ), // Get default meta values
				array(
					'<permalink>', // Add the permalink in case it was removed.
				)
			)
		);

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
		}, $urls );
	}

}
