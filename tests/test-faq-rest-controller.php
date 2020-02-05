<?php
/**
 * Tests: Tests the FAQ Rest Controller
 * @since 3.26.0
 * @package wordlift
 * @subpackage wordlift/tests
 *
 */

class FAQ_REST_Controller_Test extends Wordlift_Unit_Test_Case {
	/**
	 * @inheritdoc
	 */
	public function setUp() {
		parent::setUp();
		$this->rest_instance = new Wordlift\FAQ_Rest_Controller();
		$this->rest_instance->register_routes();
		global $wp_rest_server;

		$wp_rest_server = new WP_REST_Server();
		$this->server   = $wp_rest_server;

		do_action( 'rest_api_init' );
	}
	public function test_rest_instance_not_null() {
		$this->assertNotNull( $this->rest_instance );
	}
	public function test_given_question_and_answer_can_save_faq() {

	}


}