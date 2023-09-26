<?php
/**
 * Module Name: Pods
 * Description: Integrates pods framework for displaying schema.org fields
 * Experimental: Yes
 *
 * @since   1.0.0
 * @package wordlift
 */

use Wordlift\Modules\Common\Installer;
use Wordlift\Modules\Common\Symfony\Component\Config\FileLocator;
use Wordlift\Modules\Common\Symfony\Component\DependencyInjection\ContainerBuilder;
use Wordlift\Modules\Common\Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Wordlift\Modules\Pods\FieldDefinition\FieldDefinitionFactory;
use Wordlift\Modules\Pods\Notices;
use Wordlift\Modules\Pods\WlEntityField\Filters;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! apply_filters( 'wl_feature__enable__pods-integration', false ) ) { //phpcs:ignore WordPress.NamingConventions.ValidHookName.UseUnderscores
	return;
}

// Autoloader for plugin itself.
if ( file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
	require __DIR__ . '/vendor/autoload.php';
}

$container_builder = new ContainerBuilder();
$loader            = new YamlFileLoader( $container_builder, new FileLocator( __DIR__ ) );
$loader->load( 'services.yml' );
$container_builder->compile();

add_action(
	'plugins_loaded',
	function () use ( $container_builder ) {

		if ( ! defined( 'PODS_VERSION' ) ) {
			return;
		}

		$factory          = $container_builder->get( FieldDefinitionFactory::class );
		$field_definition = $factory->get_field_definition();
		$field_definition->register();

		/**
		 * @var $installer \Wordlift\Modules\Pods\Installer
		 */
		$installer = $container_builder->get( Installer::class );
		$installer->register_hooks();

		/**
		 * @var $notices \Wordlift\Modules\Pods\Notices
		 */
		$notices = $container_builder->get( Notices::class );
		$notices->register_hooks();

		$container_builder->get( Filters::class );

	}
);


