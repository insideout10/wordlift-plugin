<?php

/**
 * Provide custom taxonomy topic related services.
 *
 * @since 3.5.0
 */
class Wordlift_Topic_Taxonomy_Service {

	/**
	 * The Log service.
	 *
	 * @since 3.5.0
	 * @access private
	 * @var \Wordlift_Log_Service $log_service The Log service.
	 */
	private $log_service;

	/**
	 * Taxonomy name.
	 *
	 * @since 3.5.0
	 */
	const TAXONOMY_NAME = 'wl_topic';

	/**
	 * Taxonomy object type.
	 *
	 * @since 3.5.0
	 */
	const TAXONOMY_OBJECT_TYPE = 'post';

	/**
	 * Taxonomy slug.
	 *
	 * @since 3.5.0
	 */
	const TAXONOMY_SLUG = 'wl_topic';

	/**
	 * A singleton instance of the Wordlift_Topic_Taxonomy_Service service.
	 *
	 * @since 3.5.0
	 * @access private
	 * @var \Wordlift_Topic_Taxonomy_Service $instance A singleton instance of Wordlift_Topic_Taxonomy_Service.
	 */
	private static $instance;

	/**
	 * Create a Wordlift_Topic_Taxonomy_Service instance.
	 *
	 * @since 3.5.0
	 */
	public function __construct() {

		$this->log_service = Wordlift_Log_Service::get_logger( 'Wordlift_Entity_Service' );

		// Set the singleton instance.
		self::$instance = $this;

	}

	/**
	 * Get the singleton instance of the Entity service.
	 *
	 * @since 3.5.0
	 * @return Wordlift_Topic_Taxonomy_Service
	 */
	public static function get_instance() {

		return self::$instance;
	}

	/**
	 * Just register the topic taxonomy.
	 *
	 * @since 3.5.0
	 */
	public function init() {

		// See https://codex.wordpress.org/Function_Reference/register_taxonomy
		$labels = array(
			'name'              => _x( 'Topics', 'taxonomy general name', 'wordlift' ),
			'singular_name'     => _x( 'Topic', 'taxonomy singular name', 'wordlift' ),
			'search_items'      => __( 'Search Topics', 'wordlift' ),
			'all_items'         => __( 'All Topics', 'wordlift' ),
			'parent_item'       => __( 'Parent Topic', 'wordlift' ),
			'parent_item_colon' => __( 'Parent Topic:', 'wordlift' ),
			'edit_item'         => __( 'Edit Topic', 'wordlift' ),
			'update_item'       => __( 'Update Topic', 'wordlift' ),
			'add_new_item'      => __( 'Add New Topic', 'wordlift' ),
			'new_item_name'     => __( 'New Topic', 'wordlift' ),
			'menu_name'         => __( 'Topics', 'wordlift' ),
		);

		$capabilities = array(
			'manage_terms' => null,
			'edit_terms'   => null,
			'delete_terms' => null,
			'assign_terms' => 'edit_posts',
		);

		$args = array(
			'labels'            => $labels,
			'capabilities'      => $capabilities,
			'hierarchical'      => true,
			'show_admin_column' => false,
			'show_ui'           => false,
			'rewrite'           => array(
				'slug' => self::TAXONOMY_SLUG,
			),
		);

		// Register taxonomy
		register_taxonomy(
			self::TAXONOMY_NAME,
			self::TAXONOMY_OBJECT_TYPE,
			$args
		);

	}

	/**
	 * Get or create a taxonomy term from a given entity topic.
	 *
	 * @since 3.5.0
	 */
	public function get_or_create_term_from_topic_entity( $topic ) {

		// Define taxonomy term slug
		$term_slug = sanitize_title( $topic->post_title );
		// Look for an existing taxonomy term with a given slug
		$term = get_term_by( 'slug', $term_slug, self::TAXONOMY_NAME );
		if ( $term ) {
			return (int) $term->term_id;
		}
		// Otherwise create a new term and return it
		$result = wp_insert_term(
			$topic->post_title,
			self::TAXONOMY_NAME,
			array(
				'slug'        => $term_slug,
				'description' => $topic->post_content,
			)
		);

		return (int) $result['term_id'];
	}

	/**
	 * Set a topic for a given post.
	 *
	 * @since 3.5.0
	 */
	public function set_topic_for( $post_id, $topic_id ) {
		// Retrieve the topic entity post
		$topic_entity_post = get_post( $topic_id );
		// If current topic does not exist in db false is returned
		if ( null === $topic_entity_post ) {
			return false;
		}
		// Create the proper taxonomy term if needed
		$term_id = $this->get_or_create_term_from_topic_entity( $topic_entity_post );
		// Link the term to the current post
		wp_set_post_terms( $post_id, $term_id, self::TAXONOMY_NAME, false );
		return true;
	}

	/**
	 * Unlink any topic for a given post.
	 *
	 * @since 3.5.0
	 */
	public function unlink_topic_for( $post_id ) {
		wp_delete_object_term_relationships( $post_id, self::TAXONOMY_NAME );
	}

}
