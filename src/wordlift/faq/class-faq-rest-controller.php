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

	public static function insert_faq_item( $request ) {

	}
}