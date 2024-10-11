<?php
/**
 * Admin UI: Admin Entity Type Settings.
 *
 * The {@link Wordlift_Admin_Entity_Type_settings} class handles modifications
 * to the entity type list admin page.
 *
 * @link       https://wordlift.io
 *
 * @package    Wordlift
 * @subpackage Wordlift/admin
 * @since      3.11.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The Entity taxonomy list admin page controller.
 *
 * Methods to manipulate whatever is displayed on the admin list page
 * for the entity taxonomy.
 *
 * @since      3.11.0
 *
 * @package    Wordlift
 * @subpackage Wordlift/admin
 * @author     WordLift <hello@wordlift.io>
 */
class Wordlift_Admin_Entity_Type_Settings {

	/**
	 * A {@link Wordlift_Log_Service} instance.
	 *
	 * @since  3.14.0
	 * @access private
	 * @var \Wordlift_Log_Service $log A {@link Wordlift_Log_Service} instance.
	 */
	private $log;

	/**
	 * Create a {@link Wordlift_Admin_Entity_Type_Settings} instance.
	 *
	 * @since 3.14.0
	 */
	public function __construct() {

		$this->log = Wordlift_Log_Service::get_logger( 'Wordlift_Admin_Entity_Type_Settings' );

	}

	/**
	 * Handle menu registration.
	 *
	 * The registration is required, although we do not want to actually to add
	 * an item to the menu, in order to "whitelist" the access to the settings page in
	 * the admin.
	 *
	 * @since 3.11.0
	 */
	public function admin_menu() {

		/*
		 * Before anything else check if an settings form was submitted.
		 * This has to be done before any output happens in order to be able to
		 * display proper "die" error messages and redirect.
		 */
		if ( isset( $_GET['page'] ) && ( 'wl_entity_type_settings' === $_GET['page'] ) ) { //phpcs:ignore WordPress.Security.NonceVerification.Recommended

			// Validate inputs. Do not return on invalid parameters or capabilities.
			$this->validate_proper_term();

			// If proper form submission, handle it and redirect back to the settings page.
			if ( isset( $_POST['action'] ) && ( 'wl_edit_entity_type_term' === $_POST['action'] ) ) { //phpcs:ignore WordPress.Security.NonceVerification.Missing
				$this->handle_form_submission();
			}
			/**
			 * Filter: wl_feature__enable__notices.
			 *
			 * @param bool whether the notices needs to be enabled or not.
			 *
			 * @return bool
			 * @since 3.27.6
			 */
			if ( apply_filters( 'wl_feature__enable__notices', true ) ) {
				// Register admin notices handler.
				add_action( 'admin_notices', array( $this, 'admin_notice' ) );
			}
		}

		/*
		 * Use a null parent slug to prevent the menu from actually appearing
		 * in the admin menu.
		 */
		// @todo: use the new {@link Wordlift_Admin_Page}.
		add_submenu_page(
			'wl_entity_type_settings',
			__( 'Edit Entity term', 'wordlift' ),
			__( 'Edit Entity term', 'wordlift' ),
			'manage_options',
			'wl_entity_type_settings',
			array( $this, 'render' )
		);
	}

	/**
	 * Output admin notices if needed, based on the message url parameter.
	 * A value of 1 indicates that a successful save was done.
	 *
	 * @since 3.11.0
	 */
	public function admin_notice() {
		if ( isset( $_GET['message'] ) && ( '1' === $_GET['message'] ) ) { //phpcs:ignore WordPress.Security.NonceVerification.Recommended
			?>
			<div class="notice notice-success is-dismissible">
				<p><?php esc_html_e( 'Settings saved', 'wordlift' ); ?></p>
			</div>
			<?php
		}
	}

	/**
	 * Validate the existence of the entity type indicated by the tag_ID url
	 * parameter before doing any processing. Done before any output to mimic
	 * the way WordPress handles same situation with "normal" term editing screens.
	 *
	 * @since 3.11.0
	 */
	public function validate_proper_term() {

		// Validate capabilities.
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die(
				'<h1>' . esc_html__( 'Cheatin&#8217; uh?', 'default' ) . '</h1>' .
				'<p>' . esc_html__( 'Sorry, you are not allowed to edit this item.', 'default' ) . '</p>',
				403
			);
		}

		// Get the term id and the actual term.
		$term_id = isset( $_REQUEST['tag_ID'] ) ? (int) $_REQUEST['tag_ID'] : 0; //phpcs:ignore WordPress.Security.NonceVerification.Recommended

		if ( ! term_exists( $term_id, Wordlift_Entity_Type_Taxonomy_Service::TAXONOMY_NAME ) ) {
			wp_die( esc_html__( 'You attempted to edit an entity type term that doesn&#8217;t exist.', 'wordlift' ) );
		}

	}

	/**
	 * Handle the form submission of the settings form. On successful
	 * handling redirect tp the setting edit page.
	 *
	 * @since 3.11.0
	 */
	public function handle_form_submission() {

		$term_id = isset( $_POST['tag_ID'] ) ? (int) $_POST['tag_ID'] : 0;

		// Check the nonce.
		check_admin_referer( 'update-entity_type_term_' . $term_id );

		$term = get_term( $term_id, 'wl_entity_type' );

		$title       = isset( $_POST['title'] ) ? sanitize_text_field( wp_unslash( (string) $_POST['title'] ) ) : '';
		$description = isset( $_POST['description'] ) ? sanitize_text_field( wp_unslash( (string) $_POST['description'] ) ) : '';
		$this->set_setting(
			$term_id,
			trim( wp_unslash( $title ) ),
			wp_unslash( $description )
		);

		// Redirect back to the term settings page and indicate a save was done.
		$url = admin_url( "admin.php?page=wl_entity_type_settings&tag_ID=$term->term_id&message=1" );

		wp_safe_redirect( $url );
		exit();

	}

	/**
	 * Render the settings page for the term.
	 *
	 * Access and parameter validity is assumed to be done earlier.
	 *
	 * @since 3.11.0
	 */
	public function render() {

		// Set variables used by the partial
		$term_id = isset( $_REQUEST['tag_ID'] ) ? absint( $_REQUEST['tag_ID'] ) : 0;  // phpcs:ignore WordPress.Security.NonceVerification.Recommended
        // phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
		$settings = $this->get_setting( $term_id );

		include plugin_dir_path( __DIR__ ) . 'admin/partials/wordlift-admin-entity-type-settings.php';

	}

	/**
	 * Store the entity type term settings in the DB
	 *
	 * @param integer $term_id The ID of the entity type term
	 * @param string  $title The override for the terms title.
	 * @param string  $description The override for the terms description.
	 *
	 * @since 3.11.0
	 */
	public function set_setting( $term_id, $title, $description ) {

		$settings             = get_option( 'wl_entity_type_settings', array() );
		$settings[ $term_id ] = array(
			'title'       => $title,
			'description' => $description,
		);
		update_option( 'wl_entity_type_settings', $settings );

	}

	/**
	 * Retrieve the entity type term settings from the DB
	 *
	 * @param integer $term_id The ID of the entity type term
	 *
	 * @return    null|array {
	 *                null is returned when there are no settings otherwise
	 *                an array is returned with following fields
	 *
	 * @type    string    title    The overriding title for the term
	 * @type    string    description    The overriding description for the term
	 *            }
	 * @since 3.11.0
	 */
	public function get_setting( $term_id ) {

		$settings = get_option( 'wl_entity_type_settings', array() );

		if ( isset( $settings[ $term_id ] ) ) {
			return $settings[ $term_id ];
		}

		$this->log->warn( "No settings found for term id $term_id." );

		return null;
	}

}
