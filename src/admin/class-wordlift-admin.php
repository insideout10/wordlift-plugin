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

use Wordlift\Mappings\Acf_Mappings;

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
	 * The {@link Wordlift_Batch_Operation_Ajax_Adapter} instance.
	 *
	 * @since 3.20.0
	 * @access private
	 * @var \Wordlift_Batch_Operation_Ajax_Adapter $sync_batch_operation_ajax_adapter The {@link Wordlift_Batch_Operation_Ajax_Adapter} instance.
	 */
	private $sync_batch_operation_ajax_adapter;

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
	 * @param string $plugin_name The name of this plugin.
	 * @param string $version The version of this plugin.
	 * @param \Wordlift_Configuration_Service $configuration_service The configuration service.
	 * @param \Wordlift_Notice_Service $notice_service The notice service.
	 * @param \Wordlift_User_Service $user_service The {@link Wordlift_User_Service} instance.
	 *
	 * @since  1.0.0
	 *
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

			// Require the PHP files for the next code fragment.
			self::require_files();

			// Add Wordlift custom block category.
			self::add_block_category();

			new Wordlift_Dashboard_Latest_News();

			// Search Rankings.
			$search_rankings_service = new Wordlift_Admin_Search_Rankings_Service( Wordlift_Api_Service::get_instance() );
			new Wordlift_Admin_Search_Rankings_Ajax_Adapter( $search_rankings_service );

			/*
			 * Add support for `All Entity Types`.
			 *
			 * @since 3.20.0
			 *
			 * @see https://github.com/insideout10/wordlift-plugin/issues/835
			 */
			if ( WL_ALL_ENTITY_TYPES ) {
				require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-wordlift-admin-schemaorg-taxonomy-metabox.php';
				require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-wordlift-admin-schemaorg-property-metabox.php';

				// new Wordlift_Admin_Schemaorg_Property_Metabox( Wordlift_Schemaorg_Property_Service::get_instance() );
				/*
				 * The `Mappings` admin page.
				 */
				require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-wordlift-admin-mappings-page.php';
				new Wordlift_Admin_Mappings_Page();

				/*
				 * Allow sync'ing the schema.org taxonomy with the schema.org json file.
				 *
				 * @since 3.20.0
				 */
				require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/schemaorg/class-wordlift-schemaorg-sync-batch-operation.php';

				$this->sync_batch_operation_ajax_adapter = new Wordlift_Batch_Operation_Ajax_Adapter( new Wordlift_Schemaorg_Sync_Batch_Operation(), 'wl_schemaorg_sync' );

			}

			/*
			 * Add the {@link Wordlift_Admin_Term_Adapter}.
			 *
			 * @since 3.20.0
			 */
			require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-wordlift-admin-term-adapter.php';
			new Wordlift_Admin_Term_Adapter();

			/*
			 * The new dashboard.
			 *
			 * @since 3.20.0
			 *
			 * @see https://github.com/insideout10/wordlift-plugin/issues/879
			 */
			new Wordlift_Admin_Dashboard_V2(
				$search_rankings_service,
				Wordlift::get_instance()->get_dashboard_service(),
				Wordlift_Entity_Service::get_instance()
			);
			new Wordlift_Admin_Not_Enriched_Filter();

		}

		// @@todo only load this class if ACF is available.
		// Add support for ACF mappings, so that the admin edit mappings page can pick up ACF support when ACF is available.
		new Acf_Mappings();

		// Add the Admin Edit Mappings page.
		new Wordlift_Admin_Edit_Mappings_Page();

		// Set the singleton instance.
		self::$instance = $this;

	}

	/**
	 * Get the singleton instance.
	 *
	 * @return \Wordlift_Admin The singleton instance.
	 * @since 3.19.4
	 *
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

		/*
		 * Do not load our scripts on the Filter Urls plugin admin pages.
		 *
		 * @see https://github.com/insideout10/wordlift-plugin/issues/901
		 * @since 3.20.0
		 */
		$screen = get_current_screen();
		if ( is_a( $screen, 'WP_Screen' ) && 'filter-urls_page_filter_urls_form' === $screen->id ) {
			return;
		}

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
			'ajax_url'                   => admin_url( 'admin-ajax.php' ),
			// @todo remove specific actions from settings.
			'action'                     => 'entity_by_title',
			'datasetUri'                 => $this->configuration_service->get_dataset_uri(),
			'language'                   => $this->configuration_service->get_language_code(),
			'link_by_default'            => $this->configuration_service->is_link_by_default(),
			// Whether the current user is allowed to create new entities.
			//
			// @see https://github.com/insideout10/wordlift-plugin/issues/561
			'can_create_entities'        => $can_edit_wordlift_entities ? 'yes' : 'no',
			'l10n'                       => array(
				'You already published an entity with the same name'                 => __( 'You already published an entity with the same name: ', 'wordlift' ),
				'logo_selection_title'                                               => __( 'WordLift Choose Logo', 'wordlift' ),
				'logo_selection_button'                                              => array( 'text' => __( 'Choose Logo', 'wordlift' ) ),
				'Type at least 3 characters to search...'                            => _x( 'Type at least 3 characters to search...', 'Autocomplete Select', 'wordlift' ),
				'No results found for your search.'                                  => _x( 'No results found: try changing or removing some words.', 'Autocomplete Select', 'wordlift' ),
				'Please wait while we look for entities in the linked data cloud...' => _x( 'Please wait while we look for entities in the linked data cloud...', 'Autocomplete Select', 'wordlift' ),
				'Add keywords to track'                                              => __( 'Add Keywords to track', 'wordlift' ),
			),
			'wl_autocomplete_nonce'      => wp_create_nonce( 'wordlift_autocomplete' ),
			'autocomplete_scope'         => $autocomplete_scope,
			/**
			 * Allow 3rd parties to define the default editor id. This turns useful if 3rd parties load
			 * or change the TinyMCE id.
			 *
			 * The editor id is currently referenced by `src/coffee/editpost-widget/app.services.EditorAdapter.coffee`.
			 *
			 * @param string $editor The default editor id, by default `content`.
			 *
			 * @see https://github.com/insideout10/wordlift-plugin/issues/848
			 *
			 * @since 3.19.4
			 *
			 */
			'default_editor_id'          => apply_filters( 'wl_default_editor_id', 'content' ),
			/**
			 * Add the link to the Search Keywords admin page.
			 *
			 * @since 3.20.0
			 */
			'search_keywords_admin_page' => admin_url( 'admin.php?page=wl_configuration_admin_menu&tab=search-keywords' ),
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
		wp_localize_script( $this->plugin_name, 'wlSettings', apply_filters( 'wl_admin_settings', $params ) );

	}

	/**
	 * Require files needed for the Admin UI.
	 *
	 * @since 3.20.0
	 */
	private static function require_files() {

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-wordlift-admin-dashboard-latest-news.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-wordlift-admin-search-rankings-service.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-wordlift-admin-search-rankings-ajax-adapter.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-wordlift-admin-dashboard-v2.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-wordlift-admin-not-enriched-filter.php';

	}

	/**
	 * Add Wordlift custom block category.
	 *
	 * @since 3.21.0
	 */
	private static function add_block_category() {
		add_filter( 'block_categories', function ( $categories, $post ) {
			return array_merge(
				$categories,
				array(
					array(
						'slug'  => 'wordlift',
						'title' => 'WordLift Blocks',
					),
				)
			);
		}, 10, 2 );
	}

	public static function is_gutenberg() {
		if ( function_exists( 'is_gutenberg_page' ) &&
		     is_gutenberg_page()
		) {
			// The Gutenberg plugin is on.
			return true;
		}
		$current_screen = get_current_screen();
		if ( method_exists( $current_screen, 'is_block_editor' ) &&
		     $current_screen->is_block_editor()
		) {
			// Gutenberg page on 5+.
			return true;
		}

		return false;
	}

}
