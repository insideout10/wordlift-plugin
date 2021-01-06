<?php

use Wordlift\Admin\Key_Validation_Notice;

/**
 * @since 3.27.8
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * @group admin
 */
class Admin_Key_Notice_Test extends Wordlift_Unit_Test_Case {

	private $instance;

	public function setUp() {
		parent::setUp();
		global $wp_filter;
		$wp_filter = array();
		$this->instance = new Key_Validation_Notice();
	}

	public function test_instance_not_null() {
		$this->assertNotNull( $this->instance );
	}

	public function test_when_key_validation_is_errored_should_show_error() {
		ob_start();
		do_action( 'admin_notices' );
		$html = ob_get_contents();
		ob_end_clean();
		$this->assertNotNull( $html );
		$this->assertTrue( strlen( $html ) !== 0 );
	}


}