<?php

/**
 * @group entity
 */

class Test_Entity_Cached_Entity_Uri_Service extends Wordlift_Unit_Test_Case {

	/**
	 * @var PHPUnit_Framework_MockObject_MockObject|WP_UnitTest_Factory|Wordlift_Configuration_Service|null
	 */
	private $configuration_service_mock;

	/**
	 * @var PHPUnit_Framework_MockObject_MockObject|WP_UnitTest_Factory|Wordlift_Cache_Service|null
	 */
	private $cache_service_mock;

	/**
	 * @var Wordlift_Cached_Entity_Uri_Service|WP_UnitTest_Factory|null
	 */
	private $cached_entity_uri_service;

	function setUp() {
		parent::setUp();

		// Remove global filters.
		global $wp_filter;
		$wp_filter = array();

		$configuration_service_mock      = $this->getMockBuilder( 'Wordlift_Configuration_Service' )
		                                        ->disableOriginalConstructor()
		                                        ->getMock();
		$this->cache_service_mock        = $this->getMockBuilder( 'Wordlift_Cache_Service' )
		                                        ->disableOriginalConstructor()
		                                        ->getMock();
		$this->cached_entity_uri_service = new Wordlift_Cached_Entity_Uri_Service( $this->cache_service_mock );

	}

	function test_skipped_meta_key() {

		$retval = $this->cached_entity_uri_service->on_before_post_meta_change( null, null, 'key123', null );

		$this->assertNull( $retval );

	}

	function test_entity_url__post_meta_unset() {

		$this->cache_service_mock->method( 'delete_cache' )
		                         ->with( $this->equalTo( 'http://localdomain.localhost' ) );

		$this->cache_service_mock->expects( $this->once() )
		                         ->method( 'delete_cache' )
		                         ->with( $this->equalTo( 'http://localdomain.localhost' ) );

		$this->cached_entity_uri_service->on_before_post_meta_change( null, null, WL_ENTITY_URL_META_NAME, 'http://localdomain.localhost' );

	}


	function test_entity_url__post_meta_set() {

		$this->cache_service_mock->method( 'delete_cache' )
		                         ->with( $this->stringStartsWith( 'http://localdomain.localhost/' ) );

		$this->cache_service_mock->expects( $this->exactly( 3 ) )
		                         ->method( 'delete_cache' )
		                         ->withConsecutive(
		                         // This is called when the `update_post_meta` is called.
			                         array( $this->equalTo( 'http://localdomain.localhost/0' ) ),
			                         // These are called when we call `on_before_post_meta_change`.
			                         array( $this->equalTo( 'http://localdomain.localhost/0' ) ),
			                         array( $this->equalTo( 'http://localdomain.localhost/1' ) ) );

		$post_id = $this->factory()->post->create( array(
			'post_title'   => 'Title 123',
			'post_content' => 'Content 123',
		) );
		update_post_meta( $post_id, WL_ENTITY_URL_META_NAME, 'http://localdomain.localhost/0' );

		$this->cached_entity_uri_service->on_before_post_meta_change( null, $post_id, WL_ENTITY_URL_META_NAME, 'http://localdomain.localhost/1' );

	}

	function test_entity_url__corrupted_post_meta() {

		$this->cache_service_mock->method( 'delete_cache' )
		                         ->with( $this->stringStartsWith( 'http://localdomain.localhost/' ) );

		$this->cache_service_mock->expects( $this->once() )
		                         ->method( 'delete_cache' )
		                         ->with( $this->equalTo( 'http://localdomain.localhost/1' ) );

		$post_id = $this->factory()->post->create( array(
			'post_title'   => 'Title 123',
			'post_content' => 'Content 123',
		) );

		add_filter( 'get_post_metadata', array( $this, '_get_post_metadata__return_number' ), PHP_INT_MAX, 4 );

		$this->cached_entity_uri_service->on_before_post_meta_change( null, $post_id, WL_ENTITY_URL_META_NAME, 'http://localdomain.localhost/1' );

	}

	function test_entity_url__meta_value_null() {

		$this->cache_service_mock->method( 'delete_cache' )
		                         ->with( $this->stringStartsWith( 'http://localdomain.localhost/' ) );

		$this->cache_service_mock->expects( $this->exactly( 2 ) )
		                         ->method( 'delete_cache' )
		                         ->withConsecutive(
		                         // This is called when the `update_post_meta` is called.
			                         array( $this->equalTo( 'http://localdomain.localhost/0' ) ),
			                         // These are called when we call `on_before_post_meta_change`.
			                         array( $this->equalTo( 'http://localdomain.localhost/0' ) ) );

		$post_id = $this->factory()->post->create( array(
			'post_title'   => 'Title 123',
			'post_content' => 'Content 123',
		) );
		update_post_meta( $post_id, WL_ENTITY_URL_META_NAME, 'http://localdomain.localhost/0' );

		$this->cached_entity_uri_service->on_before_post_meta_change( null, $post_id, WL_ENTITY_URL_META_NAME, null );

	}

	function _get_post_metadata__return_number( $meta_id, $object_id, $meta_key, $single ) {

		return 123;
	}

}
