<?php
/**
 * @since 3.31.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */

namespace Wordlift\Videoobject\Sitemap;

use Wordlift\Videoobject\Data\Video\Video;

class Video_Sitemap {

	const CRON_ACTION_HOOK = 'wl_video_sitemap_generation';

	public function init() {

		add_action( 'wordlift_generate_video_sitemap_on', array( $this, 'schedule_generation' ) );

		add_action( self::CRON_ACTION_HOOK, array( $this, 'generate_video_sitemap' ) );

	}

	public function schedule_generation() {
		wp_schedule_event( time(), 'daily', self::CRON_ACTION_HOOK );
	}

	public function generate_video_sitemap() {

		$sitemap_start_tag = <<<EOF
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
        xmlns:video="http://www.google.com/schemas/sitemap-video/1.1">
EOF;
		$sitemap_body      = Xml_Generator::get_xml_for_all_posts_with_videos();

		$sitemap_end_tag = "</urlset>";

		$xml = $sitemap_start_tag . $sitemap_body . $sitemap_end_tag;

		if ( ! defined( ABSPATH ) ) {
			return;
		}

		$fp = fopen( ABSPATH . 'wl-video-sitemap.xml', 'w' );

		if ( ! $fp ) {
			return;
		}

		fwrite( $fp, $xml );
		fclose( $fp );
	}


}