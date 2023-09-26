<?php
/**
 * Module Name: Events
 * Description: Events listener for WordPress
 * Experimental: Yes
 *
 * @since   1.0.0
 * @package wordlift
 */

use Wordlift\Modules\Common\Symfony\Component\Config\FileLocator;
use Wordlift\Modules\Common\Symfony\Component\DependencyInjection\ContainerBuilder;
use Wordlift\Modules\Common\Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Wordlift\Modules\Events\Options_Entity\Events_Options_Entity_Include_Exclude;
use Wordlift\Modules\Events\Post_Entity\Events_Post_Entity_Jsonld;
use Wordlift\Modules\Events\Term_Entity\Events_Term_Entity_Jsonld;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Events  load.
 *
 * This function loads necessary resources for the plugin and
 * initializes services based on the 'services.yml' configuration.
 * It also registers hooks for Post, Term, and Include/Exclude entities.
 *
 * @throws Exception if there are issues during the service initialization.
 */
function __wl_events__load() {

	// Dashboard is available only for Food Kg and Gardening Kg atm
	// phpcs:ignore WordPress.NamingConventions.ValidHookName.UseUnderscores
	if ( ! apply_filters( 'wl_feature__enable__kpi-events', false ) ) {
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

	/**
	 * @var $post_entity Events_Post_Entity_Jsonld
	 */
	$post_entity = $container_builder->get( Events_Post_Entity_Jsonld::class );
	$post_entity->register_hooks();

	/**
	 * @var $term_entity Events_Term_Entity_Jsonld
	 */
	$term_entity = $container_builder->get( Events_Term_Entity_Jsonld::class );
	$term_entity->register_hooks();

	/**
	 * @var $include_exclude Events_Options_Entity_Include_Exclude
	 */
	$include_exclude = $container_builder->get( Events_Options_Entity_Include_Exclude::class );
	$include_exclude->register_hooks();
}

add_action( 'plugins_loaded', '__wl_events__load' );
