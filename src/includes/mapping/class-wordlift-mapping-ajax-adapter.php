<?php
/**
 * Created by PhpStorm.
 * User: david
 * Date: 20.09.18
 * Time: 10:53
 */

class Wordlift_Mapping_Ajax_Adapter {

	/**
	 * The {@link Wordlift_Mapping_Service} instance.
	 *
	 * @since 3.20.0
	 * @access private
	 * @var \Wordlift_Mapping_Service $mapping_service The {@link Wordlift_Mapping_Service} instance.
	 */
	private $mapping_service;

	/**
	 * Create a {@link Wordlift_Mapping_Ajax_Adapter} instance.
	 *
	 * @param Wordlift_Mapping_Service $mapping_service The {@link Wordlift_Mapping_Service} instance.
	 *
	 * @since 3.20.0
	 */
	public function __construct( $mapping_service ) {

		$this->mapping_service = $mapping_service;

		add_action( 'wp_ajax_wl_set_entity_types_for_post_type', array( $this, 'set_entity_types_for_post_type' ) );
		add_action( 'wp_ajax_wl_update_post_type_entity_types', array( $this, 'update_post_type_entity_types' ) );

	}

	public function set_entity_types_for_post_type() {

		if ( ! isset( $_REQUEST['post_type'] ) || ! isset( $_REQUEST['entity_types'] ) ) { //phpcs:ignore WordPress.Security.NonceVerification.Recommended
			return;
		}

		$post_type    = sanitize_text_field( wp_unslash( $_REQUEST['post_type'] ) ); //phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$entity_types = array_map( 'sanitize_text_field', wp_unslash( (array) $_REQUEST['entity_types'] ) ); //phpcs:ignore WordPress.Security.NonceVerification.Recommended

		$this->mapping_service->set_entity_types_for_post_type( $post_type, $entity_types );

		wp_send_json_success();

	}

	public function update_post_type_entity_types() {

		// If the nonce is invalid, return an error.
		$nonce = isset( $_REQUEST['_nonce'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['_nonce'] ) ) : '';
		if ( ! wp_verify_nonce( $nonce, 'update_post_type_entity_types' ) ) {
			wp_send_json_error( __( 'Nonce Security Check Failed!', 'wordlift' ) );
		}

		if ( empty( $_REQUEST['post_type'] ) ) {
			wp_send_json_error( __( '`post_type` is required', 'wordlift' ) );
		}

		if ( empty( $_REQUEST['entity_types'] ) ) {
			wp_send_json_error( __( '`entity_types` is required', 'wordlift' ) );
		}

		// Get the post type.
		$post_type = isset( $_REQUEST['post_type'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['post_type'] ) ) : '';

		// Get the entity types URIs.
		$entity_types = isset( $_REQUEST['entity_types'] ) ? filter_var( wp_unslash( $_REQUEST['entity_types'] ), FILTER_REQUIRE_ARRAY ) : array();

		// Get the offset.
		$offset = isset( $_REQUEST['offset'] ) ? intval( $_REQUEST['offset'] ) : 0;

		// Update and get the results.
		$result = $this->mapping_service->update( $post_type, $entity_types, $offset );

		// Add our nonce to the result.
		$result['_nonce'] = wp_create_nonce( 'update_post_type_entity_types' );

		// Finally send the results.
		wp_send_json_success( $result );

	}

}
