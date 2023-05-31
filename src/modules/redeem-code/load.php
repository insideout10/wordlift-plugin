<?php
/**
 * Module Name: Redeem Code
 * Experimental: Yes
 *
 * @since   3.46.0
 * @package wordlift
 */

use Wordlift\Modules\Common\Symfony\Component\Config\FileLocator;
use Wordlift\Modules\Common\Symfony\Component\DependencyInjection\ContainerBuilder;
use Wordlift\Modules\Common\Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

/**
 * Load Include Exclude Module.
 *
 * @return void
 */
function __wl_redeem_code() {

	// Autoloader for plugin itself.
	if ( file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
		require_once __DIR__ . '/vendor/autoload.php';
	}

	$container_builder = new ContainerBuilder();
	$loader            = new YamlFileLoader( $container_builder, new FileLocator( __DIR__ ) );
	$loader->load( 'services.yml' );
	$container_builder->compile();

	// ** @var Attachment_Field $attachment_field */
	// $attachment_field = $container_builder->get( 'Wordlift\Modules\Super_Resolution\Attachment_Field' );
	// $attachment_field->register_hooks();
}

__wl_redeem_code();
