<?php

/**
 * Provide a way to cleanup entity annotation from post content.
 *
 * @since 3.34.1
 * @see https://github.com/insideout10/wordlift-plugin/issues/1522
 */

namespace Wordlift\Task;

class All_Posts_Task implements Task {

	private $callable;

	public function __construct( $callable ) {
		$this->callable = $callable;
	}

	public function starting() {
		global $wpdb;

		// Try to get the count from transient, or load it from database.
		$count = get_transient( '_wl_task__all_posts_task__count' );
		if ( false === $count ) {
			$count = $wpdb->get_var( "SELECT COUNT( 1 ) FROM $wpdb->posts" );
			set_transient( '_wl_task__all_posts_task__count', $count, HOUR_IN_SECONDS );
		}

		return $count;
	}

	/**
	 * @param $value mixed The incoming value.
	 * @param $args {
	 *
	 * @type int $offset The index of the current item.
	 * @type int $count The total number of items as provided by the `starting` function.
	 * @type int $batch_size The number of items to process within this call.
	 * }
	 *
	 * @return void
	 */
	public function tick( $value, $args ) {
		global $wpdb;

		$ids = $wpdb->get_col(
			$wpdb->prepare(
				'
			SELECT ID FROM wp_posts ORDER BY ID LIMIT %d,%d;
		',
				$args['offset'],
				$args['batch_size']
			)
		);

		foreach ( $ids as $id ) {
			call_user_func( $this->callable, $id );
		}

	}

}
