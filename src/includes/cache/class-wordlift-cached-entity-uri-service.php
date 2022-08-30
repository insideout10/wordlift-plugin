<?php
/**
 * Services: Cached Entity Uri Service
 *
 * @since      3.16.3
 * @package    Wordlift
 * @subpackage Wordlift/includes/cache
 */

/**
 * Define the {@link Wordlift_Cached_Entity_Uri_Service} instance.
 *
 * @since 3.16.3
 */
class Wordlift_Cached_Entity_Uri_Service extends Wordlift_Entity_Uri_Service {

	/**
	 * The {@link Wordlift_Cache_Service} instance.
	 *
	 * @since  3.16.3
	 * @access private
	 * @var \Wordlift_Cache_Service $cache_service The {@link Wordlift_Cache_Service} instance.
	 */
	private $cache_service;

	/**
	 * A {@link Wordlift_Log_Service} instance.
	 *
	 * @since  3.16.3
	 * @access private
	 * @var \Wordlift_Log_Service $log A {@link Wordlift_Log_Service} instance.
	 */
	private $log;

	/**
	 * Create a {@link Wordlift_Cached_Entity_Uri_Service} instance.
	 *
	 * @param \Wordlift_Cache_Service $cache_service
	 *
	 * @since 3.16.3
	 */
	public function __construct( $cache_service ) {
		parent::__construct();

		$this->log = Wordlift_Log_Service::get_logger( get_class() );

		// Add hooks for meta being added/modified/deleted.
		$this->cache_service = $cache_service;

		add_action( 'add_post_meta', array( $this, 'on_before_post_meta_add' ), 10, 3 );
		add_action( 'update_post_meta', array( $this, 'on_before_post_meta_change' ), 10, 4 );
		add_action( 'delete_post_meta', array( $this, 'on_before_post_meta_change' ), 10, 4 );

	}

	/**
	 * Preload the URIs.
	 *
	 * @param array $uris Preload an array of URIs.
	 *
	 * @since 3.16.3
	 */
	public function preload_uris( $uris ) {

		// Filter the URIs which aren't yet cached.
		$cache_service = $this->cache_service;
		$uris_to_cache = array_filter(
			(array) $uris,
			function ( $item ) use ( $cache_service ) {
				return ! $cache_service->has_cache( $item );
			}
		);

		// Preload the URIs.
		parent::preload_uris( $uris_to_cache );

		// Store them in cache.
		if ( is_array( $this->uri_to_post ) && ! empty( $this->uri_to_post ) ) {
			foreach ( $this->uri_to_post as $uri => $post_id ) {
				$this->set_cache( $uri, $post_id );
			}
		}

	}

	/**
	 * Get the entity post for the specified URI.
	 *
	 * @param string $uri The URI.
	 *
	 * @return null|WP_Post The {@link WP_Post} or null if not found.
	 * @since 3.16.3
	 */
	public function get_entity( $uri ) {

		$this->log->trace( "Getting entity for uri $uri..." );

		// Get the cached post for the specified URI.
		$cache = $this->cache_service->get_cache( $uri );

		// Return the cached data if valid.
		if ( false !== $cache && is_numeric( $cache ) ) {
			$this->log->debug( "Cached entity $cache for uri $uri found." );

			return get_post( $cache );
		}

		// Get the actual result.
		$post = parent::get_entity( $uri );

		// Cache the result.
		if ( null !== $post ) {
			$this->set_cache( $uri, $post->ID );
		}

		// Return the result.
		return $post;
	}

	/**
	 * Set the cached URI for the specified {@link WP_Post}.
	 *
	 * @param string $uri The URI.
	 * @param int    $post_id The post ID.
	 *
	 * @since 3.16.3
	 * @since 3.29.0 takes a post ID as input.
	 */
	private function set_cache( $uri, $post_id ) {

		// Cache the result.
		$this->cache_service->set_cache( $uri, $post_id );

	}

	/**
	 * Delete the cache for the specified URIs.
	 *
	 * @param array $uris An array of URIs.
	 *
	 * @since 3.16.3
	 */
	private function delete_cache( $uris ) {

		// Delete the cache for each URI.
		foreach ( $uris as $uri ) {
			// Delete the single cache file.
			$this->cache_service->delete_cache( $uri );
		}

	}

	/**
	 * Before post meta change.
	 *
	 * When a post meta is going to be changed, we check if the `meta_key` is
	 * either the `entity_url` or the `same_as` in which case we delete the cache
	 * for all the associated URIs.
	 *
	 * @param int|array $meta_ids The {@link WP_Post} meta id(s).
	 * @param int       $post_id The {@link WP_Post} id.
	 * @param string    $meta_key The meta key.
	 * @param mixed     $meta_value The meta value(s).
	 *
	 * @since 3.16.3
	 */
	public function on_before_post_meta_change( $meta_ids, $post_id, $meta_key, $meta_value ) {

		// Bail out if we're not interested in the meta key.
		if ( WL_ENTITY_URL_META_NAME !== $meta_key && Wordlift_Schema_Service::FIELD_SAME_AS !== $meta_key ) {
			return;
		}

		$this->log->trace( "Updating/deleting $meta_key for post $post_id ($meta_key), invalidating cache..." );

		// The list of existing URIs, plus the list of URIs being deleted/updated.
		$old_value = get_post_meta( $post_id, $meta_key );

		// We expect an array here from the `get_post_meta` signature. However `get_post_meta` is prone to side effects
		// because of filters. So if it's not we return an empty array.
		if ( ! is_array( $old_value ) ) {
			$old_value = array();
		}
		$new_value = isset( $meta_value ) ? (array) $meta_value : array();
		$uris      = array_merge( $old_value, $new_value );

		// Delete the cache for those URIs.
		$this->delete_cache( $uris );

	}

	/**
	 * Hook to meta add for a {@link WP_Post}, will cause the cache to
	 * invalidate.
	 *
	 * @param int    $post_id The {@link WP_Post} id.
	 * @param string $meta_key The meta key.
	 * @param mixed  $meta_value The meta value(s).
	 *
	 * @since 3.16.3
	 */
	public function on_before_post_meta_add( $post_id, $meta_key, $meta_value ) {

		// Bail out if we're not interested in the meta key.
		if ( WL_ENTITY_URL_META_NAME !== $meta_key && Wordlift_Schema_Service::FIELD_SAME_AS !== $meta_key ) {
			return;
		}

		$this->log->trace( "Adding $meta_key for post $post_id ($meta_key), invalidating cache..." );

		// Delete the cache for the URIs being added.
		$this->delete_cache( (array) $meta_value );

	}

}
