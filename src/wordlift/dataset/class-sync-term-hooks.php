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
		add_action( 'create_term', array( $this, 'do_sync' ) );
		add_action( 'edit_term', array( $this, 'do_sync' ) );
		add_action( 'added_term_meta', array( $this, 'changed_term_meta' ), 10, 4 );
		add_action( 'updated_term_meta', array( $this, 'changed_term_meta' ), 10, 4 );
		add_action( 'deleted_term_meta', array( $this, 'changed_term_meta' ), 10, 4 );
		add_action( 'pre_delete_term', array( $this, 'delete_term' ) );

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

	/**
	 * @param $term \WP_Term
	 */
	public function delete_term( $term ) {
		$this->enqueue( array( 'do_delete', $term->term_id ) );
	}

	public function do_delete( $term_id ) {
		try {
			$this->sync_service->delete_one( Object_Type_Enum::TERM, $term_id,  get_term_meta( $term_id, 'entity_url', true ) );
		} catch ( \Exception $e ) {
			$this->log->error( "An error occurred while trying to delete term $term_id: " . $e->getMessage(), $e );
		}
	}
}
