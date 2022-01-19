<?php

namespace Wordlift\Dataset\Background;

class Sync_Background_Process_Stopped_State extends Abstract_Sync_Background_Process_State {

	/**
	 * @var Sync_Background_Process
	 */
	private $context;

	function __construct( $context ) {
		parent::__construct( Sync_Background_Process::STATE_STOPPED );

		$this->context = $context;
	}

	function enter() {
		$this->context->set_state( Sync_Background_Process::STATE_STOPPED );
	}

	function leave() {
		$this->context->set_state( null );
	}

	function task( $item ) {

		$this->context->cancel_process();

		return false;
	}

}
