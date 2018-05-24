<?php

/**
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
			return '<permalink>' === $item ? get_permalink( $post_id ) : $item;
		}, $urls );
	}

}
