<?php

use Wordlift\Admin\Key_Validation_Notice;
use Wordlift\Cache\Ttl_Cache;

/**
 * @since 3.27.8
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * @group admin
 */
class Admin_Key_Notice_Test extends Wordlift_Unit_Test_Case {


	public function setUp() {
		parent::setUp();
		global $wp_filter;
		$wp_filter = array();
		// Flush all caches, because tests assert caching.
		Ttl_Cache::flush_all();

	}

	public function test_instance_not_null() {
		$instance = new Key_Validation_Notice( null, null );
		$this->assertNotNull( $instance );
	}

	public function test_when_key_validation_is_errored_should_show_error() {

		// Create a mock key validation service.
		$stub = $this->getMockBuilder( 'Wordlift_Key_Validation_Service' )
		             ->disableOriginalConstructor()
		             ->getMock();
		$stub->method( 'is_key_valid' )->willReturn( false );
		$instance = new Key_Validation_Notice( $stub, Wordlift_Configuration_Service::get_instance() );
		$html     = $this->do_admin_notices();
		$this->assertNotNull( $html );
		$this->assertTrue( strlen( $html ) !== 0 );
	}


	public function test_when_key_validation_is_errored_but_filter_is_turned_on_should_not_show_error() {
		add_filter( 'wl_feature__enable__notices', '__return_false' );
		// Create a mock key validation service.
		$stub = $this->getMockBuilder( 'Wordlift_Key_Validation_Service' )
		             ->disableOriginalConstructor()
		             ->getMock();
		$stub->method( 'is_key_valid' )->willReturn( false );
		$instance = new Key_Validation_Notice( $stub, Wordlift_Configuration_Service::get_instance() );
		$html     = $this->do_admin_notices();
		$this->assertNotNull( $html );
		$this->assertTrue( strlen( $html ) === 0, 'Error should not be shown since the filter is turned on' );
	}


	public function test_key_validation_results_should_be_cached() {
		// Create a mock key validation service.
		$key_validation_service_mock = $this->getMockBuilder( 'Wordlift_Key_Validation_Service' )
		                                    ->disableOriginalConstructor()
		                                    ->getMock();
		$key_validation_service_mock->method( 'is_key_valid' )->willReturn( false );
		// Since its not cached. the key validation service method would be called once.
		$key_validation_service_mock->expects( $this->once() )
		                            ->method( 'is_key_valid' );
		$instance = new Key_Validation_Notice( $key_validation_service_mock, Wordlift_Configuration_Service::get_instance() );
		$this->do_admin_notices();
		// is_key_valid would be called only once.
		$this->do_admin_notices();
	}


	public function test_when_key_is_valid_should_not_show_notification() {
		// Create a mock key validation service.
		$key_validation_service_mock = $this->getMockBuilder( 'Wordlift_Key_Validation_Service' )
		                                    ->disableOriginalConstructor()
		                                    ->getMock();
		$key_validation_service_mock->method( 'is_key_valid' )->willReturn( true );
		$instance = new Key_Validation_Notice( $key_validation_service_mock, Wordlift_Configuration_Service::get_instance() );
		$html     = $this->do_admin_notices();
		$this->assertEquals( $html, '' );
	}


	public function test_when_the_close_button_clicked_should_not_show_notification() {
		$user_id                     = $this->factory->user->create( array( 'role' => 'administrator' ) );
		$user                        = wp_set_current_user( $user_id );
		$key_validation_service_mock = $this->getMockBuilder( 'Wordlift_Key_Validation_Service' )
		                                    ->disableOriginalConstructor()
		                                    ->getMock();
		$key_validation_service_mock->method( 'is_key_valid' )->willReturn( false );
		$instance                                = new Key_Validation_Notice( $key_validation_service_mock, Wordlift_Configuration_Service::get_instance() );
		$_GET['wl_key_validation_notice']        = 'wl_key_validation_notice';
		$_GET['_wl_key_validation_notice_nonce'] = wp_create_nonce( Key_Validation_Notice::KEY_VALIDATION_NONCE_ACTION );
		// Run the notification close handler.
		$instance->close_notification();
		$html     = $this->do_admin_notices();
		$this->assertNotNull( $html );
		$this->assertTrue( strlen( $html ) === 0, 'Should not show notice when it is already closed' );
	}


	private function do_admin_notices() {
		ob_start();
		do_action( 'admin_notices' );
		$html = ob_get_contents();
		ob_end_clean();

		return $html;
	}

}