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
			// We need to set this flag only on entity creation.
			if ( ! $update ) {
				update_post_meta( $post_id, self::YOAST_POST_NO_INDEX_FLAG, 1 );
			}

		}, 10, 3 );

	}

}

