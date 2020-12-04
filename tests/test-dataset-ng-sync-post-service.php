<?php

use Wordlift\Api\Response;
use Wordlift\Dataset\Sync_Object_Adapter_Factory;
use Wordlift\Dataset\Sync_Service;
use Wordlift\Object_Type_Enum;

/**
 * Class Test_Dataset_Ng
 *
 * @group dataset-ng
 */
class Test_Dataset_Ng_Sync_Post_Service extends Wordlift_Unit_Test_Case {

	/**
	 * @var PHPUnit_Framework_MockObject_MockObject|WP_UnitTest_Factory|Wordlift\Api\Api_Service|null
	 */
	private $api_service_mock;

	/**
	 * @var PHPUnit_Framework_MockObject_MockObject|WP_UnitTest_Factory|Wordlift\Jsonld\Jsonld_Service|null
	 */
	private $jsonld_service_mock;

	/**
	 * @var Sync_Object_Adapter_Factory|WP_UnitTest_Factory|null
	 */
	private $sync_object_factory;

	/**
	 * @var Sync_Service|WP_UnitTest_Factory|null
	 */
	private $sync_service;
	/**
	 * @var PHPUnit_Framework_MockObject_MockObject|WP_UnitTest_Factory|Wordlift_Entity_Service|null
	 */
	private $entity_service_mock;

	/**
	 */
	function setUp() {
		parent::setUp();

		if ( ! wp_validate_boolean( getenv( 'WL_FEATURES__DATASET_NG' ) ) ) {
			$this->markTestSkipped( '`WL_FEATURES__DATASET_NG` not enabled.' );
		}

		$this->api_service_mock    = $this->getMockBuilder( 'Wordlift\Api\Api_Service' )
		                                  ->disableOriginalConstructor()
		                                  ->getMock();
		$this->jsonld_service_mock = $this->getMockBuilder( 'Wordlift\Jsonld\Jsonld_Service' )
		                                  ->disableOriginalConstructor()
		                                  ->getMock();
		$this->sync_object_factory = new Sync_Object_Adapter_Factory();

		$this->sync_service = new Sync_Service( $this->api_service_mock, $this->sync_object_factory, $this->jsonld_service_mock, Wordlift_Entity_Service::get_instance()  );
	}

	function test_sync_one__hash_equals() {
		$post_id = $this->factory()->post->create( array(
			'post_title'   => 'Title 123',
			'post_content' => 'Content 123',
		) );
		update_post_meta( $post_id, Sync_Service::JSONLD_HASH, sha1( wp_json_encode( array( 'key123' => 'value123' ) ) ) );

		$this->jsonld_service_mock->method( 'get' )
		                          ->with(
			                          $this->equalTo( Object_Type_Enum::POST ),
			                          $this->equalTo( $post_id ) )
		                          ->willReturn( array( 'key123' => 'value123' ) );

		$this->jsonld_service_mock->expects( $this->once() )
		                          ->method( 'get' )
		                          ->with(
			                          $this->equalTo( Object_Type_Enum::POST ),
			                          $this->equalTo( $post_id ) );

		$this->assertFalse( $this->sync_service->sync_one( Object_Type_Enum::POST, $post_id ) );
	}

	function test_sync_one__success() {
		$post_id = $this->factory()->post->create( array(
			'post_title'   => 'Title 123',
			'post_content' => 'Content 123',
		) );

		$this->jsonld_service_mock->method( 'get' )
		                          ->with(
			                          $this->equalTo( Object_Type_Enum::POST ),
			                          $this->equalTo( $post_id ) )
		                          ->willReturn( array( 'key123' => 'value123' ) );

		$this->jsonld_service_mock->expects( $this->once() )
		                          ->method( 'get' )
		                          ->with(
			                          $this->equalTo( Object_Type_Enum::POST ),
			                          $this->equalTo( $post_id ) );

		$this->api_service_mock->method( 'request' )
		                       ->with(
			                       $this->equalTo( 'POST' ),
			                       $this->equalTo( '/middleware/dataset/batch' ),
			                       $this->equalTo( array( 'Content-Type' => 'application/json', ) ),
			                       $this->isJson() )
		                       ->willReturn( new Response( array(
			                       'response' => array( 'code' => 200 ),
			                       'body'     => '',
		                       ) ) );

		$this->api_service_mock->expects( $this->once() )
		                       ->method( 'request' )
		                       ->with(
			                       $this->equalTo( 'POST' ),
			                       $this->equalTo( '/middleware/dataset/batch' ),
			                       $this->equalTo( array( 'Content-Type' => 'application/json', ) ),
			                       $this->isJson() );

		$this->assertTrue( $this->sync_service->sync_one( Object_Type_Enum::POST, $post_id ) );
		$this->assertNotEmpty( get_post_meta( $post_id, Sync_Service::SYNCED_GMT, true ) );
		$this->assertEquals(
			sha1( wp_json_encode( array( 'key123' => 'value123' ) ) ),
			get_post_meta( $post_id, Sync_Service::JSONLD_HASH, true )
		);
	}

	function test_sync_one__success__with_existing_hash() {
		$post_id = $this->factory()->post->create( array(
			'post_title'   => 'Title 123',
			'post_content' => 'Content 123',
		) );
		update_post_meta( $post_id, Sync_Service::JSONLD_HASH, 'hash123' );

		$this->jsonld_service_mock->method( 'get' )
		                          ->with(
			                          $this->equalTo( Object_Type_Enum::POST ),
			                          $this->equalTo( $post_id ) )
		                          ->willReturn( array( 'key123' => 'value123' ) );

		$this->jsonld_service_mock->expects( $this->once() )
		                          ->method( 'get' )
		                          ->with(
			                          $this->equalTo( Object_Type_Enum::POST ),
			                          $this->equalTo( $post_id ) );

		$this->api_service_mock->method( 'request' )
		                       ->with(
			                       $this->equalTo( 'POST' ),
			                       $this->equalTo( '/middleware/dataset/batch' ),
			                       $this->equalTo( array( 'Content-Type' => 'application/json', ) ),
			                       $this->isJson() )
		                       ->willReturn( new Response( array(
			                       'response' => array( 'code' => 200 ),
			                       'body'     => '',
		                       ) ) );

		$this->api_service_mock->expects( $this->once() )
		                       ->method( 'request' )
		                       ->with(
			                       $this->equalTo( 'POST' ),
			                       $this->equalTo( '/middleware/dataset/batch' ),
			                       $this->equalTo( array( 'Content-Type' => 'application/json', ) ),
			                       $this->isJson() );

		$this->assertTrue( $this->sync_service->sync_one( Object_Type_Enum::POST, $post_id ) );
		$this->assertNotEmpty( get_post_meta( $post_id, Sync_Service::SYNCED_GMT, true ) );
		$this->assertEquals(
			sha1( wp_json_encode( array( 'key123' => 'value123' ) ) ),
			get_post_meta( $post_id, Sync_Service::JSONLD_HASH, true )
		);
	}

	function test_sync_one__failure() {
		$post_id = $this->factory()->post->create( array(
			'post_title'   => 'Title 123',
			'post_content' => 'Content 123',
		) );

		$this->jsonld_service_mock->method( 'get' )
		                          ->with(
			                          $this->equalTo( Object_Type_Enum::POST ),
			                          $this->equalTo( $post_id ) )
		                          ->willReturn( array( 'key123' => 'value123' ) );

		$this->jsonld_service_mock->expects( $this->once() )
		                          ->method( 'get' )
		                          ->with(
			                          $this->equalTo( Object_Type_Enum::POST ),
			                          $this->equalTo( $post_id ) );

		$this->api_service_mock->method( 'request' )
		                       ->with(
			                       $this->equalTo( 'POST' ),
			                       $this->equalTo( '/middleware/dataset/batch' ),
			                       $this->equalTo( array( 'Content-Type' => 'application/json', ) ),
			                       $this->isJson() )
		                       ->willReturn( new Response( array(
			                       'response' => array( 'code' => 500 ),
			                       'body'     => '',
		                       ) ) );

		$this->api_service_mock->expects( $this->once() )
		                       ->method( 'request' )
		                       ->with(
			                       $this->equalTo( 'POST' ),
			                       $this->equalTo( '/middleware/dataset/batch' ),
			                       $this->equalTo( array( 'Content-Type' => 'application/json', ) ),
			                       $this->isJson() );

		$this->assertFalse( $this->sync_service->sync_one( Object_Type_Enum::POST, $post_id ) );
		$this->assertEmpty( get_post_meta( $post_id, Sync_Service::SYNCED_GMT, true ) );
		$this->assertEmpty( get_post_meta( $post_id, Sync_Service::JSONLD_HASH, true ) );
	}

	function test_sync_one__failure__with_existing_hash() {
		$post_id = $this->factory()->post->create( array(
			'post_title'   => 'Title 123',
			'post_content' => 'Content 123',
		) );
		update_post_meta( $post_id, Sync_Service::JSONLD_HASH, 'hash123' );

		$this->jsonld_service_mock->method( 'get' )
		                          ->with(
			                          $this->equalTo( Object_Type_Enum::POST ),
			                          $this->equalTo( $post_id ) )
		                          ->willReturn( array( 'key123' => 'value123' ) );

		$this->jsonld_service_mock->expects( $this->once() )
		                          ->method( 'get' )
		                          ->with(
			                          $this->equalTo( Object_Type_Enum::POST ),
			                          $this->equalTo( $post_id ) );

		$this->api_service_mock->method( 'request' )
		                       ->with(
			                       $this->equalTo( 'POST' ),
			                       $this->equalTo( '/middleware/dataset/batch' ),
			                       $this->equalTo( array( 'Content-Type' => 'application/json', ) ),
			                       $this->isJson() )
		                       ->willReturn( new Response( array(
			                       'response' => array( 'code' => 500 ),
			                       'body'     => '',
		                       ) ) );

		$this->api_service_mock->expects( $this->once() )
		                       ->method( 'request' )
		                       ->with(
			                       $this->equalTo( 'POST' ),
			                       $this->equalTo( '/middleware/dataset/batch' ),
			                       $this->equalTo( array( 'Content-Type' => 'application/json', ) ),
			                       $this->isJson() );

		$this->assertFalse( $this->sync_service->sync_one( Object_Type_Enum::POST, $post_id ) );
		$this->assertEmpty( get_post_meta( $post_id, Sync_Service::SYNCED_GMT, true ) );
		$this->assertEquals( 'hash123', get_post_meta( $post_id, Sync_Service::JSONLD_HASH, true ) );
	}

	function test_delete() {
		$post_id = $this->factory()->post->create( array(
			'post_title'   => 'Title 123',
			'post_content' => 'Content 123',
		) );
		update_post_meta( $post_id, 'entity_url', 'https://localdomain.localhost' );

		$this->api_service_mock->method( 'request' )
		                       ->with(
			                       $this->equalTo( 'DELETE' ),
			                       $this->equalTo( '/middleware/dataset?uri=https%3A%2F%2Flocaldomain.localhost' ) )
		                       ->willReturn( new Response( array(
			                       'response' => array( 'code' => 200 ),
			                       'body'     => '',
		                       ) ) );

		$this->api_service_mock->expects( $this->once() )
		                       ->method( 'request' )
		                       ->with(
			                       $this->equalTo( 'DELETE' ),
			                       $this->equalTo( '/middleware/dataset?uri=https%3A%2F%2Flocaldomain.localhost' ) );

		$this->assertTrue( $this->sync_service->delete_one( Object_Type_Enum::POST, $post_id ) );

	}

}
