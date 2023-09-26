<?php

/**
 * Provide a way to clean up entity annotation from post content.
 *
 * @since 3.34.1
 * @see https://github.com/insideout10/wordlift-plugin/issues/1522
 */

namespace Wordlift\Cleanup;

use Wordlift\Content\Wordpress\Wordpress_Content_Id;
use Wordlift\Content\Wordpress\Wordpress_Content_Service;

class Post_Handler {

	/**
	 * Process post.
	 *
	 * @param int $post_id
	 */
	public static function fix( $post_id ) {
		// 3 cases:
		// 1. item id w/o the base URI: itemid="/entity/marketing"
		// 2. item id w/ domain outside the scope of dataset URI: itemid="https://data.wordlift.io/wl95583/entity/emailing"
		// 3. item id w/ domain within the scope of the dataset URI, but non existent.
		// 4. should we manipulate also the `wp:wordlift/classification` block?

		$post             = get_post( $post_id );
		$new_post_content = preg_replace_callback(
			'@<(\w+)[^<]*class="([^"]*)"\sitemid="([^"]+)"[^>]*>(.*?)</\1>@i',
			array( get_class(), 'fix_annotations' ),
			$post->post_content,
			- 1,
			$count
		);

		if ( 0 === $count || $new_post_content === $post->post_content ) {
			// Bail out if the regex doesn't match or if the post content didn't change.
			return;
		}

		wp_update_post(
			array(
				'ID'           => $post_id,
				'post_content' => $new_post_content,
			)
		);

	}

	public static function fix_annotations( $args ) {
		// Make the item id relative.
		if ( 0 === preg_match( '@(?:https?://[^/]+/[^/]+)?/?(.*)@', $args[3], $matches ) ) {
			return $args[4];
		}

		$item_id = $matches[1];

		// Bail out if the item id is empty.
		if ( empty( $item_id ) ) {
			return $args[4];
		}

		// Find a matching content.
		$content_service = Wordpress_Content_Service::get_instance();
		$content         = $content_service->get_by_entity_id_or_same_as( $item_id );

		if ( ! isset( $content ) ) {
			// No content found return only label.
			return $args[4];
		} else {
			// Get the actual entity id.
			$new_item_id = $content_service->get_entity_id(
				new Wordpress_Content_Id( $content->get_id(), $content->get_object_type_enum() )
			);

			// Replace the incoming entity id with the actual entity id.
			return str_replace( $args[3], $new_item_id, $args[0] );
		}
	}

}
