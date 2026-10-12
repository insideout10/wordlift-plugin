<?php
/**
 * Tests: Ajax Key Validation Test.
 *
 * @since   3.54.17
 * @package Wordlift
 */

use Wordlift\Entity_Type\Entity_Type_Setter;

/**
 * Define the {@link Wordlift_Ajax_Key_Validation_Test} class.
 *
 * @group   ajax
 *
 * @since   3.54.17
 * @package Wordlift
 */
class Wordlift_Ajax_Key_Validation_Test extends Wordlift_Ajax_Unit_Test_Case {

	/**
	 * The number of times `wl_key_validation_response` fired.
	 *
	 * @var int
	 */
	private $validation_responses = 0;

	/**
	 * {@inheritdoc}
	 */
	function setUp() {
		parent::setUp();

		$this->validation_responses = 0;

		// `Wordlift_Admin_Setup::admin_init` redirects admins and exits while this flag is set.
		delete_transient( '_wl_activation_redirect' );

		add_filter( 'pre_http_request', array( $this, '_mock_api' ), 10, 3 );
		add_action( 'wl_key_validation_response', array( $this, '_count_validation_response' ) );
	}

	function tearDown() {
		remove_filter( 'pre_http_request', array( $this, '_mock_api' ) );
		remove_action( 'wl_key_validation_response', array( $this, '_count_validation_response' ) );

		parent::tearDown();
	}

	function _mock_api( $response, $request, $url ) {

		if ( preg_match( '@/accounts/info$@', $url ) ) {
			return array(
				'headers'  => array(),
				'body'     => wp_json_encode(
					array(
						'datasetURI' => 'https://data.example.org/dataset/',
						'features'   => array( Entity_Type_Setter::STARTER_PLAN => true ),
					)
				),
				'response' => array(
					'code'    => 200,
					'message' => 'OK',
				),
				'cookies'  => array(),
			);
		}

		return $response;
	}

	function _count_validation_response() {
		$this->validation_responses ++;
	}

	public function test_subscriber_is_refused() {
		wp_set_current_user( self::factory()->user->create( array( 'role' => 'subscriber' ) ) );

		$_POST['_wpnonce'] = wp_create_nonce( 'wl_validate_key' );
		$_POST['key']      = 'key123';

		$response = $this->call_validate_key();

		$this->assertFalse( $response->success );
		$this->assertEquals( 'You are not allowed to validate the key.', $response->data );
		$this->assertEquals( 0, $this->validation_responses, 'The key must not be validated.' );
	}

	public function test_admin_without_nonce_is_refused() {
		wp_set_current_user( self::factory()->user->create( array( 'role' => 'administrator' ) ) );

		$_POST['key'] = 'key123';

		$response = $this->call_validate_key();

		$this->assertFalse( $response->success );
		$this->assertEquals( 'Invalid nonce.', $response->data );
		$this->assertEquals( 0, $this->validation_responses, 'The key must not be validated.' );
	}

	public function test_admin_with_wrong_nonce_is_refused() {
		wp_set_current_user( self::factory()->user->create( array( 'role' => 'administrator' ) ) );

		$_POST['_wpnonce'] = wp_create_nonce( 'wrong_nonce' );
		$_POST['key']      = 'key123';

		$response = $this->call_validate_key();

		$this->assertFalse( $response->success );
		$this->assertEquals( 'Invalid nonce.', $response->data );
		$this->assertEquals( 0, $this->validation_responses, 'The key must not be validated.' );
	}

	public function test_admin_with_nonce_is_allowed() {
		wp_set_current_user( self::factory()->user->create( array( 'role' => 'administrator' ) ) );

		$_POST['_wpnonce'] = wp_create_nonce( 'wl_validate_key' );
		$_POST['key']      = 'key123';

		$response = $this->call_validate_key();

		$this->assertTrue( $response->success );
		$this->assertTrue( $response->data->valid );
		$this->assertEquals( 1, $this->validation_responses, 'The key must be validated.' );
	}

	/**
	 * Call the `wl_validate_key` AJAX action and return the decoded response.
	 *
	 * @return object The decoded JSON response.
	 */
	private function call_validate_key() {
		try {
			$this->_handleAjax( 'wl_validate_key' );
		} catch ( WPAjaxDieContinueException $e ) {
		}

		$response = json_decode( $this->_last_response );

		$this->assertInternalType( 'object', $response, "Unexpected response: $this->_last_response" );
		$this->assertObjectHasAttribute( 'success', $response );

		return $response;
	}

}
