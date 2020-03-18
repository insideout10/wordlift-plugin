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

	public function __construct() {
		/**
		 * Order our metabox for all the post types.
		 */
		$post_types = get_post_types();
		foreach ($post_types as $post_type) {
			add_filter( 'get_user_option_meta-box-order_'.$post_type, array( $this, 'order_meta_box' ) );
		}
	}

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
	public $wordpress_excerpt_callback = null;

	/**
	 * Invokes the default callback inside our custom template callback
	 *
	 * @param $post array The post array.
	 */
	public function print_wordlift_custom_post_excerpt_box( $post ) {
		call_user_func( $this->wordpress_excerpt_callback, $post );
		// Invoke our call back to add additional html, the react script will find this id and render the component there.
		echo "<div id='" . self::WORDLIFT_EXCERPT_DIV_ID . "'></div>";
	}

	/**
	 * Replaces the default post excerpt meta box with custom post excerpt meta box.
	 */
	public function replace_post_excerpt_meta_box() {
		global $wp_meta_boxes;
		$post_type             = get_post_type();
		$core_meta_boxes       = $wp_meta_boxes[ $post_type ]["normal"]["core"];
		$post_excerpt_meta_box = $core_meta_boxes[ self::POST_EXCERPT_META_BOX_KEY ];
		$callback              = $post_excerpt_meta_box['callback'];
		/**
		 * do_meta_boxes action is called 3 times by wordpress for
		 * different set of metaboxes, so to prevent overwriting our
		 * callback with null, this check is necessary.
		 */
		if ( $callback !== null && $callback !== array( $this, 'print_wordlift_custom_post_excerpt_box' ) ) {
			$this->wordpress_excerpt_callback = $callback;
			$this->remove_default_post_excerpt_meta_box();
			$this->add_custom_post_excerpt_meta_box();
			$this->enqueue_post_excerpt_scripts();
		}
	}

	/**
	 * Removes the registered post excerpt metabox.
	 */
	private function remove_default_post_excerpt_meta_box() {

		remove_meta_box( self::POST_EXCERPT_META_BOX_KEY, get_current_screen(), 'normal' );

	}

	/**
	 * Deserializes the string, and rewrite the order for post excerpt.
	 *
	 * @param $order array
	 *
	 * @return array
	 */
	private function deserialize_and_rewrite_order($order) {
		$side = explode(',', $order['side']);
		$normal = explode(',', $order['normal']);
		$advanced = explode(',', $order['advanced']);
		$remove_array = array(self::POST_EXCERPT_META_BOX_KEY);

		// We first remove from the side category if it is previously present.
		$side = array_diff($side, $remove_array);
		if ( count($side) === 0) {
			// No boxes present, so add our excerpt box at o th position.
			array_push($side, self::POST_EXCERPT_META_BOX_KEY);
		}
		else {
			// Add custom excerpt metabox at second position
			array_splice( $side, 1, 0, $remove_array );
		}

		// We remove postexcerpt from all other metaboxes category.
		$normal = array_diff($normal, $remove_array);
		$advanced = array_diff($advanced, $remove_array);

		return array(
			'normal' => $normal,
			'side' => $side,
			'advanced' => $advanced
		);
	}

	/**
	 * Orders the post excerpt meta box to display only on sidebar.
	 *
	 * @param $order array
	 *
	 * @return array
	 */
	public function order_meta_box($order) {
		// Remove all from the advanced and normal orders, and place it on the sidebar
		$order = $this->deserialize_and_rewrite_order($order);
		// convert all arrays to string again.
		$order['side'] = implode(',', $order['side']);
		$order['normal'] = implode(',', $order['normal']);
		$order['advanced'] = implode(',', $order['advanced']);
		return $order;
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
			true
		);
		wp_enqueue_style(
			'wl-post-excerpt',
			plugin_dir_url( dirname( dirname( __FILE__ ) ) ) . 'js/dist/post-excerpt.css',
			array()
		);
		wp_localize_script( 'wl-post-excerpt',
			'_wlExcerptSettings',
			$this->get_post_excerpt_translations() );
	}

	public function get_post_excerpt_translations() {

		return array(
			'orText'         => __( 'Or use wordlift suggested post excerpt', 'wordlift' ),
			'generatingText' => __( 'Generating excerpt...', 'wordlift' ),
			'restUrl'        => get_rest_url( null, WL_REST_ROUTE_DEFAULT_NAMESPACE . '/post-excerpt' ),
			'nonce'          => wp_create_nonce( 'wp_rest' ),
			'postId'         => get_the_ID(),
		);
	}

}
