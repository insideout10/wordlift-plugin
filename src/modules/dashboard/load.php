<?php
/**
 * Module Name: Dashboard
 *
 * @since   1.0.0
 * @package wordlift
 */

use Wordlift\Modules\Common\Symfony\Component\Config\FileLocator;
use Wordlift\Modules\Common\Symfony\Component\DependencyInjection\ContainerBuilder;
use Wordlift\Modules\Common\Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Wordlift\Modules\Dashboard\App_Settings;
use Wordlift\Modules\Dashboard\Synchronization\Rest_Controller;
use Wordlift\Modules\Dashboard\Synchronization\Synchronization_Service;
use Wordlift\Modules\Dashboard\Term_Entity_Match\Term_Entity_Match_Rest_Controller;

/**
 * Load Include Exclude Module.
 *
 * @return void
 */
function __wl_dashboard__load() {

	// Dashboard is available only for Food Kg and Gardening Kg atm
	// phpcs:ignore WordPress.NamingConventions.ValidHookName.UseUnderscores
	if ( ! apply_filters( 'wl_feature__enable__food-kg', false ) && ! apply_filters( 'wl_feature__enable__gardening-kg', false ) ) {
		return;
	}

	// Autoloader for plugin itself.
	if ( file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
		require_once __DIR__ . '/vendor/autoload.php';
	}

	$container_builder = new ContainerBuilder();
	$loader            = new YamlFileLoader( $container_builder, new FileLocator( __DIR__ ) );
	$loader->load( 'services.yml' );
	$container_builder->compile();

	/**
	 * @var $rest_controller Rest_Controller
	 */
	$rest_controller = $container_builder->get( 'Wordlift\Modules\Dashboard\Synchronization\Rest_Controller' );
	$rest_controller->register_hooks();

	/**
	 * @var $term_entity_match_rest_controller Term_Entity_Match_Rest_Controller
	 */
	$term_entity_match_rest_controller = $container_builder->get( Term_Entity_Match_Rest_Controller::class );
	$term_entity_match_rest_controller->register_hooks();

	/**
	 * @var $term_entity_match_rest_controller \Wordlift\Modules\Dashboard\Post_Entity_Match\Post_Entity_Match_Rest_Controller
	 */
	$post_entity_match_rest_controller = $container_builder->get( \Wordlift\Modules\Dashboard\Post_Entity_Match\Post_Entity_Match_Rest_Controller::class );
	$post_entity_match_rest_controller->register_hooks();

	/**
	 * @var $synchronization_service Synchronization_Service
	 */
	$synchronization_service = $container_builder->get( 'Wordlift\Modules\Dashboard\Synchronization\Synchronization_Service' );
	$synchronization_service->register_hooks();

	$app_settings = $container_builder->get( App_Settings::class );
	$app_settings->register_hooks();

	/** Admin Menu */
	add_action(
		'admin_menu',
		function () {
			add_submenu_page( 'wl_admin_menu', __( 'Dashboard', 'wordlift' ), __( 'Dashboard', 'wordlift' ), 'manage_options', 'wl_admin_menu', '_wl_dashboard__main' );
		},
		9
	);

	add_action(
		'_wl_dashboard__main',
		function () {
			// why not wp_enqueue_script ? because the iframe will start loading and it won't find the
			// settings in parent window if it's not printed before.
			wp_print_scripts( WL_ANGULAR_APP_SCRIPT_HANDLE );
			$iframe_src = WL_ANGULAR_APP_URL . '#/admin/dashboard';
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo "
			<style>
			    #wlx-plugin-app {
			      margin-left: -20px;
			      width: calc(100% + 20px);
			      min-height: 1600px;
			    }
		    </style>
			<iframe id='wlx-plugin-app' src='$iframe_src'></iframe>
            ";

		}
	);

}

__wl_dashboard__load();
