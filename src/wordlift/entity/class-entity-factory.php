<?php


namespace Wordlift\Entity;


class Entity_Factory {
	/**
	 * @var Entity_Factory
	 */
	private static $instance;

	/**
	 * @var \Wordlift_Entity_Service
	 */
	private $entity_service;

	public function __construct() {

		$this->entity_service = \Wordlift_Entity_Service::get_instance();

	}

	public static function get_instance() {

		if ( isset( self::$instance ) ) {
			return self::$instance;
		}

		self::$instance = new Entity_Factory();

		return self::$instance;
	}

	public function create( $params, $post_status = 'draft' ) {

		$args = wp_parse_args( $params, array(
			'labels'      => array(),
			'description' => '',
			'same_as'     => array(),
		) );

		// Use the first label as `post_title`.
		$labels = (array) $args['labels'];
		$label  = array_shift( $labels );

		$post_id = wp_insert_post( array(
			'post_type'    => 'entity',
			'post_status'  => $post_status,
			'post_title'   => $label,
			'post_content' => $args['description'],
		) );

		// Bail out if we've got an error.
		if ( empty( $post_id ) || is_wp_error( $post_id ) ) {
			throw new \Exception( "An error occurred while creating an entity." );
		}

		$this->merge_post_meta( $post_id, \Wordlift_Entity_Service::ALTERNATIVE_LABEL_META_KEY, $labels );
		$this->merge_post_meta( $post_id, \Wordlift_Schema_Service::FIELD_SAME_AS, $args['same_as'] );

		return $post_id;
	}

	public function update( $params ) {

		$args = wp_parse_args( $params, array(
			'ID'      => array(),
			'labels'  => array(),
			'same_as' => array(),
		) );

		$post_id = $args['ID'];

		$this->merge_post_meta( $post_id, \Wordlift_Entity_Service::ALTERNATIVE_LABEL_META_KEY, $args['labels'] );
		$this->merge_post_meta( $post_id, \Wordlift_Schema_Service::FIELD_SAME_AS, $args['same_as'] );

		return $post_id;
	}

	/**
	 * @param       $post_id
	 * @param       $meta_key
	 * @param array $values
	 */
	private function merge_post_meta( $post_id, $meta_key, $values ) {

		$existing = array_merge(
			(array) get_the_title( $post_id ),
			get_post_meta( $post_id, $meta_key )
		);

		foreach ( (array) $values as $value ) {
			if ( ! in_array( $value, $existing ) ) {
				add_post_meta( $post_id, $meta_key, $value );
			}
		}
	}

}
