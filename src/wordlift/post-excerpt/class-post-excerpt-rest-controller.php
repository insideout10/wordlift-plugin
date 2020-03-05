<?php
/**
 * This file registers the rest endpoints for custom post excerpt, used for sending the request
 * to wordlift api.
 *
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * @since 3.23.0
 *
 * @package Wordlift
 * @subpackage Wordlift\Post_Excerpt
 */

namespace Wordlift\Post_Excerpt;

use WP_REST_Request;

class Post_Excerpt_Rest_Controller {

	const POST_EXCERPT_NAMESPACE = 'post-excerpt';
	/**
	 * Key for storing the meta data for the wordlift post excerpt.
	 */
	const POST_EXCERPT_META_KEY = '_wl_post_excerpt_meta';

	public static function register_routes() {
		add_action( 'rest_api_init', 'Wordlift\Post_Excerpt\Post_Excerpt_Rest_Controller::register_route_callback' );
	}

	/**
	 * Determines whether we need to get the excerpt from wordlift api,
	 * or just use the one we already obtained by generating the hash and comparing it
	 * with the previous one.
	 *
	 *  @param $request WP_REST_Request $request {@link WP_REST_Request instance}.
	 *
	 * @return string Post excerpt data.
	 */
	public static function get_post_excerpt( $request ) {
		$data    = $request->get_params();
		$post_id = $data['post_id'];
		$post_body = $data['post_body'];

	}


	public static function register_route_callback() {
		/** @var  $post_id_validation_settings array Settings used to validate post id */
		$post_id_validation_settings   = array(
			'required'          => TRUE,
			'validate_callback' => function ( $param, $request, $key ) {
				return is_numeric( $param );
			}
		);
		$post_body_validation_settings = array(
			'required'          => TRUE,
			'validate_callback' => function ( $param, $request, $key ) {
				return is_string( $param );
			}
		);
		/**
		 * Rest route for getting the excerpt from wordlift api.
		 */
		register_rest_route(
			WL_REST_ROUTE_DEFAULT_NAMESPACE,
			'/' . self::POST_EXCERPT_NAMESPACE . '/(?P<post_id>\d+)',
			array(
				'methods'             => \WP_REST_Server::READABLE,
				'callback'            => 'Wordlift\Faq\Faq_Rest_Controller::get_post_excerpt',
				'permission_callback' => function () {
					return current_user_can( 'publish_posts' );
				},
				'args'                => array(
					'post_id'   => $post_id_validation_settings,
					'post_body' => $post_body_validation_settings
				)
			)
		);
	}

}