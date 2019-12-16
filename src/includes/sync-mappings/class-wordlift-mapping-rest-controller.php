<?php 
/**
 * Define the {@link Wordlift_Mapping_REST_Controller} class.
 *
 * @since      3.25.0
 * @package    Wordlift
 * @subpackage Wordlift/includes/sync-mappings
 */
class Wordlift_Mapping_REST_Controller {
	/** Namespace for wordlift plugin */
	const WORDLIFT_NAMESPACE = 'wordlift/v1';
	/**
	 * Registers route on rest api initialisation.
	 */
	public static function register_routes() {

		add_action( 'rest_api_init', 'Wordlift_Mapping_REST_Controller::register_route_callback' );

	}

	/**
	 * Register route call back function, called when rest api gets initialised
	 */
	public static function register_route_callback() {
		register_rest_route(
			self::WORDLIFT_NAMESPACE,
			'/sync-mappings/mapping',
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => 'Wordlift_Mapping_REST_Controller::insert_or_update_mapping_item',
				'permission_callback' => function () {
					return current_user_can( 'manage_options' );
				},
			)
		);
	}

	/**
	 * Insert or update mapping item depends on data
	 *
	 * @param Array $data Data containing all the post data.
	 */
	public static function insert_or_update_mapping_item( $data ) {

	}
}
