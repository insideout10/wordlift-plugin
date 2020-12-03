<?php
/**
 * @since 3.27.7
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */

use Wordlift\Admin\Top_Entities;

/**
 * Class Top_Entities_Test
 * @group admin
 */
class Top_Entities_Test extends Wordlift_Unit_Test_Case {

	public function test_cron_should_be_registered_for_top_entities() {
		$this->assertNotFalse( wp_next_scheduled( 'wl_admin_dashboard_top_entities' ) );
	}

	public function test_cron_should_run_hourly() {
		$event = wp_get_scheduled_event( Top_Entities::CRON_ACTION );
		$this->assertNotNull( $event, "Cron should be registered" );
		$this->assertEquals( 'hourly', $event->schedule, "Cron should run hourly" );
	}

	public function test_upon_deactivation_should_remove_the_cron() {
		deactivate_wordlift();
		$this->assertFalse( wp_next_scheduled( 'wl_admin_dashboard_top_entities' ), "Cron should be removed" );

	}

}