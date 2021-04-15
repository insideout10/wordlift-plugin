<?php

use Wordlift\Videoobject\Data\Video\Video;
use Wordlift\Videoobject\Data\Video_Storage\Video_Storage_Factory;
use Wordlift\Videoobject\Sitemap\Video_Sitemap;

/**
 * Class Videoobject_Sitemap_Test
 * @group videoobject
 */
class Videoobject_Sitemap_Test extends \Wordlift_Videoobject_Unit_Test_Case {

	public function test_should_add_cron_if_wordlift_generate_video_sitemap_on_action() {
		global $wp_filter;
		$this->assertArrayHasKey( 'wordlift_generate_video_sitemap_on', $wp_filter );
		do_action( 'wordlift_generate_video_sitemap_on' );
		$this->assertTrue( is_numeric( wp_next_scheduled( Video_Sitemap::CRON_ACTION_HOOK ) ), 'Cron should be present for video sitemap' );
		$event = wp_get_scheduled_event( Video_Sitemap::CRON_ACTION_HOOK );
		$this->assertSame( $event->interval, 86400 );
	}

	public function test_when_sitemap_generation_run_a_video_sitemap_should_be_created_in_wp_content_folder() {
		$post_id               = $this->factory()->post->create();
		$storage               = Video_Storage_Factory::get_storage();
		$video                 = new Video();
		$video->name           = 'test_title';
		$video->description    = 'test_description';
		$video->thumbnail_urls = array( 'https://test-url.com' );
		$video->content_url    = 'https://content-url.com';
		$video->embed_url      = 'https://embed-url.com';
		$video->duration       = 'P1D'; // 86400 seconds
		$video->views          = 100;
		$video->id             = $video->content_url;
		$storage->add_video( $post_id, $video );
		// Fire the action.
		do_action( Video_Sitemap::CRON_ACTION_HOOK );
		$this->assertTrue( file_exists( ABSPATH . 'wl-video-sitemap.xml' ), 'The sitemap should be generated' );
		$sitemap_xml     = file_get_contents( ABSPATH . 'wl-video-sitemap.xml' );
		$expected_result = <<<EOF
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
        xmlns:video="http://www.google.com/schemas/sitemap-video/1.1">   <url>
     <loc>http://example.org/?p=8</loc>
     <video:video>
       <video:thumbnail_loc>https://test-url.com</video:thumbnail_loc>
       <video:title>test_title</video:title>
       <video:description>test_description</video:description>
       <video:content_loc>https://content-url.com</video:content_loc>
       <video:player_loc>https://embed-url.com</video:player_loc>
       <video:duration>86400</video:duration>
       <video:view_count>100</video:view_count>
       <video:live>no</video:live>
     </video:video>
   </url></urlset>
EOF;
		$this->assertEquals( $expected_result, $sitemap_xml );

	}

}