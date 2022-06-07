<?php

namespace Wordlift\Modules\Acf4so;

class Quiet_Skin extends \WP_Upgrader_Skin {

	public function feedback( $feedback, ...$args ) {
		// Dont print result.
	}

}