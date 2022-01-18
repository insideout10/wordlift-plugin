<?php
/**
 * Provide a way to clean up entity annotation from post content.
 *
 * @since 3.34.1
 * @see https://github.com/insideout10/wordlift-plugin/issues/1522
 */

namespace WordLift\Cleanup;

use Wordlift_Configuration_Service;
use Wordlift_Entity_Uri_Service;

class Post_Content_Helper {

	public function search_replace_relative_url( $post_content ) {

		$pattern = '|itemid="([^"]+)"|';
		// Match pattern against post content.
		$matches = array();
		preg_match_all( $pattern, $post_content, $matches, PREG_SET_ORDER );

		foreach ( $matches as $match ) {
			// Store item id.
			$item_id = $match[1];

			// Check if URL is relative.
			$is_url_relative = ! strpos( $match[2], 'https://data.wordlift.io/' );
			// Build full entity uri.
			$entity_uri = $is_url_relative ? trailingslashit( Wordlift_Configuration_Service::get_instance()->get_dataset_uri() . $match[1] ) : '';
			// Check if entity exists in the local dataset using uri.
			$local_entity_exists = $this->get_local_entity_exists( $entity_uri );

			// If entity exists in local dataset uri, perform replacement.
			if ( $local_entity_exists ) {
				return str_replace( " itemid=\"$item_id\"", " itemid=\"$entity_uri\"", $post_content );
			}
		}

		return $post_content;
	}


	public function get_local_entity_exists( $entity_uri ) {
		$entity_uri_service = Wordlift_Entity_Uri_Service::get_instance();

		return $entity_uri_service->get_entity( $entity_uri );
	}

}
