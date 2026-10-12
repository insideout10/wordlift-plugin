<?php
/**
 * Tests: Ajax Download Your Data and Google Analytics Export permissions.
 *
 * @since   3.54.17
 * @package Wordlift
 */

/**
 * Define the {@link Wordlift_Ajax_Admin_Export_Permissions_Test} class.
 *
 * The admin cases stop at the first check after the permission gate (an unsupported format, a permalink structure
 * other than "Post name"), which proves the gate let the request through without calling the API.
 *
 * @group   ajax
 *
 * @since   3.54.17
 * @package Wordlift
 */
class Wordlift_Ajax_Admin_Export_Permissions_Test extends Wordlift_Ajax_Unit_Test_Case {

	/**
	 * {@inheritdoc}
	 */
	function setUp() {
		parent::setUp();

		// `Wordlift_Admin_Setup::admin_init` redirects admins and exits while this flag is set.
		delete_transient( '_wl_activation_redirect' );

		global $wp_rewrite;
		if ( $wp_rewrite->permalink_structure ) {
			$this->set_permalink_structure( '' );
		}
	}

	public function exports() {
		return array(
			'download your data' => array(
				'wl_download_your_data',
				'You are not allowed to download this data.',
				'The format is not supported.',
			),
			'google analytics'   => array(
				'wl_google_analytics_export',
				'You are not allowed to export this data.',
				'The current permalink structure do not allow to export your data. Please change the permalink structure to "Post name".',
			),
		);
	}

	/**
	 * @dataProvider exports
	 */
	public function test_admin_without_nonce_is_refused( $action ) {
		wp_set_current_user( self::factory()->user->create( array( 'role' => 'administrator' ) ) );

		$this->expectException( WPAjaxDieStopException::class );
		$this->expectExceptionMessage( 'This download link has expired. Please go back to the Download Your Data page and try again.' );

		$this->_handleAjax( $action );
	}

	/**
	 * @dataProvider exports
	 */
	public function test_editor_with_nonce_is_refused( $action, $not_allowed_message ) {
		wp_set_current_user( self::factory()->user->create( array( 'role' => 'editor' ) ) );

		$_GET['_wpnonce'] = wp_create_nonce( $action );

		$this->expectException( WPAjaxDieStopException::class );
		$this->expectExceptionMessage( $not_allowed_message );

		$this->_handleAjax( $action );
	}

	/**
	 * @dataProvider exports
	 */
	public function test_admin_with_nonce_is_allowed( $action, $not_allowed_message, $next_check_message ) {
		wp_set_current_user( self::factory()->user->create( array( 'role' => 'administrator' ) ) );

		$_GET['_wpnonce'] = wp_create_nonce( $action );
		$_GET['out']      = 'unsupported';

		$this->expectException( WPAjaxDieStopException::class );
		$this->expectExceptionMessage( $next_check_message );

		$this->_handleAjax( $action );
	}

}
