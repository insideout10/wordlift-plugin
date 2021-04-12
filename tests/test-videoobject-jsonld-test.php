<?php


use Wordlift\Features\Features_Registry;
use Wordlift\Videoobject\Data\Video\Video;
use Wordlift\Videoobject\Data\Video_Storage\Video_Storage_Factory;

/**
 * Class Videoobject_Jsonld_Test
 * @group videoobject
 */
class Videoobject_Jsonld_Test extends \Wordlift_Unit_Test_Case {

	public function setUp() {
		parent::setUp();
		Features_Registry::get_instance()->clear_all();
		/**
		 * Enable videoobject for tests
		 */
		add_filter( 'wl_feature__enable__videoobject', '__return_true' );
		run_wordlift();
		Features_Registry::get_instance()->initialize_all_features();

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
	}


}