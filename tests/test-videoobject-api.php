<?php

use Wordlift\Videoobject\Data\Video\Video;
use Wordlift\Videoobject\Data\Video_Storage\Video_Storage_Factory;
use Wordlift\Videoobject\Provider\Vimeo;
use Wordlift\Videoobject\Provider\Youtube;

/**
 * Class Videoobject_Youtube_Api_Test
 * @group videoobject
 */
class Videoobject_Api_Test extends Wordlift_Videoobject_Unit_Test_Case {

	public function setUp() {
		parent::setUp();
		if ( ! getenv( 'YOUTUBE_DATA_API_KEY' ) || ! getenv( 'VIMEO_API_KEY' ) ) {
			$this->markTestSkipped( 'Test skipped because it requires youtube data api key to perform assertions' );
		}
		update_option( Youtube::YT_API_FIELD_NAME, getenv( 'YOUTUBE_DATA_API_KEY' ) );
		update_option( Vimeo::API_FIELD_NAME, getenv( 'VIMEO_API_KEY' ) );
	}

	public function test_on_save_post_with_youtube_video_should_store_it() {
		$post_content = <<<EOF
<!-- wp:embed {"url":"https://www.youtube.com/watch?v=fJAPDAK4GiI","type":"video","providerNameSlug":"youtube","responsive":true,"className":"wp-embed-aspect-16-9 wp-has-aspect-ratio"} -->
<figure class="wp-block-embed is-type-video is-provider-youtube wp-block-embed-youtube wp-embed-aspect-16-9 wp-has-aspect-ratio"><div class="wp-block-embed__wrapper">
https://www.youtube.com/watch?v=fJAPDAK4GiI
</div></figure>
<!-- /wp:embed -->
EOF;
		$post_id      = $this->create_post_with_content( $post_content );
		$videos       = Video_Storage_Factory::get_storage()->get_all_videos( $post_id );
		// we should have 1 video on the storage.
		$this->assertCount( 1, $videos );
		/**
		 * @var $video Video
		 */
		$video = $videos[0];
		// check all of the properties are not null.
		$this->assertNotNull( $video->name );
		$this->assertNotNull( $video->description );
		$this->assertNotNull( $video->thumbnail_urls );
		$this->assertNotNull( $video->upload_date );
		$this->assertNotNull( $video->duration );
		$this->assertNotNull( $video->content_url );
		$this->assertNotNull( $video->embed_url );
	}

	public function test_on_save_post_with_vimeo_video_should_store_it() {
		$post_content = <<<EOF
<!-- wp:embed {"url":"https://vimeo.com/162427937","type":"video","providerNameSlug":"vimeo","responsive":true,"className":"wp-embed-aspect-16-9 wp-has-aspect-ratio"} -->
<figure class="wp-block-embed is-type-video is-provider-vimeo wp-block-embed-vimeo wp-embed-aspect-16-9 wp-has-aspect-ratio"><div class="wp-block-embed__wrapper">
https://vimeo.com/162427937
</div></figure>
<!-- /wp:embed -->
EOF;
		$post_id      = $this->create_post_with_content( $post_content );
		$videos       = Video_Storage_Factory::get_storage()->get_all_videos( $post_id );
		// we should have 1 video on the storage.
		$this->assertCount( 1, $videos );
		/**
		 * @var $video Video
		 */
		$video = $videos[0];
		// check all of the properties are not null.
		$this->assertNotNull( $video->name );
		$this->assertNotNull( $video->description );
		$this->assertNotNull( $video->thumbnail_urls );
		$this->assertNotNull( $video->upload_date );
		$this->assertNotNull( $video->duration );
		$this->assertNotNull( $video->content_url );
		$this->assertNotNull( $video->embed_url );

	}

}