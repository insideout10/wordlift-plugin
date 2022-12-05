<?php
/**
 * Action Scheduler Extended.
 *
 * @author Mahbub Hasan Imon <mahbub@wordlift.io>
 * @package Wordlift
 */

namespace Wordlift\Dataset\Background;

class AS_Background_Process {

	/**
	 * @var string Identifier.
	 */
	public $identifier = 'as_background_process';

	public function __construct( $identifier ) {
		$this->identifier = $identifier;
	}

	/**
	 * Schedule.
	 *
	 * @return void
	 */
	public function schedule() {
		if ( false === as_has_scheduled_action( $this->identifier ) ) {
			as_enqueue_async_action( $this->identifier );
		}
	}

	/**
	 * Unschedule.
	 *
	 * @return void
	 */
	public function unschedule() {
		as_unschedule_all_actions( $this->identifier );
	}

}
