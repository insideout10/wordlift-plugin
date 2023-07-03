<?php
/**
 * @since 1.0.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */

namespace Wordlift\Vocabulary\Api;

use Wordlift\Cache\Ttl_Cache;
use Wordlift\Vocabulary\Data\Entity_List\Entity_List_Factory;
use WP_REST_Server;

/**
 * @since 1.0.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */
class Entity_Rest_Endpoint {

	const SAME_AS_META_KEY           = 'entity_same_as';
	const ALTERNATIVE_LABEL_META_KEY = '_wl_alt_label';
	const DESCRIPTION_META_KEY       = 'entity_description';
	const TYPE_META_KEY              = 'entity_type';
	const EXTERNAL_ENTITY_META_KEY   = '_wl_is_external';
	const IGNORE_TAG_FROM_LISTING    = '_wl_cmkg_ignore_tag_from_ui';

	public function register_routes() {
		$that = $this;
		add_action(
			'rest_api_init',
			function () use ( $that ) {
				$that->register_accept_route();
				$that->register_undo_route();
				$that->register_nomatch_route();
				$that->register_reject_route();
			}
		);
	}

	public function accept_entity( $request ) {
		$data        = $request->get_params();
		$term_id     = (int) $data['term_id'];
		$entity_data = (array) $data['entity'];
		$entity      = Entity_List_Factory::get_instance( $term_id );
		$entity->save_jsonld_data( $entity_data );
		update_term_meta( $term_id, self::IGNORE_TAG_FROM_LISTING, 1 );
		Ttl_Cache::flush_all();

		return $term_id;
	}

	public function undo( $request ) {
		$data    = $request->get_params();
		$term_id = (int) $data['term_id'];
		$entity  = Entity_List_Factory::get_instance( $term_id );
		$entity->clear_data();
		delete_term_meta( $term_id, self::IGNORE_TAG_FROM_LISTING );
		Ttl_Cache::flush_all();

		return $term_id;
	}

	public function mark_as_no_match( $request ) {
		$data    = $request->get_params();
		$term_id = (int) $data['term_id'];
		Ttl_Cache::flush_all();

		return update_term_meta( $term_id, self::IGNORE_TAG_FROM_LISTING, 1 );
	}

	private function register_undo_route() {
		register_rest_route(
			Api_Config::REST_NAMESPACE,
			'/entity/undo',
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'undo' ),
				'permission_callback' => function () {
					return current_user_can( 'manage_options' );
				},
				'args'                => array(
					'term_id' => array(
						// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
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
		$route = '/entity/accept';
		$this->register_entity_accept_or_reject_route( $route, array( $this, 'accept_entity' ) );
	}

	private function register_reject_route() {
		$route = '/entity/reject';
		$this->register_entity_accept_or_reject_route( $route, array( $this, 'reject_entity' ) );
	}

	public function reject_entity( $request ) {
		$data        = $request->get_params();
		$term_id     = (int) $data['term_id'];
		$entity_data = (array) $data['entity'];
		$entity      = Entity_List_Factory::get_instance( $term_id );

		$entity_id = array_key_exists( '@id', $entity_data ) ? $entity_data['@id'] : $entity_data['entityId'];
		$entity->remove_entity_by_id( $entity_id );
		Ttl_Cache::flush_all();
		return $term_id;
	}

	private function register_nomatch_route() {
		register_rest_route(
			Api_Config::REST_NAMESPACE,
			'/entity/no_match',
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'mark_as_no_match' ),
				'permission_callback' => function () {
					return current_user_can( 'manage_options' );
				},
				'args'                => array(
					'term_id' => array(
						// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
						'validate_callback' => function ( $param, $request, $key ) {
							return is_numeric( $param ) && $param;
						},
						'required'          => true,
					),
				),
			)
		);
	}

	/**
	 * @param $accept_route
	 */
	private function register_entity_accept_or_reject_route( $accept_route, $callback ) {

		register_rest_route(
			Api_Config::REST_NAMESPACE,
			$accept_route,
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => $callback,
				'permission_callback' => function () {
					return current_user_can( 'manage_options' );
				},
				'args'                => array(
					'term_id' => array(
						// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
						'validate_callback' => function ( $param, $request, $key ) {
							return is_numeric( $param ) && $param;
						},
						'required'          => true,
					),
					'entity'  => array(
						// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
						'validate_callback' => function ( $param, $request, $key ) {
							return is_array( $param );
						},
						'required'          => true,
					),
				),
			)
		);

	}

}
