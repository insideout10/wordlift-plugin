<?php
/**
 * Tests: Ajax Installer Test.
 *
 * @since   3.54.17
 * @package Wordlift
 */

use Wordlift\Modules\Common\Installer;
use Wordlift\Modules\Common\Plugin;

require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';

/**
 * Define the {@link Wordlift_Ajax_Installer_Test} class.
 *
 * @group   ajax
 *
 * @since   3.54.17
 * @package Wordlift
 */
class Wordlift_Ajax_Installer_Test extends Wordlift_Ajax_Unit_Test_Case {

	const ACTION = 'wl_install_and_activate_wl-test-plugin';

	/**
	 * @var Plugin|\PHPUnit\Framework\MockObject\MockObject
	 */
	private $plugin;

	/**
	 * @var Plugin_Upgrader|\PHPUnit\Framework\MockObject\MockObject
	 */
	private $upgrader;

	/**
	 * {@inheritdoc}
	 */
	function setUp() {
		parent::setUp();

		// `Wordlift_Admin_Setup::admin_init` redirects admins and exits while this flag is set.
		delete_transient( '_wl_activation_redirect' );

		$this->plugin = $this->createMock( Plugin::class );
		$this->plugin->method( 'get_name' )->willReturn( 'wl-test-plugin' );
		$this->plugin->method( 'get_slug' )->willReturn( 'wl-test-plugin/wl-test-plugin.php' );

		$this->upgrader = $this->createMock( Plugin_Upgrader::class );

		$installer = new Installer( $this->upgrader, $this->plugin );
		$installer->register_hooks();
	}

	public function test_nonce_action_matches_ajax_action() {
		$installer = new Installer( $this->upgrader, $this->plugin );

		$this->assertEquals( self::ACTION, $installer->get_nonce_action() );
	}

	public function test_subscriber_with_nonce_is_refused() {
		wp_set_current_user( self::factory()->user->create( array( 'role' => 'subscriber' ) ) );
		$this->plugin->method( 'is_plugin_installed' )->willReturn( false );
		$this->upgrader->expects( $this->never() )->method( 'install' );

		$_GET['_ajax_nonce'] = wp_create_nonce( self::ACTION );

		$response = $this->call();

		$this->assertFalse( $response->success );
	}

	public function test_admin_without_nonce_is_refused() {
		wp_set_current_user( self::factory()->user->create( array( 'role' => 'administrator' ) ) );
		$this->plugin->method( 'is_plugin_installed' )->willReturn( false );
		$this->upgrader->expects( $this->never() )->method( 'install' );

		$this->expectException( WPAjaxDieStopException::class );
		$this->expectExceptionMessage( '-1' );

		$this->_handleAjax( self::ACTION );
	}

	public function test_activate_only_user_cannot_install() {
		$user = self::factory()->user->create_and_get( array( 'role' => 'editor' ) );
		$user->add_cap( 'activate_plugins' );
		wp_set_current_user( $user->ID );

		$this->plugin->method( 'is_plugin_installed' )->willReturn( false );
		$this->upgrader->expects( $this->never() )->method( 'install' );

		$_GET['_ajax_nonce'] = wp_create_nonce( self::ACTION );

		$response = $this->call();

		$this->assertFalse( $response->success );
	}

	public function test_admin_with_nonce_is_allowed() {
		wp_set_current_user( self::factory()->user->create( array( 'role' => 'administrator' ) ) );

		// Already installed and activated: the handler runs through without touching the file system.
		$this->plugin->method( 'is_plugin_installed' )->willReturn( true );
		$this->plugin->method( 'is_plugin_activated' )->willReturn( true );
		$this->upgrader->expects( $this->never() )->method( 'install' );

		$_GET['_ajax_nonce'] = wp_create_nonce( self::ACTION );

		$response = $this->call();

		$this->assertTrue( $response->success );
	}

	/**
	 * Call the AJAX action and return the decoded response.
	 *
	 * @return object The decoded JSON response.
	 */
	private function call() {
		try {
			$this->_handleAjax( self::ACTION );
		} catch ( WPAjaxDieContinueException $e ) {
		}

		$response = json_decode( $this->_last_response );

		$this->assertInternalType( 'object', $response, "Unexpected response: $this->_last_response" );
		$this->assertObjectHasAttribute( 'success', $response );

		return $response;
	}

}
