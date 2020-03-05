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

final class Post_Excerpt_Meta_Box_Adapter {

	/**
	 * Key used by wordpress to add the excerpt meta box in
	 * the $wp_meta_boxes global variable.
	 */
	const POST_EXCERPT_META_BOX_KEY = 'postexcerpt';

	/**
	 * @var callable|null The default callback used by wordpress to
	 * echo the post_excerpt contents, defaults to null.
	 */
	private $wordpress_excerpt_callback;

	public function __construct() {
		$this->wordpress_excerpt_callback = null;
	}

	/**
	 * Removes the registered post excerpt metabox.
	 */
	private function remove_default_post_excerpt_meta_box() {
		remove_meta_box(self::POST_EXCERPT_META_BOX_KEY, get_current_screen(), 'normal');
	}

	/**
	 * Adds the custom post excerpt metabox.
	 */
	private function add_custom_post_excerpt_meta_box() {
		add_meta_box(
			self::POST_EXCERPT_META_BOX_KEY,
			__('Post Excerpt', 'wordlift'),
			array($this, 'print_wordlift_custom_post_excerpt_box')
		);
	}

	public function print_wordlift_custom_post_excerpt_box() {
		global $post;
		// Print the wordpress template for post excerpt
		post_excerpt_meta_box($post);
		// Invoke our call back to add additional html.
		echo "<p>additional content</p>";
	}

	/**
	 * Replaces the default post excerpt meta box with custom post excerpt meta box.
	 */
	public function replace_post_excerpt_meta_box() {
		$this->remove_default_post_excerpt_meta_box();
		$this->add_custom_post_excerpt_meta_box();
	}
}