<?php

namespace Wordlift\Mappings;

use WP_REST_Request;
use WP_REST_Server;

/**
 * Define the {@link Mappings_REST_Controller} class.
 *
 * @since      3.25.0
 * @package    Wordlift
 * @subpackage Wordlift/includes/sync-mappings
 */
class Mappings_REST_Controller {
	// Namespace for CRUD mappings.
	const MAPPINGS_NAMESPACE = '/mappings';

	/**
	 * Registers route on rest api initialisation.
	 */
	public static function register_routes() {

		add_action( 'rest_api_init', 'Wordlift\Mappings\Mappings_REST_Controller::register_route_callback' );

	}

	/**
	 * Get a single mapping item by its mapping_id
	 *
	 * @param WP_REST_Request $request {@link WP_REST_Request instance}.
	 *
	 * @return array
	 */
	public static function get_mapping_item( $request ) {
		$dbo             = new Mappings_DBO();
		$mapping_id      = $request['id'];
		$mapping_id_data = array();
		$rule_groups     = $dbo->get_rule_groups_by_mapping( $mapping_id );
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
			'/mappings',
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => 'Wordlift\Mappings\Mappings_REST_Controller::insert_or_update_mapping_item',
				'permission_callback' => function () {
					return current_user_can( 'manage_options' );
				},
			)
		);
		// Get list of mapping items.
		register_rest_route(
			WL_REST_ROUTE_DEFAULT_NAMESPACE,
			'/mappings',
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => 'Wordlift\Mappings\Mappings_REST_Controller::list_mapping_items',
				'permission_callback' => function () {
					return current_user_can( 'manage_options' );
				},
			)
		);

		// Delete mapping items by id.
		register_rest_route(
			WL_REST_ROUTE_DEFAULT_NAMESPACE,
			'mappings',
			array(
				'methods'             => WP_REST_Server::DELETABLE,
				'callback'            => 'Wordlift\Mappings\Mappings_REST_Controller::delete_mapping_items',
				'permission_callback' => function () {
					return current_user_can( 'manage_options' );
				},
			)
		);

		// Get single mapping item route.
		register_rest_route(
			WL_REST_ROUTE_DEFAULT_NAMESPACE,
			'mappings/(?P<id>\d+)',
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => 'Wordlift\Mappings\Mappings_REST_Controller::get_mapping_item',
				'permission_callback' => function () {
					return current_user_can( 'manage_options' );
				},
			)
		);

		// Update mapping items.
		register_rest_route(
			WL_REST_ROUTE_DEFAULT_NAMESPACE,
			'mappings',
			array(
				'methods'             => WP_REST_Server::EDITABLE,
				'callback'            => 'Wordlift\Mappings\Mappings_REST_Controller::update_mapping_items',
				'permission_callback' => function () {
					return current_user_can( 'manage_options' );
				},
			)
		);

		// Clone mapping items.
		register_rest_route(
			WL_REST_ROUTE_DEFAULT_NAMESPACE,
			'mappings/clone',
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => 'Wordlift\Mappings\Mappings_REST_Controller::clone_mapping_items',
				'permission_callback' => function () {
					return current_user_can( 'manage_options' );
				},
			)
		);

		// Register rest endpoint to get the terms.
		register_rest_route(
			WL_REST_ROUTE_DEFAULT_NAMESPACE,
			'mappings/get_terms',
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => 'Wordlift\Mappings\Mappings_REST_Controller::get_terms_for_the_posted_taxonomy',
				'permission_callback' => function () {
					return current_user_can( 'manage_options' );
				},
			)
		);

		// Register rest endpoint to get the terms.
		register_rest_route(
			WL_REST_ROUTE_DEFAULT_NAMESPACE,
			'mappings/get_taxonomy_terms',
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => 'Wordlift\Mappings\Mappings_REST_Controller::get_taxonomy_terms_for_the_posted_taxonomy',
				'permission_callback' => function () {
					return current_user_can( 'manage_options' );
				},
			)
		);
	}

	/**
	 * Get the taxonomy & terms
	 *
	 * @param WP_REST_Request $request {@link WP_REST_Request instance}.
	 *
	 * @return array The array of the taxonomies & terms.
	 */
	// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
	public static function get_taxonomy_terms_for_the_posted_taxonomy( $request ) {
		$taxonomy_terms  = array();
		$post_taxonomies = get_taxonomies( array(), 'objects' );

		foreach ( $post_taxonomies as $post_taxonomy ) {
			$taxonomy_config = array(
				'taxonomy'   => $post_taxonomy->name,
				'hide_empty' => false,
			);

			$total_terms = wp_count_terms( $taxonomy_config );

			$post_taxonomy_terms = get_terms( $taxonomy_config );

			if ( $total_terms ) {
				$group_taxonomy = array(
					'parentValue'   => 'post_taxonomy',
					'group_name'    => $post_taxonomy->label,
					'group_options' => array(),
				);

				foreach ( $post_taxonomy_terms as $post_taxonomy_term ) {
					array_push(
						$group_taxonomy['group_options'],
						array(
							'label'    => ' - ' . $post_taxonomy_term->name,
							'value'    => $post_taxonomy_term->slug,
							'taxonomy' => 'post_taxonomy',
						)
					);

					$post_term_children = get_term_children( $post_taxonomy_term->term_id, $post_taxonomy->name );

					foreach ( $post_term_children as $post_term_child ) {
						$child_term = get_term_by( 'id', $post_term_child, $post_taxonomy->name );

						array_push(
							$group_taxonomy['group_options'],
							array(
								'label'    => ' -- ' . $child_term->name,
								'value'    => $child_term->slug,
								'taxonomy' => 'post_taxonomy',
							)
						);
					}
				}
				array_push( $taxonomy_terms, $group_taxonomy );
			}
		}

		return $taxonomy_terms;
	}

	/**
	 * Get the terms for the posted taxonomy name.
	 *
	 * @param WP_REST_Request $request {@link WP_REST_Request instance}.
	 *
	 * @return array The array of the terms for the taxonomy.
	 */
	public static function get_terms_for_the_posted_taxonomy( $request ) {
		$post_data = $request->get_params();
		if ( ! array_key_exists( 'taxonomy', $post_data ) ) {
			return array(
				'status'  => 'failure',
				'message' => __( 'Request not valid, must post a taxonomy to get terms', 'wordlift' ),
			);
		} else {
			$taxonomy = $post_data['taxonomy'];
			$terms    = get_terms( $taxonomy, array( 'hide_empty' => false ) );
			if ( is_wp_error( $terms ) ) {
				// Return error response, if the taxonomy is not valid.
				return array(
					'status'  => 'failure',
					'message' => __( 'Request not valid, must post a valid taxonomy', 'wordlift' ),
				);
			}

			return $terms;
		}
	}

	/**
	 * Clone posted mapping items.
	 *
	 * @param WP_REST_Request $request {@link WP_REST_Request instance}.
	 *
	 * @return array
	 */
	public static function clone_mapping_items( $request ) {
		$dbo           = new Mappings_DBO();
		$post_data     = (array) $request->get_params();
		$mapping_items = (array) $post_data['mapping_items'];
		foreach ( $mapping_items as $mapping_item ) {
			$mapping_id = (int) $mapping_item['mapping_id'];
			// Clone the current mapping item.
			$cloned_mapping_id = $dbo->insert_mapping_item( $mapping_item['mapping_title'] );
			// Clone all the rule groups.
			$rule_groups_to_be_cloned = $dbo->get_rule_groups_by_mapping( $mapping_id );
			// Clone all the properties.
			$properties_to_be_cloned = $dbo->get_properties( $mapping_id );
			foreach ( $properties_to_be_cloned as $property ) {
				// Assign a new mapping id.
				$property['mapping_id'] = $cloned_mapping_id;
				// Removing this property id, since a new id needed to be created for
				// new property.
				unset( $property['property_id'] );
				$dbo->insert_or_update_property( $property );
			}
			// Loop through the rule groups and insert them in table with the mapping id.
			foreach ( $rule_groups_to_be_cloned as $rule_group ) {
				$cloned_rule_group_id = $dbo->insert_rule_group( $cloned_mapping_id );
				$original_rules       = (array) $rule_group['rules'];
				// Now we need to insert these rules for the cloned rule group id.
				foreach ( $original_rules as $clone_rule ) {
					// We should replace only rule group id in the cloned rules.
					$clone_rule['rule_group_id'] = (int) $cloned_rule_group_id;
					unset( $clone_rule['rule_id'] );
					$dbo->insert_or_update_rule_item( $clone_rule );
				}
			}
		}

		return array(
			'status'  => 'success',
			'message' => __( 'Successfully cloned mapping items', 'wordlift' ),
		);
	}

	/**
	 * Update posted mapping items.
	 *
	 * @param WP_REST_Request $request {@link WP_REST_Request instance}.
	 *
	 * @return array
	 */
	public static function update_mapping_items( $request ) {
		$dbo       = new Mappings_DBO();
		$post_data = $request->get_params();
		if ( array_key_exists( 'mapping_items', $post_data ) ) {
			$mapping_items = (array) $post_data['mapping_items'];
			foreach ( $mapping_items as $mapping_item ) {
				$dbo->insert_or_update_mapping_item( $mapping_item );
			}

			return array(
				'status'  => 'success',
				'message' => __( 'Mapping items successfully updated', 'wordlift' ),
			);
		} else {
			return array(
				'status'  => 'failure',
				'message' => __( 'Unable to update mapping item', 'wordlift' ),
			);
		}
	}

	/**
	 * Delete mapping items by mapping id
	 *
	 * @param WP_REST_Request $request {@link WP_REST_Request instance}.
	 *
	 * @return array
	 */
	public static function delete_mapping_items( $request ) {
		$dbo       = new Mappings_DBO();
		$post_data = $request->get_params();
		if ( array_key_exists( 'mapping_items', $post_data ) ) {
			$mapping_items = (array) $post_data['mapping_items'];
			foreach ( $mapping_items as $mapping_item ) {
				$dbo->delete_mapping_item( $mapping_item['mapping_id'] );
			}

			return array(
				'status'  => 'success',
				'message' => __( 'successfully deleted mapping items', 'wordlift' ),
			);
		} else {
			return array(
				'status'  => 'failure',
				'message' => __( 'Unable to delete mapping items', 'wordlift' ),
			);
		}
	}

	/**
	 * Get all mapping items
	 *
	 * @param WP_REST_Request $request {@link WP_REST_Request instance}.
	 *
	 * @return array
	 */
	// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
	public static function list_mapping_items( $request ) {
		$dbo = new Mappings_DBO();

		return $dbo->get_mappings();
	}

	/**
	 * Returns a array of rule ids for the rule group id
	 *
	 * @param Object $dbo Instance of {@link Mappings_DBO } class.
	 * @param int    $rule_group_id Primary key of rule group table.
	 *
	 * @return array A list of rule ids.
	 */
	private static function get_rule_ids( $dbo, $rule_group_id ) {
		$rule_rows_in_db = $dbo->get_rules_by_rule_group( $rule_group_id );
		$rule_ids        = array();
		foreach ( $rule_rows_in_db as $rule_row ) {
			array_push( $rule_ids, (int) $rule_row['rule_id'] );
		}

		return $rule_ids;
	}

	/**
	 * Insert or update mapping item depends on data
	 *
	 * @param Object $dbo Instance of {@link Mappings_DBO } class.
	 * @param int    $rule_group_id Refers to a rule group which this rule belongs to.
	 * @param array  $rule_list Array of rule  items.
	 *
	 * @return void
	 */
	private static function save_rules( $dbo, $rule_group_id, $rule_list ) {
		$rule_ids = self::get_rule_ids( $dbo, $rule_group_id );
		foreach ( $rule_list as $rule ) {
			// Some rules may not have rule group id, because they are inserted
			// in ui, so lets add them any way.
			$rule['rule_group_id'] = $rule_group_id;
			$dbo->insert_or_update_rule_item( $rule );
			if ( array_key_exists( 'rule_id', $rule ) ) {
				$index_to_be_removed = array_search(
					(int) $rule['rule_id'],
					$rule_ids,
					true
				);
				if ( false !== $index_to_be_removed ) {
					unset( $rule_ids[ $index_to_be_removed ] );
				}
			}
		}
		foreach ( $rule_ids as $rule_id ) {
			// Delete all the rule ids which are not posted.
			$dbo->delete_rule_item( $rule_id );
		}
	}

	/**
	 * Insert or update rule group list based on data
	 *
	 * @param Object $dbo Instance of {@link Mappings_DBO } class.
	 * @param int    $mapping_id Primary key of mapping table.
	 * @param array  $property_list { Array of property items }.
	 *
	 * @return void
	 */
	private static function save_property_list( $dbo, $mapping_id, $property_list ) {
		$properties_needed_to_be_deleted = $dbo->get_properties( $mapping_id );
		$property_ids                    = array();
		foreach ( $properties_needed_to_be_deleted as $property ) {
			array_push( $property_ids, (int) $property['property_id'] );
		}
		foreach ( $property_list as $property ) {
			if ( array_key_exists( 'property_id', $property ) ) {
				// Remove the id from the list of property ids needed to be deleted
				// because it is posted.
				$index_to_be_removed = array_search(
					(int) $property['property_id'],
					$property_ids,
					true
				);
				if ( false !== $index_to_be_removed ) {
					unset( $property_ids[ $index_to_be_removed ] );
				}
			}
			// Add mapping id to property data.
			$property['mapping_id'] = $mapping_id;
			$dbo->insert_or_update_property( $property );

		}
		// At the end remove all the property ids which are not posted.
		foreach ( $property_ids as $property_id ) {
			$dbo->delete_property( $property_id );
		}

	}

	/**
	 * Returns a array of rule group ids for the mapping id
	 *
	 * @param Object $dbo Instance of {@link Mappings_DBO } class.
	 * @param int    $mapping_id Primary key of mapping table.
	 *
	 * @return array $rule_group_ids A list of rule group ids.
	 */
	private static function get_rule_group_ids( $dbo, $mapping_id ) {
		$rule_group_rows = $dbo->get_rule_group_list( $mapping_id );
		$rule_group_ids  = array();
		foreach ( $rule_group_rows as $rule_group_row ) {
			array_push( $rule_group_ids, (int) $rule_group_row['rule_group_id'] );
		}

		return $rule_group_ids;
	}

	/**
	 * Insert or update rule group list
	 *
	 * @param Object $dbo Instance of {@link Mappings_DBO } class.
	 * @param int    $mapping_id Primary key of mapping table.
	 * @param array  $rule_group_list { Array of rule group items }.
	 *
	 * @return void
	 */
	private static function save_rule_group_list( $dbo, $mapping_id, $rule_group_list ) {
		// The rule groups not posted should be deleted.
		$rule_group_ids = self::get_rule_group_ids( $dbo, $mapping_id );
		// Loop through rule group list and save the rule group.
		foreach ( $rule_group_list as $rule_group ) {
			if ( array_key_exists( 'rule_group_id', $rule_group ) ) {
				$rule_group_id = $rule_group['rule_group_id'];
			} else {
				// New rule group, should create new rule group id.
				$rule_group_id = $dbo->insert_rule_group( $mapping_id );
			}
			$index_to_be_removed = array_search(
				(int) $rule_group_id,
				$rule_group_ids,
				true
			);
			if ( false !== $index_to_be_removed ) {
				unset( $rule_group_ids[ $index_to_be_removed ] );
			}
			self::save_rules( $dbo, $rule_group_id, $rule_group['rules'] );
		}

		// Remove all the rule groups which are not posted.
		foreach ( $rule_group_ids as $rule_group_id ) {
			$dbo->delete_rule_group_item( $rule_group_id );
		}
	}

	/**
	 * Insert or update mapping item depends on data
	 *
	 * @param WP_REST_Request $request {@link WP_REST_Request instance}.
	 *
	 * @return array
	 */
	public static function insert_or_update_mapping_item( $request ) {
		$post_data = $request->get_params() === null ? array() : $request->get_params();
		$dbo       = new Mappings_DBO();
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

			return array(
				'status'     => 'success',
				'message'    => __( 'Successfully saved mapping item', 'wordlift' ),
				'mapping_id' => (int) $mapping_id,
			);
		} else {
			return array(
				'status'  => 'error',
				'message' => __( 'Unable to save mapping item', 'wordlift' ),
			);
		}
	}
}

Mappings_REST_Controller::register_routes();
