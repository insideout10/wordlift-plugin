<?php

/**
 * Manage user-related functions.
 *
 * @since 3.1.7
 */
class Wordlift_User_Service {

	/**
	 * The meta key where the user's URI is stored.
	 *
	 * @since 3.1.7
	 */
	const URI_META_KEY = '_wl_uri';

	/**
	 * The Log service.
	 *
	 * @since 3.1.7
	 * @access private
	 * @var \Wordlift_Log_Service $log_service The Log service.
	 */
	private $log_service;

	/**
	 * The base dataset URI.
	 *
	 * @since 3.1.7
	 * @access private
	 * @var string $dataset_uri The base dataset URI.
	 */
	private $dataset_uri;

	/**
	 * The singleton instance of the User service.
	 *
	 * @since 3.1.7
	 * @access private
	 * @var \Wordlift_User_Service $user_service The singleton instance of the User service.
	 */
	private static $instance;

	/**
	 * Create an instance of the User service.
	 *
	 * @since 3.1.7
	 *
	 * @param string $dataset_uri The base dataset URI.
	 */
	public function __construct( $dataset_uri ) {

		$this->log_service = Wordlift_Log_Service::get_logger( 'Wordlift_User_Service' );

		$this->dataset_uri = $dataset_uri;

		self::$instance = $this;

	}

	/**
	 * Get the singleton instance of the User service.
	 *
	 * @since 3.1.7
	 * @return \Wordlift_User_Service The singleton instance of the User service.
	 */
	public static function get_instance() {

		return self::$instance;
	}

	/**
	 * Get the URI for a user.
	 *
	 * @since 3.1.7
	 *
	 * @param int $user_id The user id
	 *
	 * @return false|string The user's URI or false in case of failure.
	 */
	public function get_uri( $user_id ) {

		// Try to get the URI stored in the user's meta and return it if available.
		if ( false !== ( $user_uri = $this->_get_uri( $user_id ) ) ) {
			return $user_uri;
		}

		// Try to build an URI, return false in case of failure.
		if ( false === ( $user_uri = $this->_build_uri( $user_id ) ) ) {
			return false;
		}

		// Store the URI for future requests (we need a "permanent" URI).
		$this->_set_uri( $user_id, $user_uri );

		return $user_uri;
	}

	/**
	 * Get the user's URI stored in the user's meta.
	 *
	 * @since 3.1.7
	 *
	 * @param int $user_id The user id.
	 *
	 * @return false|string The user's URI or false if not found.
	 */
	private function _get_uri( $user_id ) {

		$user_uri = get_user_meta( $user_id, self::URI_META_KEY, true );

		if ( empty( $user_uri ) ) {
			return false;
		}

		return $user_uri;
	}

	/**
	 * Build an URI for a user.
	 *
	 * @since 3.1.7
	 *
	 * @param int $user_id The user's id.
	 *
	 * @return false|string The user's URI or false in case of failure.
	 */
	private function _build_uri( $user_id ) {

		// Get the user, return false in case of failure.
		if ( false === ( $user = get_userdata( $user_id ) ) ) {
			return false;
		};

		// If the nicename is not set, return a failure.
		if ( empty( $user->user_nicename ) ) {
			return false;
		}

		return "$this->dataset_uri/user/$user->user_nicename";
	}

	/**
	 * Store the URI in user's meta.
	 *
	 * @since 3.1.7
	 *
	 * @param int $user_id The user's id.
	 * @param string $user_uri The user's uri.
	 *
	 * @return int|bool Meta ID if the key didn't exist, true on successful update, false on failure.
	 */
	private function _set_uri( $user_id, $user_uri ) {

		return update_user_meta( $user_id, self::URI_META_KEY, $user_uri );
	}

}
