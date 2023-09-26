<?php
/**
 * Module Name: Super Resolution
 * Experimental: Yes
 *
 * @since   1.0.0
 * @package wordlift
 */

use Wordlift\Modules\Common\Symfony\Component\Config\FileLocator;
use Wordlift\Modules\Common\Symfony\Component\DependencyInjection\ContainerBuilder;
use Wordlift\Modules\Common\Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Wordlift\Modules\Super_Resolution\Attachment_Field;
use Wordlift\Modules\Super_Resolution\Super_Resolution_Controller;
use Wordlift\Modules\Super_Resolution\Yoast_Markup;

/**
 * Load Include Exclude Module.
 *
 * @return void
 */
function __wl_super_resolution__load() {

	// phpcs:ignore WordPress.NamingConventions.ValidHookName.UseUnderscores
	if ( ! apply_filters( 'wl_feature__enable__super-resolution', false ) ) {
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

	/** @var Attachment_Field $attachment_field */
	$attachment_field = $container_builder->get( 'Wordlift\Modules\Super_Resolution\Attachment_Field' );
	$attachment_field->register_hooks();

	/** @var Super_Resolution_Controller $controller */
	$controller = $container_builder->get( 'Wordlift\Modules\Super_Resolution\Super_Resolution_Controller' );
	$controller->register_hooks();

	/** @var Yoast_Markup $yoast_markup */
	$yoast_markup = $container_builder->get( 'Wordlift\Modules\Super_Resolution\Yoast_Markup' );
	$yoast_markup->register_hooks();

}

__wl_super_resolution__load();
