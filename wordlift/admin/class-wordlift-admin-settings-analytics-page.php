<?php
/**
 * Pages: Analytics Settings.
 *
 * Handles the WordLift admin analytics settings page.
 *
 * @since      3.21.0
 * @package    Wordlift
 * @subpackage Wordlift/analytics
 */

/**
 * Define the {@link Wordlift_Admin_Settings_Analytics_Page} class.
 *
 * @since      3.21.0
 * @package    Wordlift
 * @subpackage Wordlift/analytics
 */
class Wordlift_Admin_Settings_Analytics_Page extends Wordlift_Admin_Page {

	/**
	 * A singleton instance of the Notice service.
	 *
	 * @since  3.21.0
	 * @access private
	 * @var \Wordlift_Admin_Settings_Analytics_Page $instance A singleton instance of a {@link Wordlift_Admin_Settings_Analytics_Page} class.
	 */
	private static $instance;

	/**
	 * A {@link Wordlift_Admin_Input_Element} element renderer.
	 *
	 * @since  3.21.0
	 * @access private
	 * @var \Wordlift_Admin_Input_Element $input_element An {@link Wordlift_Admin_Input_Element} element renderer.
	 */
	private $input_element;

	/**
	 * A {@link Wordlift_Admin_Radio_Input_Element} element renderer.
	 *
	 * @since  3.21.0
	 * @access protected
	 * @var \Wordlift_Admin_Radio_Input_Element $radio_input_element A {@link Wordlift_Admin_Radio_Input_Element} element renderer.
	 */
	private $radio_input_element;

	/**
	 * Create a {@link Wordlift_Admin_Settings_Page} instance.
	 *
	 * @param \Wordlift_Admin_Input_Element       $input_element An input element class to output input boxes in a settings form.
	 * @param \Wordlift_Admin_Radio_Input_Element $radio_input_element A radio element input class for use in a settings form.
	 *
	 * @since 3.21.0
	 */
	public function __construct( $input_element, $radio_input_element ) {

		// Set a reference to the UI elements.
		$this->input_element       = $input_element;
		$this->radio_input_element = $radio_input_element;

		// Adds this page to the main admin settings tabbed menu.
		add_filter( 'wl_admin_page_tabs', array( $this, 'add_admin_page_tab' ) );

		self::$instance = $this;
	}

	/**
	 * Get the singleton instance of the Notice service.
	 *
	 * @return \Wordlift_Admin_Settings_Page The singleton instance of the settings page service.
	 * @since 3.21.0
	 */
	public static function get_instance() {

		return self::$instance;
	}

	/**
	 * @inheritdoc
	 */
	protected function get_parent_slug() {

		return 'wl_admin_menu';
	}

	/**
	 * @inheritdoc
	 */
	protected function get_capability() {

		return 'manage_options';
	}

	/**
	 * @inheritdoc
	 */
	public function get_page_title() {

		return __( 'WorldLift Analytics Settings', 'wordlift' );
	}

	/**
	 * @inheritdoc
	 */
	public function get_menu_title() {

		return __( 'Analytics Settings', 'wordlift' );
	}

	/**
	 * @inheritdoc
	 */
	public function get_menu_slug() {

		return 'wl_configuration_admin_analytics_menu';
	}

	/**
	 * @inheritdoc
	 */
	public function get_partial_name() {

		return 'wordlift-admin-settings-analytics-page.php';
	}

	/**
	 * Returns the string to use for the tab slug on main admin settings page.
	 *
	 * @method get_admin_page_tab_slug
	 * @return string
	 * @since  3.21.0
	 */
	public function get_admin_page_tab_slug() {
		return 'analytics';
	}

	/**
	 * Adds pages to the tabs menu on the main admin settings page.
	 *
	 * @method add_admin_page_tab
	 * @param array $tabs an array of tab titles and slugs to use when building a tabbed menu on option pages.
	 *
	 * @since  3.21.0
	 */
	public function add_admin_page_tab( $tabs ) {
		if ( apply_filters( 'wl_feature__enable__analytics', true ) ) {
			$tabs[] = array(
				'title' => $this->get_menu_title(),
				'slug'  => $this->get_admin_page_tab_slug(),
			);
		}

		return $tabs;
	}

	/**
	 * Configure all the configuration parameters.
	 *
	 * Called by the *admin_init* hook.
	 *
	 * @since 3.21.0
	 */
	public function admin_init() {

		// Register WordLift's analytics settings with our in class sanitizer.
		register_setting(
			'wl_analytics_settings',
			'wl_analytics_settings',
			array( $this, 'sanitize_callback' )
		);

		// Add the analytics settings setction.
		add_settings_section(
			'wl_analytics_settings_section',
			'',
			'',
			'wl_analytics_settings'
		);

		// Add a toggle to determine if analytics functions are enabled or not.
		// NOTE: this uses yes/no rather than true/false.
		add_settings_field(
			'wl-analytics-enabled',
			__( 'Enable Analytics', 'wordlift' ),
			array( $this->radio_input_element, 'render' ),
			'wl_analytics_settings',
			'wl_analytics_settings_section',
			array(
				'id'          => 'wl-analytics-enable',
				'name'        => 'wl_analytics_settings[' . Wordlift_Configuration_Service::ANALYTICS_ENABLE . ']',
				'value'       => Wordlift_Configuration_Service::get_instance()->is_analytics_enable() ? 'yes' : 'no',
				'description' => __( 'Toggle on/off the default values.', 'wordlift' ),
			)
		);

		/**
		 * A basic number field that will accept anything from 1 to 20.
		 *
		 * Represents the custom dim number for the uri.
		 */
		add_settings_field(
			'wl-analytics-entity-uri-dimension',
			__( 'Entity URI dimension', 'wordlift' ),
			array( $this->input_element, 'render' ),
			'wl_analytics_settings',
			'wl_analytics_settings_section',
			array(
				'id'          => 'wl-analytics-entity-uri-dimension',
				'name'        => 'wl_analytics_settings[' . Wordlift_Configuration_Service::ANALYTICS_ENTITY_URI_DIMENSION . ']',
				'type'        => 'number',
				'value'       => Wordlift_Configuration_Service::get_instance()->get_analytics_entity_uri_dimension(),
				'description' => __( 'Entity URI dimesion', 'wordlift' ),
			)
		);

		/**
		 * A basic number field that will accept anything from 1 to 20.
		 *
		 * Represents the custom dim number for the type.
		 */
		add_settings_field(
			'wl-analytics-entity-type-dimension',
			__( 'Entity Type dimension', 'wordlift' ),
			array( $this->input_element, 'render' ),
			'wl_analytics_settings',
			'wl_analytics_settings_section',
			array(
				'id'          => 'wl-analytics-entity-type-dimension',
				'name'        => 'wl_analytics_settings[' . Wordlift_Configuration_Service::ANALYTICS_ENTITY_TYPE_DIMENSION . ']',
				'type'        => 'number',
				'value'       => Wordlift_Configuration_Service::get_instance()->get_analytics_entity_type_dimension(),
				'description' => __( 'Entity Type dimension', 'wordlift' ),
			)
		);

	}

	/**
	 * Validates an entity uri based on an integer passed.
	 *
	 * TODO: Needs a feedback method to pass back error messages.
	 *
	 * @method validate_entity_uri
	 * @param string $uri a sting representing an entity ID that can be converted to a uri.
	 *
	 * @return int
	 * @since 3.21.0
	 */
	public function validate_entity_uri( $uri ) {
		// Basic validation is to ensure number is between 1 and 20.
		// NOTE: certain analytics accounts have a much higher value - as many
		// as 200 are allowed.
		if ( (int) $uri < 1 || (int) $uri > 20 ) {
			// if we are out of range then pass the default value.
			$uri = Wordlift_Configuration_Service::get_instance()->get_analytics_entity_uri_dimension();
		}

		return absint( $uri );
	}

	/**
	 * Validates an entity type.
	 *
	 * TODO: Needs a feedback method to pass back error messages.
	 *
	 * @method validate_entity_type
	 * @param string $type This is an entity type ID in string form - really a number.
	 *
	 * @return int
	 * @since  3.21.0
	 */
	public function validate_entity_type( $type ) {
		// Basic validation is to ensure number is between 1 and 20.
		// NOTE: certain analytics accounts have a much higher value - as many
		// as 200 are allowed.
		if ( (int) $type < 1 || (int) $type > 20 ) {
			// if we are out of range then pass the default value.
			$type = Wordlift_Configuration_Service::get_instance()->get_analytics_entity_type_dimension();
		}

		return absint( $type );
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
	 * @since 3.21.0
	 */
	public function sanitize_callback( $input ) {
		if ( ! check_admin_referer( 'wl_analytics_settings-options' ) ) {
			// Any failing nonce checks already die().
			return;
		}

		/**
		 * Validate and sanitize the $inputs and store them in $output saved.
		 */
		$output = array();
		if ( isset( $input['analytics_enable'] ) ) {
			$output['analytics_enable'] = ( 'yes' === $input['analytics_enable'] ) ? 'yes' : 'no';
		}
		if ( isset( $input['analytics_entity_uri_dimension'] ) ) {
			$output['analytics_entity_uri_dimension'] = (int) $this->validate_entity_uri( $input['analytics_entity_uri_dimension'] );
		}
		if ( isset( $input['analytics_entity_type_dimension'] ) ) {
			// This dimension cannot be the same as the one set above. If it is
			// then zero it out and it will fail validation.
			if ( isset( $output['analytics_entity_uri_dimension'] ) && $output['analytics_entity_uri_dimension'] === (int) $input['analytics_entity_type_dimension'] ) {
				$input['analytics_entity_type_dimension'] = 0;
			}
			$output['analytics_entity_type_dimension'] = (int) $this->validate_entity_type( $input['analytics_entity_type_dimension'] );
		}

		// return items added to the output for saving.
		return $output;
	}

}
