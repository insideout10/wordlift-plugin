<?php

namespace Wordlift\Dataset;

use Wordlift\Content\Wordpress\Wordpress_Content_Id;
use Wordlift\Content\Wordpress\Wordpress_User_Content_Legacy_Service;
use Wordlift\Object_Type_Enum;

class Sync_User_Hooks extends Abstract_Sync_Hooks {
	/**
	 * @var \Wordlift_Log_Service
	 */
	private $log;

	/**
	 * @var Sync_Service
	 */
	private $sync_service;

	/**
	 * Sync_User_Hooks constructor.
	 *
	 * @param Sync_Service $sync_service
	 */
	public function __construct( Sync_Service $sync_service ) {
		parent::__construct();

		$this->log = \Wordlift_Log_Service::get_logger( get_class() );

		$this->sync_service = $sync_service;

		$this->register_hooks();

	}

	private function register_hooks() {
		/**
		 * Register hooks for user and meta.
		 */
		add_action( 'user_register', array( $this, 'changed_user' ) );
		add_action( 'profile_update', array( $this, 'changed_user' ) );
		add_action( 'added_user_meta', array( $this, 'changed_user_meta' ), 10, 3 );
		add_action( 'updated_user_meta', array( $this, 'changed_user_meta' ), 10, 3 );
		add_action( 'deleted_user_meta', array( $this, 'changed_user_meta' ), 10, 3 );
		add_action( 'delete_user', array( $this, 'delete_user' ) );

	}

	public function changed_user( $user_id ) {

		$this->sync( $user_id );

	}

	public function changed_user_meta( $meta_id, $user_id, $meta_key ) {

		if ( in_array(
			$meta_key,
			apply_filters(
				'wl_dataset__sync_user_hooks__ignored_meta_keys',
				apply_filters(
					'wl_dataset__sync_hooks__ignored_meta_keys',
					array(
						'rich_editing',
						'comment_shortcuts',
						'admin_color',
						'use_ssl',
						'show_admin_bar_front',
						'wptests_capabilities',
						'wptests_user_level',
						'dismissed_wp_pointers',
						'entity_url',
					)
				)
			),
			true
		) ) {
			return;
		}

		$this->sync( $user_id );

	}

	private function sync( $user_id ) {
		$this->enqueue( array( 'do_sync', $user_id ) );
	}

	public function do_sync( $user_id ) {

		try {
			$this->sync_service->sync_one( Object_Type_Enum::USER, (int) $user_id );
		} catch ( \Exception $e ) {
			$this->log->error( "An error occurred while trying to sync user $user_id: " . $e->getMessage(), $e );
		}

	}

	public function delete_user( $user_id ) {
		$this->enqueue( array( 'do_delete', $user_id ) );
	}

	public function do_delete( $user_id ) {
		try {
			$this->sync_service->delete_one(
				Object_Type_Enum::USER,
				$user_id,
				Wordpress_User_Content_Legacy_Service::get_instance()->get_entity_id( Wordpress_Content_Id::create_user( $user_id ) )
			);
		} catch ( \Exception $e ) {
			$this->log->error( "An error occurred while trying to delete user $user_id: " . $e->getMessage(), $e );
		}

	}

}
