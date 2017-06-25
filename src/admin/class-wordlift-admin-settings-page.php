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

/**
 * Define the {@link Wordlift_Admin_Settings_Page} class.
 *
 * @since      3.11.0
 * @package    Wordlift
 * @subpackage Wordlift/admin
 */
class Wordlift_Admin_Settings_Page extends Wordlift_Admin_Page {

	/**
	 * A {@link Wordlift_Entity_Service} instance.
	 *
	 * @since  3.11.0
	 * @access private
	 * @var \Wordlift_Entity_Service $entity_service A {@link Wordlift_Entity_Service} instance.
	 */
	private $entity_service;

	/**
	 * A {@link Wordlift_Configuration_Service} instance.
	 *
	 * @since  3.11.0
	 * @access private
	 * @var \Wordlift_Configuration_Service $configuration_service A {@link Wordlift_Configuration_Service} instance.
	 */
	private $configuration_service;

	/**
	 * A {@link Wordlift_Admin_Input_Element} element renderer.
	 *
	 * @since  3.11.0
	 * @access private
	 * @var \Wordlift_Admin_Input_Element $input_element An {@link Wordlift_Admin_Input_Element} element renderer.
	 */
	private $input_element;

	/**
	 * A {@link Wordlift_Admin_Radio_Input_Element} element renderer.
	 *
	 * @since  3.13.0
	 * @access protected
	 * @var \Wordlift_Admin_Radio_Input_Element $radio_input_element A {@link Wordlift_Admin_Radio_Input_Element} element renderer.
	 */
	private $radio_input_element;

	/**
	 * A {@link Wordlift_Admin_Language_Select_Element} element renderer.
	 *
	 * @since  3.11.0
	 * @access private
	 * @var \Wordlift_Admin_Language_Select_Element $language_select_element A {@link Wordlift_Admin_Language_Select_Element} element renderer.
	 */
	private $language_select_element;

	/**
	 * A {@link Wordlift_Admin_Publisher_Element} element renderer.
	 *
	 * @since  3.11.0
	 * @access private
	 * @var \Wordlift_Admin_Publisher_Element $publisher_element A {@link Wordlift_Admin_Publisher_Element} element renderer.
	 */
	private $publisher_element;

	/**
	 * Create a {@link Wordlift_Admin_Settings_Page} instance.
	 *
	 * @since 3.11.0
	 *
	 * @param \Wordlift_Configuration_Service         $configuration_service
	 * @param \Wordlift_Entity_Service                $entity_service
	 * @param \Wordlift_Admin_Input_Element           $input_element
	 * @param \Wordlift_Admin_Language_Select_Element $language_select_element
	 * @param \Wordlift_Admin_Publisher_Element       $publisher_element
	 * @param \Wordlift_Admin_Radio_Input_Element     $radio_input_element
	 */
	function __construct( $configuration_service, $entity_service, $input_element, $language_select_element, $publisher_element, $radio_input_element ) {

		$this->configuration_service = $configuration_service;
		$this->entity_service        = $entity_service;

		// Set a reference to the UI elements.
		$this->input_element           = $input_element;
		$this->radio_input_element     = $radio_input_element;
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

		// JavaScript required for the settings page.
		// @todo: try to move to the `wordlift-admin.bundle.js`.
		wp_enqueue_script( 'wordlift-admin-settings-page', plugin_dir_url( dirname( __FILE__ ) ) . 'admin/js/wordlift-admin-settings-page.bundle.js', array( 'wp-util' ) );

	}

	/**
	 * Configure all the configuration parameters.
	 *
	 * Called by the *admin_init* hook.
	 *
	 * @since 3.11.0
	 */
	function admin_init() {

		// Register WordLift's general settings, providing our own sanitize callback
		// which will also check whether the user filled the WL Publisher form.
		register_setting(
			'wl_general_settings',
			'wl_general_settings',
			array( $this, 'sanitize_callback', )
		);

		// Add the general settings section.
		add_settings_section(
			'wl_general_settings_section', // ID used to identify this section and with which to register options.
			'',                            // Section header.
			'',                            // Callback used to render the description of the section.
			'wl_general_settings'          // Page on which to add this section of options.
		);

		$key_args = array(
			'id'          => 'wl-key',
			'name'        => 'wl_general_settings[' . Wordlift_Configuration_Service::KEY . ']',
			'value'       => $this->configuration_service->get_key(),
			'description' => _x( 'Insert the <a href="https://www.wordlift.io/blogger">WordLift Key</a> you received via email.', 'wordlift' ),
		);

		// Set the class for the key field based on the validity of the key.
		// Class should be "untouched" for an empty (virgin) value, "valid"
		// if the key is valid, or "invalid" otherwise.
		$validation_service = new Wordlift_Key_Validation_Service();

		if ( empty( $key_args['value'] ) ) {
			$key_args['css_class'] = 'untouched';
		} elseif ( $validation_service->is_valid( $key_args['value'] ) ) {
			$key_args['css_class'] = 'valid';
		} else {
			$key_args['css_class'] = 'invalid';
		}

		// Add the `key` field.
		add_settings_field(
			'wl-key',                                       // Element id used to identify the field throughout the theme.
			_x( 'WordLift Key', 'wordlift' ),               // The label to the left of the option interface element.
			// The name of the function responsible for rendering the option interface.
			array( $this->input_element, 'render', ),
			'wl_general_settings',                          // The page on which this option will be displayed.
			'wl_general_settings_section',                  // The name of the section to which this field belongs.
			$key_args                                       // The array of arguments to pass to the callback. In this case, just a description.
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

		// Add the `wl_entity_base_path` field.
		add_settings_field(
			'wl-entity-base-path',                                // ID used to identify the field throughout the theme
			_x( 'Entity Base Path', 'wordlift' ),                 // The label to the left of the option interface element
			// The name of the function responsible for rendering the option interface
			array( $this->input_element, 'render', ),
			'wl_general_settings',                                // The page on which this option will be displayed
			'wl_general_settings_section',                        // The name of the section to which this field belongs
			$entity_base_path_args
		);

		// Add the `language_name` field.
		add_settings_field(
			'wl-site-language',
			_x( 'Site Language', 'wordlift' ),
			array( $this->language_select_element, 'render' ),
			'wl_general_settings',
			'wl_general_settings_section',
			array(
				// The array of arguments to pass to the callback. In this case, just a description.
				'id'          => 'wl-site-language',
				'name'        => 'wl_general_settings[' . Wordlift_Configuration_Service::LANGUAGE . ']',
				'value'       => $this->configuration_service->get_language_code(),
				'description' => __( 'Each WordLift Key can be used only in one language. Pick yours.', 'wordlift' ),
			)
		);

		// Add the `publisher` field.
		add_settings_field(
			'wl-publisher-id',
			_x( 'Publisher', 'wordlift' ),
			array( $this->publisher_element, 'render' ),
			'wl_general_settings',
			'wl_general_settings_section',
			array(
				'id'   => 'wl-publisher-id',
				'name' => 'wl_general_settings[' . Wordlift_Configuration_Service::PUBLISHER_ID . ']',
			)
		);

		// Add the `link by default` field.
		add_settings_field(
			'wl-link-by-default',
			_x( 'Link by Default', 'wordlift' ),
			array( $this->radio_input_element, 'render' ),
			'wl_general_settings',
			'wl_general_settings_section',
			array(
				'id'          => 'wl-link-by-default',
				'name'        => 'wl_general_settings[' . Wordlift_Configuration_Service::LINK_BY_DEFAULT . ']',
				'value'       => $this->configuration_service->is_link_by_default() ? 'yes' : 'no',
				'description' => _x( 'Whether to link entities by default or not. This setting applies to all the entities.', 'wordlift' ),
			)
		);

	}

	/**
	 * Sanitize the configuration settings to be stored.
	 *
	 * If a new entity is being created for the publisher, create it and set The
	 * publisher setting.
	 *
	 * @since 3.11.0
	 *
	 * @param array $input The configuration settings array.
	 *
	 * @return array The sanitized input array.
	 */
	function sanitize_callback( $input ) {

		// Check whether a publisher name has been set.
		if ( isset( $_POST['wl_publisher'] ) && ! empty( $_POST['wl_publisher']['name'] ) ) {
			$name         = $_POST['wl_publisher']['name'];
			$type         = $_POST['wl_publisher']['type'];
			$thumbnail_id = $_POST['wl_publisher']['thumbnail_id'] ?: null;

			// Set the type URI, either http://schema.org/Person or http://schema.org/Organization.
			$type_uri = sprintf( 'http://schema.org/%s', 'organization' === $type ? 'Organization' : 'Person' );

			// Create an entity for the publisher and assign it to the input
			// parameter which WordPress automatically saves into the settings.
			$input['publisher_id'] = $this->entity_service->create( $name, $type_uri, $thumbnail_id, 'publish' );
		}

		return $input;
	}

}
