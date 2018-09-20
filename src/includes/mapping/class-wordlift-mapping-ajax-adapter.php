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

	}

	/**
	 *
	 */
	public function set_entity_types_for_post_type() {

		$post_type    = sanitize_text_field( $_REQUEST['post_type'] );
		$entity_types = $_REQUEST['entity_types'];

		$this->mapping_service->set_entity_types_for_post_type( $post_type, $entity_types );

		wp_send_json_success();

	}

}
