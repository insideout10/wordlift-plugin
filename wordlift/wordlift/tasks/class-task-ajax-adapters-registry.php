<?php

namespace Wordlift\Tasks;

class Task_Ajax_Adapters_Registry {

	private $task_ajax_adapters = array();

	public function __construct( $adapters = array() ) {

		$this->task_ajax_adapters = is_array( $adapters ) ? $adapters : array( $adapters );

	}

	/**
	 * @param Task_Ajax_Adapter $task_ajax_adapter
	 */
	public function register( $task_ajax_adapter ) {

		$this->task_ajax_adapters[] = $task_ajax_adapter;

	}

	/**
	 * @return Task_Ajax_Adapter[]
	 */
	public function get_task_ajax_adapters() {

		return $this->task_ajax_adapters;
	}

}
