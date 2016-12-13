<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://wordlift.io
 * @since      1.0.0
 *
 * @package    Wordlift
 * @subpackage Wordlift/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Wordlift
 * @subpackage Wordlift/includes
 * @author     WordLift <hello@wordlift.io>
 */
class Wordlift {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Wordlift_Loader $loader Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string $plugin_name The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string $version The current version of the plugin.
	 */
	protected $version;

	/**
	 * The Thumbnail service.
	 *
	 * @since  3.1.5
	 * @access private
	 * @var \Wordlift_Thumbnail_Service $thumbnail_service The Thumbnail service.
	 */
	private $thumbnail_service;

	/**
	 * The UI service.
	 *
	 * @since  3.2.0
	 * @access private
	 * @var \Wordlift_UI_Service $ui_service The UI service.
	 */
	private $ui_service;

	/**
	 * The Schema service.
	 *
	 * @since  3.3.0
	 * @access private
	 * @var \Wordlift_Schema_Service $schema_service The Schema service.
	 */
	private $schema_service;

	/**
	 * The Entity service.
	 *
	 * @since  3.1.0
	 * @access private
	 * @var \Wordlift_Entity_Service $entity_service The Entity service.
	 */
	private $entity_service;

	/**
	 * The Topic Taxonomy service.
	 *
	 * @since  3.5.0
	 * @access private
	 * @var \Wordlift_Topic_Taxonomy_Service The Topic Taxonomy service.
	 */
	private $topic_taxonomy_service;

	/**
	 * The User service.
	 *
	 * @since  3.1.7
	 * @access private
	 * @var \Wordlift_User_Service $user_service The User service.
	 */
	private $user_service;

	/**
	 * The Timeline service.
	 *
	 * @since  3.1.0
	 * @access private
	 * @var \Wordlift_Timeline_Service $timeline_service The Timeline service.
	 */
	private $timeline_service;

	/**
	 * The Redirect service.
	 *
	 * @since  3.2.0
	 * @access private
	 * @var \Wordlift_Redirect_Service $redirect_service The Redirect service.
	 */
	private $redirect_service;

	/**
	 * The Notice service.
	 *
	 * @since  3.3.0
	 * @access private
	 * @var \Wordlift_Notice_Service $notice_service The Notice service.
	 */
	private $notice_service;

	/**
	 * The Entity list customization.
	 *
	 * @since  3.3.0
	 * @access private
	 * @var \Wordlift_List_Service $entity_list_service The Entity list service.
	 */
	private $entity_list_service;

	/**
	 * The Entity Types Taxonomy Walker.
	 *
	 * @since  3.1.0
	 * @access private
	 * @var \Wordlift_Entity_Types_Taxonomy_Walker $entity_types_taxonomy_walker The Entity Types Taxonomy Walker
	 */
	private $entity_types_taxonomy_walker;

	/**
	 * The ShareThis service.
	 *
	 * @since  3.2.0
	 * @access private
	 * @var \Wordlift_ShareThis_Service $sharethis_service The ShareThis service.
	 */
	private $sharethis_service;

	/**
	 * The PrimaShop adapter.
	 *
	 * @since  3.2.3
	 * @access private
	 * @var \Wordlift_PrimaShop_Adapter $primashop_adapter The PrimaShop adapter.
	 */
	private $primashop_adapter;

	/**
	 * The WordLift Dashboard adapter.
	 *
	 * @since  3.4.0
	 * @access private
	 * @var \Wordlift_Dashboard_Service $dashboard_service The WordLift Dashboard service;
	 */
	private $dashboard_service;

	/**
	 * The entity type service.
	 *
	 * @since  3.6.0
	 * @access private
	 * @var \Wordlift_Entity_Post_Type_Service
	 */
	private $entity_post_type_service;

	/**
	 * The entity link service used to mangle links to entities with a custom slug or even w/o a slug.
	 *
	 * @since  3.6.0
	 * @access private
	 * @var \Wordlift_Entity_Link_Service
	 */
	private $entity_link_service;

	/**
	 * The page service instance which processes the page output in order to insert schema.org microdata to export the
	 * correct page title to Google+.
	 *
	 * @since  3.5.3
	 * @access private
	 * @var \Wordlift_Page_Service
	 */
	private $page_service;

	/**
	 * A {@link Wordlift_Sparql_Service} instance.
	 *
	 * @var    3.6.0
	 * @access private
	 * @var \Wordlift_Sparql_Service $sparql_service A {@link Wordlift_Sparql_Service} instance.
	 */
	private $sparql_service;

	/**
	 * A {@link Wordlift_Import_Service} instance.
	 *
	 * @since  3.6.0
	 * @access private
	 * @var \Wordlift_Import_Service $import_service A {@link Wordlift_Import_Service} instance.
	 */
	private $import_service;

	/**
	 * A {@link Wordlift_Rebuild_Service} instance.
	 *
	 * @since  3.6.0
	 * @access private
	 * @var \Wordlift_Rebuild_Service $rebuild_service A {@link Wordlift_Rebuild_Service} instance.
	 */
	private $rebuild_service;

	/**
	 * A {@link Wordlift_Jsonld_Service} instance.
	 *
	 * @since  3.7.0
	 * @access private
	 * @var \Wordlift_Jsonld_Service $jsonld_service A {@link Wordlift_Jsonld_Service} instance.
	 */
	private $jsonld_service;

	/**
	 *
	 * @since  3.7.0
	 * @access private
	 * @var \Wordlift_Property_Factory $property_factory
	 */
	private $property_factory;

	/**
	 * The 'Download Your Data' page.
	 *
	 * @since  3.6.0
	 * @access private
	 * @var \Wordlift_Admin_Download_Your_Data_Page $download_your_data_page The 'Download Your Data' page.
	 */
	private $download_your_data_page;

	/**
	 * The install wizard page.
	 *
	 * @since  3.9.0
	 * @access private
	 * @var \Wordlift_Admin_Install_Wizard $install_wizard The Install wizard.
	 */
	private $install_wizard;

	/**
	 * The Content Filter Service hooks up to the 'the_content' filter and provides
	 * linking of entities to their pages.
	 *
	 * @since  3.8.0
	 * @access private
	 * @var \Wordlift_Content_Filter_Service $content_filter_service A {@link Wordlift_Content_Filter_Service} instance.
	 */
	private $content_filter_service;

	/**
	 * A {@link Wordlift_Key_Validation_Service} instance.
	 *
	 * @since  3.9.0
	 * @access private
	 * @var Wordlift_Key_Validation_Service $key_validation_service A {@link Wordlift_Key_Validation_Service} instance.
	 */
	private $key_validation_service;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		$this->plugin_name = 'wordlift';
		$this->version     = '3.9.0-dev';
		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Wordlift_Loader. Orchestrates the hooks of the plugin.
	 * - Wordlift_i18n. Defines internationalization functionality.
	 * - Wordlift_Admin. Defines all hooks for the admin area.
	 * - Wordlift_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wordlift-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wordlift-i18n.php';

		/**
		 * WordLift's supported languages.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wordlift-languages.php';

		/**
		 * Provide support functions to sanitize data.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wordlift-sanitizer.php';

		/**
		 * The Redirect service.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wordlift-redirect-service.php';

		/**
		 * The Log service.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wordlift-log-service.php';

		/**
		 * The configuration service.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wordlift-configuration-service.php';

		/**
		 * The entity post type service (this is the WordPress post type, not the entity schema type).
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wordlift-entity-post-type-service.php';

		/**
		 * The entity type service (i.e. the schema type).
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wordlift-entity-type-service.php';

		/**
		 * The entity link service.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wordlift-entity-link-service.php';

		/**
		 * The Query builder.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wordlift-query-builder.php';

		/**
		 * The Schema service.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wordlift-schema-service.php';

		/**
		 * The schema:url property service.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wordlift-property-service.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wordlift-schema-url-property-service.php';

		/**
		 * The UI service.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wordlift-ui-service.php';

		/**
		 * The Thumbnail service.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wordlift-thumbnail-service.php';

		/**
		 * The Entity Types Taxonomy service.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wordlift-entity-types-taxonomy-service.php';

		/**
		 * The Entity service.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wordlift-entity-service.php';

		/**
		 * The User service.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wordlift-user-service.php';

		/**
		 * The Timeline service.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wordlift-timeline-service.php';

		/**
		 * The Topic Taxonomy service.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wordlift-topic-taxonomy-service.php';


		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wordlift-page-service.php';

		/**
		 * The SPARQL service.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wordlift-sparql-service.php';

		/**
		 * The WordLift import service.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wordlift-import-service.php';

		/**
		 * The WordLift URI service.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wordlift-uri-service.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wordlift-listable.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wordlift-property-factory.php';

		/**
		 * The WordLift rebuild service, used to rebuild the remote dataset using the local data.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wordlift-rebuild-service.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/properties/class-wordlift-property-getter-factory.php';

		/**
		 * Load the converters.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wordlift-entity-post-to-jsonld-converter.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wordlift-entity-uri-to-jsonld-converter.php';

		/**
		 * Load the content filter.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wordlift-content-filter-service.php';

		/**
		 * Load the JSON-LD service to publish entities using JSON-LD.s
		 *
		 * @since 3.8.0
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wordlift-jsonld-service.php';

		/**
		 * Load the WordLift key validation service.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wordlift-key-validation-service.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-wordlift-admin.php';

		/**
		 * The class to customize the entity list admin page.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-wordlift-admin-entity-list.php';

		/**
		 * The Entity Types Taxonomy Walker (transforms checkboxes into radios).
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-wordlift-entity-types-taxonomy-walker.php';

		/**
		 * The Notice service.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-wordlift-notice-service.php';

		/**
		 * The PrimaShop adapter.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-wordlift-primashop-adapter.php';

		/**
		 * The WordLift Dashboard service.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-wordlift-admin-dashboard.php';

		/**
		 * The admin 'Install wizard' page.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-wordlift-admin-install-wizard.php';

		/**
		 * The admin 'Download Your Data' page.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-wordlift-download-your-data-page.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-wordlift-public.php';

		/**
		 * The shortcode abstract class.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-wordlift-shortcode.php';

		/**
		 * The Timeline shortcode.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-wordlift-timeline-shortcode.php';

		/**
		 * The Navigator shortcode.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-wordlift-navigator-shortcode.php';

		/**
		 * The chord shortcode.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-wordlift-chord-shortcode.php';

		/**
		 * The geomap shortcode.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-wordlift-geomap-shortcode.php';

		/**
		 * The ShareThis service.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-wordlift-sharethis-service.php';

		$this->loader = new Wordlift_Loader();

		// Instantiate a global logger.
		global $wl_logger;
		$wl_logger = Wordlift_Log_Service::get_logger( 'WordLift' );

		// Create the configuration service.
		$configuration_service = new Wordlift_Configuration_Service();

		// Create an entity type service instance. It'll be later bound to the init action.
		$this->entity_post_type_service = new Wordlift_Entity_Post_Type_Service( Wordlift_Entity_Service::TYPE_NAME, $configuration_service->get_entity_base_path() );

		// Create an entity link service instance. It'll be later bound to the post_type_link and pre_get_posts actions.
		$this->entity_link_service = new Wordlift_Entity_Link_Service( $this->entity_post_type_service, $configuration_service->get_entity_base_path() );

		// Create an instance of the UI service.
		$this->ui_service = new Wordlift_UI_Service();

		// Create an instance of the Thumbnail service. Later it'll be hooked to post meta events.
		$this->thumbnail_service = new Wordlift_Thumbnail_Service();

		$this->sparql_service = new Wordlift_Sparql_Service();

		// Create an instance of the Schema service.
		$schema_url_property_service = new Wordlift_Schema_Url_Property_Service( $this->sparql_service );
		$this->schema_service        = new Wordlift_Schema_Service();

		// Create an instance of the Notice service.
		$this->notice_service = new Wordlift_Notice_Service();

		// Create an instance of the Entity service, passing the UI service to draw parts of the Entity admin page.
		$this->entity_service = new Wordlift_Entity_Service( $this->ui_service, $this->schema_service, $this->notice_service );

		// Create an instance of the User service.
		$this->user_service = new Wordlift_User_Service();

		// Create a new instance of the Timeline service and Timeline shortcode.
		$this->timeline_service = new Wordlift_Timeline_Service( $this->entity_service );

		// Create a new instance of the Redirect service.
		$this->redirect_service = new Wordlift_Redirect_Service( $this->entity_service );

		// Create a new instance of the Redirect service.
		$this->dashboard_service = new Wordlift_Dashboard_Service( $this->entity_service );

		// Initialize the shortcodes.
		new Wordlift_Navigator_Shortcode();
		new Wordlift_Chord_Shortcode();
		new Wordlift_Geomap_Shortcode();
		new Wordlift_Timeline_Shortcode();

		// Create entity list customization (wp-admin/edit.php)
		$this->entity_list_service = new Wordlift_Entity_List_Service( $this->entity_service );

		$this->entity_types_taxonomy_walker = new Wordlift_Entity_Types_Taxonomy_Walker();

		$this->topic_taxonomy_service = new Wordlift_Topic_Taxonomy_Service();

		// Create an instance of the ShareThis service, later we hook it to the_content and the_excerpt filters.
		$this->sharethis_service = new Wordlift_ShareThis_Service();

		// Create an instance of the PrimaShop adapter.
		$this->primashop_adapter = new Wordlift_PrimaShop_Adapter();

		$this->page_service = new Wordlift_Page_Service();

		// Create an import service instance to hook later to WP's import function.
		$this->import_service = new Wordlift_Import_Service( $this->entity_post_type_service, $this->entity_service, $this->schema_service, $this->sparql_service, wl_configuration_get_redlink_dataset_uri() );

		$uri_service = new Wordlift_Uri_Service( $GLOBALS['wpdb'] );

		// Create a Rebuild Service instance, which we'll later bound to an ajax call.
		$this->rebuild_service = new Wordlift_Rebuild_Service( $this->sparql_service, $uri_service );

		$entity_type_service = new Wordlift_Entity_Type_Service( $this->schema_service );

		$this->property_factory = new Wordlift_Property_Factory( $schema_url_property_service );
		$this->property_factory->register( Wordlift_Schema_Url_Property_Service::META_KEY, $schema_url_property_service );

		// Instantiate the JSON-LD service.
		$property_getter                = Wordlift_Property_Getter_Factory::create( $this->entity_service );
		$entity_uri_to_jsonld_converter = new Wordlift_Entity_Uri_To_Jsonld_Converter( $entity_type_service, $this->entity_service, $property_getter );
		$this->jsonld_service           = new Wordlift_Jsonld_Service( $this->entity_service, $entity_uri_to_jsonld_converter );

		// Create an instance of the Key Validation service. This service is later hooked to provide an AJAX call (only for admins).
		$this->key_validation_service = new Wordlift_Key_Validation_Service();

		//** WordPress Admin */
		$this->download_your_data_page = new Wordlift_Admin_Download_Your_Data_Page();

		// Create an instance of the install wizard.
		$this->install_wizard = new Wordlift_Admin_Install_Wizard( $configuration_service, $this->key_validation_service );

		// Create an instance of the content filter service.
		$this->content_filter_service = new Wordlift_Content_Filter_Service( $this->entity_service );

		// Load the debug service if WP is in debug mode.
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wordlift-debug-service.php';
			new Wordlift_Debug_Service( $this->entity_service );
		}

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Wordlift_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Wordlift_i18n();
		$plugin_i18n->set_domain( $this->get_plugin_name() );

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Wordlift_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

		// Hook the init action to the Topic Taxonomy service.
		$this->loader->add_action( 'init', $this->topic_taxonomy_service, 'init', 0 );

		// Hook the deleted_post_meta action to the Thumbnail service.
		$this->loader->add_action( 'deleted_post_meta', $this->thumbnail_service, 'deleted_post_meta', 10, 4 );

		// Hook the added_post_meta action to the Thumbnail service.
		$this->loader->add_action( 'added_post_meta', $this->thumbnail_service, 'added_or_updated_post_meta', 10, 4 );

		// Hook the updated_post_meta action to the Thumbnail service.
		$this->loader->add_action( 'updated_post_meta', $this->thumbnail_service, 'added_or_updated_post_meta', 10, 4 );

		// Hook posts inserts (or updates) to the user service.
		$this->loader->add_action( 'wp_insert_post', $this->user_service, 'wp_insert_post', 10, 3 );

		// Hook the AJAX wl_timeline action to the Timeline service.
		$this->loader->add_action( 'wp_ajax_wl_timeline', $this->timeline_service, 'ajax_timeline' );

		// Register custom allowed redirect hosts.
		$this->loader->add_filter( 'allowed_redirect_hosts', $this->redirect_service, 'allowed_redirect_hosts' );
		// Hook the AJAX wordlift_redirect action to the Redirect service.
		$this->loader->add_action( 'wp_ajax_wordlift_redirect', $this->redirect_service, 'ajax_redirect' );
		// Hook the AJAX wordlift_redirect action to the Redirect service.
		$this->loader->add_action( 'wp_ajax_wordlift_get_stats', $this->dashboard_service, 'ajax_get_stats' );
		// Hook the AJAX wordlift_redirect action to the Redirect service.
		$this->loader->add_action( 'wp_dashboard_setup', $this->dashboard_service, 'add_dashboard_widgets' );

		// Hook save_post to the entity service to update custom fields (such as alternate labels).
		// We have a priority of 9 because we want to be executed before data is sent to Redlink.
		$this->loader->add_action( 'save_post', $this->entity_service, 'save_post', 9, 3 );
		$this->loader->add_action( 'save_post_entity', $this->entity_service, 'set_rating_for', 10, 1 );

		$this->loader->add_action( 'edit_form_before_permalink', $this->entity_service, 'edit_form_before_permalink', 10, 1 );
		$this->loader->add_action( 'in_admin_header', $this->entity_service, 'in_admin_header' );

		// Entity listing customization (wp-admin/edit.php)
		// Add custom columns
		$this->loader->add_filter( 'manage_entity_posts_columns', $this->entity_list_service, 'register_custom_columns' );
		$this->loader->add_filter( 'manage_entity_posts_custom_column', $this->entity_list_service, 'render_custom_columns', 10, 2 );
		// Add 4W selection
		$this->loader->add_action( 'restrict_manage_posts', $this->entity_list_service, 'restrict_manage_posts_classification_scope' );
		$this->loader->add_filter( 'posts_clauses', $this->entity_list_service, 'posts_clauses_classification_scope' );

		$this->loader->add_filter( 'wp_terms_checklist_args', $this->entity_types_taxonomy_walker, 'terms_checklist_args' );

		// Hook the PrimaShop adapter to <em>prima_metabox_entity_header_args</em> in order to add header support for
		// entities.
		$this->loader->add_filter( 'prima_metabox_entity_header_args', $this->primashop_adapter, 'prima_metabox_entity_header_args', 10, 2 );

		// Filter imported post meta.
		$this->loader->add_filter( 'wp_import_post_meta', $this->import_service, 'wp_import_post_meta', 10, 3 );

		// Notify the import service when an import starts and ends.
		$this->loader->add_action( 'import_start', $this->import_service, 'import_start', 10, 0 );
		$this->loader->add_action( 'import_end', $this->import_service, 'import_end', 10, 0 );

		// Hook the AJAX wl_rebuild action to the Rebuild Service.
		$this->loader->add_action( 'wp_ajax_wl_rebuild', $this->rebuild_service, 'rebuild' );

		// Hook the menu to the Download Your Data page.
		$this->loader->add_action( 'admin_menu', $this->download_your_data_page, 'admin_menu', 100, 0 );

		// Hook the admin-ajax.php?action=wl_download_your_data&out=xyz links.
		$this->loader->add_action( 'wp_ajax_wl_download_your_data', $this->download_your_data_page, 'download_your_data', 10 );

		// Hook the AJAX wl_jsonld action to the JSON-LD service.
		$this->loader->add_action( 'wp_ajax_wl_jsonld', $this->jsonld_service, 'get' );

		// Hook the AJAX wl_validate_key action to the Key Validation service.
		$this->loader->add_action( 'wp_ajax_wl_validate_key', $this->key_validation_service, 'validate_key' );

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Wordlift_Public( $this->get_plugin_name(), $this->get_version() );

		// Register the entity post type.
		$this->loader->add_action( 'init', $this->entity_post_type_service, 'register' );

		// Bind the link generation and handling hooks to the entity link service.
		$this->loader->add_filter( 'post_type_link', $this->entity_link_service, 'post_type_link', 10, 4 );
		$this->loader->add_action( 'pre_get_posts', $this->entity_link_service, 'pre_get_posts', 10, 1 );
		$this->loader->add_filter( 'wp_unique_post_slug_is_bad_flat_slug', $this->entity_link_service, 'wp_unique_post_slug_is_bad_flat_slug', 10, 3 );
		$this->loader->add_filter( 'wp_unique_post_slug_is_bad_hierarchical_slug', $this->entity_link_service, 'wp_unique_post_slug_is_bad_hierarchical_slug', 10, 4 );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

		// Hook the content filter service to add entity links.
		$this->loader->add_filter( 'the_content', $this->content_filter_service, 'the_content' );

		// Hook the AJAX wl_timeline action to the Timeline service.
		$this->loader->add_action( 'wp_ajax_nopriv_wl_timeline', $this->timeline_service, 'ajax_timeline' );

		// Hook the ShareThis service.
		$this->loader->add_filter( 'the_content', $this->sharethis_service, 'the_content', 99 );
		$this->loader->add_filter( 'the_excerpt', $this->sharethis_service, 'the_excerpt', 99 );

		$this->loader->add_action( 'wp_head', $this->page_service, 'wp_head', PHP_INT_MAX );
		$this->loader->add_action( 'wp_footer', $this->page_service, 'wp_head', - PHP_INT_MAX );

		// Hook the AJAX wl_jsonld action to the JSON-LD service.
		$this->loader->add_action( 'wp_ajax_nopriv_wl_jsonld', $this->jsonld_service, 'get' );

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Wordlift_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
