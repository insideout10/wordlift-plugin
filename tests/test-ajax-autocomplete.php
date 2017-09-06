<?php
/**
 * Tests: Ajax Autocomplete Test.
 *
 * @since   3.15.0
 * @package Wordlift
 */
require_once 'functions.php';

/**
 * Class AjaxAutocompleteTest
 * Extend Wordlift_Ajax_Unit_Test_Case
 *
 * @since   3.15.0
 * @package Wordlift
 */
class AjaxAutocompleteTest extends Wordlift_Ajax_Unit_Test_Case {
	/**
	 * A {@link Wordlift_Configuration_Service} instance.
	 *
	 * @since  3.15.0
	 * @access private
	 * @var \Wordlift_Configuration_Service $configuration_service A {@link Wordlift_Configuration_Service} instance.
	 */
	private $configuration_service;

	/**
	 * {@inheritdoc}
	 */
	public function setUp() {
		parent::setUp();

		$wordlift = new Wordlift_Test();
		$this->configuration_service = $wordlift->get_configuration_service();
	}

	public function test_autocomplete_without_nonce() {
		// Spoof the nonce in the POST superglobal
		$_POST['query'] = 'test';

		try {
			$this->_handleAjax( 'wl_autocomplete' );
		} catch ( WPAjaxDieContinueException $e ) {}
	 
		$response = json_decode( $this->_last_response );


		$this->assertInternalType( 'object', $response );
		$this->assertObjectHasAttribute( 'success', $response );
		$this->assertFalse( $response->success );
		$this->assertObjectHasAttribute( 'data', $response );
		$this->assertObjectHasAttribute( 'message', $response->data );
		$this->assertEquals( 'Nonce field doens\'t match', $response->data->message );
	}

	public function test_autocomplete_with_nonce_without_query_param() {
		// Spoof the nonce in the POST superglobal
		$_POST['_wpnonce'] = wp_create_nonce( 'wordlift_autocomplete' );

		try {
			$this->_handleAjax( 'wl_autocomplete' );
		} catch ( WPAjaxDieContinueException $e ) {}
	 
		$response = json_decode( $this->_last_response );

		$this->assertInternalType( 'object', $response );
		$this->assertObjectHasAttribute( 'success', $response );
		$this->assertFalse( $response->success );
		$this->assertObjectHasAttribute( 'data', $response );
		$this->assertObjectHasAttribute( 'message', $response->data );
		$this->assertEquals( 'The query param is empty!', $response->data->message );

	}

	public function test_autocomplete() {
		// Spoof the nonce in the POST superglobal
		$_POST['_wpnonce'] = wp_create_nonce( 'wordlift_autocomplete' );
		$_POST['query']    = 'test';


		try {
			$this->_handleAjax( 'wl_autocomplete' );
		} catch ( WPAjaxDieContinueException $e ) {}
	 
		$response = json_decode( $this->_last_response );

		$this->assertInternalType( 'object', $response );
		$this->assertObjectHasAttribute( 'success', $response );
		$this->assertTrue( $response->success );
		$this->assertObjectHasAttribute( 'data', $response );
		$this->assertObjectHasAttribute( 'suggestions', $response->data );
	}
}
