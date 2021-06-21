<?php

namespace Wordlift\Dataset;

use Wordlift\Object_Type_Enum;

class Sync_Term_Hooks extends Abstract_Sync_Hooks {
	/**
	 * @var \Wordlift_Log_Service
	 */
	private $log;

	/**
	 * @var Sync_Service
	 */
	private $sync_service;

	/**
	 * @var Sync_Object_Adapter_Factory
	 */
	private $sync_object_factory;

	/**
	 * Sync_Term_Hooks constructor.
	 *
	 * @param Sync_Service $sync_service
	 * @param Sync_Object_Adapter_Factory $sync_object_factory
	 */
	function __construct( $sync_service, $sync_object_factory ) {
		parent::__construct();

		$this->log = \Wordlift_Log_Service::get_logger( get_class() );

		$this->sync_service        = $sync_service;
		$this->sync_object_factory = $sync_object_factory;

		$this->register_hooks();
	}

	private function register_hooks() {
		/**
		 * Register hooks for post and meta.
		 */
		add_action( 'saved_term', array( $this, 'saved_term' ) );
		add_action( 'added_term_meta', array( $this, 'changed_term_meta' ), 10, 4 );
		add_action( 'updated_term_meta', array( $this, 'changed_term_meta' ), 10, 4 );
		add_action( 'deleted_term_meta', array( $this, 'changed_term_meta' ), 10, 4 );
		add_action( 'delete_post', array( $this, 'delete_post' ) );
		// Remove post when its trashed.
		add_action( 'trashed_post', array( $this, 'delete_post' ) );
		// Save the post when its untrashed.
		add_action( 'untrashed_post', array( $this, 'save_post' ) );

	}

	public function saved_term( $term_id ) {

		// Sync all the terms without filtering.

		$this->sync( $term_id );

	}

	public function changed_term_meta( $meta_id, $term_id, $meta_key, $_meta_value ) {

		if ( in_array( $meta_key,
			apply_filters( 'wl_dataset__sync_post_hooks__ignored_meta_keys',
				apply_filters( 'wl_dataset__sync_hooks__ignored_meta_keys',
					array(
						'_pingme',
						'_encloseme',
						'entity_url',
					) ) ) )
		) {
			return;
		}

		$this->sync( $term_id );

	}

	private function sync( $term_id ) {
		$this->enqueue( array( 'do_sync', $term_id ) );
	}

	public function do_sync( $term_id ) {
		try {
			$term = get_term( $term_id );
			if ( ! isset( $term ) ) {
				return;
			}
			$this->sync_service->sync_many( array(
				$this->sync_object_factory->create( Object_Type_Enum::TERM, $term_id ),
			) );
		} catch ( \Exception $e ) {
			$this->log->error( "An error occurred while trying to sync post $term_id: " . $e->getMessage(), $e );
		}

	}

	public function delete_post( $post_id ) {
		$this->enqueue( array( 'do_delete', $post_id ) );
	}

	public function do_delete( $term_id ) {
		try {
			$this->sync_service->delete_one( Object_Type_Enum::TERM, $term_id );
		} catch ( \Exception $e ) {
			$this->log->error( "An error occurred while trying to delete term $term_id: " . $e->getMessage(), $e );
		}
	}

}