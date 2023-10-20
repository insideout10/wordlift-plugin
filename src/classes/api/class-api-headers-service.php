<?php

namespace Wordlift\Api;

use Exception;

class Api_Headers_Service {

	private static $instance = null;

	protected function __construct() {

	}

	/**
	 * This function is used to append WordPress endpoint data to every request made.
	 *
	 * @return array
	 */
	public function get_wp_headers() {
		// phpcs:ignore WordPress.NamingConventions.ValidHookName.UseUnderscores
		$is_plugin_subscription = apply_filters( 'wl_feature__enable__entity-types-professional', false ) ||
		                          // phpcs:ignore WordPress.NamingConventions.ValidHookName.UseUnderscores
								  apply_filters( 'wl_feature__enable__entity-types-business', false ) ||
		                          // phpcs:ignore WordPress.NamingConventions.ValidHookName.UseUnderscores
								  apply_filters( 'wl_feature__enable__entity-types-starter', false );

		try {
			return $is_plugin_subscription ? array(
				'X-Wordlift-Plugin-Wp-Admin' => untrailingslashit( get_admin_url() ),
				'X-Wordlift-Plugin-Wp-Json'  => untrailingslashit( get_rest_url() ),
			) : array();
		} catch ( Exception $e ) {
			return array();
		}
	}

	/**
	 * @return Api_Headers_Service
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new Api_Headers_Service();
		}

		return self::$instance;
	}

}
