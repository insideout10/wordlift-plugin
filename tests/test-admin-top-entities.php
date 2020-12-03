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

	public function test_a_single_function_should_be_present_in_action_hook() {
		global $wp_filter;
		$this->assertArrayHasKey( Top_Entities::CRON_ACTION, $wp_filter, "Atleast a single callback should be present on the cron hook" );
	}

	public function test_when_cron_executed_should_have_data_in_options() {
		update_option( Top_Entities::OPTION_KEY, '' );
		// Doing this action should save the data in db.
		do_action( Top_Entities::CRON_ACTION );
		$option_value = get_option( Top_Entities::OPTION_KEY, '' );
		$this->assertNotEquals( '', $option_value );
		$this->assertTrue( is_array( $option_value ), 'option value for top entities should be an array' );
	}

}