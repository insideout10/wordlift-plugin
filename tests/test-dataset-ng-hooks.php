<?php

use Wordlift\Api\Api_Service;
use Wordlift\Api\Response;
use Wordlift\Dataset\Sync_Object_Adapter_Factory;
use Wordlift\Dataset\Sync_Service;
use Wordlift\Jsonld\Jsonld_Service;
use Wordlift\Object_Type_Enum;

/**
 * Test Hooks.
 *
 * @group dataset-ng
 */
class Test_Dataset_Ng_Hooks extends Wordlift_Unit_Test_Case {

	/**
	 * @var Api_Service
	 */
	private $api_service_mock;

	/**
	 * @var Jsonld_Service
	 */
	private $jsonld_service_mock;

	/**
	 * @var Sync_Service
	 */
	private $sync_service;
	/**
	 * @var Sync_Object_Adapter_Factory|WP_UnitTest_Factory|null
	 */
	private $sync_object_adapter_factory;

	function setUp() {
		parent::setUp();

		// These tests make sense only if `WL_FEATURES__DATASET_NG` is enabled.
		if ( ! wp_validate_boolean( getenv( 'WL_FEATURES__DATASET_NG' ) ) ) {
			$this->markTestSkipped( '`WL_FEATURES__DATASET_NG` not enabled.' );
		}

		$this->api_service_mock            = $this->getMockBuilder( 'Wordlift\Api\Api_Service' )
		                                          ->disableOriginalConstructor()
		                                          ->getMock();
		$this->jsonld_service_mock         = $this->getMockBuilder( 'Wordlift\Jsonld\Jsonld_Service' )
		                                          ->disableOriginalConstructor()
		                                          ->getMock();
		$this->sync_object_adapter_factory = new Sync_Object_Adapter_Factory( $this->jsonld_service_mock );
//		$this->sync_service                = new Sync_Service( $this->api_service_mock, $this->sync_object_adapter_factory );

	}

	function test_object_adapter() {

		$post_id = $this->factory()->post->create( array(
			'post_title'   => 'Title 123',
			'post_content' => 'Content 123'
		) );

		$this->jsonld_service_mock->method( 'get' )
		                          ->with(
			                          self::equalTo( Object_Type_Enum::POST ),
			                          self::equalTo( $post_id ) )
		                          ->will( $this->onConsecutiveCalls(
			                          array( 'prop' => 'value' ),
			                          array( 'prop' => 'value' ),
			                          array( 'prop' => 'value' ),
			                          array( 'prop' => 'new_value' ) ) );

		$this->jsonld_service_mock->expects( $this->exactly( 4 ) )
		                          ->method( 'get' )
		                          ->with(
			                          self::equalTo( Object_Type_Enum::POST ),
			                          self::equalTo( $post_id ) );

		$object_adapter = $this->sync_object_adapter_factory->create( Object_Type_Enum::POST, $post_id );

		// 1x Jsonld_Service::get calls
		$this->assertEqualSets( array( 'prop' => 'value' ), $object_adapter->get_jsonld() );
		$this->assertTrue( $object_adapter->is_changed() );

		// 2x Jsonld_Service::get calls
		$this->assertEqualSets( array( 'prop' => 'value' ), $object_adapter->get_jsonld_and_update_hash() );
		$this->assertFalse( $object_adapter->is_changed() );

		// 1x Jsonld_Service::get call(s)
		$this->assertTrue( $object_adapter->is_changed() );

	}

//	function test_save_post() {
//
//		$this->api_service_mock->method( 'request' )
//		                       ->willReturn( new Response( array( 'response' => array( 'code' => 200 ) ) ) );
//
//		$this->jsonld_service_mock->method( 'get' )
//		                          ->willReturn( '{ "key": "value" }' );
//
//		// One for the post and one for the post meta.
//		$this->api_service_mock->expects( $this->exactly( 2 ) )
//		                       ->method( 'request' )
//		                       ->with(
//			                       $this->equalTo( 'POST' ),
//			                       $this->equalTo( '/middleware/dataset/batch' ),
//			                       $this->callback( function ( $arg ) {
//
//				                       return is_array( $arg )
//				                              && isset( $arg['Content-Type'] )
//				                              && 'application/json' === $arg['Content-Type']
//				                              && isset( $arg['X-Wordlift-Dataset-Sync-State-V1'] );
//			                       } ),
//			                       $this->isJson()
//		                       );
//
//		$this->factory()->post->create( array(
//			'post_title'   => 'Title 123',
//			'post_content' => 'Content 123'
//		) );
//
////		add_action( 'save_post', array( $this, 'sync_item' ) );
////		add_action( 'added_post_meta', array( $this, 'sync_item_on_meta_change' ), 10, 4 );
////		add_action( 'updated_post_meta', array( $this, 'sync_item_on_meta_change' ), 10, 4 );
////		add_action( 'deleted_post_meta', array( $this, 'sync_item_on_meta_change' ), 10, 4 );
////		add_action( 'delete_post', array( $this, 'delete_item' ) );
//
//	}

}
