<?php
/**
 * Services: Mapping Service.
 *
 * @since 3.20.0
 * @package Wordlift
 * @subpackage Wordlift/includes/mapping
 */

/**
 * Define the Wordlift_Mapping_Service class.
 *
 * @since 3.20.0
 */
class Wordlift_Mapping_Service {

	/**
	 * The mapping's options.
	 *
	 * @since 3.20.0
	 * @access private
	 * @var array $options The mapping's options.
	 */
	private $options;

	/**
	 * Create a {@link Wordlift_Mapping_Service} instance.
	 *
	 * @since 3.20.0
	 */
	public function __construct() {

		// Load the options.
		$this->options = get_option( 'wl_mappings', array() );

		// Hook to `wl_valid_entity_post_types` and to `wl_default_entity_types_for_post_type`.
		add_filter( 'wl_valid_entity_post_types', array( $this, 'valid_entity_post_types', ), 9 );
		add_filter( 'wl_default_entity_types_for_post_type', array(
			$this,
			'default_entity_type_for_post_type',
		), 9, 2 );

	}

	private function save_options() {

		update_option( 'wl_mappings', $this->options, true );

	}

	/**
	 * Set the default entity types for a post type.
	 *
	 * @since 3.20.0
	 *
	 * @param string $post_type Post type.
	 * @param array  $entity_types An array of entity types slugs.
	 */
	public function set_entity_types_for_post_type( $post_type, $entity_types ) {

		$this->options[ $post_type ] = $entity_types;
		$this->save_options();

	}

	/**
	 * Hook to `wl_valid_entity_post_types` to declare schema.org support for the configured post types.
	 *
	 * @since 3.20.0
	 *
	 * @param array $post_types The default post types.
	 *
	 * @return array The supported post types.
	 */
	public function valid_entity_post_types( $post_types ) {

		return array_merge( $post_types, array_keys( $this->options ) );
	}

	/**
	 * Hook to `wl_default_entity_types_for_post_type` to declare the entity types for a post type.
	 *
	 * @since 3.20.0
	 *
	 * @param array  $default The default entity types.
	 * @param string $post_type The post type.
	 *
	 * @return array The default entity types.
	 */
	public function default_entity_types_for_post_type( $default, $post_type ) {

		return isset( $this->options[ $post_type ] ) ? $this->options[ $post_type ] : $default;
	}

}
