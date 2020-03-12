<?php
/**
 * This file hooks to the metaboxes, replaces the post excerpt metabox with
 * the custom one.
 *
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * @since 3.26.0
 *
 * @package Wordlift
 * @subpackage Wordlift\Post_Excerpt
 */

namespace Wordlift\Post_Excerpt;

use Wordlift\Scripts\Scripts_Helper;

final class Post_Excerpt_Meta_Box_Adapter {

	public function enqueue_post_excerpt_scripts() {
		Scripts_Helper::enqueue_based_on_wordpress_version(
			'wl-post-excerpt',
			plugin_dir_url( dirname( dirname( __FILE__ ) ) ) . 'js/dist/post-excerpt',
			array( 'react', 'react-dom', 'wp-polyfill' ),
			TRUE
		);
		wp_enqueue_style(
			'wl-post-excerpt-style',
			plugin_dir_url( dirname( dirname( __FILE__ ) ) ) . 'js/dist/post-excerpt.css',
			array()
		);
		wp_localize_script( 'wl-post-excerpt',
			'_wlExcerptSettings',
			$this->get_post_excerpt_translations() );
	}

	public function get_post_excerpt_translations() {
		return array(
			'orText'         => __( 'Or use WordLift suggested post excerpt', 'wordlift' ),
			'generatingText' => __( 'Generating excerpt...', 'wordlift' ),
			'restUrl'        => get_rest_url( NULL, WL_REST_ROUTE_DEFAULT_NAMESPACE . '/post-excerpt' ),
			'nonce'          => wp_create_nonce( 'wp_rest' ),
			'postId'         => get_the_ID(),
		);
	}
}