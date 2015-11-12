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
	 * The Entity service.
	 *
	 * @since 3.1.0
	 * @access private
	 * @var \Wordlift_Entity_Service $entity_service The Entity service.
	 */
	private $entity_service;

	/**
	 * The Timeline service.
	 *
	 * @since 3.1.0
	 * @access private
	 * @var \Wordlift_Timeline_Service $timeline_service The Timeline service.
	 */
	private $timeline_service;

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
		$this->version     = '3.1.0';

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
		 * The Log service.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wordlift-log-service.php';

		/**
		 * The Schema service.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wordlift-schema-service.php';

		/**
		 * The Entity service.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wordlift-entity-service.php';

		/**
		 * The Timeline service.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wordlift-timeline-service.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-wordlift-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-wordlift-public.php';

		/**
		 * The Timeline shortcode.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-wordlift-timeline-shortcode.php';

		$this->loader = new Wordlift_Loader();

		// Instantiate a global logger.
		global $wl_logger;
		$wl_logger = Wordlift_Log_Service::get_logger( 'WordLift' );

		$schema_service = new Wordlift_Schema_Service();

		$this->entity_service = new Wordlift_Entity_Service();

		// Create a new instance of the Timeline service and Timeline shortcode.
		$this->timeline_service = new Wordlift_Timeline_Service( $this->entity_service );
		$timeline_shortcode     = new Wordlift_Timeline_Shortcode();

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

		// Hook the AJAX wl_timeline action to the Timeline service.
		$this->loader->add_action( 'wp_ajax_wl_timeline', $this->timeline_service, 'ajax_timeline' );

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
