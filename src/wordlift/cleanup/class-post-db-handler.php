<?php

/**
 * Provide a way to clean up entity annotations from post content.
 *
 * @since 3.34.1
 * @see https://github.com/insideout10/wordlift-plugin/issues/1522
 */

namespace Wordlift\Cleanup;

class Post_Db_Handler {

	public function update_post_content( $post_content, $post_id ) {

		$result = wp_update_post( array(
			'ID' => $post_id,
			'post_content' => $post_content,
		) );

		if ( $result instanceof \WP_Error ) {
			return false;
		}

		return true;
	}
}
