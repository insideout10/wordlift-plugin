<?php

namespace Wordlift\Vocabulary\Api;


use Wordlift\Vocabulary\Analysis_Background_Service;
use Wordlift\Vocabulary\Analysis_Service;
use WP_REST_Server;

/**
 * @since 1.0.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */
class Tag_Rest_Endpoint {
	/**
	 * @var Analysis_Service
	 */
	private $analysis_service;

	/**
	 * Tag_Rest_Endpoint constructor.
	 *
	 * @param Analysis_Service $analysis_service
	 */
	public function __construct( $analysis_service ) {

		$this->analysis_service = $analysis_service;

	}


	public function register_routes() {
		add_action( 'rest_api_init',
			function () {
				register_rest_route(
					Api_Config::REST_NAMESPACE,
					'/tags',
					array(
						'methods'             => WP_REST_Server::CREATABLE,
						'callback'            => array( $this, 'get_tags' ),
						//@todo : review the permission level
						'permission_callback' => function () {
							return current_user_can( 'manage_options' );
						},
						'args'                => array(
							'limit'  => array(
								'validate_callback' => function ( $param, $request, $key ) {
									return is_numeric( $param ) && $param;
								},
								'required'          => true,
							),
							'offset' => array(
								'validate_callback' => function ( $param, $request, $key ) {
									return is_numeric( $param );
								},
								'required'          => true,
							),
						),
					)
				);
			} );


	}


	public function get_tags( $request ) {

		$data   = $request->get_params();
		$offset = (int) $data['offset'];
		$limit  = (int) $data['limit'];
		$tags = $this->get_tags_from_db( $limit, $offset );
		$tag_data = array();

		foreach ( $tags as $tag ) {

			if ( $this->is_tag_excluded_from_ui( $tag ) ) {
				continue;
			}

			/**
			 * @param $tag \WP_Term
			 */
			$entities = $this->analysis_service->get_entities( $tag );
			if ( $entities ) {
				$tag_data[] = array(
					'tagId'          => $tag->term_id,
					'tagName'        => $tag->name,
					'tagDescription' => $tag->description,
					'tagLink'        => get_edit_tag_link( $tag->term_id, 'post_tag' ),
					'entities'       => $entities,
				);
			}
		}


		return $tag_data;
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
	public function get_tags_from_db( $limit, $offset ) {


		$args = array(
			'taxonomy'   => 'post_tag',
			'hide_empty' => false,
			'number'     => $limit,
			'offset'     => $offset,
			'meta_query' => array(
				array(
					'key'     => Analysis_Background_Service::ENTITIES_PRESENT_FOR_TERM,
					'compare' => 'EXISTS'
				)
			),
			'orderby'    => 'count',
			'order'      => 'DESC',
		);

		global $wp_version;

		if ( version_compare( $wp_version, '4.5', '<' ) ) {
			return get_terms( 'post_tag', $args );
		} else {
			return get_terms( $args );
		}


	}


}
