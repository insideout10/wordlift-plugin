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

	/**
	 *
	 */
	public function set_entity_types_for_post_type() {

		$post_type    = sanitize_text_field( $_REQUEST['post_type'] );
		$entity_types = (array) $_REQUEST['entity_types'];

		$this->mapping_service->set_entity_types_for_post_type( $post_type, $entity_types );

		wp_send_json_success();

	}

	public function update_post_type_entity_types() {

		// If the nonce is invalid, return an error.
		if ( ! wp_verify_nonce( $_REQUEST['_nonce'], 'update_post_type_entity_types' ) ) {
			wp_send_json_error( __( 'Nonce Security Check Failed!', 'wordlift' ) );
		}

		if ( empty($_REQUEST['post_type']) ) {
			wp_send_json_error( __( '`post_type` is required', 'wordlift' ) );
		}

		if ( empty($_REQUEST['entity_types']) ) {
			wp_send_json_error( __( '`entity_types` is required', 'wordlift' ) );
		}

		// Get the post type.
		$post_type = sanitize_text_field( $_REQUEST['post_type'] );

		// Get the entity types URIs.
		$entity_types = (array) $_REQUEST['entity_types'];

		// Get the offset.
		$offset = isset( $_REQUEST['offset'] ) ? intval( $_REQUEST['offset'] ) : 0;

		// Update and get the results.
		$result           = $this->mapping_service->update( $post_type, $entity_types, $offset );

		// Add our nonce to the result.
		$result['_nonce'] = wp_create_nonce( 'update_post_type_entity_types' );

		// Finally send the results.
		wp_send_json_success( $result );

	}

}
