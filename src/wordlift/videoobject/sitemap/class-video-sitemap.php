<?php
/**
 * @since 3.31.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */

namespace Wordlift\Videoobject\Sitemap;

class Video_Sitemap {

	const CRON_ACTION_HOOK = 'wl_video_sitemap_generation';

	public function init() {

		add_action( 'wordlift_generate_video_sitemap_on', array( $this, 'schedule_generation' ) );

		add_action( self::CRON_ACTION_HOOK, array( $this, 'generate_video_sitemap' ) );

		add_action( 'wordlift_generate_video_sitemap_off', array( $this, 'remove_scheduled_generation' ) );

	}

	public function schedule_generation() {
		if ( ! wp_next_scheduled( self::CRON_ACTION_HOOK ) ) {
			wp_schedule_event( time(), 'daily', self::CRON_ACTION_HOOK );
		}
	}

	public function generate_video_sitemap() {

		$sitemap_start_tag = <<<EOF
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
        xmlns:video="http://www.google.com/schemas/sitemap-video/1.1">
EOF;
		$sitemap_body      = Xml_Generator::get_xml_for_all_posts_with_videos();

		$sitemap_end_tag = "</urlset>";

		$xml = $sitemap_start_tag . $sitemap_body . $sitemap_end_tag;

		if ( ! defined( 'ABSPATH' ) ) {
			return;
		}

		file_put_contents( ABSPATH . 'wl-video-sitemap.xml', $xml );

	}

	public function remove_scheduled_generation() {
		if ( wp_next_scheduled( self::CRON_ACTION_HOOK ) ) {
			wp_clear_scheduled_hook( self::CRON_ACTION_HOOK );
		}
	}

}