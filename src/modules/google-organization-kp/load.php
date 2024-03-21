<?php

/**
 * Module Name: Google Organization Kp
 * Description: This module handles the admin screen and API and publishing methods to manage the publisher/organization KP.
 *
 * @since   3.53.0
 * @package wordlift
 */

use Wordlift\Modules\Common\Symfony\Component\Config\FileLocator;
use Wordlift\Modules\Common\Symfony\Component\DependencyInjection\ContainerBuilder;
use Wordlift\Modules\Common\Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Wordlift\Modules\Google_Organization_Kp\About_Page_Organization_Filter;
use Wordlift\Modules\Google_Organization_Kp\Rest_Controller;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function __wl_google_organization_kp_load() {

	// Autoloader for dependencies.
	if ( file_exists( __DIR__ . '/third-party/vendor/scoper-autoload.php' ) ) {
		require __DIR__ . '/third-party/vendor/scoper-autoload.php';
	}

	// Autoloader for plugin itself.
	if ( file_exists( __DIR__ . '/includes/vendor/autoload.php' ) ) {
		require __DIR__ . '/includes/vendor/autoload.php';
	}

	$container_builder = new ContainerBuilder();
	$loader            = new YamlFileLoader( $container_builder, new FileLocator( __DIR__ ) );
	$loader->load( 'services.yml' );
	$container_builder->compile();

	/**
	 * @var $rest_controller Rest_Controller
	 */
	$rest_controller = $container_builder->get( 'Wordlift\Modules\Google_Organization_Kp\Rest_Controller' );
	$rest_controller->register_hooks();

	/**
	 * @var $about_page_organization_filter About_Page_Organization_Filter
	 */
	$about_page_organization_filter = $container_builder->get( 'Wordlift\Modules\Google_Organization_Kp\About_Page_Organization_Filter' );
	$about_page_organization_filter->register_hooks();

	/** Admin Menu */
	add_action(
		'admin_menu',
		function () {
			add_submenu_page(
				'wl_admin_menu',
				__( 'About Page', 'wordlift' ),
				__( 'About Page', 'wordlift' ),
				'manage_options',
				'wl_about_page',
				'__wl_google_organization_kp_dashboard'
			);
		},
		11
	);
}

function __wl_google_organization_kp_dashboard() {
	wp_print_scripts( WL_ANGULAR_APP_SCRIPT_HANDLE );

	wp_enqueue_script(
		'iframe-resizer',
		'/wp-content/plugins/wordlift/js-client/iframe-resizer/iframeResizer.min.js',
		array(),
		wp_get_theme()->get( 'Version' ),
		array( 'in_footer' => true )
	);

	$iframe_src = WL_ANGULAR_APP_URL . '/#/admin/about-page';

	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo "
			<style>
			    #wlx-plugin-app {
				    margin-left: -20px;
					width: calc(100% + 20px);
					min-width: 100%;
			    }
		    </style>
			<iframe id='wlx-plugin-app' src='$iframe_src'></iframe>
			<script>
				window.addEventListener( 'load', function() {
					iFrameResize(
                        {
                        	resizeFrom: 'parent',
                        	heightCalculationMethod: 'documentElementOffset',
                        },
                        '#wlx-plugin-app'
                    );
				} );
			</script>
            ";
}

add_action( 'plugins_loaded', '__wl_google_organization_kp_load', 10, 0 );
