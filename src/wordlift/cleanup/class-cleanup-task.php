<?php

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

	  /*
		return get_posts(
			array(
				'fields'      => 'ids',
				'numberposts' => $limit,
				'offset'      => $offset,
				'post_type'   => Meta_Helper::SW_LINK_SUPPORTED_POST_TYPES,
				'post_status' => 'publish',
				'meta_query'  => array(
					array(
						'key' => Meta_Helper::SW_POST_PROCESSED_META_KEY,
						'compare' => 'NOT EXISTS'
					)
				)
			)
		);
		*/

	}

	function count_items() {
		/*
		return count( get_posts(
			array(
				'fields'      => 'ids',
				'post_type'   => Meta_Helper::SW_LINK_SUPPORTED_POST_TYPES,
				'post_status' => 'publish',
				'numberposts' => -1,
			)
		) ); */

	}

	function process_item( $item ) {
		/*
		$post_id  = (int) $item;
		$instance = Software_App_Post_Handler::get_instance();
		$instance->process_post_id( $post_id );
		*/
	}
}


