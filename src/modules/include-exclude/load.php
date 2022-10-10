<?php
/**
 * Module Name: Include Exclude Module
 * Description: Lets you exclude or include selective URLs from WordLift's JSON-LD
 * Experimental: Yes
 *
 * @since   1.0.0
 * @package wordlift
 */

use Wordlift\Modules\Common\Symfony\Component\Config\FileLocator;
use Wordlift\Modules\Common\Symfony\Component\DependencyInjection\ContainerBuilder;
use Wordlift\Modules\Common\Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! apply_filters( 'wl_feature__enable__food-kg', false ) ) { //phpcs:ignore WordPress.NamingConventions.ValidHookName.UseUnderscores
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
		$settings = $container_builder->get( 'WordLift\Modules\Include_Exclude\Admin\Settings' );
		$settings->register_hooks();

		$container_builder->get( 'WordLift\Modules\Include_Exclude\Plugin_Enabled' );
	}
);
