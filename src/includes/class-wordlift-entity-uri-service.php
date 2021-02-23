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

/**
 * Define the {@link Wordlift_Entity_Uri_Service} class.
 *
 * @since 3.16.3
 */
class Wordlift_Entity_Uri_Service {

	/**
	 * Holds the {@link Wordlift_Entity_Uri_Service} instance.
	 *
	 * @since 3.21.5
	 * @access private
	 * @var Wordlift_Entity_Uri_Service $instance The {@link Wordlift_Entity_Uri_Service} singleton.
	 */
	private static $instance;

	/**
	 * A {@link Wordlift_Log_Service} instance.
	 *
	 * @since  3.16.3
	 * @access private
	 * @var \Wordlift_Log_Service $log A {@link Wordlift_Log_Service} instance.
	 */
	private $log;

	/**
	 * The {@link Wordlift_Configuration_Service} instance.
	 *
	 * @since  3.16.3
	 * @access private
	 * @var \Wordlift_Configuration_Service $configuration_service The {@link Wordlift_Configuration_Service} instance.
	 */
	private $configuration_service;

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
	 * @param \Wordlift_Configuration_Service $configuration_service The {@link Wordlift_Configuration_Service} instance.
	 *
	 * @since 3.16.3
	 *
	 */
	public function __construct( $configuration_service ) {

		$this->log = Wordlift_Log_Service::get_logger( get_class() );

		$this->configuration_service = $configuration_service;

		// Add a filter to the `rest_post_dispatch` filter to add the wl_entity_url meta as `wl:entity_url`.
		add_filter( 'rest_post_dispatch', array( $this, 'rest_post_dispatch' ) );

		self::$instance = $this;

	}

	/**
	 * Get the singleton.
	 *
	 * @return Wordlift_Entity_Uri_Service The singleton instance.
	 * @since 3.21.5
	 */
	public static function get_instance() {

		return self::$instance;
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
	 *
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
			  AND pm.meta_key IN ( 'entity_url', 'entity_same_as' )
			  AND pm.meta_value IN ( '$in_entity_uris' )
			WHERE p.post_type IN ( '$in_post_types' ) 
			  AND p.post_status IN ( 'publish', 'draft', 'private', 'future' )
  		";

		// Get the posts.
		$posts = $wpdb->get_col( $sql );

		// Populate the array. We reinitialize the array on purpose because
		// we don't want these data to long live.
		$this->uri_to_post = array_reduce( $posts, function ( $carry, $item ) {
			$uris = array_merge(
				get_post_meta( $item, WL_ENTITY_URL_META_NAME ),
				get_post_meta( $item, Wordlift_Schema_Service::FIELD_SAME_AS )
			);

			return $carry
			       // Get the URI related to the post and fill them with the item id.
			       + array_fill_keys( $uris, $item );
		}, array() );

		// Add the not found URIs.
		$this->uri_to_post += array_fill_keys( $uris, null );

		$this->log->debug( count( $this->uri_to_post ) . " URI(s) preloaded." );

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
	 *
	 */
	public function get_entity( $uri ) {

		$this->log->trace( "Getting an entity post for URI $uri..." );

		// Check if we've been provided with a value otherwise return null.
		if ( empty( $uri ) ) {
			return null;
		}

		$this->log->debug( "Querying post for $uri..." );

		$query_args = array(
			// See https://github.com/insideout10/wordlift-plugin/issues/654.
			'ignore_sticky_posts' => 1,
			'posts_per_page'      => 1,
			'post_status'         => 'any',
			'post_type'           => Wordlift_Entity_Service::valid_entity_post_types(),
			'meta_query'          => array(
				array(
					'key'     => WL_ENTITY_URL_META_NAME,
					'value'   => $uri,
					'compare' => '=',
				),
			),
		);

		// Only if the current uri is not an internal uri, entity search is
		// performed also looking at sameAs values.
		//
		// This solve issues like https://github.com/insideout10/wordlift-plugin/issues/237
		if ( ! $this->is_internal( $uri ) ) {

			$query_args['meta_query']['relation'] = 'OR';
			$query_args['meta_query'][]           = array(
				'key'     => Wordlift_Schema_Service::FIELD_SAME_AS,
				'value'   => $uri,
				'compare' => '=',
			);
		}

		$posts = get_posts( $query_args );

		// Attempt to find post by URI (only for local entity URLs)
		if ( empty( $posts ) ) {

			$this->log->debug( "Finding post by $uri..." );
			$postid = $this->get_post_id_from_url( $uri );
			if ( $postid ) {
				$this->log->trace( "Found post $postid by URL" );

				return get_post( $postid );
			}

		}

		// Return null if no post is found.
		if ( empty( $posts ) ) {
			$this->log->warn( "No post for URI $uri." );

			return null;
		}

		// Return the found post.
		return current( $posts );
	}

	/**
	 * Determines whether a given uri is an internal uri or not.
	 *
	 * @param string $uri An uri.
	 *
	 * @return true if the uri internal to the current dataset otherwise false.
	 * @since 3.16.3
	 *
	 */
	public function is_internal( $uri ) {

		return ( 0 === strrpos( $uri, (string) $this->configuration_service->get_dataset_uri() ) );
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

		// Try url_to_postid
		$post_id = url_to_postid( htmlspecialchars_decode( $url ) );
		if ( $post_id !== 0 ) {
			return $post_id;
		}

		$parsed_url = parse_url( $url );
		parse_str( $parsed_url['query'], $parsed_query );

		// Try to parse WooCommerce non-pretty product URL
		if ( $parsed_query['product'] ) {
			$posts = get_posts( array(
				'name'      => $parsed_query['product'],
				'post_type' => 'product'
			) );
			if ( count( $posts ) > 0 ) {
				return $posts[0]->ID;
			}
		}

		return false;
	}

}
