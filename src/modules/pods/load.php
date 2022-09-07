<?php
/**
 * Module Name: Pods
 * Description: Integrates pods framework for displaying schema.org fields
 * Experimental: Yes
 *
 * @since   1.0.0
 * @package wordlift
 */

use Wordlift\Modules\Common\Symfony\Component\Config\FileLocator;
use Wordlift\Modules\Common\Symfony\Component\DependencyInjection\ContainerBuilder;
use Wordlift\Modules\Common\Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Wordlift\Modules\Pods\Definition;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! defined( 'PODS_VERSION' ) ) {
	return;
}


function __wl_pods_load() {
	// Autoloader for plugin itself.
	if ( file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
		require __DIR__ . '/vendor/autoload.php';
	}

	$container_builder = new ContainerBuilder();
	$loader            = new YamlFileLoader( $container_builder, new FileLocator( __DIR__ ) );
	$loader->load( 'services.yml' );
	$container_builder->compile();
	pods_register_related_object( 'wlentity', 'WordLift Entity', array( 'simple' => false ) );
}

add_filter(
	'pods_form_ui_field_pick_ajax',
	function ( $item, $name, $value, $field_options ) {
		return true;

		return isset( $field_options['pick_object'] ) && is_string( $field_options['pick_object'] ) &&
		   'wlentity' === $field_options['pick_object'];
	},
	10,
	4
);

add_filter(
	'pods_api_get_table_info',
	function ( $info, $object_type, $object, $name, $pod, $field ) {
		// @TODO: filter based on field here.
		return array( 'foo' => 'bar' );
	},
	10,
	6
);

add_filter(
	'pods_field_pick_data_ajax',
	function ( $data, $name, $_, $field ) {

		if ( ( ! $field instanceof Pods\Whatsit\Field ) || $field->get_arg( 'pick_object', false ) !== 'wlentity' ) {
			return $data;
		}

		$query    = sanitize_text_field( wp_unslash( $_REQUEST['query'] ) );
		$entities = wl_entity_get_by_title( $query, true, true, 10 );

		return array_map(
			function ( $item ) {

				/**
				 * @var $item \WP_Post
				 */
				return array(
					'id'       => $item->id,
					'icon'     => 'https:\/\/wordlift.localhost\/wp-content\/plugins\/wordlift\/images\/svg\/wl-vocabulary-icon.svg',
					'name'     => $item->title . ' (' . $item->schema_type_name . ')',
					//          'edit_link' => get_edit_post_link( $item->ID ),
					//          'link'      => get_permalink( $item->ID ),
					'selected' => false,
				);
			},
			$entities
		);

	},
	10,
	4
);


add_action( 'plugins_loaded', '__wl_pods_load' );


