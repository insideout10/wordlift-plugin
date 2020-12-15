<?php
/**
 * Tests: Deactivator Feedback Ajax Test.
 *
 * Define the test for the {@link Wordlift_Deactivator_Feedback}.
 *
 * @since      3.19.0
 * @package    Wordlift
 * @subpackage Wordlift/tests
 */

/**
 * Test the {@link Wordlift_Deactivator_Feedback} class.
 *
 * @since      3.19.0
 * @package    Wordlift
 * @subpackage Wordlift/tests
 * @group ajax
 */
class Wordlift_Deactivator_Feedback_Ajax_Test extends Wordlift_Ajax_Unit_Test_Case {

	/**
	 * Test response without nonce.
	 *
	 * @version 3.19.0
	 *
	 * @return  void
	 */
	public function test_ajax_feedback_request_without_nonce() {
		try {
			$this->_handleAjax( 'wl_deactivation_feedback' );
		} catch ( WPAjaxDieContinueException $e ) {
		}

		$response = json_decode( $this->_last_response );

		$this->assertInternalType( 'object', $response );
		$this->assertFalse( $response->success );
		$this->assertEquals( 'Nonce Security Check Failed!', $response->data );

	}

	/**
	 * Test response with wrong nonce.
	 *
	 * @version 3.19.0
	 *
	 * @return  void
	 */
	public function test_ajax_feedback_request_with_wrong_nonce() {
		$_POST['wl_deactivation_feedback_nonce'] = wp_create_nonce( 'wrong_nonce' );

		try {
			$this->_handleAjax( 'wl_deactivation_feedback' );
		} catch ( WPAjaxDieContinueException $e ) {
		}

		$response = json_decode( $this->_last_response );

		$this->assertInternalType( 'object', $response );
		$this->assertObjectHasAttribute( 'data', $response );
		$this->assertEquals( 'Nonce Security Check Failed!', $response->data );
	}

	/**
	 * Test response with correct nonce, without reason
	 *
	 * @version 3.19.0
	 *
	 * @return  void
	 */
	public function test_ajax_feedback_request_with_correct_nonce() {
		$_POST['wl_deactivation_feedback_nonce'] = wp_create_nonce( 'wl_deactivation_feedback_nonce' );

		try {
			$this->_handleAjax( 'wl_deactivation_feedback' );
		} catch ( WPAjaxDieContinueException $e ) {
		}

		$response = json_decode( $this->_last_response );

		$this->assertInternalType( 'object', $response );
		$this->assertObjectHasAttribute( 'success', $response );
		$this->assertTrue( $response->success );
	}

	/**
	 * Test response with wrong response from the server
	 *
	 * @version 3.19.0
	 *
	 * @return  void
	 */
	public function test_ajax_feedback_request_with_error_response_from_server() {
		$_POST['wl_deactivation_feedback_nonce'] = wp_create_nonce( 'wl_deactivation_feedback_nonce' );
		$_POST['reason_id']                      = 'TOO_COMPLICATED';

		add_filter(
			'pre_http_request', array(
				$this,
				'return_error_code',
			), 100
		);

		try {
			$this->_handleAjax( 'wl_deactivation_feedback' );
		} catch ( WPAjaxDieContinueException $e ) {
		}

		$response = json_decode( $this->_last_response );

		$this->assertInternalType( 'object', $response );
		$this->assertObjectHasAttribute( 'success', $response );
		$this->assertTrue( $response->success );
	}

	/**
	 * Return error response. Used by `pre_http_request` filter.
	 *
	 * @version 3.19.0
	 *
	 * @return  void
	 */
	public function return_error_code() {
		return array(
			'response' => array(
				'code'    => 404,
				'message' => 'Unauthorized',
			),
		);
	}
}
