<?php

class Wordlift_References_Rebuild_Service extends Wordlift_Listable {

	/**
	 * @since  3.18.0
	 * @access private
	 * @var \Wordlift_Linked_Data_Service $log A {@link Wordlift_Linked_Data_Service} instance.
	 */
	private $linked_data_service;

	/**
	 * A {@link Wordlift_Log_Service} instance.
	 *
	 * @since  3.18.0
	 * @access private
	 * @var \Wordlift_Log_Service $log A {@link Wordlift_Log_Service} instance.
	 */
	private $log;


	/**
	 * Wordlift_References_Rebuild_Service constructor.
	 *
	 * @param \Wordlift_Linked_Data_Service    $linked_data_service
	 */
	public function __construct( $linked_data_service ) {

		$this->log = Wordlift_Log_Service::get_logger( 'Wordlift_References_Rebuild_Service' );

		$this->linked_data_service    = $linked_data_service;

	}

	public function rebuild() {
		set_time_limit( 21600 ); // 6 hours

		$this->log->debug( 'Processing references...' );

		$this->process( array( $this, 'rebuild_single' ) );
	}

	public function rebuild_single( $post_id ) {
		$this->linked_data_service->push( $post_id );
	}

	/**
	 * @inheritdoc
	 */
	function find( $offset = 0, $limit = 10, $args = array() ) {
		global $wpdb;

		return $wpdb->get_col( $wpdb->prepare(
			"
			SELECT DISTINCT subject_id AS id
			FROM {$wpdb->prefix}wl_relation_instances
			LIMIT %d OFFSET %d
			",
			$limit,
			$offset
		) );

	}

}
