<?php

namespace Wordlift\Dataset\Background\Stages;

use Wordlift\Dataset\Sync_Object_Adapter;
use Wordlift\Dataset\Sync_Object_Adapter_Factory;
use Wordlift\Object_Type_Enum;

class Sync_Background_Process_Posts_Stage {

	/**
	 * @var Sync_Object_Adapter_Factory
	 */
	private $sync_object_adapter_factory;

	/**
	 * Sync_Background_Process_Posts_Stage constructor.
	 *
	 * @param Sync_Object_Adapter_Factory $sync_object_adapter_factory
	 */
	public function __construct( $sync_object_adapter_factory ) {
		$this->sync_object_adapter_factory = $sync_object_adapter_factory;
	}

	public function count() {

		$post_types = get_post_types( array( 'public' => true ) );

		global $wpdb;
		$in_post_type = implode( "','", array_map( 'esc_sql', $post_types ) );
		$sql          = "
			SELECT COUNT( 1 )
			FROM $wpdb->posts
			WHERE post_type IN ('$in_post_type')
				AND post_status IN ( 'publish',  'future', 'draft', 'pending', 'private' )
			";

		return $wpdb->get_var( $sql ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
	}

	/**
	 * @param int $offset
	 * @param int $limit
	 *
	 * @return Sync_Object_Adapter[]
	 */
	public function get_sync_object_adapters( $offset, $limit ) {

		$post_types = get_post_types( array( 'public' => true ) );

		global $wpdb;
		$in_post_type = implode( "','", array_map( 'esc_sql', $post_types ) );
		$sql          = "
			SELECT ID
			FROM $wpdb->posts
			WHERE post_type IN ('$in_post_type')
				AND post_status IN ( 'publish',  'future', 'draft', 'pending', 'private' )
			ORDER BY ID ASC
			LIMIT %d, %d
			";

		$ids = $wpdb->get_col( $wpdb->prepare( $sql, $offset, $limit ) ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared

		return $this->sync_object_adapter_factory
			->create_many( Object_Type_Enum::POST, array_map( 'intval', $ids ) );
	}

}
