<?php
/**
 * @since 3.27.8
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * @group admin
 */
class Admin_Key_Notice_Test extends Wordlift_Unit_Test_Case {

	private $instance;

	public function setUp() {
		parent::setUp();
		$this->instance = new \Wordlift\Admin\Key_Validation_Notice();
	}

	public function test_instance_not_null() {
		$this->assertNotNull( $this->instance );
	}

}