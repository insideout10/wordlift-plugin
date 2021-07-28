<?php

use Wordlift\Videoobject\Data\Video\Video;
use Wordlift\Videoobject\Data\Video_Storage\Video_Storage_Factory;
use Wordlift\Videoobject\Provider\Client\Youtube_Client;
use Wordlift\Videoobject\Provider\Vimeo;
use Wordlift\Videoobject\Provider\Youtube;

/**
 * Class Videoobject_Youtube_Api_Test
 * @group videoobject
 */
class Videoobject_Api_Test extends Wordlift_Videoobject_Unit_Test_Case {
	/**
	 * @return string
	 */
	public static function get_multiple_vimeo_videos_post_content() {
		$post_content = <<<EOF
<!-- wp:embed {"url":"https://vimeo.com/162427937","type":"video","providerNameSlug":"vimeo","responsive":true,"className":"wp-embed-aspect-16-9 wp-has-aspect-ratio"} -->
<figure class="wp-block-embed is-type-video is-provider-vimeo wp-block-embed-vimeo wp-embed-aspect-16-9 wp-has-aspect-ratio"><div class="wp-block-embed__wrapper">
https://vimeo.com/162427937
</div></figure>
<!-- /wp:embed -->
<!-- wp:embed {"url":"https://vimeo.com/49219317","type":"video","providerNameSlug":"vimeo","responsive":true,"className":"wp-embed-aspect-16-9 wp-has-aspect-ratio"} -->
<figure class="wp-block-embed is-type-video is-provider-vimeo wp-block-embed-vimeo wp-embed-aspect-16-9 wp-has-aspect-ratio"><div class="wp-block-embed__wrapper">
https://vimeo.com/49219317
</div></figure>
<!-- /wp:embed -->
EOF;

		return $post_content;
	}

	/**
	 * @return string
	 */
	public static function multiple_youtube_video_post_content() {
		return <<<EOF
<!-- wp:embed {"url":"https://www.youtube.com/embed/fJAPDAK4GiI","type":"video","providerNameSlug":"youtube","responsive":true,"className":"wp-embed-aspect-16-9 wp-has-aspect-ratio"} -->
<figure class="wp-block-embed is-type-video is-provider-youtube wp-block-embed-youtube wp-embed-aspect-16-9 wp-has-aspect-ratio"><div class="wp-block-embed__wrapper">
https://www.youtube.com/embed/fJAPDAK4GiI
</div></figure>
<!-- /wp:embed -->
<!-- wp:embed {"url":"https://www.youtube.com/watch?v=y-n93I5q-0g","type":"video","providerNameSlug":"youtube","responsive":true,"className":"wp-embed-aspect-16-9 wp-has-aspect-ratio"} -->
<figure class="wp-block-embed is-type-video is-provider-youtube wp-block-embed-youtube wp-embed-aspect-16-9 wp-has-aspect-ratio"><div class="wp-block-embed__wrapper">
https://www.youtube.com/watch?v=y-n93I5q-0g
</div></figure>
<!-- /wp:embed -->
EOF;
	}

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
		$this->assertNotNull( $video->id );
		$this->assertNotNull( $video->name );
		$this->assertNotNull( $video->description );
		$this->assertNotNull( $video->thumbnail_urls );
		$this->assertNotNull( $video->upload_date );
		$this->assertNotNull( $video->duration );
		$this->assertSame( 'https://i.ytimg.com/vi/fJAPDAK4GiI/default.jpg', $video->thumbnail_urls[0] );
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
		$this->validate_video_object_for_post_content( $post_content );

	}

	public function test_on_save_post_with_embed_shortcode_should_store_it() {
		$post_content = <<<EOF
[embed]https://vimeo.com/162427937[/embed]
EOF;
		$this->validate_video_object_for_post_content( $post_content );
	}


	public function test_on_save_post_should_import_data_for_jw_player() {
		$post_id = $this->factory()->post->create();
		add_post_meta( $post_id, '_jwppp-video-url-1', 'nT18k1bf' );
		wp_update_post( array(
			'post_content' => 'foo',
			'ID'           => $post_id
		) );
		$this->validate_video_object_for_post_id( $post_id );
	}

	public function test_on_save_post_with_multiple_youtube_videos_should_store_it() {
		$post_content = self::multiple_youtube_video_post_content();

		$post_id = $this->create_post_with_content( $post_content );
		$videos  = Video_Storage_Factory::get_storage()->get_all_videos( $post_id );
		// we should have 2 video on the storage.
		$this->assertCount( 2, $videos );
	}


	public function test_on_save_post_with_multiple_vimeo_videos_should_store_it() {
		$post_content = self::get_multiple_vimeo_videos_post_content();
		$post_id      = $this->create_post_with_content( $post_content );
		$videos       = Video_Storage_Factory::get_storage()->get_all_videos( $post_id );
		// we should have 2 video on the storage.
		$this->assertCount( 2, $videos );

	}


	public function test_should_extract_video_ids_from_different_youtube_urls() {

		$client = Youtube_Client::get_instance();

		$video_ids = $client->get_video_ids( array(
			'https://www.youtube.com/watch?v=3GhQqFVMJ_o&feature=youtu.be'
		) );

		$this->assertSame( array( '3GhQqFVMJ_o' ), $video_ids, 'Youtube URL with query param should work properly' );


		$video_ids = $client->get_video_ids( array(
			'https://youtu.be/3GhQqFVMJ_o'
		) );

		$this->assertSame( array( '3GhQqFVMJ_o' ), $video_ids, 'You.tube URL should work properly' );


		$video_ids = $client->get_video_ids( array(
			'https://www.youtube.com/embed/3GhQqFVMJ_o'
		) );

		$this->assertSame( array( '3GhQqFVMJ_o' ), $video_ids, 'Embed URL should work properly' );


		$video_ids = $client->get_video_ids( array(
			'https://www.youtube.com/watch?v=3GhQqFVMJ_o&list=PLJR61fXkAx11Oi6EpqJ9Es4rVOIZhwlSG'
		) );

		$this->assertSame( array( '3GhQqFVMJ_o' ), $video_ids, 'Video with playlist url should work properly' );

	}

	/**
	 * @param $post_content
	 */
	private function validate_video_object_for_post_content( $post_content ) {
		$post_id = $this->create_post_with_content( $post_content );
		$this->validate_video_object_for_post_id( $post_id );
	}

	/**
	 * @param $post_id
	 */
	private function validate_video_object_for_post_id( $post_id ) {
		$videos = Video_Storage_Factory::get_storage()->get_all_videos( $post_id );
		// we should have 1 video on the storage.
		$this->assertCount( 1, $videos );
		/**
		 * @var $video Video
		 */
		$video = $videos[0];
		// check all of the properties are not null.
		$this->assertNotNull( $video->id, 'Video id should not be null' );
		$this->assertNotNull( $video->name, 'Video name should not be null' );
		$this->assertNotNull( $video->description, 'Video description should not be null' );
		$this->assertNotNull( $video->thumbnail_urls, 'Thumbnail urls should not be null' );
		$this->assertNotNull( $video->upload_date, 'Upload date should be provided' );
		$this->assertNotNull( $video->duration, 'Duration should be set' );
		$this->assertNotNull( $video->content_url, 'Content url should be set' );
		$this->assertNotNull( $video->embed_url, 'Embed url should be set' );
	}

}