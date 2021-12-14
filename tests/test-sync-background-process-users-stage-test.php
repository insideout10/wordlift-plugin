<?php

namespace Wordlift\Dataset\Background\Stages;

use Wordlift\Object_Type_Enum;
use Wordlift_Unit_Test_Case;

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

	}

	public function test_get_sync_object_adapters() {

		$this->reset_db();

		$author_user = $this->factory->user->create();
		$this->factory()->post->create( array( 'post_author' => $author_user ) );

		// Duplicate user id shouldnt be returned.
		$this->factory()->post->create( array( 'post_author' => $author_user ) );



		// Invalid post type shouldnt be returned
		$invalid_post_type_user = $this->factory->user->create();
		$this->factory()->post->create( array( 'post_author' => $invalid_post_type_user, 'post_type' => 'invalid_post_type') );


		// Invalid post status shouldnt be returned
		$invalid_post_status_user = $this->factory->user->create();
		$this->factory()->post->create( array( 'post_author' => $invalid_post_status_user, 'post_status' => 'invalid_post_status') );


		// Create users without posts.
		$this->create_users_without_posts();

		$expected = array(
			$author_user,
		);

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
		$this->reset_db();

		$author_user = $this->factory->user->create();

		$this->factory()->post->create( array( 'post_author' => $author_user ) );
		$this->factory()->post->create( array( 'post_author' => $author_user ) );
		$this->create_users_without_posts();

		$this->assertEquals( 1, $this->sync_background_process_users_stage->count(), 'There should be 1 user .' );

	}


	protected function reset_db() {
		global $wpdb;
		// Reset users.
		$wpdb->query( "DELETE FROM $wpdb->users" );
		// Reset posts.
		$wpdb->query( "DELETE FROM $wpdb->posts" );
	}

	/**
	 * @return void
	 */
	protected function create_users_without_posts() {
		// Create users who are not authors.
		$this->factory->user->create();
		$this->factory->user->create();
		$this->factory->user->create();
		$this->factory->user->create();
	}

}
