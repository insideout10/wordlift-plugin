<?php

use Wordlift\Dataset\Sync_Post_Hooks;
use Wordlift\Object_Type_Enum;

/**
 * Test Hooks.
 *
 * @group dataset-ng
 */
class Test_Dataset_Ng_Post_Hooks extends Wordlift_Unit_Test_Case {

	/**
	 * @var PHPUnit_Framework_MockObject_MockObject
	 */
	private $sync_service_mock;

	function setUp() {
		parent::setUp();

		// These tests make sense only if `WL_FEATURES__DATASET_NG` is enabled.
		if ( ! wp_validate_boolean( getenv( 'WL_FEATURES__DATASET_NG' ) ) ) {
			$this->markTestSkipped( '`WL_FEATURES__DATASET_NG` not enabled.' );
		}

		$this->sync_service_mock = $this->getMockBuilder( 'Wordlift\Dataset\Sync_Service' )
		                                ->disableOriginalConstructor()
		                                ->getMock();

		new Sync_Post_Hooks( $this->sync_service_mock );

	}

	function test_save_post() {

		$this->sync_service_mock->method( 'sync_one' )
		                        ->willReturn( true );

		$this->sync_service_mock->expects( $this->once() )
		                        ->method( 'sync_one' )
		                        ->with(
			                        $this->equalTo( Object_Type_Enum::POST ),
			                        $this->isType( 'int' )
		                        );

		$this->factory()->post->create( array( 'post_title' => 'Title 123', 'post_content' => 'Content 123' ) );

	}

	function test_added_post_meta() {

		$this->sync_service_mock->method( 'sync_one' )
		                        ->willReturn( true );

		$this->sync_service_mock->expects( $this->exactly( 2 ) )
		                        ->method( 'sync_one' )
		                        ->with(
			                        $this->equalTo( Object_Type_Enum::POST ),
			                        $this->isType( 'int' )
		                        );

		$post_id = $this->factory()->post->create( array(
			'post_title'   => 'Title 123',
			'post_content' => 'Content 123'
		) );
		add_post_meta( $post_id, '_tmp_test_added_post_meta', 'tmp' );

	}

	function test_updated_post_meta() {

		$this->sync_service_mock->method( 'sync_one' )
		                        ->willReturn( true );

		$this->sync_service_mock->expects( $this->exactly( 3 ) )
		                        ->method( 'sync_one' )
		                        ->with(
			                        $this->equalTo( Object_Type_Enum::POST ),
			                        $this->isType( 'int' )
		                        );

		$post_id = $this->factory()->post->create( array(
			'post_title'   => 'Title 123',
			'post_content' => 'Content 123'
		) );
		add_post_meta( $post_id, '_tmp_test_added_post_meta', 'tmp_1' );
		update_post_meta( $post_id, '_tmp_test_added_post_meta', 'tmp_2' );

	}

	function test_deleted_post_meta() {

		$this->sync_service_mock->method( 'sync_one' )
		                        ->willReturn( true );

		$this->sync_service_mock->expects( $this->exactly( 3 ) )
		                        ->method( 'sync_one' )
		                        ->with(
			                        $this->equalTo( Object_Type_Enum::POST ),
			                        $this->isType( 'int' )
		                        );

		$post_id = $this->factory()->post->create( array(
			'post_title'   => 'Title 123',
			'post_content' => 'Content 123'
		) );
		add_post_meta( $post_id, '_tmp_test_added_post_meta', 'tmp_1' );
		delete_post_meta( $post_id, '_tmp_test_added_post_meta' );

	}

	function test_ignored_meta() {

		$this->sync_service_mock->method( 'sync_one' )
		                        ->willReturn( true );

		$this->sync_service_mock->expects( $this->exactly( 1 ) )
		                        ->method( 'sync_one' )
		                        ->with(
			                        $this->equalTo( Object_Type_Enum::POST ),
			                        $this->isType( 'int' )
		                        );

		add_filter( 'wl_dataset__sync_post_hooks__ignored_meta_keys', function ( $args ) {
			$args[] = '_my_custom_field';

			return $args;
		} );

		$post_id = $this->factory()->post->create( array(
			'post_title'   => 'Title 123',
			'post_content' => 'Content 123'
		) );
		add_post_meta( $post_id, '_encloseme', 'tmp' );
		add_post_meta( $post_id, '_pingme', 'tmp' );
		add_post_meta( $post_id, 'entity_url', 'tmp' );
		add_post_meta( $post_id, '_my_custom_field', 'tmp' );

	}

	function test_delete_post() {

		$this->sync_service_mock->method( 'sync_one' )
		                        ->willReturn( true );

		$this->sync_service_mock->method( 'delete_one' )
		                        ->willReturn( true );

		$this->sync_service_mock->expects( $this->once() )
		                        ->method( 'sync_one' )
		                        ->with(
			                        $this->equalTo( Object_Type_Enum::POST ),
			                        $this->isType( 'int' )
		                        );

		$this->sync_service_mock->expects( $this->once() )
		                        ->method( 'delete_one' )
		                        ->with(
			                        $this->equalTo( Object_Type_Enum::POST ),
			                        $this->isType( 'int' )
		                        );

		$post_id = $this->factory()->post->create( array(
			'post_title'   => 'Title 123',
			'post_content' => 'Content 123'
		) );

		wp_delete_post( $post_id, true );

	}

}
