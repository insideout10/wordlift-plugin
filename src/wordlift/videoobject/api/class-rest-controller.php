<?php
/**
 * @since 3.31.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */

namespace Wordlift\Videoobject\Api;

use Wordlift\Videoobject\Data\Video_Storage\Video_Storage_Factory;
use Wordlift\Videoobject\Data\Video\Video;
use WP_REST_Server;

class Rest_Controller {

	public function register_all_routes() {
		$that = $this;
		add_action( 'rest_api_init', function () use ( $that ) {
			$that->register_get_all_videos_route();
			$that->register_save_all_videos_route();
		} );
	}

	public function get_all_videos( $request ) {
		$data    = $request->get_params();
		$post_id = (int) $data['post_id'];
		$storage = Video_Storage_Factory::get_storage();

		return $storage->get_all_videos( $post_id );
	}

	public function save_all_videos( $request ) {
		$data    = $request->get_params();
		$post_id = (int) $data['post_id'];
		$videos  = (array) $data['videos'];
		if ( ! $videos ) {
			return;
		}
		$storage = Video_Storage_Factory::get_storage();
		$storage->remove_all_videos( $post_id );

		foreach ( $videos as $video ) {
			$video_obj = new Video();
			$video_obj->from( (array) $video );
			$storage->add_video( $post_id, $video_obj );
		}


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

	private function register_save_all_videos_route() {
		register_rest_route(
			WL_REST_ROUTE_DEFAULT_NAMESPACE,
			'/videos/save',
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'save_all_videos' ),
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
					'videos'  => array(
						'validate_callback' => function ( $param, $request, $key ) {
							return is_array( $param ) && $param;
						},
						'required'          => true,
					),
				),
			)
		);
	}


}