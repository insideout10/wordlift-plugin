<?php

/**
 * Provide custom taxonomy topic related services.
 *
 * @since 3.6.0
 */
class Wordlift_Topic_Taxonomy_Service {

	/**
	 * The Log service.
	 *
	 * @since 3.6.0
	 * @access private
	 * @var \Wordlift_Log_Service $log_service The Log service.
	 */
	private $log_service;

	/**
	 * Taxonomy name.
	 *
	 * @since 3.6.0
	 */
	const TAXONOMY_NAME = 'wl_topic';

	/**
	 * Taxonomy object type.
	 *
	 * @since 3.6.0
	 */
	const TAXONOMY_OBJECT_TYPE = 'post';

	/**
	 * Taxonomy slug.
	 *
	 * @since 3.6.0
	 */
	const TAXONOMY_SLUG = 'topic';

	/**
	 * A singleton instance of the Wordlift_Topic_Taxonomy_Service service.
	 *
	 * @since 3.6.0
	 * @access private
	 * @var \Wordlift_Topic_Taxonomy_Service $instance A singleton instance of Wordlift_Topic_Taxonomy_Service.
	 */
	private static $instance;

	/**
	 * Create a Wordlift_Topic_Taxonomy_Service instance.
	 *
	 * @since 3.6.0
	 *
	 */
	public function __construct() {

		$this->log_service = Wordlift_Log_Service::get_logger( 'Wordlift_Entity_Service' );

		// Set the singleton instance.
		self::$instance = $this;

	}

	/**
	 * Get the singleton instance of the Entity service.
	 *
	 * @since 3.6.0
	 * @return \Wordlift_Entity_Service The singleton instance of the Entity service.
	 */
	public static function get_instance() {

		return self::$instance;
	}


	/**
	 * Just register the topic taxonomy.
	 * 
	 * @since 3.6.0
	 *
	 */
	public function init() {

		// See https://codex.wordpress.org/Function_Reference/register_taxonomy
		$labels = array(
			'name'              => _x( 'Topics', 'taxonomy general name' ),
			'singular_name'     => _x( 'Topic', 'taxonomy singular name' ),
			'search_items'      => __( 'Search Topics' ),
			'all_items'         => __( 'All Topics' ),
			'parent_item'       => __( 'Parent Topic' ),
			'parent_item_colon' => __( 'Parent Topic:' ),
			'edit_item'         => __( 'Edit Topic' ),
			'update_item'       => __( 'Update Topic' ),
			'add_new_item'      => __( 'Add New Topic' ),
			'new_item_name'     => __( 'New Topic' ),
			'menu_name'         => __( 'Topics' ),
		);

		$capabilities = array(
			'manage_terms' => null,
			'edit_terms'   => null,
			'delete_terms' => null,
			'assign_terms' => 'edit_posts'
		);

		$args = array(
			'labels'            => $labels,
			'capabilities'      => $capabilities,
			'hierarchical'      => true,
			'show_admin_column' => true,
			'show_ui'			=> false,
			'rewrite'			=> array(
				'slug'	=> self::TAXONOMY_SLUG
				)
		);

		// Register taxonomy
		register_taxonomy( 
			self::TAXONOMY_NAME, self::TAXONOMY_OBJECT_TYPE, $args 
		);

	}

}
