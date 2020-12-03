<?php

namespace Wordlift\Admin;
/**
 * @since 3.27.7
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */
class Top_Entities {

	const CRON_ACTION = 'wl_admin_dashboard_top_entities';

	/**
	 * Option key where the top entities data is stored.
	 */
	const OPTION_KEY = 'wl_admin_dashboard_top_entities_option';


	public function __construct() {
		add_action( self::CRON_ACTION, array( $this, 'save_top_entities' ) );
	}


	public function save_top_entities() {

	}


	public static function activate() {
		if ( ! wp_next_scheduled( self::CRON_ACTION ) ) {
			wp_schedule_event( time(), 'hourly', self::CRON_ACTION );
		}
	}

	public static function deactivate() {
		$timestamp = wp_next_scheduled( self::CRON_ACTION );
		wp_unschedule_event( $timestamp, self::CRON_ACTION );
	}

}