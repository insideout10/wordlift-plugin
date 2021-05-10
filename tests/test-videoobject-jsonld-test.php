<?php


use Wordlift\Features\Features_Registry;
use Wordlift\Videoobject\Data\Video\Video;
use Wordlift\Videoobject\Data\Video_Storage\Video_Storage_Factory;
use Wordlift\Videoobject\Provider\Vimeo;
use Wordlift\Videoobject\Provider\Youtube;

/**
 * Class Videoobject_Jsonld_Test
 * @group videoobject
 */
class Videoobject_Jsonld_Test extends \Wordlift_Videoobject_Unit_Test_Case {

	public function setUp() {
		parent::setUp();
		if ( ! getenv( 'YOUTUBE_DATA_API_KEY' ) || ! getenv( 'VIMEO_API_KEY' ) ) {
			$this->markTestSkipped( 'Test skipped because it requires youtube data api key to perform assertions' );
		}
		update_option( Youtube::YT_API_FIELD_NAME, getenv( 'YOUTUBE_DATA_API_KEY' ) );
		update_option( Vimeo::API_FIELD_NAME, getenv( 'VIMEO_API_KEY' ) );
	}

	public function test_given_post_id_should_generate_correct_jsonld() {
		$post_id       = $this->factory()->post->create();
		$video_storage = Video_Storage_Factory::get_storage();
		$video         = new Video();
		$video_storage->add_video( $post_id, $video );
		$jsonld = apply_filters( 'wl_post_jsonld', array(), $post_id, array() );
		$this->assertArrayHasKey( 'video', $jsonld );
		$this->assertCount( 1, $jsonld['video'] );
		$single_video = $jsonld['video'][0];
		$this->assertArrayHasKey( '@type', $single_video );
		$this->assertArrayHasKey( 'name', $single_video );
		$this->assertArrayHasKey( 'description', $single_video );
		$this->assertArrayHasKey( 'contentUrl', $single_video );
		$this->assertArrayHasKey( 'embedUrl', $single_video );
		$this->assertArrayHasKey( 'uploadDate', $single_video );
		$this->assertArrayHasKey( 'thumbnailUrl', $single_video );
		$this->assertArrayHasKey( 'duration', $single_video );
		$this->assertFalse( array_key_exists( 'interactionStatistic', $single_video ) );
	}


	public function test_when_youtube_video_is_saved_should_generate_correct_jsonld() {
		$post_id       = $this->create_post_with_content( Videoobject_Api_Test::multiple_youtube_video_post_content() );
		$video_storage = Video_Storage_Factory::get_storage();
		$jsonld        = apply_filters( 'wl_post_jsonld', array(), $post_id, array() );
		$this->assertArrayHasKey( 'video', $jsonld );
		$this->assertCount( 2, $jsonld['video'] );
		$first_video = $jsonld['video'][0];
		$this->assertNotNull( $first_video['interactionStatistic']['userInteractionCount'] );
		$this->assertTrue( is_numeric( $first_video['interactionStatistic']['userInteractionCount'] ), 'Views should be correctly added for youtube videos' );
	}

	public function test_when_vimeo_video_is_saved_should_generate_correct_jsonld() {
		$post_id       = $this->create_post_with_content( Videoobject_Api_Test::get_multiple_vimeo_videos_post_content() );
		$video_storage = Video_Storage_Factory::get_storage();
		$jsonld        = apply_filters( 'wl_post_jsonld', array(), $post_id, array() );
		$this->assertArrayHasKey( 'video', $jsonld );
		$this->assertCount( 2, $jsonld['video'] );
		$first_video = $jsonld['video'][1];
		$this->assertNotNull( $first_video['interactionStatistic']['userInteractionCount'] );
		$this->assertTrue( is_numeric( $first_video['interactionStatistic']['userInteractionCount'] ), 'Views should be correctly added for youtube videos' );
	}


	public function test_when_jsonld_is_created_for_article_should_not_add_video_object() {
		$post_id = $this->create_post_with_content( Videoobject_Api_Test::multiple_youtube_video_post_content() );
		$jsonld  = apply_filters( 'wl_after_get_jsonld', array(
			array('@type' => 'Article')
		), $post_id, array() );
		$this->assertCount( 1, $jsonld, 'Video object should not be added for article' );
	}

	public function test_when_jsonld_is_created_for_entity_should_add_video_object() {
		$post_id = $this->create_post_with_content( Videoobject_Api_Test::multiple_youtube_video_post_content() );
		$jsonld  = apply_filters( 'wl_after_get_jsonld', array(
			array('@type' => 'Thing')
		), $post_id, array() );
		$this->assertCount( 3, $jsonld, 'Video object should  be added for Entity' );
		$this->assertArrayHasKey('@context', $jsonld[2]);
	}

	public function test_when_jsonld_is_created_for_entity_type_array_should_add_video_object() {
		$post_id = $this->create_post_with_content( Videoobject_Api_Test::multiple_youtube_video_post_content() );
		$jsonld  = apply_filters( 'wl_after_get_jsonld', array(
			array( '@type' => array( 'Thing' ) )
		), $post_id, array() );
		$this->assertCount( 3, $jsonld, 'Video object should  be added for Entity' );
	}

}