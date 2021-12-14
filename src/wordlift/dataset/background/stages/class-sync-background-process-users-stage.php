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
	function __construct( $sync_object_adapter_factory ) {
		$this->sync_object_adapter_factory = $sync_object_adapter_factory;
	}

	function count() {

		global $wpdb;

		$sql = "
			SELECT COUNT( DISTINCT post_author )
			FROM $wpdb->posts
			";


		return $wpdb->get_var( $sql );
	}

	function get_sync_object_adapters( $offset, $limit ) {

		global $wpdb;
		$sql = "
			SELECT DISTINCT post_author
			FROM $wpdb->posts
			LIMIT %d, %d
			";

		$ids = $wpdb->get_col( $wpdb->prepare( $sql, $offset, $limit ) );

		return $this->sync_object_adapter_factory->create_many( Object_Type_Enum::USER, $ids );
	}

}
