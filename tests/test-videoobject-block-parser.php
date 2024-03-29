<?php

use Wordlift\Videoobject\Parser\Parser_Factory;

/**
 * Class Videoobject_Block_Parser_Test
 * @group videoobject
 */
class Videoobject_Block_Parser_Test extends \Wordlift_Unit_Test_Case {

	public function setUp() {
		parent::setUp();
		if ( ! function_exists('parse_blocks') ) {
			$this->markTestSkipped( 'Skipped because WP < 5.0 doesnt have parse_blocks method' );
		}
	}

	public function test_should_get_video_url_from_youtube_block() {
		$post_content = <<<EOF
<!-- wp:embed {"url":"https://www.youtube.com/watch?v=fJAPDAK4GiI","type":"video","providerNameSlug":"youtube","responsive":true,"className":"wp-embed-aspect-16-9 wp-has-aspect-ratio"} -->
<figure class="wp-block-embed is-type-video is-provider-youtube wp-block-embed-youtube wp-embed-aspect-16-9 wp-has-aspect-ratio"><div class="wp-block-embed__wrapper">
https://www.youtube.com/watch?v=fJAPDAK4GiI
</div></figure>
<!-- /wp:embed -->
EOF;
		$post_id = $this->factory()->post->create( array(
			'post_content' => $post_content
		) );
		$parser     = Parser_Factory::get_parser( Parser_Factory::BLOCK_EDITOR );
		$video_urls = $parser->get_videos( $post_id );
		$this->assertCount( 1, $video_urls );
		$this->assertEquals( 'https://www.youtube.com/watch?v=fJAPDAK4GiI', $video_urls[0]->get_url() );
	}


	public function test_should_get_video_from_vimeo_block() {
		$post_content = <<<EOF
<!-- wp:embed {"url":"https://vimeo.com/162427937","type":"video","providerNameSlug":"vimeo","responsive":true,"className":"wp-embed-aspect-16-9 wp-has-aspect-ratio"} -->
<figure class="wp-block-embed is-type-video is-provider-vimeo wp-block-embed-vimeo wp-embed-aspect-16-9 wp-has-aspect-ratio"><div class="wp-block-embed__wrapper">
https://vimeo.com/162427937
</div></figure>
<!-- /wp:embed -->
EOF;
		$post_id = $this->factory()->post->create( array(
			'post_content' => $post_content
		) );
		$parser     = Parser_Factory::get_parser( Parser_Factory::BLOCK_EDITOR );
		$video_urls = $parser->get_videos( $post_id );
		$this->assertCount( 1, $video_urls );
		$this->assertEquals( 'https://vimeo.com/162427937', $video_urls[0]->get_url() );

	}

	public function test_should_get_video_from_videopress_block() {
		$post_content = <<<EOF
<!-- wp:embed {"url":"https://vimeo.com/162427937","type":"video","providerNameSlug":"vimeo","responsive":true,"className":"wp-embed-aspect-16-9 wp-has-aspect-ratio"} -->
<figure class="wp-block-embed is-type-video is-provider-vimeo wp-block-embed-vimeo wp-embed-aspect-16-9 wp-has-aspect-ratio"><div class="wp-block-embed__wrapper">
https://vimeo.com/162427937
</div></figure>
<!-- /wp:embed -->
EOF;
		$post_id = $this->factory()->post->create( array(
			'post_content' => $post_content
		) );
		$parser     = Parser_Factory::get_parser( Parser_Factory::BLOCK_EDITOR );
		$video_urls = $parser->get_videos( $post_id );
		$this->assertCount( 1, $video_urls );
		$this->assertEquals( 'https://vimeo.com/162427937', $video_urls[0]->get_url() );

	}

}