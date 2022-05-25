<?php
/**
 * Module Name: Food KG
 * Description: Lifts ingredients using semantic data from the Food KG
 * Experimental: Yes
 *
 * @since   1.0.0
 * @package wordlift
 */

use Wordlift\Api\Api_Service_Ext;
use Wordlift\Api\Default_Api_Service;
use Wordlift\Api\Network;
use Wordlift\Modules\Food_Kg\Module;
use Wordlift\Modules\Food_Kg\Preconditions;
use Wordlift\Modules\Food_Kg_Dependencies\Symfony\Component\Config\FileLocator;
use Wordlift\Modules\Food_Kg_Dependencies\Symfony\Component\DependencyInjection\ContainerBuilder;
use Wordlift\Modules\Food_Kg_Dependencies\Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'WL_FOOD_KG_FILE', __FILE__ );
define( 'WL_FOOD_KG_DIR_PATH', dirname( WL_FOOD_KG_FILE ) );

// Autoloader for dependencies.
if ( file_exists( WL_FOOD_KG_DIR_PATH . '/third-party/vendor/scoper-autoload.php' ) ) {
	require WL_FOOD_KG_DIR_PATH . '/third-party/vendor/scoper-autoload.php';
}

// Autoloader for plugin itself.
if ( file_exists( WL_FOOD_KG_DIR_PATH . '/includes/vendor/autoload.php' ) ) {
	require WL_FOOD_KG_DIR_PATH . '/includes/vendor/autoload.php';
}

add_action( 'plugin_loaded', '__wlp__food_kg__plugin_loaded' );

function __wlp__food_kg__plugin_loaded() {
	$container_builder = new ContainerBuilder();
	$loader            = new YamlFileLoader( $container_builder, new FileLocator( __DIR__ ) );
	$loader->load( 'services.yml' );

	/**
	 * @var Preconditions $preconditions
	 */
	$preconditions = $container_builder->get( 'Wordlift\Modules\Food_Kg\Preconditions' );
	if ( ! $preconditions->pass() ) {
		return;
	}

	/**
	 * @var Module $module
	 */
	$module = $container_builder->get( 'Wordlift\Modules\Food_Kg\Module' );
	$module->register_hooks();

	$jsonld = $container_builder->get( 'Wordlift\Modules\Food_Kg\Jsonld' );
	$jsonld->register_hooks();

	$notices = $container_builder->get( 'Wordlift\Modules\Food_Kg\Notices' );
	$notices->register_hooks();
}

/**
 * Hooks:
 *
 */

//$plugin = new Plugin();

/**
 * @var $api_service Api_Service_Ext
 */


/**
 * Can we enable this module?
 */
function __wlp__food_kg__is_enabled() {
	$api_service = Default_Api_Service::get_instance();

	try {
		$me_response = $api_service->me();

		return array_reduce( $me_response->networks, '__wlp__food_kg__is_enabled__has_food_kg', false );
	} catch ( Exception $e ) {
		return false;
	}
}

/**
 * @param $carry bool
 * @param $item Network
 *
 * @return bool
 */
function __wlp__food_kg__is_enabled__has_food_kg( $carry, $item ) {
	return $carry || 'https://knowledge.cafemedia.com/food/' === $item->dataset_uri;
}

/**
 * Schedule a daily refresh of ingredients.
 */

/**
 * Allow a manual refresh of ingredients.
 */

