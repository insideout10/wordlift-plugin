<?php
/**
 * Module Name: Food KG
 * Description: Lifts ingredients using semantic data from the Food KG
 * Experimental: Yes
 *
 * @since   1.0.0
 * @package wordlift
 */

use Wordlift\Modules\Common\Symfony\Component\Config\FileLocator;
use Wordlift\Modules\Common\Symfony\Component\DependencyInjection\ContainerBuilder;
use Wordlift\Modules\Common\Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Wordlift\Modules\Food_Kg\Jsonld;
use Wordlift\Modules\Food_Kg\Main_Entity\Food_Kg_Recipe_Stats;
use Wordlift\Modules\Food_Kg\Main_Ingredient_Jsonld;
use Wordlift\Modules\Food_Kg\Preconditions;
use Wordlift\Modules\Food_Kg\Term_Entity\Food_Kg_Ingredient_Stats;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'WL_FOOD_KG_FILE', __FILE__ );
define( 'WL_FOOD_KG_DIR_PATH', dirname( WL_FOOD_KG_FILE ) );

function __wl_foodkg__load() {

	// Autoloader for plugin itself.
	if ( file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
		require __DIR__ . '/vendor/autoload.php';
	}

	$container_builder = new ContainerBuilder();
	$loader            = new YamlFileLoader( $container_builder, new FileLocator( __DIR__ ) );
	$loader->load( 'services.yml' );
	$container_builder->compile();

	$notices = $container_builder->get( 'Wordlift\Modules\Food_Kg\Notices' );
	$notices->register_hooks();

	/**
	 * @var Preconditions $preconditions
	 */
	$preconditions = $container_builder->get( 'Wordlift\Modules\Food_Kg\Preconditions' );
	if ( ! $preconditions->pass() ) {
		return;
	}

	// Meta Box.
	$meta_box = $container_builder->get( 'Wordlift\Modules\Food_Kg\Admin\Meta_Box' );
	$meta_box->register_hooks();

	$module = $container_builder->get( 'Wordlift\Modules\Food_Kg\Module' );
	$module->register_hooks();

	/** @var Jsonld $jsonld */
	$jsonld = $container_builder->get( 'Wordlift\Modules\Food_Kg\Jsonld' );
	$jsonld->register_hooks();

	/** @var Main_Ingredient_Jsonld $jsonld */
	$main_ingredient_jsonld = $container_builder->get( 'Wordlift\Modules\Food_Kg\Main_Ingredient_Jsonld' );
	$main_ingredient_jsonld->register_hooks();

	// Get the runners
	$main_entity_runner = $container_builder->get( 'Wordlift\Modules\Food_Kg\Main_Entity\Food_Kg_Main_Entity_Runner' );
	$term_entity_runner = $container_builder->get( 'Wordlift\Modules\Food_Kg\Term_Entity\Food_Kg_Term_Entity_Runner' );

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
	 * @var $recipe_stats Food_Kg_Recipe_Stats
	 */
	$recipe_stats = $container_builder->get( Food_Kg_Recipe_Stats::class );
	$recipe_stats->register_hooks();

	/**
	 * @var $recipe_stats Food_Kg_Ingredient_Stats
	 */
	$recipe_stats = $container_builder->get( Food_Kg_Ingredient_Stats::class );
	$recipe_stats->register_hooks();

}

add_action( 'plugins_loaded', '__wl_foodkg__load' );

