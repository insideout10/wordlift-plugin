<?php

namespace Wordlift\Modules\Gardening_Kg\Rest;

use Wordlift\Modules\Gardening_Kg\Gardening_Kg_Scheduler;
use WP_REST_Server;

class Gardening_Kg_Controller {

	public function register_hooks() {
		add_action(
			'rest_api_init',
			function () {
				register_rest_route(
					'wordlift/v1',
					'/gardening-kg/syncs',
					array(
						'methods'  => WP_REST_Server::CREATABLE,
						'callback' => array( $this, 'create_sync' ),
					// 'permission_callback' => function () {
					// $user = wp_get_current_user();
					//
					// return is_super_admin( $user->ID ) || in_array( 'administrator', (array) $user->roles, true );
					// },
					)
				);
			}
		);
	}

	public function create_sync() {
		return as_enqueue_async_action( Gardening_Kg_Scheduler::HOOK, array(), Gardening_Kg_Scheduler::GROUP );
	}

}
