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

// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
function my_custom_attachment_fields_to_edit( $form_fields, $post ) {
	// Add your custom HTML code here
	$form_fields['wl_super_resolution'] = array(
		'label'  => __( 'Super Resolution', 'wordlift' ),
		'input'  => 'custom',
		'html'   => '',
		'custom' => '<button onclick="tb_show(\'Your Modal Title\', \'https://example.org\', {\'class\': \'wl-super-resolution-modal\'})">Upsample</button>',
	);

	return $form_fields;
}

add_filter( 'attachment_fields_to_edit', 'my_custom_attachment_fields_to_edit', 10, 2 );

function enqueue_script_on_featured_image_screen( $hook ) {
	if ( 'post.php' === $hook && 'post' === get_post_type() ) {
		$screen = get_current_screen();
		if ( 'edit' !== $screen->base && 'post' === $screen->post_type ) {
			wp_enqueue_style( 'wl-super-resolution', WL_DIR_URL . 'modules/super-resolution/css/super-resolution.css', array( 'thickbox' ), WORDLIFT_VERSION, true );
		}
	}
}

add_action( 'admin_enqueue_scripts', 'enqueue_script_on_featured_image_screen' );
