<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       http://wordlift.it
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
 * @author     WordLift <hello@wordlift.it>
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
	 * @since 3.1.5
	 * @access private
	 * @var \Wordlift_Thumbnail_Service $thumbnail_service The Thumbnail service.
	 */
	private $thumbnail_service;

	/**
	 * The UI service.
	 *
	 * @since 3.2.0
	 * @access private
	 * @var \Wordlift_UI_Service $ui_service The UI service.
	 */
	private $ui_service;

	/**
	 * The Entity service.
	 *
	 * @since 3.1.0
	 * @access private
	 * @var \Wordlift_Entity_Service $entity_service The Entity service.
	 */
	private $entity_service;

	/**
	 * The User service.
	 *
	 * @since 3.1.7
	 * @access private
	 * @var \Wordlift_User_Service $user_service The User service.
	 */
	private $user_service;

	/**
	 * The Timeline service.
	 *
	 * @since 3.1.0
	 * @access private
	 * @var \Wordlift_Timeline_Service $timeline_service The Timeline service.
	 */
	private $timeline_service;

	/**
	 * The Redirect service.
	 *
	 * @since 3.2.0
	 * @access private
	 * @var \Wordlift_Redirect_Service $redirect_service The Redirect service.
	 */
	private $redirect_service;
	
	/**
	 * The Entity list customization.
	 *
	 * @since 3.3.0
	 * @access private
	 * @var \Wordlift_List_Service $entity_list_service The Entity list service.
	 */
	private $entity_list_service;

	/**
	 * The Entity Types Taxonomy Walker.
	 *
	 * @since 3.1.0
	 * @access private
	 * @var \Wordlift_Entity_Types_Taxonomy_Walker $entity_types_taxonomy_walker The Entity Types Taxonomy Walker
	 */
	private $entity_types_taxonomy_walker;

	/**
	 * The ShareThis service.
	 *
	 * @since 3.2.0
	 * @access private
	 * @var \Wordlift_ShareThis_Service $sharethis_service The ShareThis service.
	 */
	private $sharethis_service;

	/**
	 * The PrimaShop adapter.
	 *
	 * @since 3.2.3
	 * @access private
	 * @var \Wordlift_PrimaShop_Adapter $primashop_adapter The PrimaShop adapter.
	 */
	private $primashop_adapter;

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

		$this->version = '3.3.0-dev';

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
		 * The Redirect service.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wordlift-redirect-service.php';

		/**
		 * The Log service.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wordlift-log-service.php';

		/**
		 * The Query builder.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wordlift-query-builder.php';

		/**
		 * The Schema service.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wordlift-schema-service.php';

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
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-wordlift-public.php';

		/**
		 * The Timeline shortcode.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-wordlift-timeline-shortcode.php';

		/**
		 * The ShareThis service.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-wordlift-sharethis-service.php';

		$this->loader = new Wordlift_Loader();

		// Instantiate a global logger.
		global $wl_logger;
		$wl_logger = Wordlift_Log_Service::get_logger( 'WordLift' );

		// Create an instance of the UI service.
		$this->ui_service = new Wordlift_UI_Service();

		// Create an instance of the Thumbnail service. Later it'll be hooked to post meta events.
		$this->thumbnail_service = new Wordlift_Thumbnail_Service();

		// Create an instance of the Schema service.
		new Wordlift_Schema_Service();

		// Create an instance of the Entity service, passing the UI service to draw parts of the Entity admin page.
		$this->entity_service = new Wordlift_Entity_Service( $this->ui_service );

		// Create an instance of the User service.
		$this->user_service = new Wordlift_User_Service();

		// Create a new instance of the Timeline service and Timeline shortcode.
		$this->timeline_service = new Wordlift_Timeline_Service( $this->entity_service );

		// Create a new instance of the Redirect service.
		$this->redirect_service = new Wordlift_Redirect_Service( $this->entity_service );

		// Create an instance of the Timeline shortcode.
		new Wordlift_Timeline_Shortcode();
		
		// Create entity list customization (wp-admin/edit.php)
		$this->entity_list_service = new Wordlift_Entity_List_Service();

		$this->entity_types_taxonomy_walker = new Wordlift_Entity_Types_Taxonomy_Walker();

		// Create an instance of the ShareThis service, later we hook it to the_content and the_excerpt filters.
		$this->sharethis_service = new Wordlift_ShareThis_Service();

		// Create an instance of the Notice service.
		new Wordlift_Notice_Service();

		// Create an instance of the PrimaShop adapter.
		$this->primashop_adapter = new Wordlift_PrimaShop_Adapter();
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

		// Hook the deleted_post_meta action to the Thumbnail service.
		$this->loader->add_action( 'deleted_post_meta', $this->thumbnail_service, 'deleted_post_meta', 10, 4 );

		// Hook the added_post_meta action to the Thumbnail service.
		$this->loader->add_action( 'added_post_meta', $this->thumbnail_service, 'added_post_meta', 10, 4 );

		// Hook posts inserts (or updates) to the user service.
		$this->loader->add_action( 'wp_insert_post', $this->user_service, 'wp_insert_post', 10, 3 );

		// Hook the AJAX wl_timeline action to the Timeline service.
		$this->loader->add_action( 'wp_ajax_wl_timeline', $this->timeline_service, 'ajax_timeline' );

		// Register custom allowed redirect hosts.
		$this->loader->add_filter( 'allowed_redirect_hosts', $this->redirect_service, 'allowed_redirect_hosts' );
		// Hook the AJAX wordlift_redirect action to the Redirect service.
		$this->loader->add_action( 'wp_ajax_wordlift_redirect', $this->redirect_service, 'ajax_redirect' );

		// Hook save_post to the entity service to update custom fields (such as alternate labels).
		// We have a priority of 9 because we want to be executed before data is sent to Redlink.
		$this->loader->add_action( 'save_post', $this->entity_service, 'save_post', 9, 3 );
		$this->loader->add_action( 'edit_form_before_permalink', $this->entity_service, 'edit_form_before_permalink', 10, 1 );

		// Entity listing customization (wp-admin/edit.php)
		// Add custom columns
		$this->loader->add_filter( 'manage_entity_posts_columns', $this->entity_list_service, 'register_custom_columns' );
		$this->loader->add_filter( 'manage_entity_posts_custom_column', $this->entity_list_service, 'render_custom_columns', 10, 2 );
		// Add 4W selection
		$this->loader->add_action( 'restrict_manage_posts', $this->entity_list_service, 'add_4W_filter' );
		$this->loader->add_filter( 'parse_query', $this->entity_list_service, 'add_4W_filter_query' );
		
		$this->loader->add_filter( 'wp_terms_checklist_args', $this->entity_types_taxonomy_walker, 'terms_checklist_args' );

		// Hook the PrimaShop adapter to <em>prima_metabox_entity_header_args</em> in order to add header support for
		// entities.
		$this->loader->add_filter( 'prima_metabox_entity_header_args', $this->primashop_adapter, 'prima_metabox_entity_header_args', 10, 2 );
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

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

		// Hook the AJAX wl_timeline action to the Timeline service.
		$this->loader->add_action( 'wp_ajax_nopriv_wl_timeline', $this->timeline_service, 'ajax_timeline' );

		// Hook the ShareThis service.
		$this->loader->add_filter( 'the_content', $this->sharethis_service, 'the_content', 99 );
		$this->loader->add_filter( 'the_excerpt', $this->sharethis_service, 'the_excerpt', 99 );
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
