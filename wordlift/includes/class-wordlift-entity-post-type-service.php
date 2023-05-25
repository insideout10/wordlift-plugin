<?php
/**
 * Services: Entity Post Type Service.
 *
 * Define the Entity Post Type service class.
 *
 * @since   3.6.0
 * @package Wordlift
 */

/**
 * Define a class that provides function related to the entity post type.
 *
 * @since   3.6.0
 * @package Wordlift
 */
class Wordlift_Entity_Post_Type_Service {

	/**
	 * The entity post type.
	 *
	 * @since  3.6.0
	 * @access private
	 * @var string $post_type The entity post type.
	 */
	private $post_type;

	/**
	 * The entity type slug.
	 *
	 * @since  3.6.0
	 * @access private
	 * @var string $slug The entity type slug.
	 */
	private $slug;

	/**
	 * A {@link Wordlift_Log_Service} instance.
	 *
	 * @since  3.16.3
	 * @access private
	 * @var \Wordlift_Log_Service $log A {@link Wordlift_Log_Service} instance.
	 */
	private $log;

	/**
	 * A singleton instance of the entity type service.
	 *
	 * @since  3.6.0
	 * @access private
	 * @var Wordlift_Entity_Post_Type_Service
	 */
	private static $instance;

	/**
	 * Create an entity type service instance.
	 *
	 * @param string $post_type The post type, e.g. entity.
	 * @param string $slug The entity type slug, if the slug is empty, the default slug will be used.
	 *
	 * @since 3.6.0
	 */
	public function __construct( $post_type, $slug ) {

		$this->log = Wordlift_Log_Service::get_logger( get_class() );

		$this->post_type = $post_type;

		// We cannot assign an empty slug to the register_post_type function, therefore if the slug is empty we default
		// to the type name.
		$this->slug = $slug ? $slug : $post_type;

		self::$instance = $this;

	}

	/**
	 * Get the entity type service singleton instance.
	 *
	 * @return Wordlift_Entity_Post_Type_Service The entity type service singleton instance.
	 * @since 3.6.0
	 */
	public static function get_instance() {

		return self::$instance;
	}

	/**
	 * Get the entity type slug.
	 *
	 * @return string The entity type slug.
	 * @since 3.6.0
	 */
	public function get_slug() {

		return $this->slug;
	}

	/**
	 * Get the entity post type name.
	 *
	 * @return string The entity post type.
	 * @since 3.6.0
	 */
	public function get_post_type() {

		return $this->post_type;
	}

	/**
	 * Register the WordLift entity post type. This method is hooked to WordPress' init action.
	 *
	 * @since 3.6.0
	 */
	public function register() {

		$this->log->debug( "Registering post type $this->post_type..." );

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
			'menu_name'          => __( 'Vocabulary', 'wordlift' ),
		);

		$args = array(
			'labels'          => $labels,
			'description'     => 'Holds our vocabulary (set of entities) and entity specific data',
			'public'          => true,
			'menu_position'   => 20,
			// after the pages menu.
			// Add support for 'authors' and 'revisions':
			// * see https://github.com/insideout10/wordlift-plugin/issues/395
			// * see https://github.com/insideout10/wordlift-plugin/issues/376
			'supports'        => array(
				'title',
				'editor',
				'thumbnail',
				'excerpt',
				'comments',
				'author',
				'revisions',
				'custom-fields',
			),
			'menu_icon'       => WP_CONTENT_URL . '/plugins/wordlift/images/svg/wl-vocabulary-icon.svg',
			// Although we define our slug here, we further manage linking to entities using the Wordlift_Entity_Link_Service.
			'rewrite'         => array( 'slug' => $this->slug ),
			'capability_type' => array(
				'wordlift_entity',
				'wordlift_entities',
			),
			'capabilities'    => array(
				'delete_posts' => 'delete_wordlift_entities',
			),
			'rest_base'       => 'entities',
			'show_in_rest'    => true,
			/**
			 * Filter: wl_feature__enable__vocabulary.
			 *
			 * @param bool whether the vocabulary needs to be shown in menu.
			 *
			 * @return bool
			 * @since 3.27.6
			 */
			'show_in_menu'    => apply_filters( 'wl_feature__enable__vocabulary', true ),
		);

		register_post_type( $this->post_type, $args );

		// Enable WP's standard `category` taxonomy for entities.
		//
		// While this enables editors to bind entities to the WP posts' category
		// taxonomy, in Wordlift_Category_Taxonomy_Service we also need to alter
		// WP's main category query to include the `entity` post type.
		//
		// See https://github.com/insideout10/wordlift-plugin/issues/442
		register_taxonomy_for_object_type( 'category', $this->post_type );

	}

}
