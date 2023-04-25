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

}

__wl_super_resolution__load();

// function my_custom_attachment_fields_to_edit( $form_fields, $post ) {
// Add your custom HTML code here
// $form_fields['my_custom_field'] = array(
// 'label' => 'My Custom Field',
// 'input' => 'html',
// 'html'  => '<p>My custom HTML code goes here</p>',
// );
//
// return $form_fields;
// }
// add_filter( 'attachment_fields_to_edit', 'my_custom_attachment_fields_to_edit', 10, 2 );
