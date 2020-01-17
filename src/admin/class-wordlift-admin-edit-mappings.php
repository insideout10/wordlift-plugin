<?php
/**
 * Pages: Sync Mappings page.
 *
 * Display the sync mappings page.
 *
 * @since 3.24.0
 * @package Wordlift
 * @subpackage Wordlift/admin
 */

/**
 * Define the Wordlift_Admin_Edit_Mappings class.
 *
 * @since 3.24.0
 */
class Wordlift_Admin_Edit_Mappings extends Wordlift_Admin_Page {
	/**
	 * {@inheritdoc}
	 */
	public function __construct() {

		/**
		 * Load scripts with script helper.
		 *
		 * @since 3.25.0 - Load with script helper to ensure  WP 4.4 compatibility.
		 */
		Wordlift\Scripts\Scripts_Helper::register_based_on_wordpress_version(
			'wl-edit-mappings-script',
			plugin_dir_url( dirname( __FILE__ ) ) . 'js/dist/mappings-edit',
			array( 'react', 'react-dom', 'wp-polyfill' ),
			'admin_enqueue_scripts'
		);

		add_action(
			'admin_enqueue_scripts',
			function() {
				$wordlift = \Wordlift::get_instance();
				wp_register_style(
					'wl-edit-mappings-style',
					plugin_dir_url( dirname( __FILE__ ) ) . 'js/dist/mappings-edit.css',
					$wordlift->get_version()
				);
				Wordlift_Admin_Edit_Mappings::load_ui_dependancies();
			}
		);

		$that = $this;
		add_action( 'admin_menu', array( $that, 'add_edit_mapping_menu_entry' ) );
	}
	/** Add menu entry but dont show in sidebar */
	public function add_edit_mapping_menu_entry() {
		$that = $this;
		add_submenu_page(
			null,
			__( 'Add/Edit Mappings', 'wordlift' ),
			__( 'Add/Edit Mappings', 'wordlift' ),
			'manage_options',
			'wl_edit_mapping',
			array( $that, 'render' )
		);
	}

	/**
	 * Returns array of acf options.
	 *
	 * @return Array Acf options Array.
	 */
	private static function get_acf_options() {
		$acf_options = array();
		if ( function_exists( 'acf_get_field_groups' ) ) {
			$field_groups     = acf_get_field_groups();
			$field_group_data = array();
			foreach ( $field_groups as $field_group ) {
				$group_name    = $field_group['title'];
				$group_key     = $field_group['key'];
				$group_fields  = acf_get_fields( $group_key );
				$group_options = array();
				foreach ( $group_fields as $group_field ) {
					array_push(
						$group_options,
						array(
							'label' => $group_field['label'],
							'value' => $group_field['key'],
						)
					);
				}

				array_push(
					$acf_options,
					array(
						'group_name'    => $group_name,
						'group_options' => $group_options,
					)
				);
			}
		}
		return $acf_options;
	}
	/**
	 * Returns field name options based on the choosen field type.
	 *
	 * @return Array Array of the options.
	 */
	public static function get_all_field_name_options() {
		$field_name_options = array(
			array(
				'field_type' => 'acf',
				'value'      => self::get_acf_options(),
			),
			array(
				'field_type' => 'text',
				'value'      => '',
			),
			array(
				'field_type' => 'custom_field',
				'value'      => '',
			),
		);
		return $field_name_options;
	}
	/**
	 * Load Dependancies required for js client.
	 */
	public static function load_ui_dependancies() {
		// Create ui settings array to be used by js client.
		$edit_mapping_settings                               = array();
		$edit_mapping_settings['rest_url']                   = get_rest_url(
			null,
			WL_REST_ROUTE_DEFAULT_NAMESPACE . Wordlift_Mapping_REST_Controller::MAPPINGS_NAMESPACE
		);
		$edit_mapping_settings['page']                       = 'wl_edit_mapping';
		$edit_mapping_settings['wl_edit_mapping_rest_nonce'] = wp_create_nonce( 'wp_rest' );
		if ( isset( $_REQUEST['_wl_edit_mapping_nonce'] ) && wp_verify_nonce( $_REQUEST['_wl_edit_mapping_nonce'], 'wl-edit-mapping-nonce' ) ) {
			$edit_mapping_settings['wl_edit_mapping_id'] = (int) $_REQUEST['wl_edit_mapping_id'];
		}
		$edit_mapping_settings['wl_add_mapping_text']             = __( 'Add Mapping', 'wordlift' );
		$edit_mapping_settings['wl_edit_mapping_text']            = __( 'Edit Mapping', 'wordlift' );
		$edit_mapping_settings['wl_edit_mapping_no_item']         = __( 'Unable to find the mapping item', 'wordlift' );
		$transform_function_registry                              = new Wordlift_Mapping_Transform_Function_Registry();
		$edit_mapping_settings['wl_transform_function_options']   = $transform_function_registry->get_options();
		$edit_mapping_settings['wl_field_type_options'] = array(
			array(
				'label' => __( 'Text', 'wordlift' ),
				'value' => 'text',
			),
			array(
				'label' => __( 'Custom Field', 'wordlift' ),
				'value' => 'custom_field',
			),
		);
		// Only add acf if the acf is loaded.
		if ( class_exists( 'ACF' ) ) {
			array_push(
				$edit_mapping_settings['wl_field_type_options'],
				array(
					'label' => __( 'ACF', 'wordlift' ),
					'value' => 'acf',
				)
			);
		}
		// Add wl_edit_field_name_options.
		$edit_mapping_settings['wl_field_name_options']  = self::get_all_field_name_options();
		$edit_mapping_settings['wl_logic_field_options'] = array(
			array(
				'label' => __( 'is equal to', 'wordlift' ),
				'value' => '===',
			),
			array(
				'label' => __( 'is not equal to', 'wordlift' ),
				'value' => '!==',
			),
		);

		list(
			$edit_mapping_settings['wl_rule_field_one_options'],
			$edit_mapping_settings['wl_rule_field_two_options']
		) = self::get_post_taxonomies_and_terms();

		wp_localize_script( 'wl-edit-mappings-script', 'wl_edit_mappings_config', $edit_mapping_settings );
	}

	/**
	 * Returns post type, post category, or any other post taxonomies
	 * @return Array An array of select options
	 */
	private static function get_post_taxonomies_and_terms() {
		$taxonomy_options = array();
		$term_options     = array();
		$taxonomies       = get_object_taxonomies( 'post', 'objects' );

		foreach ( $taxonomies as $taxonomy ) {
			array_push(
				$taxonomy_options,
				array(
					'label' => $taxonomy->label,
					'value' => $taxonomy->name,
				)
			);
			// Version compatibility for get_terms.
			// ( https://developer.wordpress.org/reference/functions/get_terms/ ).
			if ( version_compare( get_bloginfo( 'version' ), '4.5', '>=' ) ) {
				$terms = get_terms(
					array(
						'taxonomy'   => $taxonomy->name,
						'hide_empty' => false,
					)
				);
			}
			else {
				$terms = get_terms( $taxonomy->name );
			}

			foreach ( $terms as $term ) {
				array_push(
					$term_options,
					array(
						'label'    => $term->name,
						'value'    => $term->term_id,
						'taxonomy' => $taxonomy->name,
					)
				);
			}
		}
		list( $post_type_option, $post_type_option_values ) = self::get_post_type_key_and_value();
		array_push( $taxonomy_options, $post_type_option );
		$term_options = array_merge( $term_options, $post_type_option_values );
		return array( $taxonomy_options, $term_options );
	}


	/**
	 * Return all ACF Fields
	 * @return Array Array of ACF field name with value
	 */
	private static function get_acf_field_options() {
		$acf_field_options = array();
		// Check if ACF is loaded, or else return empty options array.
		if ( function_exists( 'get_field_objects' ) ) {
			$field_data = (array) get_field_objects();
			foreach ( $field_data as $key => $value ) {
				array_push(
					$acf_field_options,
					array(
						'label' => $value['label'],
						'value' => $key,
					)
				);
			}
		}
		return $acf_field_options;
	}

	/**
	 * Return post type option and post type option values
	 * @return Array Array of post_type_option and post_type_option_values
	 */
	private static function get_post_type_key_and_value() {
		$post_type_option_name   = array(
			'label' => __( 'Post type', 'wordlift' ),
			'value' => __( 'post_type', 'wordlift' ),
		);
		$post_type_option_values = array();
		$post_types              = get_post_types(
			array(),
			'objects'
		);
		foreach ( $post_types as $post_type ) {
			array_push(
				$post_type_option_values,
				array(
					'label'    => $post_type->label,
					'value'    => $post_type->name,
					'taxonomy' => 'post_type',
				)
			);
		}

		return array( $post_type_option_name, $post_type_option_values );
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_page_title() {

		return __( 'Edit Mappings', 'wordlift' );
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_menu_title() {

		return __( 'Edit Mappings', 'wordlift' );
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_menu_slug() {

		return 'wl_edit_mapping';
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_partial_name() {

		return 'wordlift-admin-edit-mappings.php';
	}

}
