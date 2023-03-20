<?php

namespace Wordlift\Modules\Dashboard\Bootstrap;

class Controller {

	public function register_hooks() {
		add_action( 'rest_api_init', array( $this, 'rest_api_init' ) );
	}

	public function rest_api_init() {
		register_rest_route(
			'wl-dashboard/v1',
			'/bootstrap',
			array(
				'methods'  => 'GET',
				'callback' => array( $this, 'bootstrap' ),
			)
		);
	}

	public function bootstrap() {
		return array(
			'id' => 0,
		);
	}

}
