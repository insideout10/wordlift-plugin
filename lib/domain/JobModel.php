<?php

class JobModel {
	
	public $id;
	public $state;
	public $post_id;

	function __construct($id, $state, $post_id) {
		$this->id 		= $id;
		$this->state 	= $state;
		$this->post_id 	= $post_id;
	}

	function is_running() {
		return (WORDLIFT_20_JOB_STATE_ANALYZING === $this->state);
	}

	function set_completed() {
		$this->state 	= WORDLIFT_20_JOB_STATE_COMPLETED;
	}

}

?>