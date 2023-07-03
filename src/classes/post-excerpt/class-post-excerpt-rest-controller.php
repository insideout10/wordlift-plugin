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

use Wordlift\Api\Default_Api_Service;
use Wordlift\Api\Response;
use WP_REST_Request;

class Post_Excerpt_Rest_Controller {

	const POST_EXCERPT_NAMESPACE = 'post-excerpt';
	/**
	 * Key for storing the meta data for the wordlift post excerpt.
	 */
	const POST_EXCERPT_META_KEY = '_wl_post_excerpt_meta';

	/**
	 * Url for getting the post excerpt data from wordlift api.
	 */
	const WORDLIFT_POST_EXCERPT_ENDPOINT = '/summarize';

	/**
	 * Wordlift returns excerpt in response using this key..
	 */
	const WORDLIFT_POST_EXCERPT_RESPONSE_KEY = 'summary';

	public static function register_routes() {
		add_action( 'rest_api_init', 'Wordlift\Post_Excerpt\Post_Excerpt_Rest_Controller::register_route_callback' );
	}

	/**
	 * Determines whether we need to get the excerpt from wordlift api,
	 * or just use the one we already obtained by generating the hash and comparing it
	 * with the previous one.
	 *
	 * @param $request WP_REST_Request $request {@link WP_REST_Request instance}.
	 *
	 * @return array Post excerpt data.
	 */
	public static function get_post_excerpt( $request ) {
		$data    = $request->get_params();
		$post_id = $data['post_id'];

		/**
		 * @param $data['post_body'] string The post content sent from WordPress editor. ( with strip shortcodes )
		 * @param $post_id int The post id.
		 * @param $data['post_body'] string The post content.
		 *
		 * @since 3.33.5
		 * Allow post content sent to excerpt api to be filtered.
		 */
		$post_body       = apply_filters(
			'wl_post_excerpt_post_content',
			strip_shortcodes( $data['post_body'] ),
			$post_id,
			$data['post_body']
		);
		$current_hash    = md5( $post_body );
		$server_response = self::get_post_excerpt_conditionally( $post_id, $post_body, $current_hash );
		if ( empty( $server_response ) || ! array_key_exists( 'post_excerpt', $server_response ) ) {
			return array(
				'status'  => 'error',
				'message' => __( 'Error While Generating Post Excerpt.', 'wordlift' ),
			);
		} else {
			return array(
				'status'       => 'success',
				'post_excerpt' => $server_response['post_excerpt'],
				'from_cache'   => $server_response['from_cache'],
				'message'      => __( 'Excerpt successfully generated.', 'wordlift' ),
			);
		}

	}

	/**
	 * This function determines whether to get the excerpt from the server or from the meta cache.
	 *
	 * @param $post_id int The Post id.
	 * @param $post_body string The post content
	 * @param $current_hash string md5 hash of the current post body.
	 *
	 * @return array|bool|null
	 */
	public static function get_post_excerpt_conditionally( $post_id, $post_body, $current_hash ) {
		$previous_data   = get_post_meta( $post_id, self::POST_EXCERPT_META_KEY, true );
		$server_response = null;
		if ( '' === $previous_data ) {
			// There is no data in meta, so just fetch the data from remote server.
			$server_response = self::get_post_excerpt_from_remote_server( $post_id, $post_body );
		} else {
			// If there is data in meta, get the previous hash and compare.
			$previous_hash = $previous_data['post_body_hash'];

			if ( $current_hash === $previous_hash ) {
				// then return the previous value.
				$server_response = array(
					'post_excerpt' => $previous_data['post_excerpt'],
					'from_cache'   => true,
				);
			} else {
				// send the request to external API and then send the response.
				$server_response = self::get_post_excerpt_from_remote_server( $post_id, $post_body );
			}
		}

		return $server_response;
	}

	/**
	 * Sends the remote request to the wordlift API and saves the response in meta for
	 * future use.
	 *
	 * @param $post_id int Post id which the post excerpt belongs to
	 * @param $post_body string Total text content of the post body.
	 *
	 * @return array|bool
	 */
	public static function get_post_excerpt_from_remote_server( $post_id, $post_body ) {
		// The configuration is constant for now, it might be changing in future.
		$configuration = array(
			'ratio'      => 0.0005,
			'min_length' => 60,
		);
		// Construct the url with the configuration
		$endpoint    = add_query_arg( $configuration, self::WORDLIFT_POST_EXCERPT_ENDPOINT );
		$api_service = Default_Api_Service::get_instance();
		$response    = $api_service->request(
			'POST',
			$endpoint,
			array( 'Content-Type' => 'text/plain' ),
			$post_body,
			null,
			null,
			array( 'data_format' => 'body' )
		);

		return self::save_response_to_meta_on_success( $post_id, $post_body, $response );
	}

	/**
	 * Save the post excerpt to meta if the response is successful.
	 *
	 * @param $post_id int The post id
	 * @param $post_body string Full text content of the post.
	 * @param $response Response instance
	 *
	 * @return array|bool
	 */
	public static function save_response_to_meta_on_success( $post_id, $post_body, $response ) {
		// If body exists then decode the body.
		$body = json_decode( $response->get_body(), true );
		if ( empty( $body ) || ! array_key_exists( self::WORDLIFT_POST_EXCERPT_RESPONSE_KEY, $body ) ) {
			// Bail out if we get an incorrect response
			return false;
		} else {
			$post_excerpt = (string) $body[ self::WORDLIFT_POST_EXCERPT_RESPONSE_KEY ];
			// Save it to meta.
			self::save_post_excerpt_in_meta( $post_id, $post_excerpt, $post_body );

			return array(
				'post_excerpt' => $post_excerpt,
				'from_cache'   => false,
			);
		}
	}

	/**
	 * Saves the excerpt in the post meta.
	 *
	 * @param $post_id int Post id which the post excerpt belongs to
	 * @param $post_excerpt string Post excerpt returned by the server
	 * @param $post_body string Total text content of the post body.
	 *
	 * @return void
	 */
	public static function save_post_excerpt_in_meta( $post_id, $post_excerpt, $post_body ) {
		// hash the post body and save it.
		$data = array(
			'post_body_hash' => md5( $post_body ),
			'post_excerpt'   => $post_excerpt,
		);
		update_post_meta( $post_id, self::POST_EXCERPT_META_KEY, $data );
	}

	/**
	 * This call back is invoked by the Rest api action.
	 */
	public static function register_route_callback() {
		/** @var  $post_id_validation_settings array Settings used to validate post id */
		$post_id_validation_settings   = array(
			'required'          => true,
			// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
			'validate_callback' => function ( $param, $request, $key ) {
				return is_numeric( $param );
			},
		);
		$post_body_validation_settings = array(
			'required'          => true,
			// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
			'validate_callback' => function ( $param, $request, $key ) {
				return is_string( $param );
			},
		);
		/**
		 * Rest route for getting the excerpt from wordlift api.
		 */
		register_rest_route(
			WL_REST_ROUTE_DEFAULT_NAMESPACE,
			'/' . self::POST_EXCERPT_NAMESPACE . '/(?P<post_id>\d+)',
			array(
				'methods'             => \WP_REST_Server::CREATABLE,
				'callback'            => 'Wordlift\Post_Excerpt\Post_Excerpt_Rest_Controller::get_post_excerpt',
				'permission_callback' => function () {
					return current_user_can( 'publish_posts' );
				},
				'args'                => array(
					'post_id'   => $post_id_validation_settings,
					'post_body' => $post_body_validation_settings,
				),
			)
		);
	}

}
