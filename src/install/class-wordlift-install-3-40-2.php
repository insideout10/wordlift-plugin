<?php

/**
 * Create a daily schedule for WLP for existing installs.
 *
 * @since 3.40.0
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
	}
}
