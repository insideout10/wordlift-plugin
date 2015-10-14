<?php

class WordLift_WordLiftSettingsPage {
	public function get() {
		include dirname(dirname(dirname(dirname(dirname( __FILE__ ))))) . "/html/wordlift-settings.html";
	}
}

?>