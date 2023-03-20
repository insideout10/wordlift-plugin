<?php

namespace Wordlift\Modules\Gardening_Kg\Main_Entity;

class Gardening_Kg_Main_Entity_State {

	const OPTION_KEY = '_wl_gardening_kg_main_entity__state';

	private $count;
	private $offset;
	private $last_id;
	private $started_at;
	private $stopped_at;
	private $updated_at;

	/**
	 * @return int
	 */
	public function get_count() {
		return $this->count;
	}

	/**
	 * @param int $count
	 */
	public function set_count( $count ) {
		$this->count = $count;
	}

	/**
	 * @return int
	 */
	public function get_offset() {
		return $this->offset;
	}

	/**
	 * @param int $offset
	 */
	public function set_offset( $offset ) {
		$this->offset = $offset;
	}

	/**
	 * @return mixed
	 */
	public function get_last_id() {
		return $this->last_id;
	}

	/**
	 * @param mixed $last_id
	 */
	public function set_last_id( $last_id ) {
		$this->last_id = $last_id;
	}

	/**
	 * @return mixed
	 */
	public function get_started_at() {
		return $this->started_at;
	}

	/**
	 * @param mixed $started_at
	 */
	public function set_started_at( $started_at ) {
		$this->started_at = $started_at;
	}

	/**
	 * @return mixed
	 */
	public function get_stopped_at() {
		return $this->stopped_at;
	}

	/**
	 * @param mixed $stopped_at
	 */
	public function set_stopped_at( $stopped_at ) {
		$this->stopped_at = $stopped_at;
	}

	/**
	 * @return mixed
	 */
	public function get_updated_at() {
		return $this->updated_at;
	}

	/**
	 * @param mixed $updated_at
	 */
	public function set_updated_at( $updated_at ) {
		$this->updated_at = $updated_at;
	}

	public function is_running() {
		return isset( $this->started_at ) && ! isset( $this->stopped_at );
	}

	public function to_array() {
		return array(
			'count'      => $this->get_count(),
			'offset'     => $this->get_offset(),
			'last_id'    => $this->get_last_id(),
			'started_at' => $this->get_started_at(),
			'stopped_at' => $this->get_stopped_at(),
			'updated_at' => $this->get_updated_at(),
		);
	}

	public static function from_array( array $value ) {
		$state = new static();
		$state->set_count(
			isset( $value['count'] ) ? $value['count'] : 0
		);
		$state->set_offset(
			isset( $value['offset'] ) ? $value['offset'] : 0
		);
		$state->set_last_id(
			isset( $value['last_id'] ) ? $value['last_id'] : 0
		);
		$state->set_started_at(
			isset( $value['started_at'] ) ? $value['started_at'] : null
		);
		$state->set_stopped_at(
			isset( $value['stopped_at'] ) ? $value['stopped_at'] : null
		);
		$state->set_updated_at(
			isset( $value['updated_at'] ) ? $value['updated_at'] : null
		);

		return $state;
	}

	public static function from_db() {
		return static::from_array(
			get_option( self::OPTION_KEY, array() )
		);
	}

	public function to_db() {
		$this->set_updated_at( time() );
		update_option( self::OPTION_KEY, $this->to_array(), false );
	}

}
