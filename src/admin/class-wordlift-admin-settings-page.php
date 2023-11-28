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
	 * @param \Wordlift_Entity_Service                $entity_service A {@link Wordlift_Entity_Service} instance.
	 * @param \Wordlift_Admin_Input_Element           $input_element A {@link Wordlift_Admin_Input_Element} element renderer.
	 * @param \Wordlift_Admin_Language_Select_Element $language_select_element A {@link Wordlift_Admin_Language_Select_Element} element renderer.
	 * @param \Wordlift_Admin_Country_Select_Element  $country_select_element A {@link Wordlift_Admin_Country_Select_Element} element renderer.
	 * @param \Wordlift_Admin_Publisher_Element       $publisher_element A {@link Wordlift_Admin_Publisher_Element} element renderer.
	 * @param \Wordlift_Admin_Radio_Input_Element     $radio_input_element A {@link Wordlift_Admin_Radio_Input_Element} element renderer.
	 *
	 * @since 3.11.0
	 */
	public function __construct( $entity_service, $input_element, $language_select_element, $country_select_element, $publisher_element, $radio_input_element ) {

		$this->entity_service = $entity_service;

		// Set a reference to the UI elements.
		$this->input_element           = $input_element;
		$this->radio_input_element     = $radio_input_element;
		$this->language_select_element = $language_select_element;
		$this->country_select_element  = $country_select_element;
		$this->publisher_element       = $publisher_element;

	}

	private static $instance;

	/**
	 * Get the singleton instance of the Notice service.
	 *
	 * @return \Wordlift_Admin_Settings_Page The singleton instance of the settings page service.
	 * @since 3.14.0
	 */
	public static function get_instance() {

		if ( ! isset( self::$instance ) ) {
			$publisher_element = new Wordlift_Admin_Publisher_Element(
				Wordlift_Publisher_Service::get_instance(),
				new Wordlift_Admin_Tabs_Element(),
				new Wordlift_Admin_Select2_Element()
			);

			self::$instance = new self(
				Wordlift_Entity_Service::get_instance(),
				new Wordlift_Admin_Input_Element(),
				new Wordlift_Admin_Language_Select_Element(),
				new Wordlift_Admin_Country_Select_Element(),
				$publisher_element,
				new Wordlift_Admin_Radio_Input_Element()
			);
		}

		return self::$instance;
	}

	/**
	 * @inheritdoc
	 */
	public function get_parent_slug() {

		return 'wl_admin_menu';
	}

	/**
	 * @inheritdoc
	 */
	public function get_capability() {

		return 'manage_options';
	}

	/**
	 * @inheritdoc
	 */
	public function get_page_title() {

		return __( 'WordLift Settings', 'wordlift' );
	}

	/**
	 * @inheritdoc
	 */
	public function get_menu_title() {

		return __( 'Settings', 'wordlift' );
	}

	/**
	 * @inheritdoc
	 */
	public function get_menu_slug() {

		return 'wl_configuration_admin_menu';
	}

	/**
	 * @inheritdoc
	 */
	public function get_partial_name() {

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
		wp_enqueue_script( 'wordlift-admin-settings-page', plugin_dir_url( __DIR__ ) . 'admin/js/1/settings.js', array( 'wp-util' ), WORDLIFT_VERSION, false );
		wp_enqueue_style( 'wordlift-admin-settings-page', plugin_dir_url( __DIR__ ) . 'admin/js/1/settings.css', array(), WORDLIFT_VERSION );

	}

	/**
	 * Configure all the configuration parameters.
	 *
	 * Called by the *admin_init* hook.
	 *
	 * @since 3.11.0
	 */
	public function admin_init() {
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
			'value'       => Wordlift_Configuration_Service::get_instance()->get_key(),
			'description' => __( 'Insert the <a href="https://wordlift.io">WordLift Key</a> you received via email.', 'wordlift' )
							 . ' [' . apply_filters( 'wl_production_site_url', untrailingslashit( get_option( 'home' ) ) ) . ']',
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
			'value'       => Wordlift_Configuration_Service::get_instance()->get_entity_base_path(),
			/* translators: Placeholders: %s - a link to FAQ's page. */
			'description' => sprintf( __( 'All new pages created with WordLift, will be stored inside your internal vocabulary. You can customize the url pattern of these pages in the field above. Check our <a href="%s">FAQs</a> if you need more info.', 'wordlift' ), 'https://wordlift.io/wordlift-user-faqs/#10-why-and-how-should-i-customize-the-url-of-the-entity-pages-created-in-my-vocabulary' ),
		);

		// The following call is very heavy on large web sites and is always run
		// also when not needed:
		// $entity_base_path_args['readonly'] = 0 < $this->entity_service->count();
		//
		// It is now replaced by a filter to add the `readonly` flag to the
		// input element when this is actually rendered.
		add_filter(
			'wl_admin_input_element_params',
			array(
				$this,
				'entity_path_input_element_params',
			)
		);

		// Add the `wl_entity_base_path` field.
		add_settings_field(
			'wl-entity-base-path',                                // ID used to identify the field throughout the theme
			__( 'Entity Base Path', 'wordlift' ),                 // The label to the left of the option interface element
			// The name of the function responsible for rendering the option interface
			array( $this->input_element, 'render' ),
			'wl_general_settings',                                // The page on which this option will be displayed
			'wl_general_settings_section',                        // The name of the section to which this field belongs
			$entity_base_path_args
		);

		// Add the `country_code` field.
		add_settings_field(
			'wl-country-code',
			__( 'Country', 'wordlift' ),
			array( $this->country_select_element, 'render' ),
			'wl_general_settings',
			'wl_general_settings_section',
			array(
				'id'          => 'wl-country-code',
				'name'        => 'wl_general_settings[' . Wordlift_Configuration_Service::COUNTRY_CODE . ']',
				'value'       => Wordlift_Configuration_Service::get_instance()->get_country_code(),
				'description' => __( 'Please select a country.', 'wordlift' ),
				'notice'      => __( 'The selected language is not supported in this country.</br>Please choose another country or language.', 'wordlift' ),
			)
		);

		// Add the `alternateName` field.
		add_settings_field(
			'wl-alternate-name',
			__( 'Website Alternate Name', 'wordlift' ),
			array( $this->input_element, 'render' ),
			'wl_general_settings',
			'wl_general_settings_section',
			array(
				'id'    => 'wl-alternate-name',
				'name'  => 'wl_general_settings[' . Wordlift_Configuration_Service::ALTERNATE_NAME . ']',
				'value' => Wordlift_Configuration_Service::get_instance()->get_alternate_name(),
			)
		);

		// Add the override URL.
		add_settings_field(
			'wl-override-website-url',
			__( 'Override Website URL', 'wordlift' ),
			array( $this->input_element, 'render' ),
			'wl_general_settings',
			'wl_general_settings_section',
			array(
				'id'          => 'wl-override-website-url',
				'name'        => 'wl_general_settings[' . Wordlift_Configuration_Service::OVERRIDE_WEBSITE_URL . ']',
				'value'       => Wordlift_Configuration_Service::get_instance()->get_override_website_url(),
				'pattern'     => '^https?://.+$',
				'placeholder' => __( 'Optionally type a URL like https://...', 'wordlift' ),
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
				'value'       => Wordlift_Configuration_Service::get_instance()->is_link_by_default() ? 'yes' : 'no',
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
				'value'       => 'yes' === Wordlift_Configuration_Service::get_instance()->get_diagnostic_preferences() ? 'yes' : 'no',
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
	 */
	public function sanitize_callback( $input ) {
		// No nonce verification since this callback is handled by settings api.
		// Check whether a publisher name has been set.
		// phpcs:ignore Standard.Category.SniffName.ErrorCode
		if ( isset( $_POST['wl_publisher'] ) && ! empty( $_POST['wl_publisher']['name'] ) ) { //phpcs:ignore WordPress.Security.NonceVerification.Missing
			$name = isset( $_POST['wl_publisher']['name'] ) ? sanitize_text_field( wp_unslash( (string) $_POST['wl_publisher']['name'] ) ) : ''; //phpcs:ignore WordPress.Security.NonceVerification.Missing
			$type = isset( $_POST['wl_publisher']['type'] ) ? sanitize_text_field( wp_unslash( (string) $_POST['wl_publisher']['type'] ) ) : ''; //phpcs:ignore WordPress.Security.NonceVerification.Missing
			// phpcs:ignore Standard.Category.SniffName.ErrorCode
			$thumbnail_id = isset( $_POST['wl_publisher']['thumbnail_id'] ) ? sanitize_text_field( wp_unslash( $_POST['wl_publisher']['thumbnail_id'] ) ) : null; //phpcs:ignore WordPress.Security.NonceVerification.Missing

			// Set the type URI, either http://schema.org/Person or http://schema.org/Organization.
			$type_uri = sprintf( 'http://schema.org/%s', 'organization' === $type ? 'Organization' : 'Person' );

			// Create an entity for the publisher and assign it to the input
			// parameter which WordPress automatically saves into the settings.
			$input['publisher_id'] = $this->entity_service->create( $name, $type_uri, $thumbnail_id, 'publish' );
		}

		return $input;
	}

}
