<?php

use Wordlift\Admin\Key_Validation_Notice;

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
		\Wordlift\Cache\Ttl_Cache::flush_all();

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
		$html = $this->do_admin_notices();
		$this->assertNotNull( $html );
		$this->assertTrue( strlen( $html ) !== 0 );
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

	private function do_admin_notices() {
		ob_start();
		do_action( 'admin_notices' );
		$html = ob_get_contents();
		ob_end_clean();
		return $html;
	}

}