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
	 * The {@link Wordlift_Entity_Type_Service} instance.
	 *
	 * @since 3.20.0
	 * @access private
	 * @var \Wordlift_Entity_Type_Service $entity_type_service The {@link Wordlift_Entity_Type_Service} instance.
	 */
	private $entity_type_service;

	/**
	 * The singleton instance.
	 *
	 * @since 3.20.0
	 * @access private
	 * @var \Wordlift_Mapping_Service $instance The singleton instance.
	 */
	private static $instance;

	/**
	 * Create a {@link Wordlift_Mapping_Service} instance.
	 *
	 * @since 3.20.0
	 *
	 * @param \Wordlift_Entity_Type_Service $entity_type_service The {@link Wordlift_Entity_Type_Service} instance.
	 */
	public function __construct( $entity_type_service ) {

		// Set the entity type service instance.
		$this->entity_type_service = $entity_type_service;

		// Load the options.
		$this->options = get_option( 'wl_mappings', array() );

		// Hook to `wl_valid_entity_post_types` and to `wl_default_entity_types_for_post_type`.
		add_filter( 'wl_valid_entity_post_types', array( $this, 'valid_entity_post_types' ), 9 );
		add_filter(
			'wl_default_entity_types_for_post_type',
			array(
				$this,
				'default_entity_types_for_post_type',
			),
			9,
			2
		);

		// Set the singleton instance.
		self::$instance = $this;

	}

	/**
	 * Get the singleton instance.
	 *
	 * @since 3.20.0
	 *
	 * @return \Wordlift_Mapping_Service The singleton instance.
	 */
	public static function get_instance() {

		return self::$instance;
	}

	/**
	 * Save the options.
	 *
	 * @since 3.20.0
	 */
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

	/**
	 * Update the post type with the entity types, starting at the specified offset.
	 *
	 * @since 3.20.0
	 *
	 * @param string $post_type The post type.
	 * @param array  $entity_types The entity types.
	 * @param int    $offset The offset (0 by default).
	 *
	 * @return array {
	 * The result array.
	 *
	 * @type int     $current The current offset.
	 * @type int     $next The next offset.
	 * @type int     $count The total element count.
	 * }
	 */
	public function update( $post_type, $entity_types, $offset = 0 ) {

		$entity_type_service = $this->entity_type_service;
		$tax_query           = $this->get_tax_query( $entity_types );

		return Wordlift_Batch_Action::process(
			$post_type,
			$offset,
			$tax_query,
			function ( $post_id ) use ( $entity_type_service, $entity_types ) {
				foreach ( $entity_types as $entity_type ) {
					$entity_type_service->set( $post_id, $entity_type, false );
				}
			}
		);
	}

	/**
	 * Count the number of posts that need to be assigned with the entity types.
	 *
	 * @since 3.20.0
	 *
	 * @param string $post_type The post type.
	 * @param array  $entity_types An array of entity types.
	 *
	 * @return int The number of posts to be assigned with entity types.
	 */
	public function count( $post_type, $entity_types ) {

		$tax_query = $this->get_tax_query( $entity_types );

		return Wordlift_Batch_Action::count( $post_type, $tax_query );
	}

	/**
	 * Get the taxonomy query for the specified entity types.
	 *
	 * @since 3.20.0
	 *
	 * @param array $entity_types The entity types.
	 *
	 * @return array The tax query.
	 */
	private function get_tax_query( $entity_types ) {

		$entity_type_service = $this->entity_type_service;
		$entity_types_terms  = array_filter(
			array_map(
				function ( $item ) use ( $entity_type_service ) {
					return $entity_type_service->get_term_by_uri( $item );
				},
				$entity_types
			)
		);

		$entity_types_terms_ids = array_map(
			function ( $term ) {
				return $term->term_id;
			},
			$entity_types_terms
		);

		$tax_query = array(
			'tax_query' => array(
				array(
					'taxonomy' => Wordlift_Entity_Type_Taxonomy_Service::TAXONOMY_NAME,
					'field'    => 'term_id',
					'terms'    => $entity_types_terms_ids,
					'operator' => 'NOT IN',
				),
			),
		);

		return $tax_query;
	}

}
