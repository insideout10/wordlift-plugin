<?php

class WordLift_SettingsValidator {

	public $message;

	public function validate() {
	  
		$consumerKey = get_option( 'wordlift_consumer_key' );

		if ( !empty( $consumerKey ))
			return;

echo <<<EOF

	<div class="error">
		$this->message
	</div>

EOF;

	}
}

?>