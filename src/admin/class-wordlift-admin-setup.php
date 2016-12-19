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
	 * A {@link Wordlift_Configuration_Service} instance.
	 *
	 * @since  3.9.0
	 * @access private
	 * @var Wordlift_Configuration_Service A {@link Wordlift_Configuration_Service} instance.
	 */
	private $configuration_service;

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
	 * Initialize the class and set its properties.
	 *
	 * @since    3.9.0
	 *
	 * @param Wordlift_Configuration_Service  $configuration_service  A {@link Wordlift_Configuration_Service} instance.
	 * @param Wordlift_Key_Validation_Service $key_validation_service A {@link Wordlift_Key_Validation_Service} instance.
	 * @param Wordlift_Entity_Service         $entity_service         A {@link Wordlift_Entity_Service} instance.
	 */
	public function __construct( $configuration_service, $key_validation_service, $entity_service ) {

		// Set a reference to the configuration service.
		$this->configuration_service = $configuration_service;

		// Set a reference to the key validation service.
		$this->key_validation_service = $key_validation_service;

		// Set a reference to the entity service.
		$this->entity_service = $entity_service;

		// Hook to some WP's events:
		// When WP is loaded check whether the user decided to skip the set-up, i.e. don't show us even if WL is not set up.
		add_action( 'wp_loaded', array( $this, 'hide_notices' ) );

		// Hook to `admin_menu` in order to add our own setup wizard page.
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );

		// Triggered when the user accesses the admin area, we decide whether to show our own wizard.
		add_action( 'admin_init', array( $this, 'show_page' ) );

		// Hook to `admin_notices` to display our notices.
		add_action( 'admin_notices', array( $this, 'admin_notices' ) );

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
			if ( $this->configuration_service->is_skip_wizard() ) {
				return;
			}

			// If we're already on the page or the user doesn't have permissions, return.
			if ( ( ! empty( $_GET['page'] ) && in_array( $_GET['page'], array( 'wl-setup' ) ) ) || is_network_admin() || isset( $_GET['activate-multi'] ) || ! current_user_can( 'manage_options' ) ) {
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
		if ( '' === $this->configuration_service->get_key() && ! $this->configuration_service->is_skip_wizard() ) {
			?>
            <div id="wl-message" class="updated">
                <p><?php esc_html_e( 'Welcome to WordLift &#8211; You&lsquo;re almost ready to start', 'wordlift' ); ?></p>
                <p class="submit"><a href="<?php echo esc_url( admin_url( 'admin.php?page=wl-setup' ) ); ?>"
                                     class="button-primary"><?php esc_html_e( 'Run the Setup Wizard', 'wordlift' ); ?></a>
                    <a class="button-secondary skip"
                       href="<?php echo esc_url( wp_nonce_url( add_query_arg( 'wl-hide-notice', 'install' ), 'wordlift_hide_notices_nonce', '_wl_notice_nonce' ) ); ?>"><?php esc_html_e( 'Skip Setup', 'wordlift' ); ?></a>
                </p>
            </div>
			<?php
		}

	}

	/**
	 * Handle hiding the wizard notices by user request
	 *
	 * @since    3.9.0
	 */
	public function hide_notices() {

		// If it's not a `wl-hide-notice` or the nonce is not set, return.
		if ( ! isset( $_GET['wl-hide-notice'], $_GET['_wl_notice_nonce'] ) ) {
			return;
		}

		// If the nonce is invalid, return an error.
		if ( ! wp_verify_nonce( $_GET['_wl_notice_nonce'], 'wordlift_hide_notices_nonce' ) ) {
			wp_die( __( 'Action failed. Please refresh the page and retry.', 'wordlift' ) );
		}

		// If the user doesn't have the right privileges, return an error.
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( __( 'Cheatin&#8217; huh?', 'wordlift' ) );
		}

		// Store a flag telling to skip the wizard.
		$this->configuration_service->set_skip_wizard( TRUE );

	}

	/**
	 * Register the wizard page to be able to access it
	 *
	 * @since    3.9.0
	 */
	public function admin_menu() {

		// @todo: find another way to do this, since this is adding an empty space in WP's dashboard menu.
		add_dashboard_page( '', '', 'manage_options', 'wl-setup', '' );

	}

	/**
	 * Displays the wizard page
	 *
	 * @since    3.9.0
	 */
	public function show_page() {

		// First check if we are in the wizard page at all, if not do nothing.
		if ( empty( $_GET['page'] ) || 'wl-setup' !== $_GET['page'] ) {
			return;
		}

		// If it's a POST and the `wl-save-configuration` action is set, save the configuration.
		if ( isset( $_POST['action'] ) && 'wl-save-configuration' === $_POST['action'] ) {

			// Check the nonce and the user capabilities.
			check_admin_referer( 'wl-save-configuration' );

			// Check if the user has the right privileges.
			if ( ! current_user_can( 'manage_options' ) ) {
				wp_die( __( 'Sorry, you do not have a permission to save the settings', 'wordlift' ) );
			}

			// Save the configuration.
			$this->save_configuration( $_POST );

			// Redirect to the admin's page.
			wp_redirect( admin_url() );

			exit;
		}

		include plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/wordlift-admin-setup.php';

		exit;
	}

	/**
	 * Save WordLift's configuration using the provided parameters.
	 *
	 * @since 3.9.0
	 *
	 * @param array $params An array of configuration parameters.
	 */
	private function save_configuration( $params ) {

		// We have the following parameters:
		// `key`, holding WL's key,
		// `vocabulary`, holding the vocabulary path,
		// `language`, with the language code (e.g. `en`),
		// `user_type`, the user type either `personal` or `company`,
		// `name`, with the `personal` or `company`'s name,
		// `logo`, the attachment id for the `personal` or `company` entity.

		// Store the key:
		$this->configuration_service->set_key( $params['key'] );

		// Store the vocabulary path:
		$this->configuration_service->set_entity_base_path( $params['vocabulary'] );

		// Store the site's language:
		$this->configuration_service->set_language_code( $params['language'] );

		// Set the type URI, either http://schema.org/Person or http://schema.org/Organization.
		$type_uri = sprintf( 'http://schema.org/%s', 'organization' === $params['user_type'] ? 'Organization' : 'Person' );

		// Create an entity for the publisher.
		$publisher_post_id = $this->entity_service->create( $params['name'], $type_uri, $params['logo'], 'publish' );

		// Store the publisher entity post id in the configuration.
		$this->configuration_service->set_publisher_id( $publisher_post_id );

		flush_rewrite_rules(); // Needed because of possible change to the entity base path.

	}

//	/**
//	 * Output the html for the header of the page.
//	 *
//	 * @since    3.9.0
//	 *
//	 * @param int $step The current step.
//	 */
//	public function header( $step ) {
//
//		// Enqueue styles (do we need wordlift-reloaded here?).
//		wp_enqueue_style( 'wordlift - reloaded', plugin_dir_url( dirname( __FILE__ ) ) . 'css / wordlift - reloaded . min . css' );
//		wp_enqueue_style( 'wordlift - admin - install - wizard', plugin_dir_url( dirname( __FILE__ ) ) . 'admin / css / wordlift - admin - install - wizard . css' );
//
//		wp_enqueue_script( 'wordlift - admin - install - wizard', plugin_dir_url( dirname( __FILE__ ) ) . 'admin / js / wordlift - admin - install - wizard . js' );
//		wp_localize_script( 'wordlift - admin - install - wizard', '_wlAdminSetup', array(
//			'ajaxUrl' => parse_url( self_admin_url( 'admin - ajax . php' ), PHP_URL_PATH ),
//			'action'  => 'wl_validate_key',
//			'media'   => array(
//				'title'  => __( 'WordLift Choose Logo', 'wordlift' ),
//				'button' => array( 'text' => __( 'Choose Logo', 'wordlift' ) ),
//			),
//		) );
//
//		// Include the header.
//		include plugin_dir_path( dirname( __FILE__ ) ) . 'admin / partials / wordlift - admin - install - wizard - header . php';
//
//	}
//
//	/**
//	 * Output the html for the footer of the page.
//	 *
//	 * @since    3.9.0
//	 */
//	public function footer() {
//
//		// Include the header.
//		include plugin_dir_path( dirname( __FILE__ ) ) . 'admin / partials / wordlift - admin - install - wizard - footer . php';
//
//	}
//
//	/**
//	 * Output the html for the welcome page.
//	 *
//	 * @since    3.9.0
//	 */
//	public function welcome_page() {
//
//		// Include the welcome page.
//		include plugin_dir_path( dirname( __FILE__ ) ) . 'admin / partials / wordlift - admin - install - wizard - step - 1.php';
//
//	}
//
//	/**
//	 * Output the html for the license page.
//	 *
//	 * @since    3.9.0
//	 *
//	 */
//	public function license_page() {
//		$key = '';
//		if ( isset( $_COOKIE['wl_key'] ) ) {
//			$key = $_COOKIE['wl_key'];
//		}
//
//		$valid = $this->key_validation_service->is_valid( $key ) ? 'valid' : 'invalid';
//
//		// Include the license page.
//		include plugin_dir_path( dirname( __FILE__ ) ) . 'admin / partials / wordlift - admin - install - wizard - step - 2.php';
//
//	}
//
//	/**
//	 * Output the html for the vocabulary page.
//	 *
//	 * @since    3.9.0
//	 *
//	 */
//	public function vocabulary_page() {
//
//		$slug = ' / ' . __( 'vocabulary', 'wordlift' ) . ' / ';
//		if ( isset( $_COOKIE['wl_slug'] ) ) {
//			$slug = $_COOKIE['wl_slug'];
//		}
//
//		// Include the vocabulary page.
//		include plugin_dir_path( dirname( __FILE__ ) ) . 'admin / partials / wordlift - admin - install - wizard - step - 3.php';
//
//	}
//
//	/**
//	 * Output the html for the language page.
//	 *
//	 * @since    3.9.0
//	 *
//	 */
//	public function language_page() {
//
//		$langs = Wordlift_Languages::get_languages();
//
//		$locale = get_locale();
//		$parts  = explode( '_', $locale );
//		$lang   = $parts[0];
//
//		if ( isset( $_COOKIE['wl_lang'] ) ) {
//			$lang = $_COOKIE['wl_lang'];
//		}
//
//		if ( ! isset( $langs[ $lang ] ) ) {
//			$lang = 'en'; // Use english by default.
//		}
//
//		// Include the language page.
//		include plugin_dir_path( dirname( __FILE__ ) ) . 'admin / partials / wordlift - admin - install - wizard - step - 4.php';
//
//	}
//
//	/**
//	 * Output the html for the publisher page.
//	 *
//	 * @since    3.9.0
//	 *
//	 */
//	public function publisher_page() {
//
//		$type = 'personal';
//		if ( isset( $_COOKIE['wl_type'] ) ) {
//			$type = $_COOKIE['wl_type'];
//		}
//
//		$name = '';
//		if ( isset( $_COOKIE['wl_name'] ) ) {
//			$name = $_COOKIE['wl_name'];
//		}
//
//		$image_id = 0;
//		if ( isset( $_COOKIE['wl_image_id'] ) ) {
//			$image_id = $_COOKIE['wl_image_id'];
//		}
//
//		$image_url = 0;
//		if ( isset( $_COOKIE['wl_image_url'] ) ) {
//			$image_url = $_COOKIE['wl_image_url'];
//		}
//
//		// Include the publisher page.
//		include plugin_dir_path( dirname( __FILE__ ) ) . 'admin / partials / wordlift - admin - install - wizard - step - 5.php';
//
//	}

//	/**
//	 * Finish the wizard, store all settings from the cookie into the DB.
//	 *
//	 * @since    3.9.0
//	 *
//	 */
//	public function finish() {
//
//		// If the nonce isn't set, exit .
//		if ( ! isset( $_GET['_wl_finish_nonce'] ) ) {
//			return;
//		}
//
//		// Check the nonce.
//		if ( ! wp_verify_nonce( $_GET['_wl_finish_nonce'], 'wordlift_finish_nonce' ) ) {
//			wp_die( __( 'Action failed. Please refresh the page and retry.', 'wordlift' ) );
//		}
//
//		// Check if the user has the right privileges.
//		if ( ! current_user_can( 'manage_options' ) ) {
//			wp_die( __( 'Sorry, you do not have a permission to save the settings', 'wordlift' ) );
//		}
//
//		// Update the configuration, set WordLift's key,
//		$this->configuration_service->set_key( $_COOKIE['wl_key'] );
//
//		// Set WordLift's language code.
//		$this->configuration_service->set_language_code( $_COOKIE['wl_lang'] );
//
//		// Set the entity base path.
//		// @todo should we use stripslashes here? (and move it to the configuration service).
//		$this->configuration_service->set_entity_base_path( trim( $_COOKIE['wl_slug'], '\\' ) );
//
//		// Create an entity for the publisher.
//		$post_id = wp_insert_post( array(
//			'post_type'    => Wordlift_Entity_Service::TYPE_NAME,
//			'post_title'   => $_COOKIE['wl_name'],
//			'post_status'  => 'publish',
//			'post_content' => '',
//		) );
//
//		// Set a thumbnail if a logo was selected.
//		if ( ! empty( $_COOKIE['wl_image_id'] ) && 0 < $image_id = absint( $_COOKIE['wl_image_id'] ) ) {
//			set_post_thumbnail( $post_id, $image_id );
//		}
//
//		// Set the type URI, either http://schema.org/Person or http://schema.org/Organization.
//		$type_uri = sprintf( 'http://schema.org/%s', 'company' === $_COOKIE['wl_type'] ? 'Organization' : 'Person' );
//
//		// Set the entity type.
//		Wordlift_Entity_Type_Service::get_instance()->set( $post_id, $type_uri );
//
//		// Store the publisher entity post id in the configuration.
//		$this->configuration_service->set_publisher_id( $post_id );
//
//		flush_rewrite_rules(); // Needed because of possible change to the entity base path.
//		wp_redirect( admin_url( 'admin.php?page=wl_configuration_admin_menu' ) );
//		exit();
//
//	}

}
