<?php

namespace Wordlift\Modules\Gardening_Kg\Main_Entity;

use Wordlift\Api\Api_Service;
use Wordlift\Modules\Common\Synchronization\Runner;
use Wordlift\Modules\Common\Synchronization\Store;
use Wordlift\Object_Type_Enum;

class Gardening_Kg_Main_Entity_Runner implements Runner {

	/**
	 * @var Store $store
	 */
	private $store;

	/**
	 * @var Api_Service
	 */
	private $api_service;

	/**
	 * @param Store       $store
	 * @param Api_Service $api_service
	 */
	public function __construct( Store $store, Api_Service $api_service ) {
		$this->store       = $store;
		$this->api_service = $api_service;
	}

	public function run( $last_id ) {
		$batch_size = 100;
		$items      = (array) $this->store->list_items( $last_id, $batch_size );

		foreach ( $items as $item ) {
			$this->process( $item );
		}

		// Count the processed items.
		$count_items = count( $items );

		// We're done, since the number of items is less than the requested qty.
		if ( $count_items < $batch_size ) {
			return array( $count_items, null );
		}

		// Get the last ID.
		$last_id = end( $items );

		// Finally return the count.
		return array( $count_items, $last_id );
	}

	// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
	private function process( $item ) {
		global $wpdb;

		// Get the entity data for non lifted abouts.
		$id = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT id FROM {$wpdb->prefix}wl_entities WHERE content_id = %d AND content_type = %d AND about_jsonld IS NULL",
				$item,
				Object_Type_Enum::POST
			)
		);

		// Exit if not found.
		if ( ! isset( $id ) ) {
			return;
		}

		// Lift.
		$post = get_post( $item );

		// Skip if this post must not be processed, e.g. it's not the right post_type, it's not `publish`, ...
		if ( ! $this->should_process( $post ) ) {
			return;
		}

		$title = wp_strip_all_tags( $post->post_title );

		$response = $this->api_service->request(
			'POST',
			'/thirdparty/cafemedia/gardening-kg/abouts',
			array(
				'accept'       => 'text/plain',
				'content-type' => 'text/plain',
			),
			$title
		);

		$response_body = trim( $response->get_body() );
		$lines         = explode( "\n", $response_body );
		$fields        = explode( "\t", $lines[0] );
		$jsonld        = isset( $fields[1] ) ? trim( $fields[1] ) : '';
		// No results.
		if ( empty( $jsonld ) ) {
			return;
		}

		// Store the results in the database
		$wpdb->query(
			$wpdb->prepare(
				"UPDATE {$wpdb->prefix}wl_entities SET about_jsonld = %s WHERE id = %d",
				$fields[1],
				$item
			)
		);
	}

	private function should_process( $post ) {
		return is_a( $post, 'WP_Post' ) &&
			   'publish' === $post->post_status &&
			   in_array( $post->post_type, array( 'post', 'page' ), true );
	}

	public function get_total() {
		global $wpdb;

		return intval( $wpdb->get_var( "SELECT COUNT(1) FROM $wpdb->posts WHERE post_status = 'publish'" ) );
	}

}
