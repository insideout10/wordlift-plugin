<?php

namespace Wordlift\Dataset\Background\Stages;

use Wordlift\Dataset\Sync_Object_Adapter_Factory;
use Wordlift\Object_Type_Enum;

class Sync_Background_Process_Users_Stage {

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

		global $wpdb;
		$in_post_type = $this->get_post_types_string();
		$sql          = "
			SELECT COUNT( DISTINCT post_author )
			FROM $wpdb->posts
			WHERE post_type IN ('$in_post_type')
			AND post_status IN ( 'publish',  'future', 'draft', 'pending', 'private' )
			";

		return $wpdb->get_var( $sql ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
	}

	public function get_sync_object_adapters( $offset, $limit ) {

		global $wpdb;
		$in_post_type = $this->get_post_types_string();
		$sql          = "
			SELECT DISTINCT post_author
			FROM $wpdb->posts
			WHERE post_type IN ('$in_post_type')
			AND post_status IN ( 'publish',  'future', 'draft', 'pending', 'private' )
			LIMIT %d, %d
			";

		$ids = $wpdb->get_col( $wpdb->prepare( $sql, $offset, $limit ) ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared

		return $this->sync_object_adapter_factory->create_many( Object_Type_Enum::USER, $ids );
	}

	/**
	 * @return string
	 */
	protected function get_post_types_string() {
		$post_types = get_post_types( array( 'public' => true ) );

		return implode( "','", array_map( 'esc_sql', $post_types ) );
	}

}
