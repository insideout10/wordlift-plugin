<?php 
/**
 * Define the {@link Wordlift_Mapping_REST_Controller} class.
 *
 * @since      3.25.0
 * @package    Wordlift
 * @subpackage Wordlift/includes/sync-mappings
 */
class Wordlift_Mapping_REST_Controller {
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
			WL_REST_ROUTE_DEFAULT_NAMESPACE,
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
	 * @param WP_REST_Request $request {@link WP_REST_Request instance}.
	 */
	public static function insert_or_update_mapping_item( $request ) {
		$post_data   = $request->get_post_params();
		$mapping_dbo = new Wordlift_Mapping_DBO();
		// Do validation, remove all incomplete data.
		$mapping_item = array();
		if ( array_key_exists( 'mapping_id', $post_data ) ) {
			$mapping_item['mapping_id'] = $post_data['mapping_id'];
		}
		$mapping_item['title'] = $post_data['mapping_title'];
		// lets save the mapping item.
		$mapping_dbo->insert_or_update_mapping_item()
	}
}
