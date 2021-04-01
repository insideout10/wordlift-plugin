<?php

namespace Wordlift\Dataset\Background;

use Wordlift_Unit_Test_Case;

/**
 * Class Sync_Background_Process_Test
 *
 * @group sync
 *
 * @package Wordlift\Dataset\Background
 */
class Sync_Background_Process_Test extends Wordlift_Unit_Test_Case {

	private $mock_sync_service;

	private $mock_sync_object_adapter_factory;

	/**
	 * @var Sync_Background_Process
	 */
	private $sync_background_process;

	function setUp() {
		parent::setUp();

		$this->mock_sync_service =
			$this->getMockBuilder( 'Wordlift\Dataset\Sync_Service' )
			     ->disableOriginalConstructor()
			     ->setMethods( array( 'delete_all' ) )
			     ->getMock();

		$this->mock_sync_object_adapter_factory =
			$this->getMockBuilder( 'Wordlift\Dataset\Sync_Object_Adapter_Factory' )
			     ->disableOriginalConstructor()
			     ->setMethods( array() )
			     ->getMock();

		$this->sync_background_process =
			new Sync_Background_Process( $this->mock_sync_service, $this->mock_sync_object_adapter_factory );

	}

	public function test_stop() {

		$this->sync_background_process->stop();

		$this->assertEquals( Sync_Background_Process::STATE_STOPPED, $this->sync_background_process->get_state() );

	}

	public function test_start() {

		$this->mock_sync_service
			->expects( $this->once() )
			->method( 'delete_all' );

		$this->sync_background_process->start();

		$this->assertEquals( Sync_Background_Process::STATE_STARTED, $this->sync_background_process->get_state() );

	}

	public function test_set_state() {

		$this->sync_background_process->set_state( Sync_Background_Process::STATE_STARTED );
		$this->assertEquals( Sync_Background_Process::STATE_STARTED, $this->sync_background_process->get_state() );

		$this->sync_background_process->set_state( Sync_Background_Process::STATE_STOPPED );
		$this->assertEquals( Sync_Background_Process::STATE_STOPPED, $this->sync_background_process->get_state() );

		$this->sync_background_process->set_state( null );
		$this->assertEquals( Sync_Background_Process::STATE_STOPPED, $this->sync_background_process->get_state() );

	}

	public function test_get_info() {

		$this->assertEquals(
			'Wordlift\Dataset\Background\Sync_Background_Process_Info',
			get_class( $this->sync_background_process->get_info() )
		);

	}

}
