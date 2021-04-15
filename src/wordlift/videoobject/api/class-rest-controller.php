<?php
/**
 * @since 3.31.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */

namespace Wordlift\Videoobject\Api;

use Wordlift\Vocabulary\Api\Api_Config;
use WP_REST_Server;

class Rest_Controller {

	public function register_all_routes() {
		$that = $this;
		add_action( 'rest_api_init', function () use ( $that ) {
			$that->register_get_all_videos_route();
		} );
	}

	private function register_get_all_videos_route() {
		register_rest_route(
			WL_REST_ROUTE_DEFAULT_NAMESPACE,
			'/videos',
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'get_all_videos' ),
				'permission_callback' => function () {
					return current_user_can( 'manage_options' );
				},
				'args'                => array(
					'post_id' => array(
						'validate_callback' => function ( $param, $request, $key ) {
							return is_numeric( $param ) && $param;
						},
						'required'          => true,
					),
				),
			)
		);
	}

}