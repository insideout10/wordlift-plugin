<?php

namespace Wordlift\Modules\Food_Kg;

class Ingredients_API {

    public function register_hooks() {
        add_action( 'rest_api_init', array( $this, 'register_routes' ) );
    }

    public function register_routes() {
        register_rest_route(
            WL_REST_ROUTE_DEFAULT_NAMESPACE,
            '/ingredients',
            array(
                'methods'             => \WP_REST_Server::READABLE,
                'callback'            => array( $this, 'get_ingredients' ),
                'permission_callback' => '__return_true',
            )
        );
    }

    public function get_ingredients( \WP_REST_Request $request ) {
        var_dump( $request );
        wp_send_json_success( $request );
    }
}
