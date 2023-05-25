<?php
/**
 * This file provides the install 3.27.0 procedure.
 *
 * This procedure runs the one-time routine needed for issue related to
 * entities saved from Block Editor have no slug.
 *
 * @see https://github.com/insideout10/wordlift-plugin/issues/1098
 *
 * @author Akshay Raje <akshay@wordlift.io>
 * @since 3.27.0
 * @package Wordlift/install
 */

class Wordlift_Install_3_27_0 extends Wordlift_Install {

	/**
	 * {@inheritdoc}
	 */
	protected static $version = '3.27.0';

	/**
	 * @inheritDoc
	 */
	public function install() {
		global $wpdb;

		$posts_with_empty_post_name = $wpdb->get_results(
			"    SELECT ID, post_title 
		    FROM $wpdb->posts
		    WHERE post_type = 'entity'
		    AND post_status = 'publish' 
		    AND post_name = ''"
		);

		foreach ( $posts_with_empty_post_name as $post ) {
			wp_update_post(
				array(
					'ID'        => $post->ID,
					'post_name' => sanitize_title( $post->post_title ),
				)
			);
		}

	}

}
