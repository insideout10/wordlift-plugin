<?php

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

	}

}