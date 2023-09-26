<?php

namespace Wordlift\Dataset\Background;

use Wordlift\Common\Background_Process\Action_Scheduler\State;

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
		$this->context->unschedule();
		$this->context->set_state( Sync_Background_Process::STATE_STOPPED );
	}

	public function leave() {
		$this->context->set_state( null );
	}

	// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
	public function task( $item ) {

		return State::complete();
	}

}
