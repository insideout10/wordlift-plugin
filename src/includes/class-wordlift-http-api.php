<?php
/**
 * Service: Http Api.
 *
 * Handle calls to `/wl-api`.
 *
 * See https://make.wordpress.org/plugins/2012/06/07/rewrite-endpoints-api/.
 *
 * @since 3.15.3
 */

/**
 * Define the {@link Wordlift_Http_Api} class.
 *
 * @since 3.15.3
 */
class Wordlift_Http_Api {

	/**
	 * A {@link Wordlift_Log_Service} instance.
	 *
	 * @since 3.15.3
	 *
	 * @var \Wordlift_Log_Service $log A {@link Wordlift_Log_Service} instance.
	 */
	private $log;

	/**
	 * Create a {@link Wordlift_End_Point} instance.
	 *
	 * @since 3.15.3
	 */
	public function __construct() {

		$this->log = Wordlift_Log_Service::get_logger( get_class() );

		add_action( 'init', array( $this, 'add_rewrite_endpoint' ) );
		add_action( 'template_redirect', array( $this, 'template_redirect' ) );

		// region SAMPLE ACTIONS.
		add_action(
			'admin_post_wl_hello_world',
			array(
				$this,
				'hello_world',
			)
		);
		add_action(
			'admin_post_nopriv_wl_hello_world',
			array(
				$this,
				'nopriv_hello_world',
			)
		);
		// endregion
	}

	/**
	 * Add the `wl-api` rewrite end-point.
	 *
	 * @since 3.15.3
	 */
	public function add_rewrite_endpoint() {

		add_rewrite_endpoint( 'wl-api', EP_ROOT );
		$this->ensure_rewrite_rules_are_flushed();

	}

	/**
	 * Handle `template_redirect` hooks.
	 *
	 * @since 3.15.3
	 */
	public function template_redirect() {

		global $wp_query;

		if ( ! isset( $wp_query->query_vars['wl-api'] ) ) {
			$this->log->trace( 'Skipping, not a `wl-api` call.' );

			return;
		}

		$action = isset( $_REQUEST['action'] ) ? sanitize_text_field( wp_unslash( (string) $_REQUEST['action'] ) ) : ''; //phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$this->do_action( $action );

		exit;

	}

	/**
	 * Do the requested action.
	 *
	 * @param string $action The action to execute.
	 *
	 * @since 3.15.3
	 */
	private function do_action( $action ) {

		if ( empty( $action ) ) {
			return;
		}

		if ( ! wp_validate_auth_cookie( '', 'logged_in' ) ) {
			/**
			 * Fires on a non-authenticated admin post request for the given action.
			 *
			 * The dynamic portion of the hook name, `$action`, refers to the given
			 * request action.
			 *
			 * @since 2.6.0
			 */
			do_action( "admin_post_nopriv_{$action}" );
		} else {
			/**
			 * Fires on an authenticated admin post request for the given action.
			 *
			 * The dynamic portion of the hook name, `$action`, refers to the given
			 * request action.
			 *
			 * @since 2.6.0
			 */
			do_action( "admin_post_{$action}" );
		}

	}

	/**
	 * Test function, anonymous.
	 *
	 * @since 3.15.3
	 */
	public function nopriv_hello_world() {

		wp_die( 'Hello World! (from anonymous)' );

	}

	/**
	 * Test function, authenticated.
	 *
	 * @since 3.15.3
	 */
	public function hello_world() {

		wp_die( 'Hello World! (from authenticated)' );

	}

	/**
	 * Ensure that the rewrite rules are flushed the first time.
	 *
	 * @since 3.16.0 changed the value from 1 to `yes` to avoid type juggling issues.
	 * @since 3.15.3
	 */
	public static function ensure_rewrite_rules_are_flushed() {

		// See https://github.com/insideout10/wordlift-plugin/issues/698.
		if ( 'yes' !== get_option( 'wl_http_api' ) ) {
			update_option( 'wl_http_api', 'yes' );
			add_action(
				'wp_loaded',
				function () {
					flush_rewrite_rules();
				}
			);
		}

	}

	/**
	 * Called by {@see activate_wordlift}, resets the `wl_http_api` option flag in order to force WordLift to
	 * reinitialize the `wl-api` route.
	 *
	 * @see https://github.com/insideout10/wordlift-plugin/issues/820 related issue.
	 *
	 * @since 3.19.2
	 */
	public static function activate() {

		// Force the plugin to reinitialize the rewrite rules.
		update_option( 'wl_http_api', 'no' );

	}

	/**
	 * Delete the option when the plugin is deactivated.
	 *
	 * @since 3.19.4
	 *
	 * @see https://github.com/insideout10/wordlift-plugin/issues/846
	 */
	public static function deactivate() {

		delete_option( 'wl_http_api' );

	}

}
