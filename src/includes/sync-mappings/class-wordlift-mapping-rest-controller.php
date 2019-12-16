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
	 * @param Int   $mapping_id Primary key of mapping table.
	 * @param Array $rule_group_list { Array of rule group items }.
	 */
	private static function save_rule_group_list( $mapping_id, $rule_group_list ) {
		// Loop through rule group list and save the rule group.
		foreach ( $rule_group_list as $rule_group ) {
			if ( array_key_exists( 'rule_group_id', $rule_group ) ) {
				
			}
			else {
				// new rule group, should create new rule group id
			}
		}
	}

	/**
	 * Insert or update mapping item depends on data
	 *
	 * @param WP_REST_Request $request {@link WP_REST_Request instance}.
	 */
	public static function insert_or_update_mapping_item( $request ) {
		$post_data   = $request->get_body_params();
		$mapping_dbo = new Wordlift_Mapping_DBO();

		// check if valid object is posted.
		if ( array_key_exists( 'mapping_title', $post_data ) &&
			array_key_exists( 'rule_group_list' ) &&
			array_key_exists( 'property_list' ) ) {
			// Do validation, remove all incomplete data.
			$mapping_item = array();
			if ( array_key_exists( 'mapping_id', $post_data ) ) {
				$mapping_item['mapping_id'] = $post_data['mapping_id'];
			}
			$mapping_item['mapping_title'] = $post_data['mapping_title'];
			// lets save the mapping item.
			$mapping_id = $mapping_dbo->insert_or_update_mapping_item( $mapping_item );
			self::save_rule_group_list( $mapping_id, $post_data['rule_group_list'] );
		}
	}
}
