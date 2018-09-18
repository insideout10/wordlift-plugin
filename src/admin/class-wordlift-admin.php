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
	 * The {@link Wordlift_Configuration_Service} instance.
	 *
	 * @since  3.14.0
	 * @access private
	 * @var \Wordlift_Configuration_Service $configuration_service The {@link Wordlift_Configuration_Service} instance.
	 */
	private $configuration_service;

	/**
	 * The {@link Wordlift_User_Service} instance.
	 *
	 * @since  3.14.0
	 * @access private
	 * @var \Wordlift_User_Service $user_service The {@link Wordlift_User_Service} instance.
	 */
	private $user_service;

	/**
	 * The singleton instance.
	 *
	 * @since 3.19.4
	 * @access private
	 * @var Wordlift_Admin $instance The singleton instance.
	 */
	private static $instance;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since  1.0.0
	 *
	 * @param string                          $plugin_name The name of this plugin.
	 * @param string                          $version The version of this plugin.
	 * @param \Wordlift_Configuration_Service $configuration_service The configuration service.
	 * @param \Wordlift_Notice_Service        $notice_service The notice service.
	 * @param \Wordlift_User_Service          $user_service The {@link Wordlift_User_Service} instance.
	 */
	public function __construct( $plugin_name, $version, $configuration_service, $notice_service, $user_service ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;

		$this->configuration_service = $configuration_service;
		$this->user_service          = $user_service;

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

		// Load additional code if we're in the admin UI.
		if ( is_admin() ) {
			require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-wordlift-admin-dashboard-latest-news.php';

			new Wordlift_Dashboard_Latest_News();

			/*
			 * Add support for `All Entity Types`.
			 * @see https://github.com/insideout10/wordlift-plugin/issues/835
			 */
			if ( WL_ALL_ENTITY_TYPES ) {
				require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-wordlift-admin-schemaorg-taxonomy-metabox.php';
				require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-wordlift-admin-schemaorg-property-metabox.php';

				new Wordlift_Admin_Schemaorg_Property_Metabox( Wordlift_Schemaorg_Property_Service::get_instance() );
			}

		}

		// Set the singleton instance.
		self::$instance = $this;

	}

	/**
	 * Get the singleton instance.
	 *
	 * @since 3.19.4
	 *
	 * @return \Wordlift_Admin The singleton instance.
	 */
	public static function get_instance() {

		return self::$instance;
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
		 * between the defined Wordlift_Schemaorg_Property_Servicehooks and the functions defined in this
		 * class.
		 */

		// Enqueue the admin scripts.
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/1/admin.js', array(
			'jquery',
			'underscore',
			'backbone',
		), $this->version, false );


		$can_edit_wordlift_entities = current_user_can( 'edit_wordlift_entities' );

		/*
		 * People that can create entities will see the scope set in the wp-config.php file (by default `cloud`). People
		 * that cannot edit create entities will always see the local entities.
		 *
		 * @see https://github.com/insideout10/wordlift-plugin/issues/839
		 */
		$autocomplete_scope = $can_edit_wordlift_entities ? WL_AUTOCOMPLETE_SCOPE : "local";

		// Set the basic params.
		$params = array(
			// @todo scripts in admin should use wp.post.
			'ajax_url'              => admin_url( 'admin-ajax.php' ),
			// @todo remove specific actions from settings.
			'action'                => 'entity_by_title',
			'datasetUri'            => $this->configuration_service->get_dataset_uri(),
			'language'              => $this->configuration_service->get_language_code(),
			'link_by_default'       => $this->configuration_service->is_link_by_default(),
			// Whether the current user is allowed to create new entities.
			//
			// @see https://github.com/insideout10/wordlift-plugin/issues/561
			'can_create_entities'   => $can_edit_wordlift_entities ? 'yes' : 'no',
			'l10n'                  => array(
				'You already published an entity with the same name'                 => __( 'You already published an entity with the same name: ', 'wordlift' ),
				'logo_selection_title'                                               => __( 'WordLift Choose Logo', 'wordlift' ),
				'logo_selection_button'                                              => array( 'text' => __( 'Choose Logo', 'wordlift' ) ),
				'Type at least 3 characters to search...'                            => _x( 'Type at least 3 characters to search...', 'Autocomplete Select', 'wordlift' ),
				'No results found for your search.'                                  => _x( 'No results found: try changing or removing some words.', 'Autocomplete Select', 'wordlift' ),
				'Please wait while we look for entities in the linked data cloud...' => _x( 'Please wait while we look for entities in the linked data cloud...', 'Autocomplete Select', 'wordlift' ),
			),
			'wl_autocomplete_nonce' => wp_create_nonce( 'wordlift_autocomplete' ),
			'autocomplete_scope'    => $autocomplete_scope,
			/**
			 * Allow 3rd parties to define the default editor id. This turns useful if 3rd parties load
			 * or change the TinyMCE id.
			 *
			 * The editor id is currently referenced by `src/coffee/editpost-widget/app.services.EditorAdapter.coffee`.
			 *
			 * @since 3.19.4
			 *
			 * @see https://github.com/insideout10/wordlift-plugin/issues/848
			 *
			 * @param string $editor The default editor id, by default `content`.
			 */
			'default_editor_id'     => apply_filters( 'wl_default_editor_id', 'content' ),
		);

		// Set post-related values if there's a current post.
		if ( null !== $post = $entity_being_edited = get_post() ) {

			$params['post_id']           = $entity_being_edited->ID;
			$entity_service              = Wordlift_Entity_Service::get_instance();
			$params['entityBeingEdited'] = isset( $entity_being_edited->post_type ) && $entity_service->is_entity( $post->ID ) && is_numeric( get_the_ID() );
			// We add the `itemId` here to give a chance to the analysis to use it in order to tell WLS to exclude it
			// from the results, since we don't want the current entity to be discovered by the analysis.
			//
			// See https://github.com/insideout10/wordlift-plugin/issues/345
			$params['itemId']                      = $entity_service->get_uri( $entity_being_edited->ID );
			$params['wl_schemaorg_property_nonce'] = wp_create_nonce( 'wl_schemaorg_property' );

			/*
			 * Add the `properties` if `WL_ALL_ENTITY_TYPES` is enabled.
			 *
			 * @see https://github.com/insideout10/wordlift-plugin/issues/835
			 */
			if ( WL_ALL_ENTITY_TYPES ) {
				$params['properties'] = Wordlift_Schemaorg_Property_Service::get_instance()->get_all( $post->ID );
			}

		}

		// Finally output the params as `wlSettings` for JavaScript code.
		wp_localize_script( $this->plugin_name, 'wlSettings', $params );

	}

}
