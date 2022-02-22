<?php

namespace Wordlift\Api;

class Api_Headers_Service {

	private static $instance = null;

	protected function __construct() {

	}

	/**
	 * This function is used to append WordPress endpoint data to every request made.
	 * @return array
	 */
	public function get_wp_headers() {
		return array(
			'X-Wordlift-Plugin-Wp-Admin' => untrailingslashit( get_admin_url() ),
			'X-Wordlift-Plugin-Wp-Json'  => untrailingslashit( get_rest_url() )
		);
	}

	public static function get_instance() {
		if ( self::$instance === null ) {
			self::$instance = new Api_Headers_Service();
		}

		return self::$instance;
	}


}