<?php

/**
 * Define a class that provides function related to the entity post type.
 * @since 3.6.0
 */
class Wordlift_Entity_Type_Service {

	/**
	 * The entity type slug.
	 *
	 * @since 3.6.0
	 * @access private
	 * @var string $slug The entity type slug.
	 */
	private $slug;

	/**
	 * A singleton instance of the entity type service.
	 *
	 * @since 3.6.0
	 * @access private
	 * @var Wordlift_Entity_Type_Service
	 */
	private static $instance;

	/**
	 * Create an entity type service instance.
	 *
	 * @since 3.6.0
	 *
	 * @param string $slug The entity type slug.
	 */
	public function __construct( $slug ) {

		$this->slug = $slug;

		self::$instance = $this;

	}

	/**
	 * Get the entity type service singleton instance.
	 *
	 * @since 3.6.0
	 *
	 * @return Wordlift_Entity_Type_Service The entity type service singleton instance.
	 */
	public static function get_instance() {

		return self::$instance;
	}

	/**
	 * Register the WordLift entity post type. This method is hooked to WordPress' init action.
	 *
	 * @since 3.6.0
	 */
	public function register() {

		$labels = array(
			'name'               => _x( 'Vocabulary', 'post type general name', 'wordlift' ),
			'singular_name'      => _x( 'Entity', 'post type singular name', 'wordlift' ),
			'add_new'            => _x( 'Add New Entity', 'entity', 'wordlift' ),
			'add_new_item'       => __( 'Add New Entity', 'wordlift' ),
			'edit_item'          => __( 'Edit Entity', 'wordlift' ),
			'new_item'           => __( 'New Entity', 'wordlift' ),
			'all_items'          => __( 'All Entities', 'wordlift' ),
			'view_item'          => __( 'View Entity', 'wordlift' ),
			'search_items'       => __( 'Search in Vocabulary', 'wordlift' ),
			'not_found'          => __( 'No entities found', 'wordlift' ),
			'not_found_in_trash' => __( 'No entities found in the Trash', 'wordlift' ),
			'parent_item_colon'  => '',
			'menu_name'          => __( 'Vocabulary', 'wordlift' )
		);

		$args = array(
			'labels'        => $labels,
			'description'   => 'Holds our vocabulary (set of entities) and entity specific data',
			'public'        => true,
			'menu_position' => 20, // after the pages menu.
			'supports'      => array( 'title', 'editor', 'thumbnail', 'excerpt', 'comments' ),
			'has_archive'   => true,
			'menu_icon'     => WP_CONTENT_URL . '/plugins/wordlift/images/svg/wl-vocabulary-icon.svg',
			'rewrite'       => array( 'slug' => $this->slug )
		);

		register_post_type( Wordlift_Entity_Service::TYPE_NAME, $args );

	}


}
