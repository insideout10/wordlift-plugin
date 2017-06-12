<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://wordlift.io
 * @since      1.0.0
 *
 * @package    Wordlift
 * @subpackage Wordlift/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Wordlift
 * @subpackage Wordlift/admin
 * @author     WordLift <hello@wordlift.io>
 */
class Wordlift_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $plugin_name The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $version The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 *
	 * @param   string                           $plugin_name           The name of this plugin.
	 * @param   string                           $version               The version of this plugin.
	 * @param    \Wordlift_Configuration_Service $configuration_service The configuration service.
	 * @param    \Wordlift_Notice_Service        $notice_service        The notice service.
	 */
	public function __construct( $plugin_name, $version, $configuration_service, $notice_service ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;

		$dataset_uri = $configuration_service->get_dataset_uri();
		$key         = $configuration_service->get_key();

		if ( empty( $dataset_uri ) ) {
			$settings_page = Wordlift_Admin_Settings_Page::get_instance();
			if ( empty( $key ) ) {
				$error = sprintf( esc_html__( "WordLift's key isn't set, please open the %s to set WordLift's key.", 'wordlift' ), '<a href="' . $settings_page->get_url() . '">' . esc_html__( 'settings page', 'wordlift' ) . '</a>' );
			} else {
				$error = sprintf( esc_html__( "WordLift's dataset URI is not configured: please open the %s to set WordLift's key again.", 'wordlift' ), '<a href="' . $settings_page->get_url() . '">' . esc_html__( 'settings page', 'wordlift' ) . '</a>' );
			}
			$notice_service->add_error( $error );
		}

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Wordlift_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Wordlift_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/wordlift-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Wordlift_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Wordlift_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		// Enqueue the admin scripts.
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/wordlift-admin.bundle.js', array(
			'jquery',
			'underscore',
			'backbone',
		), $this->version, false );

		// Set the basic params.
		$params = array(
			// @todo scripts in admin should use wp.post.
			'ajax_url'   => admin_url( 'admin-ajax.php' ),
			// @todo remove specific actions from settings.
			'action'     => 'entity_by_title',
			'datasetUri' => Wordlift_Configuration_Service::get_instance()->get_dataset_uri(),
			'language'   => Wordlift_Configuration_Service::get_instance()->get_language_code(),
			'l10n'       => array(
				'You already published an entity with the same name' => __( 'You already published an entity with the same name: ', 'wordlift' ),
				'logo_selection_title'                               => __( 'WordLift Choose Logo', 'wordlift' ),
				'logo_selection_button'                              => array( 'text' => __( 'Choose Logo', 'wordlift' ) ),
			),
		);

		// Set post-related values if there's a current post.
		if ( null !== $post = $entity_being_edited = get_post() ) {

			$params['post_id']           = $entity_being_edited->ID;
			$params['entityBeingEdited'] = isset( $entity_being_edited->post_type ) && Wordlift_Entity_Service::TYPE_NAME == $entity_being_edited->post_type && is_numeric( get_the_ID() );
			// We add the `itemId` here to give a chance to the analysis to use it in order to tell WLS to exclude it
			// from the results, since we don't want the current entity to be discovered by the analysis.
			//
			// See https://github.com/insideout10/wordlift-plugin/issues/345
			$params['itemId'] = Wordlift_Entity_Service::get_instance()->get_uri( $entity_being_edited->ID );

		}

		// Finally output the params as `wlSettings` for JavaScript code.
		wp_localize_script( $this->plugin_name, 'wlSettings', $params );

	}

}
