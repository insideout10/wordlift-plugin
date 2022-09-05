<?php
/**
 * This file provides a Wordlift_Ajax_Unit_Test_Case abstract class to support
 * WP's AJAX testing while configuring Wordlift.
 *
 * @since   3.8.0
 * @package Wordlift
 */

require_once( 'functions.php' );

/**
 * Define the {@link Wordlift_Ajax_Unit_Test_Case} class.
 *
 * @since 3.8.0
 */
abstract class Wordlift_Ajax_Unit_Test_Case extends WP_Ajax_UnitTestCase {
	/**
	 * The {@link Wordlift_UnitTest_Factory_For_Entity} instance.
	 *
	 * @since  3.14.0
	 * @access protected
	 * @var \Wordlift_UnitTest_Factory_For_Entity $entity_factory The {@link Wordlift_UnitTest_Factory_For_Entity} instance.
	 */
	protected $entity_factory;

	protected $server;

	function setUp() {
		parent::setUp();

		delete_transient( '_wl_installing' );
		delete_option( 'wl_db_version' );

		$this->assertFalse( get_option( 'wl_db_version' ), '`wl_db_version` should be false.' );

		Wordlift_Install_Service::get_instance()->install();

		// Configure WordPress with the test settings.
		wl_configure_wordpress_test();

		$this->entity_factory = new Wordlift_UnitTest_Factory_For_Entity( $this->factory() );

		// Disable WordPress updates to avoid filtered wp_remote_* requests to
		// WordPress' own servers to fail miserably.
		remove_action( 'admin_init', '_maybe_update_core' );
		remove_action( 'admin_init', '_maybe_update_plugins' );
		remove_action( 'admin_init', '_maybe_update_themes' );

		// Ensure WLP's hooks are saved.
		$this->_backup_hooks();
	}

	/**
	 * The class {@see WP_UnitTestCase} alters custom tables by making them temporary.
	 *
	 * Since we defined foreign keys on these tables we need them persistent, therefore we override the WP_UnitTestCase
	 * functions in order to return the query as is (i.e. without the TEMPORARY modifier).
	 *
	 * @param string $query The original query.
	 *
	 * @return string The original query.
	 *
	 * @since 3.25.0
	 */
	function _create_temporary_tables( $query ) {

		if ( false !== strpos( $query, '_wl_mapping_' ) ) {
			return $query;
		}

		return parent::_create_temporary_tables( $query );
	}

	/**
	 * The class {@see WP_UnitTestCase} alters custom tables by making them temporary.
	 *
	 * Since we defined foreign keys on these tables we need them persistent, therefore we override the WP_UnitTestCase
	 * functions in order to return the query as is (i.e. without the TEMPORARY modifier).
	 *
	 * @param string $query The original query.
	 *
	 * @return string The original query.
	 *
	 * @since 3.25.0
	 */
	function _drop_temporary_tables( $query ) {

		if ( false !== strpos( $query, '_wl_mapping_' ) ) {
			return $query;
		}

		return parent::_drop_temporary_tables( $query );
	}

	/**
	 * Mimic the ajax handling of admin-ajax.php
	 * Capture the output via output buffering, and if there is any, store
	 * it in $this->_last_message.
	 *
	 * @param string $action
	 * @param string|null $body The http request body.
	 */
	protected function _handleAjax( $action, $body = null ) {

		// Start output buffering
		ini_set( 'implicit_flush', false );
		ob_start();

		// Build the request
		$_POST['action'] = $action;
		$_GET['action']  = $action;
		$_REQUEST        = array_merge( $_POST, $_GET );

		// Call the hooks
		do_action( 'admin_init' );
		do_action( 'wp_ajax_' . $_REQUEST['action'], $body );

		// Save the output
		$buffer = ob_get_clean();
		if ( ! empty( $buffer ) ) {
			$this->_last_response = $buffer;
		}
	}

	/**
	 * Mimic the ajax handling of REST endpoint
	 * Capture the output and if there is any, store
	 * it in $this->_last_message.
	 *
	 * @param string $endpoint
	 * @param string $action
	 */
	protected function _handleRest( $endpoint, $action = 'GET' ) {

		global $wp_rest_server;
		$this->server = $wp_rest_server = new \WP_REST_Server;
		do_action( 'rest_api_init' );

		$request              = new WP_REST_Request( $action, $endpoint );
		$request->set_query_params( $_GET );

		$response             = $this->server->dispatch( $request );

		$data                 = $response->get_data();
		$this->_last_response = $data;

	}

}
