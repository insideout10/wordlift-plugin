<?php


use Wordlift\Videoobject\Data\Video\Video;
use Wordlift\Videoobject\Data\Video_Storage\Video_Storage_Factory;

class Vo_Jsonld_Test extends \Wordlift_Vocabulary_Unit_Test_Case {

	public function test_given_post_id_should_generate_correct_jsonld() {
		$post_id       = $this->factory()->post->create();
		$video_storage = Video_Storage_Factory::get_storage();
		$video         = new Video();
		$video_storage->add_video( $post_id, $video );
		$jsonld = apply_filters( 'wl_post_jsonld', array(), $post_id, array() );
		$this->assertArrayHasKey( 'video', $jsonld );
		$this->assertCount( 1, $jsonld['video'] );
	}


}