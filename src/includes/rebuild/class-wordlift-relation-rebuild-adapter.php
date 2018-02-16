<?php

class Wordlift_Relation_Rebuild_Adapter {
	/**
	 * @var Wordlift_Relation_Rebuild_Service
	 */
	private $relation_rebuild_service;


	/**
	 * Wordlift_Relation_Rebuild_Adapter constructor.
	 *
	 * @param \Wordlift_Relation_Rebuild_Service $relation_rebuild_service
	 */
	public function __construct( $relation_rebuild_service ) {
		$this->relation_rebuild_service = $relation_rebuild_service;
	}

	public function process_all() {

		$this->relation_rebuild_service->process_all();

		ob_clean();

		wp_send_json_success( array(
			'count' => $this->relation_rebuild_service->get_count(),
		) );

	}

}
