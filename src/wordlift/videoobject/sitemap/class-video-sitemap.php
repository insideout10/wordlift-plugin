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


	}



}