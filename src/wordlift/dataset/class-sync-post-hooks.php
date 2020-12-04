<?php

namespace Wordlift\Dataset;

use Wordlift\Object_Type_Enum;

class Sync_Post_Hooks {
	/**
	 * @var \Wordlift_Log_Service
	 */
	private $log;

	/**
	 * @var Sync_Service
	 */
	private $sync_service;

	/**
	 * Sync_Post_Hooks constructor.
	 *
	 * @param Sync_Service $sync_service
	 */
	function __construct( $sync_service ) {

		$this->log = \Wordlift_Log_Service::get_logger( get_class() );

		$this->sync_service = $sync_service;

		$this->register_hooks();

	}

	private function register_hooks() {
		/**
		 * Register hooks for post and meta.
		 */
		add_action( 'save_post', array( $this, 'save_post' ) );
		add_action( 'added_post_meta', array( $this, 'changed_post_meta' ), 10, 4 );
		add_action( 'updated_post_meta', array( $this, 'changed_post_meta' ), 10, 4 );
		add_action( 'deleted_post_meta', array( $this, 'changed_post_meta' ), 10, 4 );
		add_action( 'delete_post', array( $this, 'delete_post' ) );

	}

	public function save_post( $post_id ) {

		$this->sync( $post_id );

	}

	public function changed_post_meta( $meta_id, $post_id, $meta_key, $_meta_value ) {

		if ( in_array( $meta_key, apply_filters( 'wl_dataset__sync_post_hooks__ignored_meta_keys', array(
			'_pingme',
			'_encloseme',
			'entity_url',
		) ) ) ) {
			return;
		}

		$this->sync( $post_id );

	}

	private function sync( $post_id ) {

		try {
			$this->sync_service->sync_one( Object_Type_Enum::POST, $post_id );
		} catch ( \Exception $e ) {
			$this->log->error( "An error occurred while trying to sync post $post_id: " . $e->getMessage(), $e );
		}

	}

	public function delete_post( $post_id ) {

		try {
			$this->sync_service->delete_one( Object_Type_Enum::POST, $post_id );
		} catch ( \Exception $e ) {
			$this->log->error( "An error occurred while trying to delete post $post_id: " . $e->getMessage(), $e );
		}

	}

}