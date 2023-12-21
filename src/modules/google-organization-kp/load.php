<?php

<<<<<<< HEAD
=======
use Wordlift\Modules\Common\Symfony\Component\Config\FileLocator;
use Wordlift\Modules\Common\Symfony\Component\DependencyInjection\ContainerBuilder;
use Wordlift\Modules\Common\Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

>>>>>>> See #1717: Initial work on Google Organization KP API
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Bail out if the feature isn't enabled.
if ( ! apply_filters( 'wl_feature__enable__google-organization-kp', true ) ) { // phpcs:ignore WordPress.NamingConventions.ValidHookName.UseUnderscores
	return;
}
<<<<<<< HEAD
=======

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
>>>>>>> See #1717: Initial work on Google Organization KP API
