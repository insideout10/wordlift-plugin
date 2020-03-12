<?php
/**
 * This file hooks to the metaboxes, replaces the post excerpt metabox with
 * the custom one.
 *
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * @since 3.23.0
 *
 * @package Wordlift
 * @subpackage Wordlift\Post_Excerpt
 */

namespace Wordlift\Post_Excerpt;

use Wordlift\Scripts\Scripts_Helper;

final class Post_Excerpt_Meta_Box_Adapter {

	/**
	 * Key used by wordpress to add the excerpt meta box in
	 * the $wp_meta_boxes global variable.
	 */
	const POST_EXCERPT_META_BOX_KEY = 'postexcerpt';

	/**
	 * Div id used for adding the additional content
	 * to the wordpress excerpt meta box.
	 */
	const WORDLIFT_EXCERPT_DIV_ID = 'wl-custom-excerpt-wrapper';

	/**
	 * @var callable|null The default callback used by wordpress to
	 * echo the post_excerpt contents, defaults to null.
	 */
	private $wordpress_excerpt_callback;

	public function __construct() {
		$this->wordpress_excerpt_callback = NULL;
	}

	public function print_wordlift_custom_post_excerpt_box() {
		global $post;
		post_excerpt_meta_box($post);
		echo "<div id='" . self::WORDLIFT_EXCERPT_DIV_ID . "'></div>";
	}

	/**
	 * Replaces the default post excerpt meta box with custom post excerpt meta box.
	 */
	public function replace_post_excerpt_meta_box() {
		$this->remove_default_post_excerpt_meta_box();
		$this->add_custom_post_excerpt_meta_box();
		$this->enqueue_post_excerpt_scripts();
	}

	/**
	 * Removes the registered post excerpt metabox.
	 */
	private function remove_default_post_excerpt_meta_box() {
		remove_meta_box( self::POST_EXCERPT_META_BOX_KEY, get_current_screen(), 'normal' );
	}

	/**
	 * Adds the custom post excerpt metabox.
	 */
	private function add_custom_post_excerpt_meta_box() {
		add_meta_box(
			self::POST_EXCERPT_META_BOX_KEY,
			__( 'Post Excerpt', 'wordlift' ),
			array( $this, 'print_wordlift_custom_post_excerpt_box' )
		);
	}

	private function enqueue_post_excerpt_scripts() {
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