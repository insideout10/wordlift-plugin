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
	 * A singleton instance of the Notice service.
	 *
	 * @since  3.2.0
	 * @access private
	 * @var \Wordlift_Notice_Service $instance A singleton instance of the Notice service.
	 */
	private static $instance;

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
	 * A {@link Wordlift_Admin_Country_Select_Element} element renderer.
	 *
	 * @since  3.18.0
	 * @access private
	 * @var \Wordlift_Admin_Country_Select_Element $country_select_element A {@link Wordlift_Admin_Country_Select_Element} element renderer.
	 */
	private $country_select_element;

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
	 * @param \Wordlift_Configuration_Service $configuration_service A {@link Wordlift_Configuration_Service} instance.
	 * @param \Wordlift_Entity_Service $entity_service A {@link Wordlift_Entity_Service} instance.
	 * @param \Wordlift_Admin_Input_Element $input_element A {@link Wordlift_Admin_Input_Element} element renderer.
	 * @param \Wordlift_Admin_Language_Select_Element $language_select_element A {@link Wordlift_Admin_Language_Select_Element} element renderer.
	 * @param \Wordlift_Admin_Country_Select_Element $country_select_element A {@link Wordlift_Admin_Country_Select_Element} element renderer.
	 * @param \Wordlift_Admin_Publisher_Element $publisher_element A {@link Wordlift_Admin_Publisher_Element} element renderer.
	 * @param \Wordlift_Admin_Radio_Input_Element $radio_input_element A {@link Wordlift_Admin_Radio_Input_Element} element renderer.
	 *
	 * @since 3.11.0
	 *
	 */
	function __construct( $configuration_service, $entity_service, $input_element, $language_select_element, $country_select_element, $publisher_element, $radio_input_element ) {

		$this->configuration_service = $configuration_service;
		$this->entity_service        = $entity_service;

		// Set a reference to the UI elements.
		$this->input_element           = $input_element;
		$this->radio_input_element     = $radio_input_element;
		$this->language_select_element = $language_select_element;
		$this->country_select_element  = $country_select_element;
		$this->publisher_element       = $publisher_element;

		self::$instance = $this;

	}

	/**
	 * Get the singleton instance of the Notice service.
	 *
	 * @return \Wordlift_Admin_Settings_Page The singleton instance of the settings page service.
	 * @since 3.14.0
	 */
	public static function get_instance() {

		return self::$instance;
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

		return 'WordLift Settings';
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
		wp_enqueue_script( 'wordlift-admin-settings-page', plugin_dir_url( dirname( __FILE__ ) ) . 'admin/js/1/settings.js', array( 'wp-util' ) );
		wp_enqueue_style( 'wordlift-admin-settings-page', plugin_dir_url( dirname( __FILE__ ) ) . 'admin/js/1/settings.css' );

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
			array( $this, 'sanitize_callback' )
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
			'description' => __( 'Insert the <a href="https://www.wordlift.io/blogger">WordLift Key</a> you received via email.', 'wordlift' )
			                 . ' [' . get_option( 'home' ) . ']',
		);

		// Before we were used to validate the key beforehand, but this means
		// an http call whenever a page is opened in the admin area. Therefore
		// we now leave the input `untouched`, leaving to the client to update
		// the `css_class`.
		//
		// See https://github.com/insideout10/wordlift-plugin/issues/669.
		$key_args['css_class'] = 'untouched';

		// Add the `key` field.
		add_settings_field(
			'wl-key',                                       // Element id used to identify the field throughout the theme.
			__( 'WordLift Key', 'wordlift' ),               // The label to the left of the option interface element.
			// The name of the function responsible for rendering the option interface.
			array( $this->input_element, 'render' ),
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
			/* translators: Placeholders: %s - a link to FAQ's page. */
			'description' => sprintf( __( 'All new pages created with WordLift, will be stored inside your internal vocabulary. You can customize the url pattern of these pages in the field above. Check our <a href="%s">FAQs</a> if you need more info.', 'wordlift' ), 'https://wordlift.io/wordlift-user-faqs/#10-why-and-how-should-i-customize-the-url-of-the-entity-pages-created-in-my-vocabulary' ),
		);

		// The following call is very heavy on large web sites and is always run
		// also when not needed:
		// $entity_base_path_args['readonly'] = 0 < $this->entity_service->count();
		//
		// It is now replaced by a filter to add the `readonly` flag to the
		// input element when this is actually rendered.
		add_filter( 'wl_admin_input_element_params', array(
			$this,
			'entity_path_input_element_params',
		) );

		// Add the `wl_entity_base_path` field.
		add_settings_field(
			'wl-entity-base-path',                                // ID used to identify the field throughout the theme
			__( 'Entity Base Path', 'wordlift' ),                 // The label to the left of the option interface element
			// The name of the function responsible for rendering the option interface
			array( $this->input_element, 'render', ),
			'wl_general_settings',                                // The page on which this option will be displayed
			'wl_general_settings_section',                        // The name of the section to which this field belongs
			$entity_base_path_args
		);

		// Add the `language_name` field.
		add_settings_field(
			'wl-site-language',
			__( 'Site Language', 'wordlift' ),
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

		// Add the `country_code` field.
		add_settings_field(
			'wl-country-code',
			_x( 'Country', 'wordlift' ),
			array( $this->country_select_element, 'render' ),
			'wl_general_settings',
			'wl_general_settings_section',
			array(
				'id'          => 'wl-country-code',
				'name'        => 'wl_general_settings[' . Wordlift_Configuration_Service::COUNTRY_CODE . ']',
				'value'       => $this->configuration_service->get_country_code(),
				'description' => __( 'Please select a country.', 'wordlift' ),
				'notice'      => __( 'The selected language is not supported in this country.</br>Please choose another country or language.', 'wordlift' ),
			)
		);

		// Add the `publisher` field.
		add_settings_field(
			'wl-publisher-id',
			__( 'Publisher', 'wordlift' ),
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
			__( 'Link by Default', 'wordlift' ),
			array( $this->radio_input_element, 'render' ),
			'wl_general_settings',
			'wl_general_settings_section',
			array(
				'id'          => 'wl-link-by-default',
				'name'        => 'wl_general_settings[' . Wordlift_Configuration_Service::LINK_BY_DEFAULT . ']',
				'value'       => $this->configuration_service->is_link_by_default() ? 'yes' : 'no',
				'description' => __( 'Whether to link entities by default or not. This setting applies to all the entities.', 'wordlift' ),
			)
		);

		// Add the `diagnostic data` field.
		add_settings_field(
			'wl-send-diagnostic',
			__( 'Send Diagnostic Data', 'wordlift' ),
			array( $this->radio_input_element, 'render' ),
			'wl_general_settings',
			'wl_general_settings_section',
			array(
				'id'          => 'wl-send-diagnostic',
				'name'        => 'wl_general_settings[' . Wordlift_Configuration_Service::SEND_DIAGNOSTIC . ']',
				'value'       => 'yes' === $this->configuration_service->get_diagnostic_preferences() ? 'yes' : 'no',
				'description' => __( 'Whether to send diagnostic data or not.', 'wordlift' ),
			)
		);

	}

	/**
	 * Filter the {@link Wordlift_Admin_Input_Element} in order to add the
	 * `readonly` flag to the `wl-entity-base-path` input.
	 *
	 * @param array $args An array of {@link Wordlift_Admin_Input_Element} parameters.
	 *
	 * @return array The updated array.
	 * @since 3.17.0
	 *
	 */
	public function entity_path_input_element_params( $args ) {

		// Bail out if it's not the `wl-entity-base-path`).
		if ( 'wl-entity-base-path' !== $args['id'] ) {
			return $args;
		}

		// Set the readonly flag according to the entities count.
		$args['readonly'] = 0 < $this->entity_service->count();

		// Return the updated args.
		return $args;
	}

	/**
	 * Sanitize the configuration settings to be stored.
	 *
	 * If a new entity is being created for the publisher, create it and set The
	 * publisher setting.
	 *
	 * @param array $input The configuration settings array.
	 *
	 * @return array The sanitized input array.
	 * @since 3.11.0
	 *
	 */
	function sanitize_callback( $input ) {

		// Validate the selected country.
		$this->validate_country();

		// Check whether a publisher name has been set.
		if ( isset( $_POST['wl_publisher'] ) && ! empty( $_POST['wl_publisher']['name'] ) ) { // WPCS: CSRF, input var, sanitization ok.
			$name         = isset( $_POST['wl_publisher']['name'] ) ? (string) $_POST['wl_publisher']['name'] : '';
			$type         = isset( $_POST['wl_publisher']['type'] ) ? (string) $_POST['wl_publisher']['type'] : '';
			$thumbnail_id = isset( $_POST['wl_publisher']['thumbnail_id'] ) ? $_POST['wl_publisher']['thumbnail_id'] : null; // WPCS: CSRF, input var, sanitization ok.

			// Set the type URI, either http://schema.org/Person or http://schema.org/Organization.
			$type_uri = sprintf( 'http://schema.org/%s', 'organization' === $type ? 'Organization' : 'Person' );

			// Create an entity for the publisher and assign it to the input
			// parameter which WordPress automatically saves into the settings.
			$input['publisher_id'] = $this->entity_service->create( $name, $type_uri, $thumbnail_id, 'publish' );
		}

		return $input;
	}

	/**
	 * Check whether the currently selected country supports the site language.
	 *
	 * @since 3.18.0
	 */
	private function validate_country() {

		// Bail out if for some reason the country and language are not set.
		if (
			empty( $_POST['wl_general_settings']['site_language'] ) && // WPCS: CSRF, input var, sanitization ok.
			empty( $_POST['wl_general_settings']['country_code'] ) // WPCS: CSRF, input var, sanitization ok.
		) {
			return;
		}

		// Get the values.
		$language = $_POST['wl_general_settings']['site_language']; // WPCS: CSRF, input var, sanitization ok.
		$country  = $_POST['wl_general_settings']['country_code']; // WPCS: CSRF, input var, sanitization ok.
		$codes    = Wordlift_Countries::get_codes();

		// Check whether the chosen country has language limitations
		// and whether the chosen language is supported for that country.
		if (
			! empty( $codes[ $country ] ) &&
			! in_array( $language, $codes[ $country ] )
		) {
			// Otherwise add an error.
			add_settings_error(
				'wl-country-code',
				esc_attr( 'settings_updated' ),
				_x( 'The selected language is not supported for the currently chosen country. Please choose another country or language.', 'wordlift' )
			);
		}
	}

}
