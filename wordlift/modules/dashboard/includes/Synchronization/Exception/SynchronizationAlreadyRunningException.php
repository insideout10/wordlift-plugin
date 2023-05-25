<?php

namespace Wordlift\Modules\Dashboard\Synchronization\Exception;

class SynchronizationAlreadyRunningException extends \Exception {
	public function __construct() {
		$this->message = __( 'A Synchronization is already running', 'wordlift' );
	}
}
