<?php

namespace Wordlift\Dataset\Background\Stages;

use Wordlift\Object_Type_Enum;
use Wordlift_Unit_Test_Case;

/**
 * Class Sync_Background_Process_Posts_Stage_Test
 *
 * @group sync
 *
 * @package Wordlift\Dataset\Background\Stages
 */
class Sync_Background_Process_Posts_Stage_Test extends Wordlift_Unit_Test_Case {

	/**
	 * @var \PHPUnit_Framework_MockObject_MockObject|Wordlift\Dataset\Sync_Object_Adapter_Factory
	 */
	private $mock_sync_object_adapter_factory;
	/**
	 * @var Sync_Background_Process_Posts_Stage
	 */
	private $sync_background_process_posts_stage;

	function setUp() {
		parent::setUp();

		$this->mock_sync_object_adapter_factory =
			$this->getMockBuilder( 'Wordlift\Dataset\Sync_Object_Adapter_Factory' )
			     ->disableOriginalConstructor()
			     ->setMethods( array( 'create_many' ) )
			     ->getMock();

		$this->sync_background_process_posts_stage =
			new Sync_Background_Process_Posts_Stage( $this->mock_sync_object_adapter_factory );

	}

	public function test_get_sync_object_adapters() {

		global $wpdb;
		$wpdb->query( "DELETE FROM $wpdb->posts" );

		$expected = array(
			$this->factory->post->create( array(
				'post_status' => 'publish'
			) ),
			$this->factory->post->create( array(
				'post_status' => 'future'
			) ),
			$this->factory->post->create( array(
				'post_status' => 'draft'
			) ),
			$this->factory->post->create( array(
				'post_status' => 'pending'
			) ),
			$this->factory->post->create( array(
				'post_status' => 'private'
			) ),
		);

		$this->factory->post->create( array(
			'post_status' => 'trash'
		) );

		$this->factory->post->create( array(
			'post_status' => 'auto-draft'
		) );

		$this->mock_sync_object_adapter_factory->expects( $this->once() )
		                                       ->method( 'create_many' )
		                                       ->with(
			                                       $this->equalTo( Object_Type_Enum::POST ),
			                                       $this->equalTo( $expected )
		                                       )
		                                       ->willReturn( array() );

		$this->sync_background_process_posts_stage->get_sync_object_adapters( 0, 10 );

	}

	public function test_count() {

		global $wpdb;
		$wpdb->query( "DELETE FROM $wpdb->posts" );

		$expected = array(
			$this->factory->post->create( array(
				'post_status' => 'publish'
			) ),
			$this->factory->post->create( array(
				'post_status' => 'future'
			) ),
			$this->factory->post->create( array(
				'post_status' => 'draft'
			) ),
			$this->factory->post->create( array(
				'post_status' => 'pending'
			) ),
			$this->factory->post->create( array(
				'post_status' => 'private'
			) ),
		);

		$this->factory->post->create( array(
			'post_status' => 'trash'
		) );

		$this->factory->post->create( array(
			'post_status' => 'auto-draft'
		) );

		$this->assertEquals( 5, $this->sync_background_process_posts_stage->count(), 'There must be 5 posts.' );

	}

}
