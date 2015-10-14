<?php

class WordLift_SettingsValidator {

	public $message;
	public $consumerKeyOptionName;

	public function validate() {
	  
		$consumerKey = get_option( $this->consumerKeyOptionName );

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