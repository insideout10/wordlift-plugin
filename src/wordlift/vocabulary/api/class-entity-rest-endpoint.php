<?php
/**
 * @since 1.0.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */

namespace Wordlift\Vocabulary\Api;

use WP_REST_Server;

/**
 * @since 1.0.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */
class Entity_Rest_Endpoint {

	const SAME_AS_META_KEY = 'entity_same_as';
	const ALTERNATIVE_LABEL_META_KEY = '_wl_alt_label';
	const DESCRIPTION_META_KEY = 'entity_description';
	const TYPE_META_KEY = 'entity_type';
	const EXTERNAL_ENTITY_META_KEY = '_wl_is_external';
	const IGNORE_TAG_FROM_LISTING = '_wl_cmkg_ignore_tag_from_ui';


	public function register_routes() {
		add_action( 'rest_api_init',
			function () {
				$this->register_accept_route();
				$this->register_undo_route();
				$this->register_nomatch_route();
			} );
	}


	public function accept_entity( $request ) {
		$data        = $request->get_params();
		$term_id     = (int) $data['term_id'];
		$entity_data = (array) $data['entity'];


		$same_as_list = array_merge( $entity_data['sameAs'], array( $entity_data['@id'] ) );

		// Insert Same As
		delete_term_meta( $term_id, self::SAME_AS_META_KEY );
		foreach ( $same_as_list as $same_as ) {
			add_term_meta( $term_id, self::SAME_AS_META_KEY, $same_as );
		}

		// Insert Alt labels
		$alt_labels = array( (string) $entity_data['name'] );
		delete_term_meta( $term_id, self::ALTERNATIVE_LABEL_META_KEY );
		foreach ( $alt_labels as $alt_label ) {
			add_term_meta( $term_id, self::ALTERNATIVE_LABEL_META_KEY, $alt_label );
		}

		// Insert description and type
		add_term_meta( $term_id, self::DESCRIPTION_META_KEY, $entity_data['description'], true );
		add_term_meta( $term_id, self::TYPE_META_KEY, $entity_data['@type'], true );

		// Mark as external
		add_term_meta( $term_id, $this::EXTERNAL_ENTITY_META_KEY, 1, true );

		// Mark as ignored from ui
		update_term_meta( $term_id, self::IGNORE_TAG_FROM_LISTING, 1 );

		return $term_id;
	}

	public function undo( $request ) {
		$data    = $request->get_params();
		$term_id = (int) $data['term_id'];
		// Insert Same As
		delete_term_meta( $term_id, self::SAME_AS_META_KEY );
		delete_term_meta( $term_id, self::ALTERNATIVE_LABEL_META_KEY );
		delete_term_meta( $term_id, self::DESCRIPTION_META_KEY );
		delete_term_meta( $term_id, self::TYPE_META_KEY );
		delete_term_meta( $term_id, self::EXTERNAL_ENTITY_META_KEY );
		delete_term_meta( $term_id, self::IGNORE_TAG_FROM_LISTING );

		return $term_id;
	}


	public function mark_as_no_match( $request ) {
		$data    = $request->get_params();
		$term_id = (int) $data['term_id'];

		return add_term_meta( $term_id, self::IGNORE_TAG_FROM_LISTING, 1 );
	}


	private function register_undo_route() {
		register_rest_route(
			Api_Config::REST_NAMESPACE,
			'/entity/undo',
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'undo' ),
				//@todo : review the permission level
				'permission_callback' => function () {
					return current_user_can( 'manage_options' );
				},
				'args'                => array(
					'term_id' => array(
						'validate_callback' => function ( $param, $request, $key ) {
							return is_numeric( $param ) && $param;
						},
						'required'          => true,
					),
				),
			)
		);
	}

	private function register_accept_route() {
		register_rest_route(
			Api_Config::REST_NAMESPACE,
			'/entity/accept',
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'accept_entity' ),
				//@todo : review the permission level
				'permission_callback' => function () {
					return current_user_can( 'manage_options' );
				},
				'args'                => array(
					'term_id' => array(
						'validate_callback' => function ( $param, $request, $key ) {
							return is_numeric( $param ) && $param;
						},
						'required'          => true,
					),
					'entity'  => array(
						'validate_callback' => function ( $param, $request, $key ) {
							return is_array( $param );
						},
						'required'          => true,
					),
				),
			)
		);
	}

	private function register_nomatch_route() {
		register_rest_route(
			Api_Config::REST_NAMESPACE,
			'/entity/no_match',
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'mark_as_no_match' ),
				//@todo : review the permission level
				'permission_callback' => function () {
					return current_user_can( 'manage_options' );
				},
				'args'                => array(
					'term_id' => array(
						'validate_callback' => function ( $param, $request, $key ) {
							return is_numeric( $param ) && $param;
						},
						'required'          => true,
					),
				),
			)
		);
	}

}