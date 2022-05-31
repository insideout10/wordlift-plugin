<?php

namespace Wordlift\Modules\Plugin_Installer;

class Quiet_Skin extends \WP_Upgrader_Skin {

	public function feedback( $feedback, ...$args ) {
		// Dont print result.
	}

}