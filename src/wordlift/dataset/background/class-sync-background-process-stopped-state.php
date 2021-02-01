<?php

namespace Wordlift\Dataset\Background;

class Sync_Background_Process_Stopped_State implements Sync_Background_Process_State {

	/**
	 * @var Sync_Background_Process
	 */
	private $context;

	function __construct( $context ) {
		$this->context = $context;
	}

	function enter() {
		$this->context->set_state( Sync_Background_Process::STATE_STOPPED );
	}

	function leave() {
		$this->context->set_state( null );
	}

	function task( $args ) {

		return false;
	}
}