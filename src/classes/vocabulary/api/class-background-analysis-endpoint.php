<?php
/**
 * @since 1.1.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */

namespace Wordlift\Vocabulary\Api;

use Wordlift\Vocabulary\Analysis_Background_Process;
use Wordlift\Vocabulary\Analysis_Background_Service;
use Wordlift\Vocabulary\Cache\Cache;
use Wordlift\Vocabulary\Sync_State;
use WP_REST_Server;

class Background_Analysis_Endpoint {

	/**
	 * @var Analysis_Background_Service
	 */
	private $background_service;
	/**
	 * @var Cache
	 */
	private $cache_service;

	public function __construct( $background_service, $cache_service ) {
		$this->background_service = $background_service;
		$this->cache_service      = $cache_service;
		$this->register_routes();
	}

	public function register_routes() {
		$that = $this;
		add_action(
			'rest_api_init',
			function () use ( $that ) {
				$that->register_start_route();
				$that->register_stop_route();
				$that->register_stats_route();
				$that->register_restart_route();
			}
		);
	}

	private function register_start_route() {
		register_rest_route(
			Api_Config::REST_NAMESPACE,
			'/background_analysis/start',
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'start' ),
				'permission_callback' => function () {
					return current_user_can( 'manage_options' );
				},
			)
		);
	}

	private function register_stop_route() {
		register_rest_route(
			Api_Config::REST_NAMESPACE,
			'/background_analysis/stop',
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'stop' ),
				'permission_callback' => function () {
					return current_user_can( 'manage_options' );
				},
			)
		);
	}

	private function register_stats_route() {
		register_rest_route(
			Api_Config::REST_NAMESPACE,
			'/background_analysis/stats',
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'get_stats' ),
				'permission_callback' => function () {
					return current_user_can( 'manage_options' );
				},
			)
		);
	}

	private function register_restart_route() {
		register_rest_route(
			Api_Config::REST_NAMESPACE,
			'/background_analysis/restart',
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'restart' ),
				'permission_callback' => function () {
					return current_user_can( 'manage_options' );
				},
			)
		);
	}

	public function get_stats() {
		/**
		 * @var $state Sync_State
		 */
		$state = get_option( Analysis_Background_Process::WL_CMKG_ANALYSIS_BACKGROUND_PROCESS, Sync_State::unknown() );

		return $state->get_array();
	}

	public function start() {
		$this->background_service->start();

		return true;
	}

	public function restart() {
		$this->background_service->cancel();
		// clear the flags and restart again.
		global $wpdb;

		// Remove the flags, if the tag is already accepted we wont remove that ui flag.

		$wpdb->query(
			$wpdb->prepare(
				"DELETE FROM $wpdb->termmeta WHERE meta_key=%s OR meta_key=%s",
				array(
					Analysis_Background_Service::ANALYSIS_DONE_FLAG,
					Analysis_Background_Service::ENTITIES_PRESENT_FOR_TERM,
				)
			)
		);
		// clear the cache
		$this->cache_service->flush_all();
		$this->background_service->start();

		return array( 'status' => 'restart_complete' );
	}

	public function stop() {
		$this->background_service->stop();

		return true;
	}

}
