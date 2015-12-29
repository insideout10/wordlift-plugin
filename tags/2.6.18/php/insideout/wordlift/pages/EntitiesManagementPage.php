<?php

class WordLift_EntitiesManagementPage {

	public function get() {
		include dirname(dirname(dirname(dirname(dirname( __FILE__ ))))) . "/html/entities-management.html";
	}

}

?>