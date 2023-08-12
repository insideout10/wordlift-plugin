<?php

use Wordlift\Cache\Ttl_Cache;

/**
 * Create a daily schedule for WLP for existing installs.
 *
 * @since 3.40.2
 */
class Wordlift_Install_3_40_2 extends Wordlift_Install {

	/**
	 * {@inheritdoc}
	 */
	protected static $version = '3.40.2';

	public function install() {
		if ( ! wp_next_scheduled( 'wl_daily_cron' ) ) {
			wp_schedule_event( time(), 'daily', 'wl_daily_cron' );
		}

		do_action(
			'update_option_wl_exclude_include_urls_settings',
			array(),
			get_option( 'wl_exclude_include_urls_settings', array() )
		);

		Ttl_Cache::flush_all();
	}
}
