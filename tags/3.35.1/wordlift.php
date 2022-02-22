<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://wordlift.io
 * @since             1.0.0
 * @package           Wordlift
 *
 * @wordpress-plugin
 * Plugin Name:       WordLift
 * Plugin URI:        https://wordlift.io
 * Description:       WordLift brings the power of AI to organize content, attract new readers and get their attention. To activate the plugin <a href="https://wordlift.io/">visit our website</a>.
 * Version:           3.35.1
 * Author:            WordLift, Insideout10
 * Author URI:        https://wordlift.io
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wordlift
 * Domain Path:       /languages
 */

use Wordlift\Admin\Key_Validation_Notice;
use Wordlift\Admin\Top_Entities;
use Wordlift\Api_Data\Api_Data_Hooks;
use Wordlift\Cache\Ttl_Cache_Cleaner;
use Wordlift\Features\Features_Registry;
use Wordlift\Post\Post_Adapter;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Filter to disable WLP on any request, defaults to true.
 * @since 3.33.6
 *
 */
if ( ! apply_filters( 'wl_is_enabled', true ) ) {
	return;
}

require_once plugin_dir_path( __FILE__ ) . 'vendor/autoload.php';

/*
	 * We introduce the WordLift autoloader, since we start using classes in namespaces, i.e. Wordlift\Http.
	 *
	 * @since 3.21.2
	 */
wordlift_plugin_autoload_register();


// Include WordLift constants.
require_once( 'wordlift_constants.php' );

// Load modules.
require_once( 'modules/core/wordlift_core.php' );

require_once( 'deprecations.php' );

// Load early to enable/disable features.
require_once plugin_dir_path( __FILE__ ) . 'wordlift/features/index.php';

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-wordlift-activator.php
 */
function activate_wordlift() {

	$log = Wordlift_Log_Service::get_logger( 'activate_wordlift' );

	$log->info( 'Activating WordLift...' );

	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wordlift-activator.php';
	Wordlift_Activator::activate();

	/**
	 * Tell the {@link Wordlift_Http_Api} class that we're activating, to let it run activation tasks.
	 *
	 * @see https://github.com/insideout10/wordlift-plugin/issues/820 related issue.
	 * @since 3.19.2
	 */
	Wordlift_Http_Api::activate();

	// Ensure the post type is registered before flushing the rewrite rules.
	Wordlift_Entity_Post_Type_Service::get_instance()->register();
	flush_rewrite_rules();
	/**
	 * @since 3.27.7
	 * @see https://github.com/insideout10/wordlift-plugin/issues/1214
	 */
	Top_Entities::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-wordlift-deactivator.php
 */
function deactivate_wordlift() {

	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wordlift-deactivator.php';
	Wordlift_Deactivator::deactivate();
	Wordlift_Http_Api::deactivate();
	Ttl_Cache_Cleaner::deactivate();
	/**
	 * @since 3.27.7
	 * @see https://github.com/insideout10/wordlift-plugin/issues/1214
	 */
	Top_Entities::deactivate();
	/**
	 * @since 3.27.8
	 * Remove notification flag on deactivation.
	 */
	Key_Validation_Notice::remove_notification_flag();
	flush_rewrite_rules();

}

register_activation_hook( __FILE__, 'activate_wordlift' );
register_deactivation_hook( __FILE__, 'deactivate_wordlift' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-wordlift.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_wordlift() {
	/**
	 * Filter: wl_feature__enable__widgets.
	 *
	 * @param bool whether the widgets needed to be registered, defaults to true.
	 *
	 * @return bool
	 * @since 3.27.6
	 */
	if ( apply_filters( 'wl_feature__enable__widgets', true ) ) {
		add_action( 'widgets_init', 'wl_register_chord_widget' );
		add_action( 'widgets_init', 'wl_register_geo_widget' );
		add_action( 'widgets_init', 'wl_register_timeline_widget' );
	}
	add_filter( 'widget_text', 'do_shortcode' );


	/**
	 * Filter: wl_feature__enable__analysis
	 *
	 * @param bool Whether to send api request to analysis or not
	 *
	 * @return bool
	 * @since 3.27.6
	 */
	if ( apply_filters( 'wl_feature__enable__analysis', true ) ) {
		add_action( 'wp_ajax_wl_analyze', 'wl_ajax_analyze_action' );
	} else {
		add_action( 'wp_ajax_wl_analyze', 'wl_ajax_analyze_disabled_action' );
	}

	$plugin = new Wordlift();
	$plugin->run();

	// Initialize the TTL Cache Cleaner.
	new Ttl_Cache_Cleaner();

	// Load the new Post Adapter.
	new Post_Adapter();

	// Load the API Data Hooks.
	new Api_Data_Hooks();

	add_action( 'plugins_loaded', function () {
		// All features from registry should be initialized here.
		$features_registry = Features_Registry::get_instance();
		$features_registry->initialize_all_features();
	}, 5 );

	add_action( 'plugins_loaded', function () use ( $plugin ) {

		new Wordlift_Products_Navigator_Shortcode_REST();

		// Register the Dataset module, requires `$api_service`.
		require_once plugin_dir_path( __FILE__ ) . 'wordlift/dataset/index.php';
		require_once plugin_dir_path( __FILE__ ) . 'wordlift/shipping-data/index.php';

		/*
		 * Require the Entity annotation cleanup module.
		 *
		 * @since 3.34.6
		 */
		require_once plugin_dir_path( __FILE__ ) . 'wordlift/cleanup/index.php';

		/*
		 * Import LOD entities.
		 *
		 * @since 3.35.0
		 */
		require_once plugin_dir_path( __FILE__ ) . 'wordlift/lod-import/index.php';

	} );

}

run_wordlift();

/**
 * Register our autoload routine.
 *
 * @throws Exception
 * @since 3.21.2
 */
function wordlift_plugin_autoload_register() {

	spl_autoload_register( function ( $class_name ) {

		// Bail out if these are not our classes.
		if ( 0 !== strpos( $class_name, 'Wordlift\\' ) ) {
			return false;
		}

		$class_name_lc = strtolower( str_replace( '_', '-', $class_name ) );

		preg_match( '|^(?:(.*)\\\\)?(.+?)$|', $class_name_lc, $matches );

		$path = str_replace( '\\', DIRECTORY_SEPARATOR, $matches[1] );
		$file = 'class-' . $matches[2] . '.php';

		$full_path = plugin_dir_path( __FILE__ ) . $path . DIRECTORY_SEPARATOR . $file;

		if ( ! file_exists( $full_path ) ) {
			echo esc_html( "Class $class_name not found at $full_path." );

			return false;
		}

		require_once $full_path;

		return true;
	} );

}

function wl_block_categories( $categories, $post ) {
	return array_merge(
		$categories,
		array(
			array(
				'slug'  => 'wordlift',
				'title' => __( 'WordLift', 'wordlift' ),
			),
		)
	);
}

add_filter( 'block_categories', 'wl_block_categories', 10, 2 );

// Temporary fix for a typo in WooCommerce Extension.
add_filter( 'wl_feature__enable__dataset', function ( $value ) {
	return apply_filters( 'wl_features__enable__dataset', $value );
} );
