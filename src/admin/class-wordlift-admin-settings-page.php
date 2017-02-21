<?php
/**
 * Pages: Admin Settings.
 *
 * Handles the WordLift admin settings page.
 *
 * @since      3.11.0
 * @package    Wordlift
 * @subpackage Wordlift/admin
 */

require_once( plugin_dir_path( dirname( __FILE__ ) ) . 'modules/configuration/wordlift_configuration_constants.php' );
require_once( plugin_dir_path( dirname( __FILE__ ) ) . 'modules/configuration/wordlift_configuration_settings.php' );

/**
 * Define the {@link Wordlift_Admin_Settings_Page} class.
 *
 * @since      3.11.0
 * @package    Wordlift
 * @subpackage Wordlift/admin
 */
class Wordlift_Admin_Settings_Page extends Wordlift_Admin_Page {

	/**
	 * The maximum number of entities to be displayed in a "simple" publisher
	 * select without a search box.
	 *
	 * @since    3.11
	 * @access   private
	 * @var      integer $max_entities_without_search The maximum number of entities
	 *  to be displayed in a "simple" publisher select without a search box.
	 */
	private $max_entities_without_search;

	/**
	 * The maximum number of entities to load when called via AJAX.
	 *
	 * @since  3.11.0
	 * @access private
	 * @var int $max_entities_without_ajax The maximum number of entities to load when called via AJAX.
	 */
	private $max_entities_without_ajax;

	/**
	 * A {@link Wordlift_Entity_Service} instance.
	 *
	 * @since  3.11.0
	 * @access protected
	 * @var \Wordlift_Entity_Service $entity_service A {@link Wordlift_Entity_Service} instance.
	 */
	private $entity_service;

	/**
	 * A {@link Wordlift_Configuration_Service} instance.
	 *
	 * @since  3.11.0
	 * @access protected
	 * @var \Wordlift_Configuration_Service $configuration_service A {@link Wordlift_Configuration_Service} instance.
	 */
	private $configuration_service;
	/**
	 * @var
	 */
	private $input_element;
	/**
	 * @var
	 */
	private $language_select_element;
	/**
	 * @var
	 */
	private $publisher_element;

	/**
	 * Create a {@link Wordlift_Admin_Settings_Page} instance.
	 *
	 * @since 3.11.0
	 *
	 * @param int                                     $max_entities_without_search The maximum number of entities to be displayed in a "simple" publisher select without a search box.
	 * @param int                                     $max_entities_without_ajax
	 * @param \Wordlift_Configuration_Service         $configuration_service
	 * @param \Wordlift_Entity_Service                $entity_service
	 * @param \Wordlift_Admin_Input_Element           $input_element
	 * @param \Wordlift_Admin_Language_Select_Element $language_select_element
	 * @param \Wordlift_Admin_Publisher_Element       $publisher_element
	 */
	function __construct( $max_entities_without_search, $max_entities_without_ajax, $configuration_service, $entity_service, $input_element, $language_select_element, $publisher_element ) {

		$this->max_entities_without_search = $max_entities_without_search;
		$this->max_entities_without_ajax   = $max_entities_without_ajax;
		$this->configuration_service       = $configuration_service;
		$this->entity_service              = $entity_service;

		// Set a reference to the UI elements.
		$this->input_element           = $input_element;
		$this->language_select_element = $language_select_element;
		$this->publisher_element       = $publisher_element;

	}

	/**
	 * @inheritdoc
	 */
	function get_parent_slug() {

		return 'wl_admin_menu';
	}

	/**
	 * @inheritdoc
	 */
	function get_capability() {

		return 'manage_options';
	}

	/**
	 * @inheritdoc
	 */
	function get_page_title() {

		return 'WorldLift Settings';
	}

	/**
	 * @inheritdoc
	 */
	function get_menu_title() {

		return 'Settings';
	}

	/**
	 * @inheritdoc
	 */
	function get_menu_slug() {

		return 'wl_configuration_admin_menu';
	}

	/**
	 * @inheritdoc
	 */
	function get_partial_name() {

		return 'wordlift-admin-settings-page.php';
	}

	/**
	 * @inheritdoc
	 */
	public function enqueue_scripts() {

		// Enqueue the media scripts to be used for the publisher's logo selection.
		wp_enqueue_media();

	}

	/**
	 * Configure all the configuration parameters.
	 *
	 * Called by the *admin_init* hook.
	 *
	 * @since 3.11.0
	 */
	function admin_init() {

		register_setting( 'wl_general_settings', 'wl_general_settings', array(
			$this,
			'sanitize_settings',
		) );

		add_settings_section(
			'wl_general_settings_section', // ID used to identify this section and with which to register options.
			'',                            // Section header.
			'',                            // Callback used to render the description of the section.
			'wl_general_settings'          // Page on which to add this section of options.
		);

		$key_args = array(
			'id'          => 'wl-key',
			'name'        => 'wl_general_settings[key]',
			'value'       => $this->configuration_service->get_key(),
			'description' => __( 'Insert the <a href="https://www.wordlift.io/blogger">WordLift Key</a> you received via email.', 'wordlift' ),
		);

		// Set the class for the key field based on the validity of the key.
		// Class should be "untouched" for an empty (virgin) value, "valid"
		// if the key is valid, or "invalid" otherwise.
		$validation_service = new Wordlift_Key_Validation_Service();

		if ( empty( $key_args['value'] ) ) {
			$key_args['class'] = 'untouched';
		} elseif ( $validation_service->is_valid( $key_args['value'] ) ) {
			$key_args['class'] = 'valid';
		} else {
			$key_args['class'] = 'invalid';
		}

		add_settings_field(
			WL_CONFIG_WORDLIFT_KEY,           // ID used to identify the field throughout the theme.
			__( 'WordLift Key', 'wordlift' ), // The label to the left of the option interface element.
			array( $this->input_element, 'render', ),
			// The name of the function responsible for rendering the option interface
			'wl_general_settings',         // The page on which this option will be displayed
			'wl_general_settings_section',      // The name of the section to which this field belongs
			$key_args                             // The array of arguments to pass to the callback. In this case, just a description.
		);

		// Entity Base Path input.
		$entity_base_path_args = array(
			// The array of arguments to pass to the callback. In this case, just a description.
			'id'          => 'wl-entity-base-path',
			'name'        => 'wl_general_settings[' . Wordlift_Configuration_Service::ENTITY_BASE_PATH_KEY . ']',
			'value'       => $this->configuration_service->get_entity_base_path(),
			'description' => sprintf( _x( 'All new pages created with WordLift, will be stored inside your internal vocabulary. You can customize the url pattern of these pages in the field above. Check our <a href="%s">FAQs</a> if you need more info.', 'wordlift' ), 'https://wordlift.io/wordlift-user-faqs/#10-why-and-how-should-i-customize-the-url-of-the-entity-pages-created-in-my-vocabulary' ),
		);

		$entity_base_path_args['readonly'] = 0 < $this->entity_service->count();

		add_settings_field(
			Wordlift_Configuration_Service::ENTITY_BASE_PATH_KEY, // ID used to identify the field throughout the theme
			__( 'Entity Base Path', 'wordlift' ),                 // The label to the left of the option interface element
			array( $this->input_element, 'render', ),
			// The name of the function responsible for rendering the option interface
			'wl_general_settings',                                // The page on which this option will be displayed
			'wl_general_settings_section',                        // The name of the section to which this field belongs
			$entity_base_path_args
		);

		// Site Language input.
		add_settings_field(
			WL_CONFIG_SITE_LANGUAGE_NAME,
			__( 'Site Language', 'wordlift' ),
			array( $this->language_select_element, 'render' ),
			'wl_general_settings',
			'wl_general_settings_section',
			array(
				// The array of arguments to pass to the callback. In this case, just a description.
				'id'          => 'wl-site-language',
				'name'        => 'wl_general_settings[site_language]',
				'value'       => $this->configuration_service->get_language_code(),
				'description' => __( 'Each WordLift Key can be used only in one language. Pick yours.', 'wordlift' ),
			)
		);

		add_settings_field(
			'wl_publisher',
			__( 'Publisher', 'wordlift' ),
			array( $this->publisher_element, 'render' ),
			'wl_general_settings',
			'wl_general_settings_section'
		);

	}

	/**
	 * Sanitize the configuration settings to be stored. Configured as a hook from *wl_configuration_settings*.
	 *
	 * If a new entity is being created for the publisher, create it and set The
	 * publisher setting.
	 *
	 * @since 3.11.0
	 *
	 * @param array $input The configuration settings array.
	 *
	 * @return mixed
	 */
	function sanitize_settings( $input ) {

		$input = apply_filters( 'wl_configuration_sanitize_settings', $input, $input );

		// If the user creates a new publisher entities the information is not part of the
		// "option" itself and need to get it from other $_POST values.
		if ( isset( $_POST['wl-setting-panel'] ) && ( 'wl-create-entity' == $_POST['wl-setting-panel'] ) ) {

			// validate publisher type
			if ( ! isset( $_POST['wl-publisher-type'] ) || ! in_array( $_POST['wl-publisher-type'], array(
					'person',
					'company',
				) )
			) {
				return $input;
			}

			// Set the type URI, either http://schema.org/Person or http://schema.org/Organization.
			$type_uri = sprintf( 'http://schema.org/%s', 'company' === $_POST['wl-publisher-type'] ? 'Organization' : 'Person' );

			// validate publisher logo
			if ( 'company' === $_POST['wl-publisher-type'] ) {
				if ( ! isset( $_POST['wl-publisher-logo-id'] ) || ! is_numeric( $_POST['wl-publisher-logo-id'] ) ) {
					return $input;
				}

				$logo = intval( $_POST['wl-publisher-logo-id'] );
			} else {
				$logo = 0;
			}

			// Create an entity for the publisher.
			$publisher_post_id = $this->entity_service->create( $_POST['wl-publisher-name'], $type_uri, $logo, 'publish' );

			$input[ Wordlift_Configuration_Service::PUBLISHER_ID ] = $publisher_post_id;
		}

		return $input;

	}


	/**
	 * Intercept the change of the WordLift key in order to set the dataset URI.
	 *
	 * @since 3.0.0
	 *
	 * @param array $old_value The old settings.
	 * @param array $new_value The new settings.
	 */
	function update_key( $old_value, $new_value ) {

		// Check the old key value and the new one. We're going to ask for the dataset URI only if the key has changed.
		$old_key = isset( $old_value['key'] ) ? $old_value['key'] : '';
		$new_key = isset( $new_value['key'] ) ? $new_value['key'] : '';

		// If the key hasn't changed, don't do anything.
		// WARN The 'update_option' hook is fired only if the new and old value are not equal
		if ( $old_key === $new_key ) {
			return;
		}

		// If the key is empty, empty the dataset URI.
		if ( '' === $new_key ) {
			$this->configuration_service->set_dataset_uri( '' );
		}

		// Request the dataset URI.
		$response = wp_remote_get( wl_configuration_get_accounts_by_key_dataset_uri( $new_key ), unserialize( WL_REDLINK_API_HTTP_OPTIONS ) );

		// If the response is valid, then set the value.
		if ( ! is_wp_error( $response ) && 200 === (int) $response['response']['code'] ) {

			$this->configuration_service->set_dataset_uri( $response['body'] );

		} else {
			// TO DO User notification is needed here.
		}

	}

}
