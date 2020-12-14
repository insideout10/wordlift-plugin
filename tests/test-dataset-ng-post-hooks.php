<?php

use Wordlift\Dataset\Sync_Object_Adapter_Factory;
use Wordlift\Dataset\Sync_Post_Adapter;
use Wordlift\Dataset\Sync_Post_Hooks;
use Wordlift\Dataset\Sync_User_Adapter;
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

		// Remove the global filters, since we're going to call `shutdown`, we don't want that to have side effects.
		global $wp_filter;
		$wp_filter = array();

		$this->sync_service_mock = $this->getMockBuilder( 'Wordlift\Dataset\Sync_Service' )
		                                ->disableOriginalConstructor()
		                                ->getMock();
		$sync_object_factory     = new Sync_Object_Adapter_Factory();

		new Sync_Post_Hooks( $this->sync_service_mock, $sync_object_factory );

	}

	function test_save_post() {

		$this->sync_service_mock->method( 'sync_many' )
		                        ->willReturn( true );

		$this->sync_service_mock->expects( $this->once() )
		                        ->method( 'sync_many' )
		                        ->with( $this->callback( function ( $arg ) {
			                        return is_array( $arg )
			                               && 2 === count( $arg )
			                               && $arg[0] instanceof Sync_Post_Adapter
			                               && $arg[1] instanceof Sync_User_Adapter;
		                        } ) );

		$this->factory()->post->create( array( 'post_title' => 'Title 123', 'post_content' => 'Content 123' ) );
		do_action( 'shutdown' );

	}

	function test_added_post_meta() {

		$this->sync_service_mock->method( 'sync_many' )
		                        ->willReturn( true );

		$this->sync_service_mock->expects( $this->exactly( 2 ) )
		                        ->method( 'sync_many' )
		                        ->with( $this->callback( function ( $arg ) {
			                        return is_array( $arg )
			                               && 2 === count( $arg )
			                               && $arg[0] instanceof Sync_Post_Adapter
			                               && $arg[1] instanceof Sync_User_Adapter;
		                        } ) );

		$post_id = $this->factory()->post->create( array(
			'post_title'   => 'Title 123',
			'post_content' => 'Content 123'
		) );
		add_post_meta( $post_id, '_tmp_test_added_post_meta', 'tmp' );
		do_action( 'shutdown' );

	}

	function test_updated_post_meta() {

		$this->sync_service_mock->method( 'sync_many' )
		                        ->willReturn( true );

		$this->sync_service_mock->expects( $this->exactly( 3 ) )
		                        ->method( 'sync_many' )
		                        ->with( $this->callback( function ( $arg ) {
			                        return is_array( $arg )
			                               && 2 === count( $arg )
			                               && $arg[0] instanceof Sync_Post_Adapter
			                               && $arg[1] instanceof Sync_User_Adapter;
		                        } ) );

		$post_id = $this->factory()->post->create( array(
			'post_title'   => 'Title 123',
			'post_content' => 'Content 123'
		) );
		add_post_meta( $post_id, '_tmp_test_added_post_meta', 'tmp_1' );
		update_post_meta( $post_id, '_tmp_test_added_post_meta', 'tmp_2' );
		do_action( 'shutdown' );

	}

	function test_deleted_post_meta() {

		$this->sync_service_mock->method( 'sync_many' )
		                        ->willReturn( true );

		$this->sync_service_mock->expects( $this->exactly( 3 ) )
		                        ->method( 'sync_many' )
		                        ->with( $this->callback( function ( $arg ) {
			                        return is_array( $arg )
			                               && 2 === count( $arg )
			                               && $arg[0] instanceof Sync_Post_Adapter
			                               && $arg[1] instanceof Sync_User_Adapter;
		                        } ) );

		$post_id = $this->factory()->post->create( array(
			'post_title'   => 'Title 123',
			'post_content' => 'Content 123'
		) );
		add_post_meta( $post_id, '_tmp_test_added_post_meta', 'tmp_1' );
		delete_post_meta( $post_id, '_tmp_test_added_post_meta' );
		do_action( 'shutdown' );

	}

	function test_ignored_meta() {

		$this->sync_service_mock->method( 'sync_many' )
		                        ->willReturn( true );

		$this->sync_service_mock->expects( $this->once() )
		                        ->method( 'sync_many' )
		                        ->with( $this->callback( function ( $arg ) {
			                        return is_array( $arg )
			                               && 2 === count( $arg )
			                               && $arg[0] instanceof Sync_Post_Adapter
			                               && $arg[1] instanceof Sync_User_Adapter;
		                        } ) );

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
		do_action( 'shutdown' );

	}

	function test_delete_post() {

		$this->sync_service_mock->method( 'delete_one' )
		                        ->willReturn( true );

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
		do_action( 'shutdown' );

	}

	public function test_create_three_posts_sync_should_be_called_three_times() {
		$this->sync_service_mock->method( 'sync_many' )
		                        ->willReturn( true );

		$this->sync_service_mock->expects( $this->exactly( 3 ) )
		                        ->method( 'sync_many' )
		                        ->with( $this->callback( function ( $arg ) {
			                        return is_array( $arg )
			                               && 2 === count( $arg )
			                               && $arg[0] instanceof Sync_Post_Adapter
			                               && $arg[1] instanceof Sync_User_Adapter;
		                        } ) );

		$this->factory()->post->create( array( 'post_title' => 'Title 1', 'post_content' => 'Content 1' ) );
		$this->factory()->post->create( array( 'post_title' => 'Title 2', 'post_content' => 'Content 2' ) );
		$this->factory()->post->create( array( 'post_title' => 'Title 3', 'post_content' => 'Content 3' ) );
		do_action( 'shutdown' );
	}

	public function test_multiple_updates_to_post_meta_should_invoke_only_one_time() {
		$this->sync_service_mock->method( 'sync_many' )
		                        ->willReturn( true );

		$this->sync_service_mock->expects( $this->exactly( 1 ) )
		                        ->method( 'sync_many' )
		                        ->with( $this->callback( function ( $arg ) {
			                        return is_array( $arg )
			                               && 2 === count( $arg )
			                               && $arg[0] instanceof Sync_Post_Adapter
			                               && $arg[1] instanceof Sync_User_Adapter;
		                        } ) );


		$post_id = $this->factory()->post->create( array( 'post_title' => 'Title 3', 'post_content' => 'Content 3' ) );
		update_post_meta( $post_id, 'foo1', 'bar1' );
		delete_post_meta( $post_id, 'foo1' );
		update_post_meta( $post_id, 'foo2', 'bar2' );
		do_action( 'shutdown' );
	}

}
