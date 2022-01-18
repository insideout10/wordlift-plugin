<?php

/**
 * Provide a way to cleanup entity annotation from post content.
 *
 * @since 3.34.1
 * @see https://github.com/insideout10/wordlift-plugin/issues/1522
 */

namespace Wordlift\Cleanup;

use Wordlift\Tasks\Task;

class Cleanup_Task implements Task {

	function get_id() {
		return 'wl_entity_annotation_cleanup';
	}

	function get_label() {
		return __( 'Entity Annotation Cleanup', 'wordlift' );
	}

	function list_items( $limit = 15, $offset = 0 ) {

		return get_posts(
			array(
				'fields'      => 'ids',
				'numberposts' => $limit,
				'offset'      => $offset,
				'post_type'   => 'post',
				'post_status' => 'publish',

			)
		);

	}

	function count_items() {

		return count( get_posts(
			array(
				'fields'      => 'ids',
				'post_type'   => 'post',
				'post_status' => 'publish',
				'numberposts' => - 1,
			)
		) );

	}

	function process_item( $item ) {
		$post_id                                = (int) $item;
		$entity_annotation_cleanup_post_handler = Post_Handler::get_instance();
		$entity_annotation_cleanup_post_handler->process_post( $post_id );

	}
}


