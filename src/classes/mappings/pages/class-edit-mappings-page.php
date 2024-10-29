<?php
/**
 * Pages: Sync Mappings page.
 *
 * Display the sync mappings page.
 *
 * @since 3.24.0
 * @package Wordlift
 * @subpackage Wordlift\Mappings\Pages
 */

namespace Wordlift\Mappings\Pages;

use Wordlift;
use Wordlift\Mappings\Mappings_REST_Controller;
use Wordlift\Mappings\Mappings_Transform_Functions_Registry;
use Wordlift\Scripts\Scripts_Helper;
use Wordlift_Admin_Page;

/**
 * Define the Wordlift_Admin_Edit_Mappings class.
 *
 * @since 3.24.0
 */
class Edit_Mappings_Page extends Wordlift_Admin_Page {

	/** Instance to store the registry class.
	 *
	 * @var Mappings_Transform_Functions_Registry { @link Mappings_Transform_Functions_Registry instance}
	 */
	public $transform_function_registry;

	/**
	 * Edit_Mappings_Page constructor.
	 *
	 * @param $transform_function_registry Mappings_Transform_Functions_Registry { @link Mappings_Transform_Functions_Registry instance }
	 */
	public function __construct( $transform_function_registry ) {
		parent::__construct();
		$this->transform_function_registry = $transform_function_registry;
	}

	public function render() {
		// Render all the settings when this method is called, because the partial page is loaded after
		// this method.
		// Load the UI dependencies.
		$edit_mapping_settings = $this->get_ui_settings_array();
		// Supply the settings to js client.
		wp_localize_script( 'wl-mappings-edit', 'wl_edit_mappings_config', $edit_mapping_settings );

		parent::render();
	}

	/**
	 * Load the text settings needed for the edit_mappings_page.
	 *
	 * @param array $edit_mapping_settings Key value pair of settings used by edit mappings page.
	 *
	 * @return array Adding text settings to the main settings array.
	 */
	private function load_text_settings_for_edit_mapping_page( array $edit_mapping_settings ) {
		$edit_mapping_settings['wl_add_mapping_text']     = __( 'Add Mapping', 'wordlift' );
		$edit_mapping_settings['wl_edit_mapping_text']    = __( 'Edit Mapping', 'wordlift' );
		$edit_mapping_settings['wl_edit_mapping_no_item'] = __( 'Unable to find the mapping item', 'wordlift' );
		$edit_mapping_settings['page']                    = 'wl_edit_mapping';

		return $edit_mapping_settings;
	}

	/**
	 * The base class {@link Wordlift_Admin_Page} will add the admin page to the WordLift menu.
	 *
	 * We don't want this page to be in the menu though. Therefore we override the `parent_slug` so that WordPress won't
	 * show it there.
	 *
	 * @return null return null to avoid this page to be displayed in WordLift's menu.
	 */
	protected function get_parent_slug() {
		return 'wl_edit_mappings';
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
		return 'wordlift-admin-mappings-edit.php';
	}

	/**
	 * {@inheritdoc}
	 */
	public function enqueue_scripts() {

		// Enqueue the script.
		Scripts_Helper::enqueue_based_on_wordpress_version(
			'wl-mappings-edit',
			plugin_dir_url( dirname( dirname( __DIR__ ) ) ) . 'js/dist/mappings-edit',
			array( 'react', 'react-dom', 'wp-polyfill' ),
			true
		);

		// Enqueue the style.
		wp_enqueue_style(
			'wl-mappings-edit',
			plugin_dir_url( dirname( dirname( __DIR__ ) ) ) . 'js/dist/mappings-edit.css',
			array(),
			WORDLIFT_VERSION
		);
	}

	/**
	 * Returns field name options based on the chosen field type.
	 * if string is returned a text field would be shown to user, if an array of options is returned
	 * then the select box would be shown to user.
	 *
	 * @return array Array of the options.
	 */
	public static function get_all_field_name_options() {

		$options = array(
			array(
				'field_type' => Wordlift\Mappings\Jsonld_Converter::FIELD_TYPE_TEXT_FIELD,
				'value'      => '',
				'label'      => __( 'Fixed Text', 'wordlift' ),
			),
			// @@todo maybe it makes sense to move this one as well to Wordlift/Mappings/Custom_Fields_Mappings.
			array(
				'field_type' => Wordlift\Mappings\Jsonld_Converter::FIELD_TYPE_CUSTOM_FIELD,
				'value'      => '',
				'label'      => __( 'Custom Field', 'wordlift' ),
			),
		);

		/**
		 * Allow 3rd parties to add field types.
		 *
		 * @param array An array of Field Types.
		 *
		 * @return array An array of Field Types.
		 *
		 * @since 3.25.0
		 */
		return apply_filters( 'wl_mappings_field_types', $options );
	}

	/**
	 * @return array An Array containing key value pairs of settings.
	 * @since 3.25.0
	 * Load dependencies required for js client.
	 */
	public function get_ui_settings_array() {
		// Create ui settings array to be used by js client.
		$edit_mapping_settings                                  = array();
		$edit_mapping_settings                                  = $this->load_rest_settings( $edit_mapping_settings );
		$edit_mapping_settings                                  = $this->load_text_settings_for_edit_mapping_page( $edit_mapping_settings );
		$edit_mapping_settings['wl_transform_function_options'] = $this->transform_function_registry->get_options();
		$edit_mapping_settings                                  = $this->load_field_type_and_name_options( $edit_mapping_settings );
		// Load logic field options.
		$edit_mapping_settings = $this->load_logic_field_options( $edit_mapping_settings );
		$edit_mapping_settings = $this->load_rule_field_options( $edit_mapping_settings );

		return $edit_mapping_settings;
	}

	/**
	 * Returns post type, post category, or any other post taxonomies
	 *
	 * @return array An array of select options
	 */
	private static function get_post_taxonomies_and_terms() {
		$taxonomy_options = array();
		$term_options     = array();
		$taxonomies       = get_object_taxonomies( 'post', 'objects' );

		foreach ( $taxonomies as $taxonomy ) {
			array_push(
				$taxonomy_options,
				array(
					'label'      => $taxonomy->label,
					'value'      => $taxonomy->name,
					'api_source' => 'taxonomy',
				)
			);
		}
		// Post type is also included in the list of taxonomies, so get the post type and merge with options.
		$post_type_array  = self::get_post_type_key_and_value();
		$post_type_option = $post_type_array['post_type_option_name'];
		// Get also the list of post types from the post_type_array.
		$post_type_option_values = $post_type_array['post_type_option_values'];

		$post_taxonomy_array  = self::get_post_taxonomy_key_and_value();
		$post_taxonomy_option = $post_taxonomy_array['post_taxonomy_option_name'];

		// Merge the post type option and post types in the taxonomy options
		array_push( $taxonomy_options, $post_type_option, $post_taxonomy_option );
		$term_options = array_merge( $term_options, $post_type_option_values );

		return array(
			'taxonomy_options' => $taxonomy_options,
			'term_options'     => $term_options,
		);
	}

	/**
	 * Return post type option and post type option values.
	 *
	 * @return array Array of post_type_option and post_type_option_values.
	 */
	private static function get_post_type_key_and_value() {
		$post_type_option_name   = array(
			'label'      => __( 'Post type', 'wordlift' ),
			'value'      => Wordlift\Mappings\Validators\Post_Type_Rule_Validator::POST_TYPE,
			// Left empty since post types are provided locally.
			'api_source' => '',
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
					'label'        => $post_type->label,
					'value'        => $post_type->name,
					'parent_value' => 'post_type',
				)
			);
		}

		return array(
			'post_type_option_name'   => $post_type_option_name,
			'post_type_option_values' => $post_type_option_values,
		);
	}

	/**
	 * Return post type option and post type option values.
	 *
	 * @return array Array of post_type_option and post_type_option_values.
	 */
	private static function get_post_taxonomy_key_and_value() {

		$post_taxonomy_option_name   = array(
			'label'      => __( 'Post Taxonomy', 'wordlift' ),
			'value'      => Wordlift\Mappings\Validators\Post_Taxonomy_Term_Rule_Validator::POST_TAXONOMY,
			'api_source' => 'post_taxonomy',
		);
		$post_taxonomy_option_values = array();

		return array(
			'post_taxonomy_option_name'   => $post_taxonomy_option_name,
			'post_taxonomy_option_values' => $post_taxonomy_option_values,
		);
	}

	/**
	 * This function loads the equal to, not equal to operator to the edit mapping settings.
	 *
	 * @param array $edit_mapping_settings
	 *
	 * @return array Loads the logic field options to the $edit_mapping_settings.
	 */
	private function load_logic_field_options( array $edit_mapping_settings ) {
		$edit_mapping_settings['wl_logic_field_options'] = array(
			array(
				'label' => __( 'is equal to', 'wordlift' ),
				'value' => Wordlift\Mappings\Validators\Rule_Validator::IS_EQUAL_TO,
			),
			array(
				'label' => __( 'is not equal to', 'wordlift' ),
				'value' => Wordlift\Mappings\Validators\Rule_Validator::IS_NOT_EQUAL_TO,
			),
		);

		return $edit_mapping_settings;
	}

	/**
	 * Validates the nonce posted by client and then assign the mapping id which should be edited.
	 *
	 * @param array $edit_mapping_settings
	 *
	 * @return array Edit mapping settings array with the mapping id if the nonce is valid.
	 */
	private function validate_nonce_and_assign_mapping_id( array $edit_mapping_settings ) {
		// We verify the nonce before making to load the edit mapping page for the wl_edit_mapping_id
		if ( isset( $_REQUEST['_wl_edit_mapping_nonce'] )
			 && wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST['_wl_edit_mapping_nonce'] ) ), 'wl-edit-mapping-nonce' ) ) {
			// We're using `INPUT_GET` here because this is a link from the UI, i.e. no POST.
			$mapping_id                                  = isset( $_REQUEST['wl_edit_mapping_id'] ) ?
				(int) filter_var( sanitize_text_field( wp_unslash( $_REQUEST['wl_edit_mapping_id'] ) ), FILTER_VALIDATE_INT ) : 0;
			$edit_mapping_settings['wl_edit_mapping_id'] = $mapping_id;
		}

		return $edit_mapping_settings;
	}

	/**
	 * Load the rest settings required for the edit_mappings js client.
	 *
	 * @param array $edit_mapping_settings
	 *
	 * @return array
	 */
	private function load_rest_settings( array $edit_mapping_settings ) {
		$edit_mapping_settings['rest_url']                   = get_rest_url(
			null,
			WL_REST_ROUTE_DEFAULT_NAMESPACE . Mappings_REST_Controller::MAPPINGS_NAMESPACE
		);
		$edit_mapping_settings['wl_edit_mapping_rest_nonce'] = wp_create_nonce( 'wp_rest' );
		$edit_mapping_settings                               = $this->validate_nonce_and_assign_mapping_id( $edit_mapping_settings );

		return $edit_mapping_settings;
	}

	/**
	 * Load the rule field options in to the settings.
	 *
	 * @param array $edit_mapping_settings
	 *
	 * @return array Return the settings.
	 */
	private function load_rule_field_options( array $edit_mapping_settings ) {
		// Load the rule field options.
		$rule_field_data                                    = self::get_post_taxonomies_and_terms();
		$edit_mapping_settings['wl_rule_field_one_options'] = $rule_field_data['taxonomy_options'];
		$edit_mapping_settings['wl_rule_field_two_options'] = $rule_field_data['term_options'];

		/**
		 * Allow 3rd parties to add ui options.
		 *
		 * @param array Array of Rule field one options where each item is in format
		 *
		 *  array ( 'label' => string, 'value' => string, 'api_source'=>string);
		 *
		 *  Leave api_source empty string to ensure didnt fetch rule field two options
		 *  from api.
		 *
		 * @return array Array of Rule field one options
		 *
		 * @since 3.27.0
		 */
		$edit_mapping_settings['wl_rule_field_one_options'] = apply_filters(
			'wl_mappings_rule_field_one_options',
			$edit_mapping_settings['wl_rule_field_one_options']
		);

		/**
		 * Allow 3rd parties to add rule field two options.
		 *
		 * @param array Array of Rule field two option where each item is in format
		 *
		 * array ( 'label' => string, 'value' => string, 'parent_value' => string );
		 *
		 * where parent_value is the value of the parent option in the rule_field_one_option.
		 *
		 * @since 3.27.0
		 */
		$edit_mapping_settings['wl_rule_field_two_options'] = apply_filters(
			'wl_mappings_rule_field_two_options',
			$edit_mapping_settings['wl_rule_field_two_options']
		);

		return $edit_mapping_settings;
	}

	/**
	 * Load field type and field name options to the settings array.
	 *
	 * @param array $edit_mapping_settings
	 *
	 * @return array
	 */
	private function load_field_type_and_name_options( array $edit_mapping_settings ) {
		$all_field_name_options  = self::get_all_field_name_options();
		$all_field_types_options = array_map(
			function ( $item ) {
				return array(
					'label' => $item['label'],
					'value' => $item['field_type'],
				);
			},
			$all_field_name_options
		);

		$edit_mapping_settings['wl_field_type_options'] = $all_field_types_options;
		// Add wl_edit_field_name_options.
		$edit_mapping_settings['wl_field_name_options'] = $all_field_name_options;

		return $edit_mapping_settings;
	}

}
