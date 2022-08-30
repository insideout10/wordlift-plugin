<?php
namespace Wordlift\Common\Background_Process;

abstract class Data_Source {

	/**
	 * @var string The key which is provided to the background process
	 * in order to retrieve its state, it should be the same as the one provided to
	 * background process.
	 */
	private $state_storage_key;

	public function __construct( $state_storage_key ) {
		$this->state_storage_key = $state_storage_key;
	}

	/**
	 * A list of item ids.
	 *
	 * @return int[]
	 */
	abstract public function next();

	/**
	 * The count of total items which needs to be processed.
	 *
	 * @return int[]
	 */
	abstract public function count();

	/**
	 * A numerical value indicating how many items should be processed per
	 * background call.
	 *
	 * @return int
	 */
	abstract public function get_batch_size();

	public function get_state() {
		try {
			return get_option( $this->state_storage_key, Sync_State::unknown() );
		} catch ( \Exception $e ) {
			return Sync_State::unknown();
		}

	}

}
