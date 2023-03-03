<?php
/**
 * Module Name: Dashboard
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

/**
 * Load Include Exclude Module.
 *
 * @return void
 */
function __wl_dashboard__load() {

	// Autoloader for plugin itself.
	if ( file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
		require_once __DIR__ . '/vendor/autoload.php';
	}

	$container_builder = new ContainerBuilder();
	$loader            = new YamlFileLoader( $container_builder, new FileLocator( __DIR__ ) );
	$loader->load( 'services.yml' );
	$container_builder->compile();

}

function __wl_dashboard__main() {
	?>
	Hello World!
	<?php
}

__wl_dashboard__load();
