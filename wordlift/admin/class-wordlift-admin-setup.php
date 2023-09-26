<?php
/**
 * Admin UI: Wordlift_Admin_Install_Wizard
 *
 * The {@link Wordlift_Admin_Install_Wizard} class handles WL's installation wizard by checking whether WL is configured
 * and, if not, displays a notice with a link to the configuration wizard.
 *
 * @link       https://wordlift.io
 *
 * @package    Wordlift
 * @subpackage Wordlift/admin
 * @since      3.9.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The install wizard.
 *
 * Methods to track and implement the various steps of the install wizard
 * which is triggered on dirst install of the plugin (when there are no settings)
 *
 * @package    Wordlift
 * @subpackage Wordlift/admin
 * @author     WordLift <hello@wordlift.io>
 */
class Wordlift_Admin_Setup {

	/**
	 * A {@link Wordlift_Key_Validation_Service} instance.
	 *
	 * @since  3.9.0
	 * @access private
	 * @var Wordlift_Key_Validation_Service A {@link Wordlift_Key_Validation_Service} instance.
	 */
	private $key_validation_service;

	/**
	 * A {@link Wordlift_Entity_Service} instance.
	 *
	 * @since  3.9.0
	 * @access private
	 * @var Wordlift_Entity_Service $entity_service A {@link Wordlift_Entity_Service} instance.
	 */
	private $entity_service;

	/**
	 * A {@link Wordlift_Admin_Language_Select_Element} element renderer.
	 *
	 * @since  3.20.0
	 * @access private
	 * @var \Wordlift_Admin_Language_Select_Element $language_select_element A {@link Wordlift_Admin_Language_Select_Element} element renderer.
	 */
	private $language_select_element;

	/**
	 * A {@link Wordlift_Admin_Country_Select_Element} element renderer.
	 *
	 * @since  3.20.0
	 * @access private
	 * @var \Wordlift_Admin_Country_Select_Element $country_select_element A {@link Wordlift_Admin_Country_Select_Element} element renderer.
	 */
	private $country_select_element;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @param \Wordlift_Key_Validation_Service        $key_validation_service A {@link Wordlift_Key_Validation_Service} instance.
	 * @param \Wordlift_Entity_Service                $entity_service A {@link Wordlift_Entity_Service} instance.
	 * @param \Wordlift_Admin_Language_Select_Element $language_select_element A {@link Wordlift_Admin_Language_Select_Element} element renderer.
	 * @param \Wordlift_Admin_Country_Select_Element  $country_select_element A {@link Wordlift_Admin_Country_Select_Element} element renderer.
	 *
	 * @since    3.9.0
	 */
	public function __construct( $key_validation_service, $entity_service, $language_select_element, $country_select_element ) {

		// Set a reference to the key validation service.
		$this->key_validation_service = $key_validation_service;

		// Set a reference to the entity service.
		$this->entity_service = $entity_service;

		// Set a reference to the UI elements language and country.
		$this->language_select_element = $language_select_element;
		$this->country_select_element  = $country_select_element;

		// Hook to some WP's events:
		// When WP is loaded check whether the user decided to skip the set-up, i.e. don't show us even if WL is not set up.
		add_action( 'wp_loaded', array( $this, 'hide_notices' ) );

		// Hook to `admin_menu` in order to add our own setup wizard page.
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );

		// Triggered when the user accesses the admin area, we decide whether to show our own wizard.
		add_action( 'admin_init', array( $this, 'show_page' ) );

		/**
		 * Filter: wl_feature__enable__notices.
		 *
		 * @param bool whether the notices needs to be enabled or not.
		 *
		 * @return bool
		 * @since 3.27.6
		 */
		if ( apply_filters( 'wl_feature__enable__notices', true ) ) {
			// Hook to `admin_notices` to display our notices.
			add_action( 'admin_notices', array( $this, 'admin_notices' ) );
		}
	}

	/**
	 * Hook to `admin_init` and redirect to WordLift's setup page if the `_wl_activation_redirect` transient flag is set.
	 *
	 * @since 3.9.0
	 */
	public function admin_init() {

		// If the `_wl_activation_redirect` is set, the redirect to the setup page.
		if ( get_transient( '_wl_activation_redirect' ) ) {
			delete_transient( '_wl_activation_redirect' );

			// If the user asked to skip the wizard then comply.
			if ( Wordlift_Configuration_Service::get_instance()->is_skip_wizard() ) {
				return;
			}

			// If we're already on the page or the user doesn't have permissions, return.
			if ( ( ! empty( $_GET['page'] ) && 'wl-setup' === $_GET['page'] ) || is_network_admin() || isset( $_GET['activate-multi'] ) || ! current_user_can( 'manage_options' ) ) { //phpcs:ignore WordPress.Security.NonceVerification.Recommended
				return;
			}

			// Finally redirect to the setup page.
			wp_safe_redirect( admin_url( 'index.php?page=wl-setup' ) );

			exit;
		}

	}

	/**
	 * Generate an admin notice suggesting to start the wizard if there is no configuration.
	 *
	 * @since    3.9.0
	 */
	public function admin_notices() {

		// Use `wl_configuration_get_key` to check whether WL's key is set and that the user didn't disable the wizard.
		if ( '' === Wordlift_Configuration_Service::get_instance()->get_key() && ! Wordlift_Configuration_Service::get_instance()->is_skip_wizard() ) { ?>
			<div id="wl-message" class="updated">
				<p><?php esc_html_e( 'Welcome to WordLift &#8211; You&lsquo;re almost ready to start', 'wordlift' ); ?></p>
				<p class="submit">
					<a href="<?php echo esc_url( admin_url( 'admin.php?page=wl-setup' ) ); ?>"
					   class="button-primary"><?php esc_html_e( 'Run the Setup Wizard', 'wordlift' ); ?></a>
					<a class="button-secondary skip"
					   href="<?php echo esc_url( wp_nonce_url( add_query_arg( 'wl-hide-notice', 'install' ), 'wordlift_hide_notices_nonce', '_wl_notice_nonce' ) ); ?>"><?php esc_html_e( 'Skip Setup', 'wordlift' ); ?></a>
				</p>
			</div>
			<?php
		}

	}

	/**
	 * Handle hiding the wizard notices by user request.
	 *
	 * @since    3.9.0
	 */
	public function hide_notices() {

		// If it's not a `wl-hide-notice` or the nonce is not set, return.
		if ( ! isset( $_GET['wl-hide-notice'], $_GET['_wl_notice_nonce'] ) ) {
			return;
		}

		// If the nonce is invalid, return an error.
		if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_wl_notice_nonce'] ) ), 'wordlift_hide_notices_nonce' ) ) {
			wp_die( esc_html__( 'Action failed. Please refresh the page and retry.', 'wordlift' ) );
		}

		// If the user doesn't have the right privileges, return an error.
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'Cheatin&#8217; huh?', 'wordlift' ) );
		}

		// Store a flag telling to skip the wizard.
		Wordlift_Configuration_Service::get_instance()->set_skip_wizard( true );

	}

	/**
	 * Register the wizard page to be able to access it.
	 *
	 * @since    3.9.0
	 */
	public function admin_menu() {
		/**
		 *
		 * @see https://github.com/insideout10/wordlift-plugin/issues/1209
		 * @since 3.27.7
		 */
		// @todo: find another way to do this, since this is adding an empty space in WP's dashboard menu.
		add_dashboard_page( '', '', 'manage_options', 'wl-setup', '' );
	}

	/**
	 * Displays the wizard page.
	 *
	 * @since    3.9.0
	 */
	public function show_page() {

		// First check if we are in the wizard page at all, if not do nothing.
		if ( empty( $_GET['page'] ) || 'wl-setup' !== $_GET['page'] ) {
			return;
		}

		// Allow 3rd parties to provide alternative setups, this is useful for whitelabel setups.
		do_action( 'wl_admin_setup__pre' );

		// If it's a POST and the `wl-save-configuration` action is set, save the configuration.
		if ( isset( $_POST['action'] ) && 'wl-save-configuration' === $_POST['action'] ) {

			// Check the nonce and the user capabilities.
			check_admin_referer( 'wl-save-configuration' );

			// Check if the user has the right privileges.
			if ( ! current_user_can( 'manage_options' ) ) {
				wp_die( esc_html__( 'Sorry, you do not have a permission to save the settings', 'wordlift' ) );
			}

			// Save the configuration.
			$this->save_configuration( $_POST );

			// Redirect to the admin's page.
			wp_safe_redirect( admin_url() );
			exit();
		}

		// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
		$language_select = $this->language_select_element;
		// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
		$country_select = $this->country_select_element;

		include plugin_dir_path( __DIR__ ) . 'admin/partials/wordlift-admin-setup.php';

		exit;
	}

	/**
	 * Save WordLift's configuration using the provided parameters.
	 *
	 * @param array $params An array of configuration parameters.
	 *
	 * @since 3.9.0
	 */
	public function save_configuration( $params ) {

		// We have the following parameters:
		// `key`, holding WL's key,
		// `vocabulary`, holding the vocabulary path,
		// `wl-country-code` with the country code (e.g. `us`),
		// `share-diagnostic`, the user preferences about sharing data with us.
		// `user_type`, the user type either `personal` or `company`,
		// `name`, with the `personal` or `company`'s name,
		// `logo`, the attachment id for the `personal` or `company` entity.

		// Store the key:
		Wordlift_Configuration_Service::get_instance()->set_key( $params['key'] );

		// Store the vocabulary path:
		Wordlift_Configuration_Service::get_instance()->set_entity_base_path( $params['vocabulary'] );

		// Store the site's country:
		Wordlift_Configuration_Service::get_instance()->set_country_code( $params['wl-country-code'] );

		// Store the preferences in variable, because if the checkbox is not checked
		// the `share-diagnostic` will not exists in `$params` array.
		$share_diagnostic_preferences = empty( $params['share-diagnostic'] ) ? 'no' : 'yes';

		// Store the diagnostic preferences:
		Wordlift_Configuration_Service::get_instance()->set_diagnostic_preferences( $share_diagnostic_preferences );

		// Set the type URI, either http://schema.org/Person or http://schema.org/Organization.
		$type_uri = sprintf( 'http://schema.org/%s', 'organization' === $params['user_type'] ? 'Organization' : 'Person' );

		// Create an entity for the publisher.
		$publisher_post_id = $this->entity_service->create( $params['name'], $type_uri, $params['logo'], 'publish' );

		// Store the publisher entity post id in the configuration.
		Wordlift_Configuration_Service::get_instance()->set_publisher_id( $publisher_post_id );

		flush_rewrite_rules(); // Needed because of possible change to the entity base path.

	}

}
