<?php
/**
 * @since 3.27.7
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */

/**
 * Class Top_Entities_Test
 * @group admin
 */
class Top_Entities_Test extends Wordlift_Unit_Test_Case {

	public function test_cron_should_be_registered_for_top_entities() {
		$this->assertNotFalse( wp_next_scheduled( 'wl_admin_dashboard_top_entities' ) );
	}

}