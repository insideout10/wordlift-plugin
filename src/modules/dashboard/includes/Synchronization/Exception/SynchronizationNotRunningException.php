<?php

namespace Wordlift\Modules\Dashboard\Synchronization\Exception;

use Exception;

class SynchronizationNotRunningException extends Exception {
	public function __construct() {
		$this->message = __( 'A Synchronization is not running', 'wordlift' );
	}
}
