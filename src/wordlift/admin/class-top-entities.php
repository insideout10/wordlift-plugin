<?php

namespace Wordlift\Admin;
/**
 * @since 3.27.7
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */
class Top_Entities {

	const CRON_ACTION = 'wl_admin_dashboard_top_entities';

	public static function activate() {
		if ( ! wp_next_scheduled( self::CRON_ACTION ) ) {
			wp_schedule_event( time(), 'hourly', self::CRON_ACTION );
		}
	}

}