<?php

namespace Wordlift\Dataset;

use Wordlift\Object_Type_Enum;

class Sync_Post_Adapter extends Abstract_Sync_Object_Adapter {
	/**
	 * @var int
	 */
	private $post_id;

	/**
	 * Sync_Term_Adapter constructor.
	 *
	 * @param int $post_id
	 *
	 * @throws \Exception when an error occurs.
	 */
	public function __construct( $post_id ) {
		parent::__construct( Object_Type_Enum::POST, $post_id );

		$this->post_id = $post_id;
	}

	public function is_published() {
		return ( 'publish' === get_post_status( $this->post_id ) );
	}

	public function is_public() {
		// Check if the post type is public.
		$post_type     = get_post_type( $this->post_id );
		$post_type_obj = get_post_type_object( $post_type );

		return $post_type_obj->public;
	}

	public function set_values( $arr ) {
		global $wpdb;

		$field_names  = implode( ', ', array_map( 'esc_sql', array_keys( $arr ) ) );
		$field_values = "'" . implode( "', '", array_map( 'esc_sql', array_values( $arr ) ) ) . "'";

		$update_stmt = implode(
			', ',
			array_map(
				function ( $key ) use ( $arr ) {
					return "$key = '" . esc_sql( $arr[ $key ] ) . "'";
				},
				array_keys( $arr )
			)
		);

		$wpdb->query(
			$wpdb->prepare(
				"INSERT INTO {$wpdb->prefix}wl_entities( content_id, content_type, $field_names ) VALUES ( %d, %d, $field_values ) ON DUPLICATE KEY UPDATE $update_stmt;", // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
				$this->post_id,
				Object_Type_Enum::POST
			)
		);
	}

	public function get_value( $key ) {
		global $wpdb;

		return $wpdb->get_var(
			$wpdb->prepare(
				"SELECT $key FROM {$wpdb->prefix}wl_entities WHERE content_id = %d AND content_type = %d",  // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
				$this->post_id,
				Object_Type_Enum::POST
			)
		);
	}

}
