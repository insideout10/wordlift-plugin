<?php 
/**
 * Define the {@link Wordlift_Mapping_REST_Controller} class.
 *
 * @since      3.25.0
 * @package    Wordlift
 * @subpackage Wordlift/includes/sync-mappings
 */
class Wordlift_Mapping_REST_Controller {
	// Namespace for CRUD mappings.
	const MAPPINGS_NAMESPACE = '/sync-mappings/mappings';
	/**
	 * Registers route on rest api initialisation.
	 */
	public static function register_routes() {

		add_action( 'rest_api_init', 'Wordlift_Mapping_REST_Controller::register_route_callback' );

	}
	/**
	 * Get a single mapping item by its mapping_id
	 *
	 * @param WP_REST_Request $request {@link WP_REST_Request instance}.
	 */
	public static function get_mapping_item( $request ) {
		$dbo             = new Wordlift_Mapping_DBO();
		$mapping_id      = $request['id'];
		$mapping_id_data = array();
		$rule_groups     = $dbo->get_rule_group_list_with_rules( $mapping_id );
		$properties      = $dbo->get_properties( $mapping_id );
		$mapping_row     = $dbo->get_mapping_item_data( $mapping_id );

		$mapping_id_data['mapping_id']      = $mapping_id;
		$mapping_id_data['property_list']   = $properties;
		$mapping_id_data['rule_group_list'] = $rule_groups;
		$mapping_id_data['mapping_title']   = $mapping_row['mapping_title'];
		return $mapping_id_data;
	}
	/**
	 * Register route call back function, called when rest api gets initialised
	 *
	 * @return void
	 */
	public static function register_route_callback() {
		register_rest_route(
			WL_REST_ROUTE_DEFAULT_NAMESPACE,
			'/sync-mappings/mappings',
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => 'Wordlift_Mapping_REST_Controller::insert_or_update_mapping_item',
				'permission_callback' => function () {
					return current_user_can( 'manage_options' );
				},
			)
		);
		// Get list of mapping items.
		register_rest_route(
			WL_REST_ROUTE_DEFAULT_NAMESPACE,
			'/sync-mappings/mappings',
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => 'Wordlift_Mapping_REST_Controller::list_mapping_items',
				'permission_callback' => function () {
					return current_user_can( 'manage_options' );
				},
			)
		);

		// Delete mapping items by id.
		register_rest_route(
			WL_REST_ROUTE_DEFAULT_NAMESPACE,
			'sync-mappings/mappings',
			array(
				'methods'             => WP_REST_Server::DELETABLE,
				'callback'            => 'Wordlift_Mapping_REST_Controller::delete_mapping_items',
				'permission_callback' => function () {
					return current_user_can( 'manage_options' );
				},
			)
		);

		// Get single mapping item route.
		register_rest_route(
			WL_REST_ROUTE_DEFAULT_NAMESPACE,
			'sync-mappings/mappings/(?P<id>\d+)',
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => 'Wordlift_Mapping_REST_Controller::get_mapping_item',
				'permission_callback' => function () {
					return current_user_can( 'manage_options' );
				},
			)
		);
	}

	/**
	 * Delete mapping items by mapping id
	 *
	 * @param WP_REST_Request $request {@link WP_REST_Request instance}.
	 */
	public static function delete_mapping_items( $request ) {
		$dbo = new Wordlift_Mapping_DBO();
		$post_data = $request->get_body_params();
		if ( array_key_exists( 'mapping_ids', $post_data ) ) {
			$mapping_ids = $post_data['mapping_ids'];
			foreach ( $mapping_ids as $mapping_id ) {
				$dbo->delete_mapping_item( $mapping_id );
			}
		}
	}

	/**
	 * Get all mapping items
	 *
	 * @param WP_REST_Request $request {@link WP_REST_Request instance}.
	 */
	public static function list_mapping_items( $request ) {
		$dbo = new Wordlift_Mapping_DBO();
		$mapping_items =  $dbo->get_mapping_items();
		return $mapping_items;
	}

	/**
	 * Insert or update mapping item depends on data
	 *
	 * @param Object $dbo Instance of {@link Wordlift_Mapping_DBO } class.
	 * @param Int    $rule_group_id Refers to a rule group which this rule belongs to.
	 * @param Array  $rule_list  Array of rule  items.
	 * @return void
	 */
	private static function save_rules( $dbo, $rule_group_id, $rule_list ) {
		foreach ( $rule_list as $rule ) {
			// Some rules may not have rule group id, because they are inserted
			// in ui, so lets add them any way.
			$rule['rule_group_id'] = $rule_group_id;
			$dbo->insert_or_update_rule_item( $rule );
		}
	}

	/**
	 * Insert or update rule group list based on data
	 *
	 * @param Object $dbo Instance of {@link Wordlift_Mapping_DBO } class.
	 * @param Int    $mapping_id Primary key of mapping table.
	 * @param Array  $property_list { Array of property items }.
	 * @return void
	 */
	private static function save_property_list( $dbo, $mapping_id, $property_list ) {
		foreach ( $property_list as $property ) {
			$dbo->insert_or_update_property( $mapping_id, $property );
		}
	}
	/**
	 * Insert or update rule group list
	 *
	 * @param Object $dbo Instance of {@link Wordlift_Mapping_DBO } class.
	 * @param Int    $mapping_id Primary key of mapping table.
	 * @param Array  $rule_group_list { Array of rule group items }.
	 * @return void
	 */
	private static function save_rule_group_list( $dbo, $mapping_id, $rule_group_list ) {
		// Loop through rule group list and save the rule group.
		foreach ( $rule_group_list as $rule_group ) {
			if ( array_key_exists( 'rule_group_id', $rule_group ) ) {
				$rule_group_id = $rule_group['rule_group_id'];
			}
			else {
				// New rule group, should create new rule group id.
				$rule_group_id = $dbo->insert_rule_group( $mapping_id );
			}
			self::save_rules( $dbo, $rule_group_id, $rule_group['rules'] );
		}
	}

	/**
	 * Insert or update mapping item depends on data
	 *
	 * @param WP_REST_Request $request {@link WP_REST_Request instance}.
	 */
	public static function insert_or_update_mapping_item( $request ) {
		$post_data   = $request->get_body_params();
		$dbo = new Wordlift_Mapping_DBO();

		// check if valid object is posted.
		if ( array_key_exists( 'mapping_title', $post_data ) &&
			array_key_exists( 'rule_group_list', $post_data ) &&
			array_key_exists( 'property_list', $post_data ) ) {
			// Do validation, remove all incomplete data.
			$mapping_item = array();
			if ( array_key_exists( 'mapping_id', $post_data ) ) {
				$mapping_item['mapping_id'] = $post_data['mapping_id'];
			}
			$mapping_item['mapping_title'] = $post_data['mapping_title'];
			// lets save the mapping item.
			$mapping_id = $dbo->insert_or_update_mapping_item( $mapping_item );
			self::save_rule_group_list( $dbo, $mapping_id, $post_data['rule_group_list'] );
			self::save_property_list( $dbo, $mapping_id, $post_data['property_list'] );
		}
	}
}

Wordlift_Mapping_REST_Controller::register_routes();
