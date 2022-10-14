<?php
/**
 * Test the {@link Wordlift_Key_Validation_Service} class.
 *
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * @since 3.38.6
 *
 * @package    Wordlift
 * @subpackage Wordlift/tests
 */


/**
 * Define the Key_Validation_Service_Test.
 *
 * @since 3.38.6
 * @group backend
 */
class Key_Validation_Service_Test extends Wordlift_Unit_Test_Case {

	/**
	 * @var Wordlift_Key_Validation_Service
	 */
	private $key_validation_service;


	public function setUp() {
		parent::setUp();
		$this->key_validation_service = new Wordlift_Key_Validation_Service();
	}

	public function tearDown() {
		parent::tearDown();
		remove_filter('pre_http_request',  array( $this, '_wl_mock_http_request') );
	}

	public function test_when_key_is_validated_should_set_network_dataset_ids() {
		add_filter( 'pre_http_request', array( $this, '_wl_mock_http_request'), 10, 3 );
		$this->key_validation_service->get_account_info('my_key_linked_to_network_datasets');
		$this->assertEquals(
			Wordlift_Configuration_Service::get_instance()->get_network_dataset_ids(),
			array('one', 'two', 'three'),
			"Network dataset ids should be synced"
		);
	}

	public function _wl_mock_http_request($response, $request, $url) {

		if ( 'Key my_key_linked_to_network_datasets'  === $request['headers']['Authorization'] ) {

			return array(
				'body'     => file_get_contents( __DIR__ . '/assets/key-validation-response-with-network-datasets.json' ),
				'headers'  => array( 'content-type' => 'application/json' ),
				'response' => array( 'code' => 200, )
			);
		}
	}

}
