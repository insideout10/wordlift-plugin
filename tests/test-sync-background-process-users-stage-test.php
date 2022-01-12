<?php

use Wordlift\Dataset\Background\Stages\Sync_Background_Process_Users_Stage;
use Wordlift\Object_Type_Enum;

/**
 * Class Sync_Background_Process_Users_Stage_Test
 *
 * @group sync
 *
 * @package Wordlift\Dataset\Background\Stages
 */
class Sync_Background_Process_Users_Stage_Test extends Wordlift_Unit_Test_Case {

	/**
	 * @var \PHPUnit_Framework_MockObject_MockObject|Wordlift\Dataset\Sync_Object_Adapter_Factory
	 */
	private $mock_sync_object_adapter_factory;
	/**
	 * @var Sync_Background_Process_Users_Stage
	 */
	private $sync_background_process_users_stage;

	function setUp() {
		parent::setUp();

		$this->mock_sync_object_adapter_factory =
			$this->getMockBuilder( 'Wordlift\Dataset\Sync_Object_Adapter_Factory' )
			     ->disableOriginalConstructor()
			     ->setMethods( array( 'create_many' ) )
			     ->getMock();

		$this->sync_background_process_users_stage =
			new Sync_Background_Process_Users_Stage( $this->mock_sync_object_adapter_factory );

		global $wpdb;
		// Reset users.
		$wpdb->query( "DELETE FROM $wpdb->users" );
		// Reset posts.
		$wpdb->query( "DELETE FROM $wpdb->posts" );

	}

	public function test_get_sync_object_adapters() {

		$author_user = $this->factory()->user->create();
		$this->factory()->post->create( array( 'post_author' => $author_user ) );

		// Duplicate user id shouldn't be returned.
		$this->factory()->post->create( array( 'post_author' => $author_user ) );

		$this->create_invalid_post_type_and_status_users();

		// Create users without posts.
		$this->factory()->user->create_many( 4 );

		$expected = array( $author_user, );

		$this->mock_sync_object_adapter_factory->expects( $this->once() )
		                                       ->method( 'create_many' )
		                                       ->with(
			                                       $this->equalTo( Object_Type_Enum::USER ),
			                                       $this->equalTo( $expected )
		                                       )
		                                       ->willReturn( array() );

		$this->sync_background_process_users_stage->get_sync_object_adapters( 0, 10 );

	}

	public function test_count() {

		$author_user = $this->factory()->user->create();

		$this->factory()->post->create( array( 'post_author' => $author_user ) );
		$this->factory()->post->create( array( 'post_author' => $author_user ) );
		$this->create_invalid_post_type_and_status_users();
		$this->factory()->user->create_many( 4 );

		$this->assertEquals( 1, $this->sync_background_process_users_stage->count(), 'There should be 1 user .' );

	}

	/**
	 * @return void
	 */
	protected function create_invalid_post_type_and_status_users() {
		// Invalid post type shouldn't be returned
		$invalid_post_type_user = $this->factory()->user->create();
		$this->factory()->post->create( array(
			'post_author' => $invalid_post_type_user,
			'post_type'   => 'invalid_post_type'
		) );


		// Invalid post status shouldn't be returned
		$invalid_post_status_user = $this->factory()->user->create();
		$this->factory()->post->create( array(
			'post_author' => $invalid_post_status_user,
			'post_status' => 'invalid_post_status'
		) );
	}

}
