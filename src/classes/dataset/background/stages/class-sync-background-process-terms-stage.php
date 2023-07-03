<?php

namespace Wordlift\Dataset\Background\Stages;

use Wordlift\Dataset\Sync_Object_Adapter_Factory;
use Wordlift\Object_Type_Enum;

class Sync_Background_Process_Terms_Stage {

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

		$taxonomies    = get_taxonomies( array( 'public' => true ) );
		$in_taxonomies = implode( "','", array_map( 'esc_sql', $taxonomies ) );

		global $wpdb;
		$sql = "
			SELECT COUNT( 1 )
			FROM $wpdb->term_taxonomy
			WHERE taxonomy IN ('$in_taxonomies')
			";

		return $wpdb->get_var( $sql ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
	}

	public function get_sync_object_adapters( $offset, $limit ) {

		$taxonomies    = get_taxonomies( array( 'public' => true ) );
		$in_taxonomies = implode( "','", array_map( 'esc_sql', $taxonomies ) );

		global $wpdb;
		$sql = "
			SELECT term_id
			FROM $wpdb->term_taxonomy
		    WHERE taxonomy IN ('$in_taxonomies')
		    ORDER BY term_id ASC
			LIMIT %d, %d
			";

		$ids = $wpdb->get_col( $wpdb->prepare( $sql, $offset, $limit ) ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared

		return $this->sync_object_adapter_factory->create_many( Object_Type_Enum::TERM, $ids );
	}

}
