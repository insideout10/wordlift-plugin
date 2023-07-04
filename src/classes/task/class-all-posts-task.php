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

	/**
	 * @var string|null
	 */
	private $post_type;

	/**
	 * @var string|null
	 */
	private $id;

	public function __construct( $callable, $post_type = null, $id = null ) {
		$this->callable  = $callable;
		$this->post_type = $post_type;
		$this->id        = $id;
	}

	public function get_id() {
		return isset( $this->id ) ? $this->id : hash( 'sha256', get_class( $this ) );
	}

	public function starting() {
		global $wpdb;

		// Try to get the count from transient, or load it from database.
		$key   = $this->get_transient_key();
		$count = get_transient( $key );
		if ( false === $count ) {
			$count = $wpdb->get_var(
				"SELECT COUNT( 1 ) 
				FROM $wpdb->posts "
				// Prepare is called with the `add_post_type_filter` function.
				// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
				. $this->where( $this->add_post_type_filter() )
			);
			set_transient( $key, $count, HOUR_IN_SECONDS );
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
				"SELECT ID FROM $wpdb->posts "
				// Prepare is called with the `add_post_type_filter` function.
				// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
				. $this->where( $this->add_post_type_filter() )
				. ' ORDER BY ID LIMIT %d,%d',
				$args['offset'],
				$args['batch_size']
			)
		);

		foreach ( $ids as $id ) {
			call_user_func( $this->callable, $id );
		}

	}

	private function add_post_type_filter() {
		global $wpdb;
		if ( isset( $this->post_type ) ) {
			return $wpdb->prepare( ' post_type = %s ', $this->post_type );
		}

		return '';
	}

	private function where( $filter ) {
		if ( ! empty( $filter ) ) {
			return " WHERE $filter";
		}

		return '';
	}

	private function get_transient_key() {
		return '_wl_task__all_posts_task__count' . ( isset( $this->post_type ) ? '__' . $this->post_type : '' );
	}

}
