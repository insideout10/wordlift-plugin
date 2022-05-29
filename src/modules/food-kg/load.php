<?php
/**
 * Module Name: Food KG
 * Description: Lifts ingredients using semantic data from the Food KG
 * Experimental: Yes
 *
 * @since   1.0.0
 * @package wordlift
 */

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

$module = $container_builder->get( 'Wordlift\Modules\Food_Kg\Module' );
$module->register_hooks();

$jsonld = $container_builder->get( 'Wordlift\Modules\Food_Kg\Jsonld' );
$jsonld->register_hooks();

$notices = $container_builder->get( 'Wordlift\Modules\Food_Kg\Notices' );
$notices->register_hooks();


/**
 * ######## ADMIN ########
 */
if ( is_admin() ) {
	$page = $container_builder->get( 'Wordlift\Modules\Food_Kg\Page' );
	$page->register_hooks();
}
