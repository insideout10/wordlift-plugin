<?php

namespace Wordlift\Entity;

/**
 * @since 3.28.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */
class Entity_No_Index_Flag {

	const YOAST_POST_NO_INDEX_FLAG = '_yoast_wpseo_meta-robots-noindex';

	public function __construct() {

		$no_index_flag = self::YOAST_POST_NO_INDEX_FLAG;

		add_action(
			'wp_insert_post',
			function ( $post_id, $post, $update ) use ( $no_index_flag ) {

				$post_type = get_post_type( $post_id );

				if ( \Wordlift_Entity_Service::TYPE_NAME !== $post_type ) {
					// Don't set this flag for any other post types.
					return;
				}

				// We need to set this flag only on entity creation.
				if ( ! $update ) {
					update_post_meta( $post_id, $no_index_flag, 1 );
				}

			},
			PHP_INT_MAX,
			3
		);

		add_action(
			'post_updated',
			function ( $post_id ) use ( $no_index_flag ) {
				if ( get_post_type( $post_id ) !== \Wordlift_Entity_Service::TYPE_NAME ) {
					return;
				}
				// if the post is updated, remove this flag
				delete_post_meta( $post_id, $no_index_flag );
			},
			PHP_INT_MAX
		);

	}

}

