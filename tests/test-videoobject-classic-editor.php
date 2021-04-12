<?php

use Wordlift\Videoobject\Parser\Parser_Factory;

/**
 * Class Videoobject_Classic_Editor_Test
 * @group videoobject
 */
class Videoobject_Classic_Editor_Test extends Wordlift_Unit_Test_Case {

	public function test_should_get_video_object_from_classic_editor() {

		$post_content = <<<EOF
https://www.youtube.com/watch?v=fJAPDAK4GiI
EOF;
		$post_id      = $this->create_post_with_content( $post_content );
		$parser       = Parser_Factory::get_parser( Parser_Factory::CLASSIC_EDITOR );
		$videos       = $parser->get_videos( $post_id );
		$this->assertCount( 1, $videos );
		$this->assertEquals( 'https://www.youtube.com/watch?v=fJAPDAK4GiI', $videos[0]->get_url() );
	}


}