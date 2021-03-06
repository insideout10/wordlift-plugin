<?php

use Wordlift\Videoobject\Parser\Parser_Factory;

/**
 * Class Videoobject_Classic_Editor_Test
 * @group videoobject
 */
class Videoobject_Classic_Editor_Test extends Wordlift_Unit_Test_Case {

	public function test_should_get_video_object_from_classic_editor_for_youtube_link() {

		$post_content = <<<EOF
https://www.youtube.com/watch?v=fJAPDAK4GiI
EOF;
		$post_id      = $this->create_post_with_content( $post_content );
		$parser       = Parser_Factory::get_parser( Parser_Factory::CLASSIC_EDITOR );
		$videos       = $parser->get_videos( $post_id );
		$this->assertCount( 1, $videos );
		$this->assertEquals( 'https://www.youtube.com/watch?v=fJAPDAK4GiI', $videos[0]->get_url() );
	}

	public function test_should_not_get_video_object_from_classic_editor_if_video_url_is_inside_link() {
		$post_content = <<<EOF
<a href='https://www.youtube.com/watch?v=fJAPDAK4GiI'>my video</a>
EOF;
		$post_id      = $this->create_post_with_content( $post_content );
		$parser       = Parser_Factory::get_parser( Parser_Factory::CLASSIC_EDITOR );
		$videos       = $parser->get_videos( $post_id );
		$this->assertCount( 0, $videos );
	}

	public function test_should_get_video_object_from_classic_editor_for_vimeo_link() {

		$post_content = <<<EOF
https://vimeo.com/162427937
EOF;
		$post_id      = $this->create_post_with_content( $post_content );
		$parser       = Parser_Factory::get_parser( Parser_Factory::CLASSIC_EDITOR );
		$videos       = $parser->get_videos( $post_id );
		$this->assertCount( 1, $videos );
		$this->assertEquals( 'https://vimeo.com/162427937', $videos[0]->get_url() );
	}


	public function test_should_get_multiple_videos_from_classic_editor() {
		$post_content = <<<EOF
https://vimeo.com/162427937
https://www.youtube.com/watch?v=fJAPDAK4GiI
EOF;
		$post_id      = $this->create_post_with_content( $post_content );
		$parser       = Parser_Factory::get_parser( Parser_Factory::CLASSIC_EDITOR );
		$videos       = $parser->get_videos( $post_id );
		$this->assertCount( 2, $videos );
	}



}