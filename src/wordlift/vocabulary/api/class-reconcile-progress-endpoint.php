<?php
/**
 * @since 1.3.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */

namespace Wordlift\Vocabulary\Api;

use Wordlift\Vocabulary\Analysis_Background_Service;
use Wordlift\Vocabulary\Terms_Compat;

/**
 * This endpoint is used to obtain the reconcile progress, number of tags accepted / total number of tags.
 */
class Reconcile_Progress_Endpoint {

	public function register_routes() {
		$that = $this;
		add_action(
			'rest_api_init',
			function () use ( $that ) {
				$that->register_progress_route();
			}
		);
	}

	public function progress() {

		$total_tags = count(
			Terms_Compat::get_terms(
				Terms_Compat::get_public_taxonomies(),
				array(
					'hide_empty' => false,
					'fields'     => 'ids',
					'meta_query' => array(
						array(
							'key'     => Analysis_Background_Service::ENTITIES_PRESENT_FOR_TERM,
							'compare' => '=',
							'value'   => '1',
						),
					),
				)
			)
		);

		$completed = count(
			Terms_Compat::get_terms(
				Terms_Compat::get_public_taxonomies(),
				array(
					'hide_empty' => false,
					'fields'     => 'ids',
					'meta_query' => array(
						array(
							'key'     => Entity_Rest_Endpoint::IGNORE_TAG_FROM_LISTING,
							'compare' => '=',
							'value'   => '1',
						),
					),
				)
			)
		);

		return array(
			'completed' => $completed,
			'total'     => $total_tags,
		);
	}

	private function register_progress_route() {
		register_rest_route(
			Api_Config::REST_NAMESPACE,
			'/reconcile_progress/progress',
			array(
				'methods'             => \WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'progress' ),
				'permission_callback' => function () {
					return current_user_can( 'manage_options' );
				},
			)
		);
	}

}
