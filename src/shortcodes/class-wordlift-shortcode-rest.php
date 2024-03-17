<?php

use Wordlift\Cache\Ttl_Cache;

/**
 * A base abstract class for shortcode REST backend which does
 * some common tasks.
 *
 * @since 3.5.4
 */
abstract class Wordlift_Shortcode_REST {

	/**
	 * The cache_ttl, set by extending classes.
	 */
	const CACHE_TTL = 86400; // 24 hours

	/**
	 * @var $endpoint string The endpoint.
	 */
	private $endpoint;

	/**
	 * @var $args array The args.
	 */
	private $args;

	public function __construct( $endpoint, $args ) {

		$scope          = $this;
		$this->endpoint = $endpoint;
		$this->args     = $args;

		// Register rest route with callback
		add_action(
			'rest_api_init',
			function () use ( $scope ) {
				register_rest_route(
					WL_REST_ROUTE_DEFAULT_NAMESPACE,
					$scope->endpoint,
					array(
						'methods'             => WP_REST_Server::READABLE,
						'permission_callback' => '__return_true',
						'callback'            => array( $scope, 'rest_callback' ),
						'args'                => $scope->args,
					)
				);
			}
		);

		// Optimizations: disable unneeded plugins on this specific REST call. WPSeo is slowing down the responses quite a bit.
		add_action(
			'plugins_loaded',
			function () use ( $scope ) {

				if ( ! defined( 'REST_REQUEST' ) || ! REST_REQUEST || ! $scope->is_endpoint() ) {
					return;
				}

				remove_action( 'plugins_loaded', 'rocket_init' );
				remove_action( 'plugins_loaded', 'wpseo_premium_init', 14 );
				remove_action( 'plugins_loaded', 'wpseo_init', 14 );
			},
			0
		);

		add_action(
			'init',
			function () use ( $scope ) {

				if ( ! defined( 'REST_REQUEST' ) || ! REST_REQUEST || ! $scope->is_endpoint() ) {
					return;
				}

				remove_action( 'init', 'wp_widgets_init', 1 );
				remove_action( 'init', 'gglcptch_init' );
			},
			0
		);

	}

	abstract public function get_data( $request );

	public function rest_callback( WP_REST_Request $request ) {

		// Respond from origin if TTL is 0
		if ( static::CACHE_TTL === 0 ) {

			$data     = $this->get_data( $request );
			$response = rest_ensure_response( $data );
			if ( is_wp_error( $data ) ) {
				return $response;
			}
			$response->header( 'Access-Control-Allow-Origin', '*' );
			$response->header( 'X-WordLift-Cache', 'MISS' );

			return $response;
		}

		// Create the cache key.
		$cache_key_params = $request->get_params();
		unset( $cache_key_params['uniqid'] );
		unset( $cache_key_params['rest_route'] );
		$cache_key = array( 'request_params' => $cache_key_params );

		// Create the TTL cache and try to get the results.
		$cache         = new Ttl_Cache( $this->endpoint, static::CACHE_TTL );
		$cache_results = $cache->get( $cache_key );

		if ( isset( $cache_results ) ) {

			$response = rest_ensure_response( $cache_results );
			$response->header( 'Access-Control-Allow-Origin', '*' );
			$response->header( 'X-WordLift-Cache', 'HIT' );

			return $response;
		}

		$data     = $this->get_data( $request );
		$response = rest_ensure_response( $data );
		if ( is_wp_error( $data ) ) {
			return $response;
		}
		$response->header( 'Access-Control-Allow-Origin', '*' );
		$response->header( 'X-WordLift-Cache', 'MISS' );

		// Put the result before sending the json to the client, since sending the json will terminate us.
		$cache->put( $cache_key, $data );

		return $response;

	}

	private function is_endpoint() {
		$compare_route = WL_REST_ROUTE_DEFAULT_NAMESPACE . $this->endpoint;

		// Directly accessing $_SERVER['REQUEST_URI'] or $_GET['rest_route'] here as it's too early to use global $wp reliably

		if ( isset( $_SERVER['REQUEST_URI'] ) && strpos( esc_url_raw( wp_unslash( (string) $_SERVER['REQUEST_URI'] ) ), $compare_route ) ) {
			return true;
		}
		if ( ! empty( $_GET['rest_route'] ) && strpos( esc_url_raw( wp_unslash( (string) $_GET['rest_route'] ) ), $compare_route ) ) { //phpcs:ignore WordPress.Security.NonceVerification.Recommended
			return true;
		}

		return false;
	}

}
