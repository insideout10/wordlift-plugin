<?php

namespace Wordlift\Vocabulary\Api;

use Wordlift\Vocabulary\Analysis_Background_Service;
use Wordlift\Vocabulary\Data\Term_Data\Term_Data_Factory;
use Wordlift\Vocabulary\Terms_Compat;
use WP_REST_Server;

/**
 * @since 1.0.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */
class Tag_Rest_Endpoint {

	/**
	 * @var Term_Data_Factory
	 */
	private $term_data_factory;

	/**
	 * Tag_Rest_Endpoint constructor.
	 *
	 * @param Term_Data_Factory $term_data_factory
	 */
	public function __construct( $term_data_factory ) {

		$this->term_data_factory = $term_data_factory;

	}

	public function register_routes() {
		$that = $this;
		add_action(
			'rest_api_init',
			function () use ( $that ) {
				register_rest_route(
					Api_Config::REST_NAMESPACE,
					'/tags',
					array(
						'methods'             => WP_REST_Server::CREATABLE,
						'callback'            => array( $that, 'get_tags' ),
						// @todo : review the permission level
						'permission_callback' => function () {
							return current_user_can( 'manage_options' );
						},
						'args'                => array(
							'limit'  => array(
								// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
								'validate_callback' => function ( $param, $request, $key ) {
									return is_numeric( $param ) && $param;
								},
								'required'          => true,
							),
							'offset' => array(
								// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
								'validate_callback' => function ( $param, $request, $key ) {
									return is_numeric( $param );
								},
								'required'          => true,
							),
						),
					)
				);
			}
		);

	}

	public function get_tags( $request ) {

		$data           = $request->get_params();
		$offset         = (int) $data['offset'];
		$limit          = (int) $data['limit'];
		$tags           = $this->get_terms_from_db( $limit, $offset );
		$term_data_list = array();

		foreach ( $tags as $tag ) {

			if ( $this->is_tag_excluded_from_ui( $tag ) ) {
				continue;
			}

			/**
			 * @param $tag \WP_Term
			 */
			$term_data_instance = $this->term_data_factory->get_term_data( $tag );
			$term_data          = $term_data_instance->get_data();
			if ( $term_data['entities'] ) {
				$term_data_list[] = $term_data;
			}
		}

		return $term_data_list;
	}

	/**
	 * @param \WP_Term $tag
	 *
	 * @return bool
	 */
	private function is_tag_excluded_from_ui( $tag ) {
		return (int) get_term_meta( $tag->term_id, Entity_Rest_Endpoint::IGNORE_TAG_FROM_LISTING, true ) === 1;
	}

	/**
	 * @param $limit
	 * @param $offset
	 *
	 * @return int|\WP_Error|\WP_Term[]
	 */
	public function get_terms_from_db( $limit, $offset ) {

		return Terms_Compat::get_terms(
			Terms_Compat::get_public_taxonomies(),
			array(
				'hide_empty' => false,
				'number'     => $limit,
				'offset'     => $offset,
				'meta_query' => array(
					array(
						'key'     => Analysis_Background_Service::ENTITIES_PRESENT_FOR_TERM,
						'compare' => 'EXISTS',
					),
				),
				'orderby'    => 'count',
				'order'      => 'DESC',
			)
		);

	}

}
