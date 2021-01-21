<?php

namespace Wordlift\Entity;
/**
 * @since 3.28.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */
class Entity_No_Index_Flag {


	const YOAST_POST_NO_INDEX_FLAG = '_yoast_wpseo_meta-robots-noindex';

	public function __construct() {

		add_action( 'wp_insert_post', function ( $post_id, $post, $update ) {

			$post_type = get_post_type( $post_id );

			if ( $post_type !== \Wordlift_Entity_Service::TYPE_NAME ) {
				// Dont set this flag for any other post types.
				return;
			}

			// We need to set this flag only on entity creation.
			if (  $update ) {
				// if the post is updated, remove this flag
				delete_post_meta( $post_id, self::YOAST_POST_NO_INDEX_FLAG );

			} else {
				// If it is created first time, add the flag.
				update_post_meta( $post_id, self::YOAST_POST_NO_INDEX_FLAG, 1 );
			}

		}, PHP_INT_MAX, 3 );

	}

}

