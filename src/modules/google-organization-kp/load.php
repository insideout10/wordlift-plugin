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
}

__wl_google_organization_kp_load();
