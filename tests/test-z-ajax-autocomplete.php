<?php
/**
 * Tests: Ajax Autocomplete Test.
 *
 * @since   3.15.0
 * @package Wordlift
 */

/**
 * Class Wordlift_Autocomplete_Test
 * Extend Wordlift_Ajax_Unit_Test_Case
 *
 * @group   ajax
 *
 * @since   3.15.0
 * @package Wordlift
 */
class Wordlift_Autocomplete_Test extends Wordlift_Ajax_Unit_Test_Case {

	/**
	 * @expectedException WPAjaxDieStopException
	 */
	public function test_autocomplete_without_nonce() {
		$_POST['query'] = 'test';

		try {
			$this->_handleAjax( 'wl_autocomplete' );
		} catch ( WPAjaxDieContinueException $e ) {
		}

		$response = json_decode( $this->_last_response );

		$this->assertInternalType( 'object', $response );
		$this->assertObjectHasAttribute( 'success', $response );
		$this->assertFalse( $response->success );
		$this->assertObjectHasAttribute( 'data', $response );
		$this->assertObjectHasAttribute( 'message', $response->data );
		$this->assertEquals( 'Nonce field doesn\'t match.', $response->data->message );
	}

	/**
	 * @expectedException WPAjaxDieStopException
	 */
	public function test_autocomplete_with_wrong_nonce() {
		$_POST['_wpnonce'] = wp_create_nonce( 'wrong_nonce' );
		$_POST['query']    = 'test';

		try {
			$this->_handleAjax( 'wl_autocomplete' );
		} catch ( WPAjaxDieContinueException $e ) {
		}

		$response = json_decode( $this->_last_response );

		$this->assertInternalType( 'object', $response );
		$this->assertObjectHasAttribute( 'success', $response );
		$this->assertFalse( $response->success );
		$this->assertObjectHasAttribute( 'data', $response );
		$this->assertObjectHasAttribute( 'message', $response->data );
		$this->assertEquals( 'Nonce field doesn`t match.', $response->data->message );
	}

	public function test_autocomplete_with_nonce_without_query_param() {
		// Spoof the nonce in the POST superglobal
		$_POST['_wpnonce'] = wp_create_nonce( 'wl_autocomplete' );

		try {
			$this->_handleAjax( 'wl_autocomplete' );
		} catch ( WPAjaxDieContinueException $e ) {
		}

		$response = json_decode( $this->_last_response );

		$this->assertInternalType( 'object', $response );
		$this->assertObjectHasAttribute( 'success', $response );
		$this->assertFalse( $response->success );
		$this->assertObjectHasAttribute( 'data', $response );
		$this->assertObjectHasAttribute( 'message', $response->data );
		$this->assertEquals( 'The query param is empty.', $response->data->message );
	}

	public function test_autocomplete_with_nonce_with_empty_query_param() {
		// Spoof the nonce in the POST superglobal
		$_POST['_wpnonce'] = wp_create_nonce( 'wl_autocomplete' );
		$_POST['query']    = '';

		try {
			$this->_handleAjax( 'wl_autocomplete' );
		} catch ( WPAjaxDieContinueException $e ) {
		}

		$response = json_decode( $this->_last_response );

		$this->assertInternalType( 'object', $response );
		$this->assertObjectHasAttribute( 'success', $response );
		$this->assertFalse( $response->success );
		$this->assertObjectHasAttribute( 'data', $response );
		$this->assertObjectHasAttribute( 'message', $response->data );
		$this->assertEquals( 'The query param is empty.', $response->data->message );
	}

	public function test_autocomplete_error_status_code() {
		// Spoof the nonce in the POST superglobal
		$_POST['_wpnonce'] = wp_create_nonce( 'wl_autocomplete' );
		$_POST['query']    = 'test';

		add_filter( 'pre_http_request', array(
			$this,
			'return_error_code',
		), 100 );

		try {
			$this->_handleAjax( 'wl_autocomplete' );
		} catch ( WPAjaxDieContinueException $e ) {
		}

		$response = json_decode( $this->_last_response );

		$this->assertInternalType( 'object', $response );
		$this->assertObjectHasAttribute( 'success', $response );
		$this->assertEmpty( $response->data, 'Expected an empty response, instead it was: ' . var_export( $response, true ) );

//		$this->assertFalse( $response->success );
//		$this->assertObjectHasAttribute( 'data', $response );
//		$this->assertObjectHasAttribute( 'message', $response->data );
//		$this->assertEquals( 'Error: Something went wrong.', $response->data->message );
	}

	public function test_autocomplete_wp_error() {
		// Spoof the nonce in the POST superglobal
		$_POST['_wpnonce'] = wp_create_nonce( 'wl_autocomplete' );
		$_POST['query']    = 'test';

		add_filter( 'pre_http_request', array(
			$this,
			'return_wp_error',
		), 100 );

		try {
			$this->_handleAjax( 'wl_autocomplete' );
		} catch ( WPAjaxDieContinueException $e ) {
		}

		$response = json_decode( $this->_last_response );

		$this->assertInternalType( 'object', $response );
		$this->assertObjectHasAttribute( 'success', $response );
		$this->assertEmpty( $response->data, 'Expected an empty response, instead it was: ' . var_export( $response, true ) );

//		$this->assertFalse( $response->success );
//		$this->assertObjectHasAttribute( 'data', $response );
//		$this->assertObjectHasAttribute( 'message', $response->data );
//		$this->assertEquals( 'Error: WP Error!', $response->data->message );
	}


	public function return_wp_error() {
		return new WP_Error( 'broke', 'WP Error!' );
	}

	public function return_error_code() {
		return array(
			'response' => array(
				'code' => 404,
			),
		);
	}

}
