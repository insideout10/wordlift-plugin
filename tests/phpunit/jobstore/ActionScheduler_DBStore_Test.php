<?php

use Action_Scheduler\Tests\DataStores\AbstractStoreTest;

/**
 * Class ActionScheduler_DBStore_Test
 * @group tables
 */
class ActionScheduler_DBStore_Test extends AbstractStoreTest {

	public function setUp() {
		global $wpdb;

		// Delete all actions before each test.
		$wpdb->query( "DELETE FROM {$wpdb->actionscheduler_actions}" );

		parent::setUp();
	}

	/**
	 * Get data store for tests.
	 *
	 * @return ActionScheduler_DBStore
	 */
	protected function get_store() {
		return new ActionScheduler_DBStore();
	}

	public function test_create_action() {
		$time      = as_get_datetime_object();
		$schedule  = new ActionScheduler_SimpleSchedule( $time );
		$action    = new ActionScheduler_Action( ActionScheduler_Callbacks::HOOK_WITH_CALLBACK, [], $schedule );
		$store     = new ActionScheduler_DBStore();
		$action_id = $store->save_action( $action );

		$this->assertNotEmpty( $action_id );
	}

	public function test_create_action_with_scheduled_date() {
		$time        = as_get_datetime_object( strtotime( '-1 week' ) );
		$action      = new ActionScheduler_Action( ActionScheduler_Callbacks::HOOK_WITH_CALLBACK, [], new ActionScheduler_SimpleSchedule( $time ) );
		$store       = new ActionScheduler_DBStore();
		$action_id   = $store->save_action( $action, $time );
		$action_date = $store->get_date( $action_id );

		$this->assertEquals( $time->format( 'U' ), $action_date->format( 'U' ) );
	}

	public function test_retrieve_action() {
		$time      = as_get_datetime_object();
		$schedule  = new ActionScheduler_SimpleSchedule( $time );
		$action    = new ActionScheduler_Action( ActionScheduler_Callbacks::HOOK_WITH_CALLBACK, [], $schedule, 'my_group' );
		$store     = new ActionScheduler_DBStore();
		$action_id = $store->save_action( $action );

		$retrieved = $store->fetch_action( $action_id );
		$this->assertEquals( $action->get_hook(), $retrieved->get_hook() );
		$this->assertEqualSets( $action->get_args(), $retrieved->get_args() );
		$this->assertEquals( $action->get_schedule()->get_date()->format( 'U' ), $retrieved->get_schedule()->get_date()->format( 'U' ) );
		$this->assertEquals( $action->get_group(), $retrieved->get_group() );
	}

	public function test_cancel_action() {
		$time      = as_get_datetime_object();
		$schedule  = new ActionScheduler_SimpleSchedule( $time );
		$action    = new ActionScheduler_Action( ActionScheduler_Callbacks::HOOK_WITH_CALLBACK, [], $schedule, 'my_group' );
		$store     = new ActionScheduler_DBStore();
		$action_id = $store->save_action( $action );
		$store->cancel_action( $action_id );

		$fetched = $store->fetch_action( $action_id );
		$this->assertInstanceOf( 'ActionScheduler_CanceledAction', $fetched );
	}

	public function test_cancel_actions_by_hook() {
		$store   = new ActionScheduler_DBStore();
		$actions = [];
		$hook    = 'by_hook_test';
		for ( $day = 1; $day <= 3; $day++ ) {
			$delta     = sprintf( '+%d day', $day );
			$time      = as_get_datetime_object( $delta );
			$schedule  = new ActionScheduler_SimpleSchedule( $time );
			$action    = new ActionScheduler_Action( $hook, [], $schedule, 'my_group' );
			$actions[] = $store->save_action( $action );
		}
		$store->cancel_actions_by_hook( $hook );

		foreach ( $actions as $action_id ) {
			$fetched = $store->fetch_action( $action_id );
			$this->assertInstanceOf( 'ActionScheduler_CanceledAction', $fetched );
		}
	}

	public function test_cancel_actions_by_group() {
		$store   = new ActionScheduler_DBStore();
		$actions = [];
		$group   = 'by_group_test';
		for ( $day = 1; $day <= 3; $day++ ) {
			$delta     = sprintf( '+%d day', $day );
			$time      = as_get_datetime_object( $delta );
			$schedule  = new ActionScheduler_SimpleSchedule( $time );
			$action    = new ActionScheduler_Action( ActionScheduler_Callbacks::HOOK_WITH_CALLBACK, [], $schedule, $group );
			$actions[] = $store->save_action( $action );
		}
		$store->cancel_actions_by_group( $group );

		foreach ( $actions as $action_id ) {
			$fetched = $store->fetch_action( $action_id );
			$this->assertInstanceOf( 'ActionScheduler_CanceledAction', $fetched );
		}
	}

	public function test_claim_actions() {
		$created_actions = [];
		$store           = new ActionScheduler_DBStore();
		for ( $i = 3; $i > - 3; $i -- ) {
			$time     = as_get_datetime_object( $i . ' hours' );
			$schedule = new ActionScheduler_SimpleSchedule( $time );
			$action   = new ActionScheduler_Action( ActionScheduler_Callbacks::HOOK_WITH_CALLBACK, [ $i ], $schedule, 'my_group' );

			$created_actions[] = $store->save_action( $action );
		}

		$claim = $store->stake_claim();
		$this->assertInstanceof( 'ActionScheduler_ActionClaim', $claim );

		$this->assertCount( 3, $claim->get_actions() );
		$this->assertEqualSets( array_slice( $created_actions, 3, 3 ), $claim->get_actions() );
	}

	public function test_claim_actions_order() {

		$store           = new ActionScheduler_DBStore();
		$schedule        = new ActionScheduler_SimpleSchedule( as_get_datetime_object( '-1 hour' ) );
		$created_actions = array(
			$store->save_action( new ActionScheduler_Action( ActionScheduler_Callbacks::HOOK_WITH_CALLBACK, array( 1 ), $schedule, 'my_group' ) ),
			$store->save_action( new ActionScheduler_Action( ActionScheduler_Callbacks::HOOK_WITH_CALLBACK, array( 1 ), $schedule, 'my_group' ) ),
		);

		$claim = $store->stake_claim();
		$this->assertInstanceof( 'ActionScheduler_ActionClaim', $claim );

		// Verify uniqueness of action IDs.
		$this->assertEquals( 2, count( array_unique( $created_actions ) ) );

		// Verify the count and order of the actions.
		$claimed_actions = $claim->get_actions();
		$this->assertCount( 2, $claimed_actions );
		$this->assertEquals( $created_actions, $claimed_actions );

		// Verify the reversed order doesn't pass.
		$reversed_actions = array_reverse( $created_actions );
		$this->assertNotEquals( $reversed_actions, $claimed_actions );
	}

	public function test_claim_actions_by_hooks() {
		$created_actions = $created_actions_by_hook = [];
		$store           = new ActionScheduler_DBStore();
		$unique_hook_one = 'my_unique_hook_one';
		$unique_hook_two = 'my_unique_hook_two';
		$unique_hooks    = array(
			$unique_hook_one,
			$unique_hook_two,
		);

		for ( $i = 3; $i > - 3; $i -- ) {
			foreach ( $unique_hooks as $unique_hook ) {
				$time     = as_get_datetime_object( $i . ' hours' );
				$schedule = new ActionScheduler_SimpleSchedule( $time );
				$action   = new ActionScheduler_Action( $unique_hook, [ $i ], $schedule, 'my_group' );

				$action_id         = $store->save_action( $action );
				$created_actions[] = $created_actions_by_hook[ $unique_hook ][] = $action_id;
			}
		}

		$claim = $store->stake_claim( 10, null, $unique_hooks );
		$this->assertInstanceof( 'ActionScheduler_ActionClaim', $claim );
		$this->assertCount( 6, $claim->get_actions() );
		$this->assertEqualSets( array_slice( $created_actions, 6, 6 ), $claim->get_actions() );

		$store->release_claim( $claim );

		$claim = $store->stake_claim( 10, null, array( $unique_hook_one ) );
		$this->assertInstanceof( 'ActionScheduler_ActionClaim', $claim );
		$this->assertCount( 3, $claim->get_actions() );
		$this->assertEqualSets( array_slice( $created_actions_by_hook[ $unique_hook_one ], 3, 3 ), $claim->get_actions() );

		$store->release_claim( $claim );

		$claim = $store->stake_claim( 10, null, array( $unique_hook_two ) );
		$this->assertInstanceof( 'ActionScheduler_ActionClaim', $claim );
		$this->assertCount( 3, $claim->get_actions() );
		$this->assertEqualSets( array_slice( $created_actions_by_hook[ $unique_hook_two ], 3, 3 ), $claim->get_actions() );
	}

	public function test_claim_actions_by_group() {
		$created_actions  = [];
		$store            = new ActionScheduler_DBStore();
		$unique_group_one = 'my_unique_group_one';
		$unique_group_two = 'my_unique_group_two';
		$unique_groups    = array(
			$unique_group_one,
			$unique_group_two,
		);

		for ( $i = 3; $i > - 3; $i -- ) {
			foreach ( $unique_groups as $unique_group ) {
				$time     = as_get_datetime_object( $i . ' hours' );
				$schedule = new ActionScheduler_SimpleSchedule( $time );
				$action   = new ActionScheduler_Action( ActionScheduler_Callbacks::HOOK_WITH_CALLBACK, [ $i ], $schedule, $unique_group );

				$created_actions[ $unique_group ][] = $store->save_action( $action );
			}
		}

		$claim = $store->stake_claim( 10, null, array(), $unique_group_one );
		$this->assertInstanceof( 'ActionScheduler_ActionClaim', $claim );
		$this->assertCount( 3, $claim->get_actions() );
		$this->assertEqualSets( array_slice( $created_actions[ $unique_group_one ], 3, 3 ), $claim->get_actions() );

		$store->release_claim( $claim );

		$claim = $store->stake_claim( 10, null, array(), $unique_group_two );
		$this->assertInstanceof( 'ActionScheduler_ActionClaim', $claim );
		$this->assertCount( 3, $claim->get_actions() );
		$this->assertEqualSets( array_slice( $created_actions[ $unique_group_two ], 3, 3 ), $claim->get_actions() );
	}

	public function test_claim_actions_by_hook_and_group() {
		$created_actions = $created_actions_by_hook = [];
		$store           = new ActionScheduler_DBStore();

		$unique_hook_one = 'my_other_unique_hook_one';
		$unique_hook_two = 'my_other_unique_hook_two';
		$unique_hooks    = array(
			$unique_hook_one,
			$unique_hook_two,
		);

		$unique_group_one = 'my_other_other_unique_group_one';
		$unique_group_two = 'my_other_unique_group_two';
		$unique_groups    = array(
			$unique_group_one,
			$unique_group_two,
		);

		for ( $i = 3; $i > - 3; $i -- ) {
			foreach ( $unique_hooks as $unique_hook ) {
				foreach ( $unique_groups as $unique_group ) {
					$time     = as_get_datetime_object( $i . ' hours' );
					$schedule = new ActionScheduler_SimpleSchedule( $time );
					$action   = new ActionScheduler_Action( $unique_hook, [ $i ], $schedule, $unique_group );

					$action_id = $store->save_action( $action );
					$created_actions[ $unique_group ][] = $action_id;
					$created_actions_by_hook[ $unique_hook ][ $unique_group ][] = $action_id;
				}
			}
		}

		/** Test Both Hooks with Each Group */

		$claim = $store->stake_claim( 10, null, $unique_hooks, $unique_group_one );
		$this->assertInstanceof( 'ActionScheduler_ActionClaim', $claim );
		$this->assertCount( 6, $claim->get_actions() );
		$this->assertEqualSets( array_slice( $created_actions[ $unique_group_one ], 6, 6 ), $claim->get_actions() );

		$store->release_claim( $claim );

		$claim = $store->stake_claim( 10, null, $unique_hooks, $unique_group_two );
		$this->assertInstanceof( 'ActionScheduler_ActionClaim', $claim );
		$this->assertCount( 6, $claim->get_actions() );
		$this->assertEqualSets( array_slice( $created_actions[ $unique_group_two ], 6, 6 ), $claim->get_actions() );

		$store->release_claim( $claim );

		/** Test Just One Hook with Group One */

		$claim = $store->stake_claim( 10, null, array( $unique_hook_one ), $unique_group_one );
		$this->assertInstanceof( 'ActionScheduler_ActionClaim', $claim );
		$this->assertCount( 3, $claim->get_actions() );
		$this->assertEqualSets( array_slice( $created_actions_by_hook[ $unique_hook_one ][ $unique_group_one ], 3, 3 ), $claim->get_actions() );

		$store->release_claim( $claim );

		$claim = $store->stake_claim( 24, null, array( $unique_hook_two ), $unique_group_one );
		$this->assertInstanceof( 'ActionScheduler_ActionClaim', $claim );
		$this->assertCount( 3, $claim->get_actions() );
		$this->assertEqualSets( array_slice( $created_actions_by_hook[ $unique_hook_two ][ $unique_group_one ], 3, 3 ), $claim->get_actions() );

		$store->release_claim( $claim );

		/** Test Just One Hook with Group Two */

		$claim = $store->stake_claim( 10, null, array( $unique_hook_one ), $unique_group_two );
		$this->assertInstanceof( 'ActionScheduler_ActionClaim', $claim );
		$this->assertCount( 3, $claim->get_actions() );
		$this->assertEqualSets( array_slice( $created_actions_by_hook[ $unique_hook_one ][ $unique_group_two ], 3, 3 ), $claim->get_actions() );

		$store->release_claim( $claim );

		$claim = $store->stake_claim( 24, null, array( $unique_hook_two ), $unique_group_two );
		$this->assertInstanceof( 'ActionScheduler_ActionClaim', $claim );
		$this->assertCount( 3, $claim->get_actions() );
		$this->assertEqualSets( array_slice( $created_actions_by_hook[ $unique_hook_two ][ $unique_group_two ], 3, 3 ), $claim->get_actions() );
	}

	/**
	 * The query used to claim actions explicitly ignores future pending actions, but it
	 * is still possible under unusual conditions (such as if MySQL runs out of temporary
	 * storage space) for such actions to be returned.
	 *
	 * When this happens, we still expect the store to filter them out, otherwise there is
	 * a risk that actions will be unexpectedly processed ahead of time.
	 *
	 * @see https://github.com/woocommerce/action-scheduler/issues/634
	 */
	public function test_claim_filters_out_unexpected_future_actions() {
		$group = __METHOD__;
		$store = new ActionScheduler_DBStore();

		// Create 4 actions: 2 that are already due (-3hrs and -1hrs) and 2 that are not yet due (+1hr and +3hrs).
		for ( $i = -3; $i <= 3; $i += 2 ) {
			$schedule     = new ActionScheduler_SimpleSchedule( as_get_datetime_object( $i . ' hours' ) );
			$action_ids[] = $store->save_action( new ActionScheduler_Action( 'test_' . $i, array(), $schedule, $group ) );
		}

		// This callback is used to simulate the unusual conditions whereby MySQL might unexpectedly return future
		// actions, contrary to the conditions used by the store object when staking its claim.
		$simulate_unexpected_db_behavior = function( $sql ) use ( $action_ids ) {
			global $wpdb;

			// Look out for the claim update query, ignore all others.
			if (
				0 !== strpos( $sql, "UPDATE $wpdb->actionscheduler_actions" )
				|| ! preg_match( "/claim_id = 0 AND scheduled_date_gmt <= '([0-9:\-\s]{19})'/", $sql, $matches )
				|| count( $matches ) !== 2
			) {
				return $sql;
			}

			// Now modify the query, forcing it to also return the future actions we created.
			return str_replace( $matches[1], as_get_datetime_object( '+4 hours' )->format( 'Y-m-d H:i:s' ), $sql );
		};

		add_filter( 'query', $simulate_unexpected_db_behavior );
		$claim = $store->stake_claim( 10, null, array(), $group );
		$claimed_actions = $claim->get_actions();
		$this->assertCount( 2, $claimed_actions );

		// Cleanup.
		remove_filter( 'query', $simulate_unexpected_db_behavior );
		$store->release_claim( $claim );
	}

	public function test_duplicate_claim() {
		$created_actions = [];
		$store           = new ActionScheduler_DBStore();
		for ( $i = 0; $i > - 3; $i -- ) {
			$time     = as_get_datetime_object( $i . ' hours' );
			$schedule = new ActionScheduler_SimpleSchedule( $time );
			$action   = new ActionScheduler_Action( ActionScheduler_Callbacks::HOOK_WITH_CALLBACK, [ $i ], $schedule, 'my_group' );

			$created_actions[] = $store->save_action( $action );
		}

		$claim1 = $store->stake_claim();
		$claim2 = $store->stake_claim();
		$this->assertCount( 3, $claim1->get_actions() );
		$this->assertCount( 0, $claim2->get_actions() );
	}

	public function test_release_claim() {
		$created_actions = [];
		$store           = new ActionScheduler_DBStore();
		for ( $i = 0; $i > - 3; $i -- ) {
			$time     = as_get_datetime_object( $i . ' hours' );
			$schedule = new ActionScheduler_SimpleSchedule( $time );
			$action   = new ActionScheduler_Action( ActionScheduler_Callbacks::HOOK_WITH_CALLBACK, [ $i ], $schedule, 'my_group' );

			$created_actions[] = $store->save_action( $action );
		}

		$claim1 = $store->stake_claim();

		$store->release_claim( $claim1 );

		$claim2 = $store->stake_claim();
		$this->assertCount( 3, $claim2->get_actions() );
	}

	public function test_search() {
		$created_actions = [];
		$store           = new ActionScheduler_DBStore();
		for ( $i = - 3; $i <= 3; $i ++ ) {
			$time     = as_get_datetime_object( $i . ' hours' );
			$schedule = new ActionScheduler_SimpleSchedule( $time );
			$action   = new ActionScheduler_Action( ActionScheduler_Callbacks::HOOK_WITH_CALLBACK, [ $i ], $schedule, 'my_group' );

			$created_actions[] = $store->save_action( $action );
		}

		$next_no_args = $store->find_action( ActionScheduler_Callbacks::HOOK_WITH_CALLBACK );
		$this->assertEquals( $created_actions[ 0 ], $next_no_args );

		$next_with_args = $store->find_action( ActionScheduler_Callbacks::HOOK_WITH_CALLBACK, [ 'args' => [ 1 ] ] );
		$this->assertEquals( $created_actions[ 4 ], $next_with_args );

		$non_existent = $store->find_action( ActionScheduler_Callbacks::HOOK_WITH_CALLBACK, [ 'args' => [ 17 ] ] );
		$this->assertNull( $non_existent );
	}

	public function test_search_by_group() {
		$store    = new ActionScheduler_DBStore();
		$schedule = new ActionScheduler_SimpleSchedule( as_get_datetime_object( 'tomorrow' ) );

		$abc = $store->save_action( new ActionScheduler_Action( ActionScheduler_Callbacks::HOOK_WITH_CALLBACK, [ 1 ], $schedule, 'abc' ) );
		$def = $store->save_action( new ActionScheduler_Action( ActionScheduler_Callbacks::HOOK_WITH_CALLBACK, [ 1 ], $schedule, 'def' ) );
		$ghi = $store->save_action( new ActionScheduler_Action( ActionScheduler_Callbacks::HOOK_WITH_CALLBACK, [ 1 ], $schedule, 'ghi' ) );

		$this->assertEquals( $abc, $store->find_action( ActionScheduler_Callbacks::HOOK_WITH_CALLBACK, [ 'group' => 'abc' ] ) );
		$this->assertEquals( $def, $store->find_action( ActionScheduler_Callbacks::HOOK_WITH_CALLBACK, [ 'group' => 'def' ] ) );
		$this->assertEquals( $ghi, $store->find_action( ActionScheduler_Callbacks::HOOK_WITH_CALLBACK, [ 'group' => 'ghi' ] ) );
	}

	public function test_get_run_date() {
		$time      = as_get_datetime_object( '-10 minutes' );
		$schedule  = new ActionScheduler_IntervalSchedule( $time, HOUR_IN_SECONDS );
		$action    = new ActionScheduler_Action( ActionScheduler_Callbacks::HOOK_WITH_CALLBACK, [], $schedule );
		$store     = new ActionScheduler_DBStore();
		$action_id = $store->save_action( $action );

		$this->assertEquals( $time->format( 'U' ), $store->get_date( $action_id )->format( 'U' ) );

		$action = $store->fetch_action( $action_id );
		$action->execute();
		$now = as_get_datetime_object();
		$store->mark_complete( $action_id );

		$this->assertEquals( $now->format( 'U' ), $store->get_date( $action_id )->format( 'U' ) );

		$next          = $action->get_schedule()->get_next( $now );
		$new_action_id = $store->save_action( $action, $next );

		$this->assertEquals( (int) ( $now->format( 'U' ) ) + HOUR_IN_SECONDS, $store->get_date( $new_action_id )->format( 'U' ) );
	}

	/**
	 * Test creating a unique action.
	 */
	public function test_create_action_unique() {
		$time     = as_get_datetime_object();
		$hook     = md5( rand() );
		$schedule = new ActionScheduler_SimpleSchedule( $time );
		$store    = new ActionScheduler_DBStore();
		$action   = new ActionScheduler_Action( $hook, array(), $schedule );

		$action_id = $store->save_action( $action );
		$this->assertNotEquals( 0, $action_id );
		$action_from_db = $store->fetch_action( $action_id );
		$this->assertTrue( is_a( $action_from_db, ActionScheduler_Action::class ) );

		$action = new ActionScheduler_Action( $hook, array(), $schedule );
		$action_id_duplicate = $store->save_unique_action( $action );
		$this->assertEquals( 0, $action_id_duplicate );
	}

	/**
	 * Test saving unique actions across different groups. Different groups should be saved, same groups shouldn't.
	 */
	public function test_create_action_unique_with_different_groups() {
		$time     = as_get_datetime_object();
		$hook     = md5( rand() );
		$schedule = new ActionScheduler_SimpleSchedule( $time );
		$store    = new ActionScheduler_DBStore();
		$action   = new ActionScheduler_Action( $hook, array(), $schedule, 'group1' );

		$action_id = $store->save_action( $action );
		$action_from_db = $store->fetch_action( $action_id );
		$this->assertNotEquals( 0, $action_id );
		$this->assertTrue( is_a( $action_from_db, ActionScheduler_Action::class ) );

		$action2 = new ActionScheduler_Action( $hook, array(), $schedule, 'group2' );
		$action_id_group2 = $store->save_unique_action( $action2 );
		$this->assertNotEquals( 0, $action_id_group2 );
		$action_2_from_db = $store->fetch_action( $action_id_group2 );
		$this->assertTrue( is_a( $action_2_from_db, ActionScheduler_Action::class ) );

		$action3 = new ActionScheduler_Action( $hook, array(), $schedule, 'group2' );
		$action_id_group2_double = $store->save_unique_action( $action3 );
		$this->assertEquals( 0, $action_id_group2_double );
	}

	/**
	 * Test saving a unique action first, and then successfully scheduling a non-unique action.
	 */
	public function test_create_action_unique_and_then_non_unique() {
		$time     = as_get_datetime_object();
		$hook     = md5( rand() );
		$schedule = new ActionScheduler_SimpleSchedule( $time );
		$store    = new ActionScheduler_DBStore();
		$action   = new ActionScheduler_Action( $hook, array(), $schedule );

		$action_id = $store->save_unique_action( $action );
		$this->assertNotEquals( 0, $action_id );
		$action_from_db = $store->fetch_action( $action_id );
		$this->assertTrue( is_a( $action_from_db, ActionScheduler_Action::class ) );

		// Non unique action is scheduled even if the previous one was unique.
		$action = new ActionScheduler_Action( $hook, array(), $schedule );
		$action_id_duplicate = $store->save_action( $action );
		$this->assertNotEquals( 0, $action_id_duplicate );
		$action_from_db_duplicate = $store->fetch_action( $action_id_duplicate );
		$this->assertTrue( is_a( $action_from_db_duplicate, ActionScheduler_Action::class ) );
	}

	/**
	 * Test asserting that action when an action is created with empty args, it matches with actions created with args for uniqueness.
	 */
	public function test_create_action_unique_with_empty_array() {
		$time     = as_get_datetime_object();
		$hook     = md5( rand() );
		$schedule = new ActionScheduler_SimpleSchedule( $time );
		$store    = new ActionScheduler_DBStore();
		$action   = new ActionScheduler_Action( $hook, array( 'foo' => 'bar' ), $schedule );

		$action_id = $store->save_unique_action( $action );
		$this->assertNotEquals( 0, $action_id );
		$action_from_db = $store->fetch_action( $action_id );
		$this->assertTrue( is_a( $action_from_db, ActionScheduler_Action::class ) );

		$action_with_empty_args = new ActionScheduler_Action( $hook, array(), $schedule );
		$action_id_duplicate = $store->save_unique_action( $action_with_empty_args );
		$this->assertEquals( 0, $action_id_duplicate );
	}

	/**
	 * Uniqueness does not check for args, so actions with different args can't be scheduled when unique is true.
	 */
	public function test_create_action_unique_with_different_args_still_fail() {
		$time     = as_get_datetime_object();
		$hook     = md5( rand() );
		$schedule = new ActionScheduler_SimpleSchedule( $time );
		$store    = new ActionScheduler_DBStore();
		$action   = new ActionScheduler_Action( $hook, array( 'foo' => 'bar' ), $schedule );

		$action_id = $store->save_unique_action( $action );
		$this->assertNotEquals( 0, $action_id );
		$action_from_db = $store->fetch_action( $action_id );
		$this->assertTrue( is_a( $action_from_db, ActionScheduler_Action::class ) );

		$action_with_diff_args = new ActionScheduler_Action( $hook, array( 'foo' => 'bazz' ), $schedule );
		$action_id_duplicate = $store->save_unique_action( $action_with_diff_args );
		$this->assertEquals( 0, $action_id_duplicate );
	}
}
