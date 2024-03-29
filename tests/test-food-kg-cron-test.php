<?php

/**
 * @group food-kg
 */
class Food_Kg_Cron_Test extends Wordlift_Unit_Test_Case {
	function setUp() {
		remove_all_actions( 'wl_key_updated' );
		parent::setUp(); // TODO: Change the autogenerated stub

	}

	public function test_when_daily_cron_action_fired_should_start_background_process() {
		do_action( Wordlift\Modules\Food_Kg\Module::RUN_EVENT );
		$this->assertTrue( as_has_scheduled_action( 'wl_main_ingredient_sync' ), 'Main ingredient background process should run daily' );
	}

}
