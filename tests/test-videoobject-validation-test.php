<?php

use Wordlift\Videoobject\Data\Video_Storage\Video_Storage_Factory;
use Wordlift\Videoobject\Provider\Vimeo;
use Wordlift\Videoobject\Provider\Youtube;

/**
 * Class Videoobject_Validation_Test
 * @group videoobject
 */
class Videoobject_Validation_Test extends \Wordlift_Videoobject_Unit_Test_Case {
	public function setUp() {
		parent::setUp();
		if ( ! getenv( 'YOUTUBE_DATA_API_KEY' ) || ! getenv( 'VIMEO_API_KEY' ) ) {
			$this->markTestSkipped( 'Test skipped because it requires youtube data api key to perform assertions' );
		}
		update_option( Youtube::YT_API_FIELD_NAME, getenv( 'YOUTUBE_DATA_API_KEY' ) );
		update_option( Vimeo::API_FIELD_NAME, getenv( 'VIMEO_API_KEY' ) );
	}

//	public function test_should_group_videos_by_provider() {
//
//	}



	public function test_should_not_send_requests_for_the_videos_which_are_already_stored() {
		$post_content = Videoobject_Api_Test::multiple_youtube_video_post_content();
		$post_id      = $this->create_post_with_content( $post_content );
		$this->assertCount( 2, Video_Storage_Factory::get_storage()->get_all_videos( $post_id ), '2 videos should be present in video storage' );
		wp_update_post( array(
			'ID'           => $post_id,
			'post_content' => $post_content . "<br/>"
		) );
	}

	public function test_should_remove_all_the_videos_which_are_removed_from_post_content() {
		$post_content = Videoobject_Api_Test::multiple_youtube_video_post_content();
		$post_id      = $this->create_post_with_content( $post_content );
		$this->assertCount( 2, Video_Storage_Factory::get_storage()->get_all_videos( $post_id ), '2 videos should be present in video storage' );
		// When we update the post content, this videos should be removed.
		wp_update_post( array(
			'ID'           => $post_id,
			'post_content' => ''
		) );
		$this->assertCount( 0, Video_Storage_Factory::get_storage()->get_all_videos( $post_id ),
			'The videos not in the post content should be removed' );
	}


}