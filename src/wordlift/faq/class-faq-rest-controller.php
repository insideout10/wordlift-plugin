<?php
/**
 * This file defines the rest controller for FAQ
 *
 * @since 3.26.0
 * @package Wordlift\FAQ
 */

namespace Wordlift\FAQ;

use WP_REST_Request;

/**
 * Class FAQ_Rest_Controller
 * @package Wordlift\FAQ
 *
 */
class FAQ_Rest_Controller {
	const FAQ_META_KEY = 'wl_faq';
	public function register_routes() {
		add_action( 'rest_api_init', 'Wordlift\FAQ\FAQ_Rest_Controller::register_route_callback' );
	}

	public static function register_route_callback() {
		/**
		 * Rest route for creating new faq item / updating faq items.
		 */
		register_rest_route(
			WL_REST_ROUTE_DEFAULT_NAMESPACE,
			'/faq',
			array(
				'methods'             => \WP_REST_Server::CREATABLE,
				'callback'            => 'Wordlift\FAQ\FAQ_Rest_Controller::insert_faq_item',
				'permission_callback' => function () {
					return current_user_can( 'publish_posts' );
				},
			)
		);
		/**
		 * Rest route for getting the faq items.
		 */
		register_rest_route(
			WL_REST_ROUTE_DEFAULT_NAMESPACE,
			'/faq',
			array(
				'methods'             => \WP_REST_Server::READABLE,
				'callback'            => 'Wordlift\FAQ\FAQ_Rest_Controller::get_faq_items',
				'permission_callback' => function () {
					return current_user_can( 'publish_posts' );
				},
			)
		);

	}

	/**
	 * Get all FAQ items for a post id.
	 *
	 * @param $request $request WP_REST_Request $request {@link WP_REST_Request instance}.
	 *
	 * @return array Result array, if post id is not given then error array is returned.
	 */
	public static function get_faq_items( $request ) {
		$data = $request->get_params();
		if ( array_key_exists('post_id', $data ) ) {
			$post_id = (int) $data['post_id'];
			return get_post_meta( $post_id, self::FAQ_META_KEY, true );
		}
		else {
			return array(
				'status'  => 'failure',
				'message' => __( 'Invalid data, post_id missing', 'wordlift' )
			);
		}
	}

	/**
	 * Insert a single FAQ item.
	 *
	 * @param $request WP_REST_Request $request {@link WP_REST_Request instance}.
	 *
	 * @return array Associative array whether the faq item is inserted or not
	 */
	public static function insert_faq_item( $request ) {
		$post_data = $request->get_params();
		if ( array_key_exists('post_id', $post_data) &&
		     array_key_exists( 'faq_items', $post_data) ) {
			$post_id = $post_data['post_id'];
			$faq_items = $post_data['faq_items'];
			add_post_meta( (int) $post_id, self::FAQ_META_KEY, $faq_items);
			return array(
				'status' => 'success',
				'message' => __('Faq Item successfully inserted.')
			);
		}
		else {
			return array(
				'status'  => 'failure',
				'message' => __( 'Invalid data, post_id or faq_items missing', 'wordlift' )
			);
		}
	}
}