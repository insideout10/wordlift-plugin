<?php
/**
 * Taxonomies: Search Keyword Taxonomy.
 *
 * @since 3.20.0
 * @package Wordlift
 * @subpackage Wordlift/includes/search-keywords
 */

/**
 * Define the Wordlift_Search_Keyword_Taxonomy class.
 *
 * @since 3.20.0
 */
class Wordlift_Search_Keyword_Taxonomy {

	/**
	 * The taxonomy name.
	 *
	 * @since 3.20.0
	 */
	const TAXONOMY_NAME = 'wl_search_keywords';

	/**
	 * The {@link Wordlift_Api_Service} instance.
	 *
	 * @since 3.20.0
	 * @access private
	 * @var \Wordlift_Api_Service $api_service The {@link Wordlift_Api_Service} instance.
	 */
	private $api_service;

	/**
	 * The singleton instance.
	 *
	 * @since 3.20.0
	 * @access private
	 * @var \Wordlift_Search_Keyword_Taxonomy $instance The singleton instance.
	 */
	private static $instance;

	/**
	 * Create a Wordlift_Search_Keyword_Taxonomy instance.
	 *
	 * @param $api_service Wordlift_Api_Service WordLift's API Service.
	 *
	 * @since 3.20.0
	 */
	public function __construct( $api_service ) {

		$this->api_service = $api_service;

		// Register the taxonomy.
		add_action( 'init', array( $this, 'init' ) );

		// Catch new terms.
		add_action( 'created_' . self::TAXONOMY_NAME, array( $this, 'created' ) );

		// Delete terms.
		add_action( 'delete_' . self::TAXONOMY_NAME, array( $this, 'delete' ), 10, 3 );

		// Catch requests to list the taxonomy terms.
		add_filter( 'get_terms_defaults', array( $this, 'get_terms_defaults' ), 10, 2 );

		// Set the singleton instance.
		self::$instance = $this;

	}

	/**
	 * Get the singleton instance.
	 *
	 * @since 3.20.0
	 *
	 * @return \Wordlift_Search_Keyword_Taxonomy The singleton instance.
	 */
	public static function get_instance() {

		return self::$instance;
	}

	/**
	 * Register the taxonomy.
	 *
	 * @since 3.20.0
	 */
	public function init() {

		// Add new taxonomy, NOT hierarchical (like tags)
		$labels = array(
			'name'                       => _x( 'Search Keywords', 'taxonomy general name', 'wordlift' ),
			'singular_name'              => _x( 'Search Keyword', 'taxonomy singular name', 'wordlift' ),
			'search_items'               => __( 'Search Search Keywords', 'wordlift' ),
			'popular_items'              => __( 'Popular Search Keywords', 'wordlift' ),
			'all_items'                  => __( 'All Search Keywords', 'wordlift' ),
			'parent_item'                => null,
			'parent_item_colon'          => null,
			'edit_item'                  => __( 'Edit Search Keyword', 'wordlift' ),
			'update_item'                => __( 'Update Search Keyword', 'wordlift' ),
			'add_new_item'               => __( 'Add New Search Keyword', 'wordlift' ),
			'new_item_name'              => __( 'New Search Keyword Name', 'wordlift' ),
			'separate_items_with_commas' => __( 'Separate search keywords with commas', 'wordlift' ),
			'add_or_remove_items'        => __( 'Add or remove search keywords', 'wordlift' ),
			'choose_from_most_used'      => __( 'Choose from the most used search keywords', 'wordlift' ),
			'not_found'                  => __( 'No search keywords found.', 'wordlift' ),
			'menu_name'                  => __( 'Search Keywords', 'wordlift' ),
		);

		$args = array(
			'hierarchical'       => false,
			'labels'             => $labels,
			'show_ui'            => true,
			'show_tagcloud'      => false,
			'show_in_quick_edit' => true,
			'query_var'          => true,
			'rewrite'            => array( 'slug' => 'wl_search_keywords' ),
			'public'             => false,
		);

		register_taxonomy( self::TAXONOMY_NAME, null, $args );

	}

	/**
	 * Hook to the created_{taxonomy}.
	 *
	 * Synchronize the keywords with the remote keywords.
	 *
	 * @param int $term_id Term ID.
	 *
	 * @since 3.20.0
	 */
	public function created( $term_id ) {

		/** @var WP_Term $term */
		$term = get_term( $term_id );

		$this->api_service->post( 'keywords', array(
			'value' => $term->name,
		) );

	}

	/**
	 * Delete the term
	 *
	 * @since 3.20.0
	 *
	 * @param int   $term Term ID.
	 * @param int   $tt_id Term taxonomy ID.
	 * @param mixed $deleted_term Copy of the already-deleted term, in the form specified
	 *                              by the parent function. WP_Error otherwise.
	 */
	public function delete( $term, $tt_id, $deleted_term ) {

		$term_name = is_object( $deleted_term ) ? $deleted_term->name : $deleted_term['name'];

		$this->api_service->delete( 'keywords/' . rawurlencode( $term_name ) );

	}


	/**
	 * Refresh all the taxonomy terms.
	 *
	 * @since 3.20.0
	 *
	 * @param array $defaults An array of default get_terms() arguments.
	 * @param array $taxonomies An array of taxonomies.
	 *
	 * @return array The `$defaults` array, unchanged.
	 */
	public function get_terms_defaults( $defaults, $taxonomies ) {

		// Bail out if the request is not about our taxonomy.
		if ( ! is_array( $taxonomies ) || ! in_array( self::TAXONOMY_NAME, $taxonomies ) ) {
			return $defaults;
		}

		// Remove any potential loop.
		remove_filter( 'get_terms_defaults', array( $this, 'get_terms_defaults' ) );
		remove_action( 'created_' . self::TAXONOMY_NAME, array( $this, 'created' ) );
		remove_action( 'delete_' . self::TAXONOMY_NAME, array( $this, 'delete' ) );

		// Save all the keywords.
		$keywords = $this->api_service->get( 'keywords' );

		// Bail out if we received an error.
		if ( is_wp_error( $keywords ) ) {
			return $defaults;
		}

		// Get the local terms.
		$terms = get_terms( self::TAXONOMY_NAME, array( 'get' => 'all', ) );

		// Delete terms that do not exist any more.
		/** @var WP_Term $term */
		foreach ( $terms as $term ) {
			if ( ! in_array( $term->name, $keywords ) ) {
				wp_delete_term( $term->term_id, self::TAXONOMY_NAME );
			}
		}

		// Get the term name.
		$term_names = array_map( function ( $term ) {
			return $term->name;
		}, $terms );

		// Insert terms that do not exist.
		foreach ( $keywords as $keyword ) {
			if ( ! in_array( $keyword, $term_names ) ) {
				wp_insert_term( $keyword, self::TAXONOMY_NAME );
			}
		}

		// Add us back.
		add_action( 'delete_' . self::TAXONOMY_NAME, array( $this, 'delete' ), 10, 3 );
		add_action( 'created_' . self::TAXONOMY_NAME, array( $this, 'created' ) );
		add_filter( 'get_terms_defaults', array( $this, 'get_terms_defaults' ), 10, 2 );


		return $defaults;
	}

}
