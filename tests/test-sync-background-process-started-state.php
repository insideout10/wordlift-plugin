<?php

namespace Wordlift\Dataset\Background;

use PHPUnit_Framework_MockObject_MockObject;
use stdClass;
use Wordlift\Object_Type_Enum;
use Wordlift_Unit_Test_Case;

/**
 * Class Sync_Background_Process_Started_State_Test
 *
 * @group sync
 *
 * @package Wordlift\Dataset\Background
 */
class Sync_Background_Process_Started_State_Test extends Wordlift_Unit_Test_Case {

	/**
	 * @var PHPUnit_Framework_MockObject_MockObject
	 */
	private $mock_sync_background_process;

	/**
	 * @var PHPUnit_Framework_MockObject_MockObject
	 */
	private $mock_sync_service;

	/**
	 * @var PHPUnit_Framework_MockObject_MockObject
	 */
	private $mock_sync_object_adapter_factory;

	/**
	 * @var Sync_Background_Process_Started_State
	 */
	private $sync_background_process_started_state;

	function setUp() {
		parent::setUp();

		$this->mock_sync_background_process =
			$this->getMockBuilder( 'Wordlift\Dataset\Background\Sync_Background_Process' )
			     ->disableOriginalConstructor()
			     ->setMethods( array( 'get_info',  'set_state', 'stop' ) )
			     ->getMock();

		$this->mock_sync_service =
			$this->getMockBuilder( 'Wordlift\Dataset\Sync_Service' )
			     ->disableOriginalConstructor()
			     ->setMethods( array( 'delete_all', 'sync_many' ) )
			     ->getMock();

		$this->mock_sync_object_adapter_factory =
			$this->getMockBuilder( 'Wordlift\Dataset\Sync_Object_Adapter_Factory' )
			     ->disableOriginalConstructor()
			     ->setMethods( array( 'create_many' ) )
			     ->getMock();

		$this->sync_background_process_started_state = new Sync_Background_Process_Started_State(
			$this->mock_sync_background_process,
			$this->mock_sync_service,
			$this->mock_sync_object_adapter_factory
		);
	}

	public function test_leave() {

		$this->mock_sync_background_process
			->expects( $this->once() )
			->method( 'set_state' )
			->with( $this->isNull() );

		$this->sync_background_process_started_state->leave();

	}

	public function test_task() {
		global $wp_filter;
		$wp_filter = array();
		$this->factory->term->create_many( 20 );

		update_option( '_wl_sync_background_process_offset', 6 );
		update_option( '_wl_sync_background_process_stage', 1 );
		update_option( '_wl_sync_background_process_count', array( 5, 10, 15 ) );

		$this->mock_sync_object_adapter_factory
			->expects( $this->once() )
			->method( 'create_many' )
			->with( $this->equalTo( Object_Type_Enum::TERM ), $this->callback( function ( $arg ) {
				// We expect 4 items, i.e. process count 10 - process offset 6.
				return 4 === count( $arg );
			} ) )
			->willReturn( array( 1, 2, 3, 4 ) );

		$this->mock_sync_service
			->expects( $this->once() )
			->method( 'sync_many' )
			->with( $this->equalTo( array( 1, 2, 3, 4 ) ), $this->isTrue() );

		$this->assertTrue(  $this->sync_background_process_started_state->task(array())->has_next() );

	}

	public function test_task_and_stop() {

		$this->create_users_with_post( 15 );

		update_option( '_wl_sync_background_process_offset', 10 );
		update_option( '_wl_sync_background_process_stage', 2 );
		update_option( '_wl_sync_background_process_count', array( 5, 10, 15 ) );

		$this->mock_sync_object_adapter_factory
			->expects( $this->once() )
			->method( 'create_many' )
			->with( $this->equalTo( Object_Type_Enum::USER ), $this->callback( function ( $arg ) {
				// We expect 4 items, i.e. process count 10 - process offset 6.
				return 5 === count( $arg );
			} ) )
			->willReturn( array( 1, 2, 3, 4, 5 ) );

		$this->mock_sync_service
			->expects( $this->once() )
			->method( 'sync_many' )
			->with( $this->equalTo( array( 1, 2, 3, 4, 5 ) ), $this->isTrue() );

		$this->assertFalse( $this->sync_background_process_started_state->task( true )->has_next() );

	}

	public function test_api_service__request() {

		$mock_retval    = new StdClass();
		$mock_retval->a = "a\nb";

		$this->mock_sync_background_process
			->expects( $this->once() )
			->method( 'get_info' )
			->willReturn( $mock_retval );

		$args = $this->sync_background_process_started_state->api_service__request( array( 'headers' => array() ) );

		$this->assertTrue( isset( $args['headers']['X-Wordlift-Dataset-Sync-State-V1'] ) );

		$this->assertEquals( '{"a":"a\nb"}', $args['headers']['X-Wordlift-Dataset-Sync-State-V1'] );

	}

	public function test_enter() {

		$this->mock_sync_service
			->expects( $this->once() )
			->method( 'delete_all' );

		$this->mock_sync_background_process
			->expects( $this->once() )
			->method( 'set_state' )
			->with( Sync_Background_Process::STATE_STARTED );



		$this->sync_background_process_started_state->enter();

	}

	private function create_users_with_post( $number_of_users_to_be_created ) {
		for ( $i = 0; $i < $number_of_users_to_be_created; $i++) {
			$user_id = $this->factory()->user->create();
			$this->factory()->post->create( array( 'post_author' => $user_id ) );
		}
	}

}
