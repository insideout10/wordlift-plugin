<?php
/**
 * Services: Entity Uri Service
 *
 * Provides access to entities' URIs, i.e. URIs stored as `entity_url` (the main
 * entity item ID) or as `same_as`.
 *
 * @since      3.16.3
 * @package    Wordlift
 * @subpackage Wordlift/includes
 */

use Wordlift\Content\Wordpress\Wordpress_Content_Id;
use Wordlift\Content\Wordpress\Wordpress_Content_Service;

/**
 * Define the {@link Wordlift_Entity_Uri_Service} class.
 *
 * @since 3.16.3
 */
class Wordlift_Entity_Uri_Service {

	/**
	 * A {@link Wordlift_Log_Service} instance.
	 *
	 * @since  3.16.3
	 * @access private
	 * @var \Wordlift_Log_Service $log A {@link Wordlift_Log_Service} instance.
	 */
	private $log;

	/**
	 * An array of URIs to post ID valid for the current request.
	 *
	 * @since  3.16.3
	 * @access private
	 * @var array $uri_to_post An array of URIs to post ID valid for the current request.
	 */
	protected $uri_to_post;

	/**
	 * Create a {@link Wordlift_Entity_Uri_Service} instance.
	 *
	 * @since 3.16.3
	 */
	protected function __construct() {

		$this->log = Wordlift_Log_Service::get_logger( get_class() );

		// Add a filter to the `rest_post_dispatch` filter to add the wl_entity_url meta as `wl:entity_url`.
		add_filter( 'rest_post_dispatch', array( $this, 'rest_post_dispatch' ) );
		add_filter( 'wl_content_service__post__not_found', array( $this, 'content_service__post__not_found' ), 10, 2 );

	}

	/**
	 * Holds the {@link Wordlift_Entity_Uri_Service} instance.
	 *
	 * @since 3.21.5
	 * @access private
	 * @var Wordlift_Entity_Uri_Service $instance The {@link Wordlift_Entity_Uri_Service} singleton.
	 */
	private static $instance = null;

	/**
	 * Get the singleton.
	 *
	 * @return Wordlift_Entity_Uri_Service The singleton instance.
	 * @since 3.21.5
	 */
	public static function get_instance() {

		if ( ! isset( self::$instance ) ) {
			$entity_uri_cache_service = new Wordlift_File_Cache_Service( WL_TEMP_DIR . 'entity_uri/' );
			self::$instance           = new Wordlift_Cached_Entity_Uri_Service( $entity_uri_cache_service );

		}

		return self::$instance;
	}

	/**
	 * Try to find a post when the content service doesn't find it.
	 *
	 * @param WP_Post|null $post
	 * @param string       $uri
	 *
	 * @return false|int
	 */
	public function content_service__post__not_found( $post, $uri ) {
		return $this->get_post_id_from_url( $uri );
	}

	/**
	 * Preload the provided URIs in the local cache.
	 *
	 * This function will populate the local `$uri_to_post` array by running a
	 * single query with all the URIs and returning the mappings in the array.
	 *
	 * @param array $uris An array of URIs.
	 *
	 * @since 3.16.3
	 */
	public function preload_uris( $uris ) {

		// Bail out if there are no URIs.
		if ( 0 === count( $uris ) ) {
			return;
		}

		$this->log->trace( 'Preloading ' . count( $uris ) . ' URI(s)...' );

		global $wpdb;
		$in_post_types  = implode( "','", array_map( 'esc_sql', Wordlift_Entity_Service::valid_entity_post_types() ) );
		$in_entity_uris = implode( "','", array_map( 'esc_sql', $uris ) );
		$sql            = "
			SELECT ID FROM $wpdb->posts p
			INNER JOIN $wpdb->postmeta pm
			 ON pm.post_id = p.ID
			  AND pm.meta_key IN ( 'entity_same_as' )
			  AND pm.meta_value IN ( '$in_entity_uris' )
			WHERE p.post_type IN ( '$in_post_types' ) 
			  AND p.post_status IN ( 'publish', 'draft', 'private', 'future' )
  		";

		// Get the posts.
		$posts = $wpdb->get_col( $sql ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared

		// Populate the array. We reinitialize the array on purpose because
		// we don't want these data to long live.
		$this->uri_to_post = array_reduce(
			$posts,
			function ( $carry, $item ) {
				$uris = get_post_meta( $item, Wordlift_Schema_Service::FIELD_SAME_AS );

				$uri = Wordpress_Content_Service::get_instance()
												->get_entity_id( Wordpress_Content_Id::create_post( $item ) );

				if ( isset( $uri ) ) {
					$uris[] = $uri;
				}

				return $carry
					   // Get the URI related to the post and fill them with the item id.
					   + array_fill_keys( $uris, $item );
			},
			array()
		);

		// Add the not found URIs.
		$this->uri_to_post += array_fill_keys( $uris, null );

		$this->log->debug( count( $this->uri_to_post ) . ' URI(s) preloaded.' );

	}

	/**
	 * Reset the URI to post local cache.
	 *
	 * @since 3.16.3
	 */
	public function reset_uris() {

		$this->uri_to_post = array();

	}

	/**
	 * Find entity posts by the entity URI. Entity as searched by their entity URI or same as.
	 *
	 * @param string $uri The entity URI.
	 *
	 * @return WP_Post|null A WP_Post instance or null if not found.
	 * @since 3.2.0
	 */
	public function get_entity( $uri ) {
		if ( ! isset( $uri ) ) {
			return null;
		}

		$this->log->trace( "Getting an entity post for URI $uri..." );

		$content = Wordpress_Content_Service::get_instance()->get_by_entity_id_or_same_as( $uri );

		// Return null if the content isn't found or isn't a post.
		if ( ! isset( $content ) || ! is_a( $content->get_bag(), '\WP_Post' ) ) {
			return null;
		}

		return $content->get_bag();
	}

	/**
	 * Determines whether a given uri is an internal uri or not.
	 *
	 * @param string $uri An uri.
	 *
	 * @return true if the uri internal to the current dataset otherwise false.
	 * @since 3.16.3
	 */
	public function is_internal( $uri ) {

		return ( 0 === strrpos( $uri, (string) Wordlift_Configuration_Service::get_instance()->get_dataset_uri() ) );
	}

	/**
	 * Hook to `rest_post_dispatch` to alter the response and add the `wl_entity_url` post meta as `wl:entity_url`.
	 *
	 * We're using this filter instead of the well known `register_meta` / `register_rest_field` because we still need
	 * to provide full compatibility with WordPress 4.4+.
	 *
	 * @param WP_HTTP_Response $result Result to send to the client. Usually a WP_REST_Response.
	 *
	 * @return WP_HTTP_Response The result to send to the client.
	 *
	 * @since 3.23.0
	 */
	public function rest_post_dispatch( $result ) {

		// Get a reference to the actual data.
		$data = &$result->data;

		// Bail out if we don't have the required parameters, or if the type is not a valid entity.
		if ( ! is_array( $data ) || ! isset( $data['id'] ) || ! isset( $data['type'] )
			 || ! Wordlift_Entity_Type_Service::is_valid_entity_post_type( $data['type'] ) ) {
			return $result;
		}

		// Add the `wl:entity_url`.
		$data['wl:entity_url'] = Wordlift_Entity_Service::get_instance()->get_uri( $data['id'] );

		return $result;
	}

	/**
	 * Helper function to fetch post_id from a WordPress URL
	 * Primarily used when dataset is not enabled
	 *
	 * @param $url
	 *
	 * @return int Post ID | bool false
	 */
	public function get_post_id_from_url( $url ) {
		global $wp_rewrite;

		// We need to check that rewrite is available because the `url_to_postid` uses it and can raise an exception
		// otherwise.
		if ( $wp_rewrite === null ) {
			return false;
		}

		// Try url_to_postid
		$post_id = url_to_postid( htmlspecialchars_decode( $url ) );
		if ( 0 !== $post_id ) {
			return $post_id;
		}

		$parsed_url = wp_parse_url( $url );

		if ( ! isset( $parsed_url['query'] ) ) {
			return false;
		}

		parse_str( $parsed_url['query'], $parsed_query );

		// Try to parse WooCommerce non-pretty product URL
		if ( $parsed_query['product'] ) {
			$posts = get_posts(
				array(
					'name'      => $parsed_query['product'],
					'post_type' => 'product',
				)
			);
			if ( count( $posts ) > 0 ) {
				return $posts[0]->ID;
			}
		}

		return false;
	}

}
