<?php
/**
 * Module Name: Gardening Kg
 *
 * @since   1.0.0
 * @package wordlift
 */

use Wordlift\Modules\Common\Symfony\Component\Config\FileLocator;
use Wordlift\Modules\Common\Symfony\Component\DependencyInjection\ContainerBuilder;
use Wordlift\Modules\Common\Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Wordlift\Modules\Gardening_Kg\Jsonld;
use Wordlift\Modules\Gardening_Kg\Main_Entity\Gardening_Kg_Post_Stats;
use Wordlift\Modules\Gardening_Kg\Preconditions;
use Wordlift\Modules\Gardening_Kg\Term_Entity\Gardening_Kg_Term_Stats;

// if ( ! defined( 'ABSPATH' ) ) {
// exit;
// }

/**
 * Load Include Exclude Module.
 *
 * @return void
 */
function __wl_gardening_kg__load() {

	// Autoloader for plugin itself.
	if ( file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
		require_once __DIR__ . '/vendor/autoload.php';
	}

	$container_builder = new ContainerBuilder();
	$loader            = new YamlFileLoader( $container_builder, new FileLocator( __DIR__ ) );
	$loader->load( 'services.yml' );
	$container_builder->compile();

	/**
	 * @var Preconditions $preconditions
	 */
	$preconditions = $container_builder->get( 'Wordlift\Modules\Gardening_Kg\Preconditions' );
	if ( ! $preconditions->pass() ) {
		return;
	}

	// Get the runners
	$main_entity_runner = $container_builder->get( 'Wordlift\Modules\Gardening_Kg\Main_Entity\Gardening_Kg_Main_Entity_Runner' );
	$term_entity_runner = $container_builder->get( 'Wordlift\Modules\Gardening_Kg\Term_Entity\Gardening_Kg_Term_Entity_Runner' );

	// Add the runners, this is called by the Dashboard Synchronization.
	add_filter(
		'wl_dashboard__synchronization__runners',
		function ( $runners ) use ( $main_entity_runner, $term_entity_runner ) {
			$runners[] = $term_entity_runner;
			$runners[] = $main_entity_runner;

			return $runners;
		}
	);

	/**
	 * @var Gardening_Kg_Post_Stats $post_stats
	 */
	$post_stats = $container_builder->get( Gardening_Kg_Post_Stats::class );
	$post_stats->register_hooks();

	/**
	 * @var Gardening_Kg_Term_Stats $term_stats
	 */
	$term_stats = $container_builder->get( Gardening_Kg_Term_Stats::class );
	$term_stats->register_hooks();

	$jsonld_hooks = $container_builder->get( Jsonld::class );
	$jsonld_hooks->register_hooks();

}

__wl_gardening_kg__load();
