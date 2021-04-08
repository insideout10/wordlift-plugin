<?php

namespace Wordlift_Videoobject\Videoobject\Video\Jsonld;

class Vo_Jsonld_Test {

	public function test_given_post_id_should_generate_correct_jsonld() {
		$post_id = $this->factory()->post->create();
		$video_storage = Video_Storage::get_instance();
		$video  = new Video();
		$video_storage->add_video( $post_id, $video );
	}


}