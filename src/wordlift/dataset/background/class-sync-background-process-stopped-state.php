<?php

namespace Wordlift\Dataset\Background;

class Sync_Background_Process_Stopped_State extends Abstract_Sync_Background_Process_State {

	/**
	 * @var Sync_Background_Process
	 */
	private $context;

	public function __construct( $context ) {
		parent::__construct( Sync_Background_Process::STATE_STOPPED );

		$this->context = $context;
	}

	public function enter() {
		$this->context->set_state( Sync_Background_Process::STATE_STOPPED );
	}

	public function leave() {
		$this->context->set_state( null );
	}

	public function task() {
		as_unschedule_action( 'wl_sync_data_task' );
		return false;
	}

}
