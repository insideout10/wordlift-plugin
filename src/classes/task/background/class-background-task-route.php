<?php

namespace Wordlift\Task\Background;

use Exception;
use Wordlift\Assertions;
use WP_REST_Server;

class Background_Task_Route {

	const VERSION_STRING = 'wordlift/v1';

	/**
	 * @var Background_Route_Task
	 */
	private $background_task;

	/**
	 * @var string
	 */
	private $route_name;

	public function __construct( $background_task, $route_name ) {
		$this->background_task = $background_task;
		$this->route_name      = $route_name;
	}

	public static function create( $task, $route_name ) {
		$route = new self( $task, $route_name );

		add_action( 'rest_api_init', array( $route, 'register' ) );

		return $route;
	}

	/**
	 * @throws Exception if the input value is invalid.
	 */
	public function register() {
		Assertions::starts_with( $this->route_name, '/', 'The route name must start with a slash.' );

		register_rest_route(
			self::VERSION_STRING,
			$this->route_name,
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => function () {
					return $this->background_task->start();
				},
				'permission_callback' => array( $this, 'permission_callback' ),
			)
		);

		register_rest_route(
			self::VERSION_STRING,
			$this->route_name,
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => function () {
					return $this->background_task->get_info();
				},
				'permission_callback' => array( $this, 'permission_callback' ),
			)
		);

		register_rest_route(
			self::VERSION_STRING,
			$this->route_name,
			array(
				'methods'             => WP_REST_Server::DELETABLE,
				'callback'            => function () {
					return $this->background_task->stop();
				},
				'permission_callback' => array( $this, 'permission_callback' ),
			)
		);

		register_rest_route(
			self::VERSION_STRING,
			$this->route_name,
			array(
				'methods'             => 'PUT',
				'callback'            => function () {
					return $this->background_task->resume();
				},
				'permission_callback' => array( $this, 'permission_callback' ),
			)
		);

	}

	public function permission_callback() {
		$user = wp_get_current_user();

		return is_super_admin( $user->ID ) || in_array( 'administrator', (array) $user->roles, true );
	}

	public function get_rest_path() {
		return self::VERSION_STRING . $this->route_name;
	}

}
