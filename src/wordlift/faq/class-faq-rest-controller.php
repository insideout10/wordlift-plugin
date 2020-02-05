<?php
/**
 * This file defines the rest controller for FAQ
 *
 * @since 3.26.0
 * @package Wordlift\FAQ
 */

namespace Wordlift\FAQ;

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
	}

	/**
	 * Insert a single FAQ item.
	 *
	 * @param $request \WP_REST_Request $request {@link WP_REST_Request instance}.
	 *
	 * @return array Associative array whether the faq item is inserted or not
	 */
	public static function insert_faq_item( $request ) {
		$post_data = $request->get_params();
		if ( array_key_exists('post_id', $post_data) &&
		     array_key_exists( 'faq_items', $post_data) ) {
			$post_id = $post_data['post_id'];
			/**
			 * Add index as the identifier to the faq_items,
			 * later to be used for updating or deleting.
			 */
			$faq_items = array();
			foreach ( $post_data['faq_items'] as $index => $faq_item ) {
				$faq_item['faq_id'] = $index;
				array_push( $faq_items, $faq_item );
			}
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