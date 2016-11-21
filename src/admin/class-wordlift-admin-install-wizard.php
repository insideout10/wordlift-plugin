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
 * @author     WordLift <hello@wordlift.it>
 */
class Wordlift_Admin_Install_Wizard {

	/**
	 * The current step.
	 *
	 * @since  3.9.0
	 * @access private
	 * @var int $step The current step.
	 */
	private $step;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    3.9.0
	 */
	public function __construct() {

		// Get the current step (or start by 1 if not set).
		$this->step = get_option( 'wl_general_settings_wizard', 1 );

		// Hook to some WP's events:
		//  - when WP is loaded check whether the user decided to skip the set-up, i.e. don't show us even if WL is not
		//    set up.
		add_action( 'wp_loaded', array( $this, 'hide_notices' ) );

		//  - hook to `admin_menu` in order to add our own setup wizard page.
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );

		//  - triggered when the user accesses the admin area, we decide whether to show our own wizard.
		add_action( 'admin_init', array( $this, 'show_page' ) );

		//  - hook to `admin_notices` to display our notices.
		add_action( 'admin_notices', array( $this, 'admin_notices' ) );

	}

	/**
	 * Generate an admin notice suggesting to start the wizard if there is no configuration.
	 *
	 * @since    3.9.0
	 */
	public function admin_notices() {

		// Use `wl_configuration_get_key` to check whether WL's key is set.
		if ( empty( wl_configuration_get_key() ) ) {
			?>
			<div id="wl-message" class="updated">
				<p><?php _e( '<strong>Welcome to WordLift</strong> &#8211; You&lsquo;re almost ready to start', 'wordlift' ); ?></p>
				<p class="submit"><a href="<?php echo esc_url( admin_url( 'admin.php?page=wl-setup' ) ); ?>"
				                     class="button-primary"><?php _e( 'Run the Setup Wizard', 'wordlift' ); ?></a> <a
						class="button-secondary skip"
						href="<?php echo esc_url( wp_nonce_url( add_query_arg( 'wl-hide-notice', 'install' ), 'wordlift_hide_notices_nonce', '_wl_notice_nonce' ) ); ?>"><?php _e( 'Skip Setup', 'wordlift' ); ?></a>
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

		if ( isset( $_GET['wl-hide-notice'] ) && isset( $_GET['_wl_notice_nonce'] ) ) {
			if ( ! wp_verify_nonce( $_GET['_wl_notice_nonce'], 'wordlift_hide_notices_nonce' ) ) {
				wp_die( __( 'Action failed. Please refresh the page and retry.', 'wordlift' ) );
			}

			if ( ! current_user_can( 'manage_options' ) ) {
				wp_die( __( 'Cheatin&#8217; huh?', 'wordlift' ) );
			}

			$o = get_option( 'wl_general_settings', false );
			if ( ! is_array( $o ) ) {
				$o = array();
			}
			$o['no_wizard'] = true;
			update_option( 'wl_general_settings', $o );
		}

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

		// first check if we are in the wizard page at all, if not do nothing
		if ( empty( $_GET['page'] ) || 'wl-setup' !== $_GET['page'] ) {
			return;
		}

		// check and sanitize the wizard step being requested
		$step = 'welcome';
		if ( ! empty( $_GET['step'] ) ) {
			$step = $_GET['step'];
			if ( ! in_array( $step, array( 'welcome', 'license', 'vocabulary', 'language', 'publisher', 'finish' ) ) ) {
				$step = 'welcome';
			}
		}

		$this->step = $step;

		$this->header();
		switch ( $step ) {
			case 'welcome' :
				$this->welcome_page();
				break;
			case 'license' :
				$this->license_page();
				break;
			case 'vocabulary' :
				$this->vocabulary_page();
				break;
			case 'language' :
				$this->language_page();
				break;
			case 'publisher' :
				$this->publisher_page();
				break;
			case 'finish' :
				$this->finish();
				break;
		}
		$this->footer();
		exit;
	}

	/**
	 * Output the html for the header of the page
	 *
	 * @since    3.9.0
	 */
	public function header() {

		// Enqueue styles (do we need wordlift-reloaded here?).
		wp_enqueue_style( 'wordlift-reloaded', plugin_dir_url( dirname( __FILE__ ) ) . 'css/wordlift-reloaded.min.css' );
		wp_enqueue_style( 'wordlift-admin-install-wizard', plugin_dir_url( dirname( __FILE__ ) ) . 'admin/css/wordlift-admin-install-wizard.css' );

		// Include the header.
		include plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/wordlift-admin-install-wizard-header.php';

	}

	/**
	 * Output the html for the footer of the page
	 *
	 * @since    3.9.0
	 */
	public function footer() {

		// Include the header.
		include plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/wordlift-admin-install-wizard-footer.php';

	}

	/**
	 * Output the html for the welcome page
	 *
	 * @since    3.9.0
	 */
	public function welcome_page() {

		// Include the welcome page.
		include plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/wordlift-admin-install-wizard-step-1.php';

	}

	/**
	 * Output the html for the license page
	 *
	 * @since    3.9.0
	 *
	 */
	public function license_page() {
		$key = '';
		if ( isset( $_COOKIE['wl_key'] ) ) {
			$key = $_COOKIE['wl_key'];
		}
		$valid = 'invalid';
		if ( $this->validate_key( $key ) ) {
			$valid = 'valid';
		}

		// Include the license page.
		include plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/wordlift-admin-install-wizard-step-2.php';

	}

	/**
	 * Output the html for the vocabulary page
	 *
	 * @since    3.9.0
	 *
	 */
	public function vocabulary_page() {
		$slug = '/' . __( 'vocabulary', 'wordlift' ) . '/';
		if ( isset( $_COOKIE['wl_slug'] ) ) {
			$slug = $_COOKIE['wl_slug'];
		}

		// Include the vocabulary page.
		include plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/wordlift-admin-install-wizard-step-3.php';

	}

	/**
	 * Output the html for the language page
	 *
	 * @since    3.9.0
	 *
	 */
	public function language_page() {

		$langs = array(
			''   => 'English',
			'zh' => '中文',
			'es' => 'Español',
			'ru' => 'Русский',
			'pt' => 'Português',
			'fr' => 'Français',
			'it' => 'Italiano',
			'nl' => 'Nederlands',
			'sv' => 'Svenska',
			'da' => 'Dansk',
			'tr' => 'Türkçe',
		);

		$locale = get_locale();
		$parts  = explode( '_', $locale );
		$lang   = $parts[0];

		if ( isset( $_COOKIE['wl_lang'] ) ) {
			$lang = $_COOKIE['wl_lang'];
		}

		if ( ! isset( $langs[ $lang ] ) ) {
			$lang = '';
		}

		// Include the language page.
		include plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/wordlift-admin-install-wizard-step-4.php';

	}

	/**
	 * Output the html for the publisher page
	 *
	 * @since    3.9.0
	 *
	 */
	public function publisher_page() {
		$type = 'personal';
		if ( isset( $_COOKIE['wl_type'] ) ) {
			$type = $_COOKIE['wl_type'];
		}

		$name = '';
		if ( isset( $_COOKIE['wl_name'] ) ) {
			$name = $_COOKIE['wl_name'];
		}

		$image_id = 0;
		if ( isset( $_COOKIE['wl_image_id'] ) ) {
			$image_id = $_COOKIE['wl_image_id'];
		}

		$image_url = 0;
		if ( isset( $_COOKIE['wl_image_url'] ) ) {
			$image_url = $_COOKIE['wl_image_url'];
		}

		// Include the publisher page.
		include plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/wordlift-admin-install-wizard-step-5.php';

	}

	/**
	 * Finish the wizard, store all settings from the cookie into the DB
	 *
	 * @since    3.9.0
	 *
	 */
	public function finish() {

		if ( isset( $_GET['_wl_finish_nonce'] ) ) {
			if ( ! wp_verify_nonce( $_GET['_wl_finish_nonce'], 'wordlift_finish_nonce' ) ) {
				wp_die( __( 'Action failed. Please refresh the page and retry.', 'wordlift' ) );
			}

			if ( ! current_user_can( 'manage_options' ) ) {
				wp_die( __( 'sorry, you do not have a permission to save the settings', 'wordlift' ) );
			}

			$option                        = array();
			$option['key']                 = $_COOKIE['wl_key'];
			$option['site_language']       = $_COOKIE['wl_lang'];
			$option['wl_entity_base_path'] = trim( $_COOKIE['wl_slug'], '\\' );

			// create an entity for the publisher
			$args    = array(
				'post_type'    => Wordlift_Entity_Service::TYPE_NAME,
				'post_title'   => $_COOKIE['wl_name'],
				'post_status'  => 'publish',
				'post_content' => '',
			);
			$post_id = wp_insert_post( $args );

			// set a thumbnail if a logo was selected
			if ( ! empty( (int) $_COOKIE['wl_image_id'] ) ) {
				set_post_thumbnail( $post_id, (int) $_COOKIE['wl_image_id'] );
			}

			$type_uri = 'http://schema.org/Person';
			if ( 'company' == $_COOKIE['wl_type'] ) {
				$type_uri = 'http://schema.org/Organization';
			}
			Wordlift_Entity_Type_Service::get_instance()->set( $post_id, $type_uri );

			$option['publisher_entity'] = $post_id;
			update_option( 'wl_general_settings', $option );

			flush_rewrite_rules(); // needed because of possible change to the entity base path
			wp_redirect( admin_url( 'admin.php?page=wl_configuration_admin_menu' ) );
			die();

		}
	}

	/**
	 * Checks if a key is valid by using an API to communicate with the wordlift server
	 *
	 * @since    3.9.0
	 *
	 * @param    string $key The key to validate
	 *
	 * @return    bool    truue if the key is valid, false otherwise
	 */
	public static function validate_key( $key ) {
		$valid = false;

		// Request the dataset URI as a way to validate the key
		$response = wp_remote_get( wl_configuration_get_accounts_by_key_dataset_uri( $key ), unserialize( WL_REDLINK_API_HTTP_OPTIONS ) );

		// If the response is valid, the key is valid
		if ( ! is_wp_error( $response ) && 200 === (int) $response['response']['code'] ) {
			$valid = true;
		}

		return $valid;
	}
}

/**
 * Handle the ajax request to validate the key.
 *
 * Request should pass the key as the "key" parameter in a post request
 * Output is a JSON object with a "valid" field which is a boolean indicating if the key is valid
 *
 * @since    3.9.0
 *
 */
function wl_ajax_validate_key() {

	$valid = false;
	if ( isset( $_POST['key'] ) ) {
		$valid = Wordlift_Admin_Install_Wizard::validate_key( $_POST['key'] );
	}

	wp_send_json( array( 'valid' => $valid ) );
}

add_action( 'wp_ajax_wl_validate_key', 'wl_ajax_validate_key' );
