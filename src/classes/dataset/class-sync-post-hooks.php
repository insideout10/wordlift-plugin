<?php

namespace Wordlift\Dataset;

use Wordlift\Content\Wordpress\Wordpress_Content_Id;
use Wordlift\Content\Wordpress\Wordpress_Content_Service;
use Wordlift\Object_Type_Enum;

class Sync_Post_Hooks extends Abstract_Sync_Hooks {
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
	 * Sync_Post_Hooks constructor.
	 *
	 * @param Sync_Service                $sync_service
	 * @param Sync_Object_Adapter_Factory $sync_object_factory
	 */
	public function __construct( $sync_service, $sync_object_factory ) {
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
		add_action( 'save_post', array( $this, 'save_post' ) );
		add_action( 'added_post_meta', array( $this, 'changed_post_meta' ), 10, 4 );
		add_action( 'updated_post_meta', array( $this, 'changed_post_meta' ), 10, 4 );
		add_action( 'deleted_post_meta', array( $this, 'changed_post_meta' ), 10, 4 );

		// running this on delete_post wouldnt work because at that time
		// entity_url would be deleted, so we cant delete this item from KG.
		add_action( 'before_delete_post', array( $this, 'delete_post' ) );

		// Remove post when its trashed.
		add_action( 'trashed_post', array( $this, 'delete_post' ) );
		// Save the post when its untrashed.
		add_action( 'untrashed_post', array( $this, 'save_post' ) );
		// Get sticky posts changes.
		add_action( 'update_option_sticky_posts', array( $this, 'sticky_posts' ), 10, 2 );

	}

	public function save_post( $post_id ) {

		if ( ! in_array( get_post_type( $post_id ), \Wordlift_Entity_Service::valid_entity_post_types(), true ) ) {
			return;
		}

		$this->sync( $post_id );

	}

	public function sticky_posts( $old_value, $value ) {
		foreach ( $old_value as $post_id ) {
			$this->sync( $post_id );
		}

		foreach ( $value as $post_id ) {
			$this->sync( $post_id );
		}
	}

	// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
	public function changed_post_meta( $meta_id, $post_id, $meta_key, $_meta_value ) {

		if ( in_array(
			$meta_key,
			apply_filters(
				'wl_dataset__sync_post_hooks__ignored_meta_keys',
				apply_filters(
					'wl_dataset__sync_hooks__ignored_meta_keys',
					array(
						'_pingme',
						'_encloseme',
						'entity_url',
					)
				)
			),
			true
		)
			 || ! in_array( get_post_type( $post_id ), \Wordlift_Entity_Service::valid_entity_post_types(), true )
		) {
			return;
		}

		$this->sync( $post_id );

	}

	private function sync( $post_id ) {
		$this->enqueue( array( 'do_sync', $post_id ) );
	}

	public function do_sync( $post_id ) {
		try {
			$post = get_post( $post_id );
			if ( ! isset( $post ) ) {
				return;
			}
			$this->sync_service->sync_many(
				array(
					$this->sync_object_factory->create( Object_Type_Enum::POST, $post_id ),
					$this->sync_object_factory->create( Object_Type_Enum::USER, $post->post_author ),
				)
			);
		} catch ( \Exception $e ) {
			$this->log->error( "An error occurred while trying to sync post $post_id: " . $e->getMessage(), $e );
		}

	}

	public function delete_post( $post_id ) {
		$this->enqueue( array( 'do_delete', $post_id ) );
	}

	public function do_delete( $post_id ) {
		try {
			$this->sync_service->delete_one(
				Object_Type_Enum::POST,
				$post_id,
				Wordpress_Content_Service::get_instance()->get_entity_id( Wordpress_Content_Id::create_post( $post_id ) )
			);
		} catch ( \Exception $e ) {
			$this->log->error( "An error occurred while trying to delete post $post_id: " . $e->getMessage(), $e );
		}
	}

}
