<?php

class WordLift_SampleAdminMenu {

	public function writePageContent() {
		echo "Hello!";

		do_settings_sections('sample_admin_menu');
	}

}

?>