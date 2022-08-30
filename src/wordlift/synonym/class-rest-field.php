<?php

namespace Wordlift\Synonym;

use Wordlift_Entity_Service;

class Rest_Field {

	public function __construct() {
		add_action( 'rest_api_init', array( $this, 'register_rest_field' ) );
	}

	public function register_rest_field() {

		if ( ! function_exists( 'register_rest_field' ) ) {
			return;
		}

		$post_types = Wordlift_Entity_Service::valid_entity_post_types();

		foreach ( $post_types as $post_type ) {

			register_rest_field(
				$post_type,
				\Wordlift_Entity_Service::ALTERNATIVE_LABEL_META_KEY,
				array(
					'get_callback'    => array( $this, 'get_value' ),
					'update_callback' => array( $this, 'update_value' ),
				)
			);

		}

	}

	/**
	 * @param $meta_values array
	 * @param $post \WP_Post
	 * @param $meta_key string
	 */
	// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
	public function update_value( $meta_values, $post, $meta_key ) {

		if ( ! is_array( $meta_values ) ) {
			return;
		}

		$entity_service = Wordlift_Entity_Service::get_instance();

		$entity_service->set_alternative_labels( $post->ID, $meta_values );
	}

	/**
	 * @param $post array Post array.
	 *
	 * @return array|mixed
	 */
	public function get_value( $post ) {
		$data = get_post_meta( (int) $post['id'], \Wordlift_Entity_Service::ALTERNATIVE_LABEL_META_KEY );
		if ( ! is_array( $data ) ) {
			return array();
		}

		return $data;
	}

}
