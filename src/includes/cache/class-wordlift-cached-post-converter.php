<?php
/**
 * Converters: Cache Post Converter
 *
 * The Cached Post Converter looks for cached copies of converter results before
 * actually calling the underlying {@link Wordlift_Post_Converter} (injected
 * via the constructor).
 *
 * @since      3.16.0
 * @package    Wordlift
 * @subpackage Wordlift/includes/cache
 */

/**
 * Define the {@link Wordlift_Cached_Post_Converter} class.
 *
 * @since 3.16.0
 */
class Wordlift_Cached_Post_Converter implements Wordlift_Post_Converter {

	/**
	 * A {@link Wordlift_Post_Converter} instance.
	 *
	 * @since 3.16.0
	 *
	 * @var \Wordlift_Post_Converter $converter A {@link Wordlift_Post_Converter} instance.
	 */
	private $converter;

	/**
	 * A {@link Wordlift_Log_Service} instance.
	 *
	 * @since 3.16.0
	 *
	 * @var Wordlift_Log_Service \$log A {@link Wordlift_Log_Service} instance.
	 */
	private $log;

	/**
	 * @var Wordlift_Cache_Service
	 */
	private $cache_service;

	/**
	 * The {@link Wordlift_Configuration_Service} instance.
	 *
	 * @since  3.16.0
	 * @access private
	 * @var \Wordlift_Configuration_Service $configuration_service The {@link Wordlift_Configuration_Service} instance.
	 */
	private $configuration_service;

	/**
	 * Wordlift_Cached_Post_Converter constructor.
	 *
	 * @param \Wordlift_Post_Converter        $converter             The {@link Wordlift_Post_Converter} implementation.
	 * @param \Wordlift_Cache_Service         $cache_service         The {@link Wordlift_Cache_Service} implementation.
	 * @param \Wordlift_Configuration_Service $configuration_service The {@link Wordlift_Configuration_Service} instance.
	 */
	public function __construct( $converter, $cache_service, $configuration_service ) {

		$this->log = Wordlift_Log_Service::get_logger( get_class() );

		$this->converter             = $converter;
		$this->cache_service         = $cache_service;
		$this->configuration_service = $configuration_service;

		$this->init_hooks();

	}

	/**
	 * Hooks to catch post/post meta changes in order to invalidate the cache.
	 *
	 * @since 3.16.0
	 */
	private function init_hooks() {

		// Hook on post save to flush relevant cache.
		add_action( 'save_post', array( $this, 'save_post' ) );

		add_action( 'added_post_meta', array(
			$this,
			'changed_post_meta',
		), 10, 2 );
		add_action( 'updated_post_meta', array(
			$this,
			'changed_post_meta',
		), 10, 2 );
		add_action( 'deleted_post_meta', array(
			$this,
			'changed_post_meta',
		), 10, 2 );

		// Flush cache when wordlift settings were updated.
		add_action( 'update_option_wl_general_settings', array(
			$this,
			'update_option_wl_general_settings',
		) );

		// Flushes the cache when permalink structure is changed.
		add_action( 'update_option_permalink_structure', array( $this, 'permalinks_structure_changed' ) );

		// Invalid cache on relationship change.
		add_action( 'wl_relation_added', array( $this, 'relation_changed' ) );
		add_action( 'wl_relation_deleted', array( $this, 'relation_changed' ) );

	}

	/**
	 * @inheritdoc
	 */
	public function convert( $post_id, &$references = array(), &$cache = false ) {

		$this->log->trace( "Converting post $post_id..." );

		// Try to get a cached result.
		$contents = $this->get_cache( $post_id, $references );

		// Return the cached contents if any.
		if ( false !== $contents ) {
			$this->log->debug( "Cached contents found for post $post_id." );

			// Inform the caller that this is cached result.
			$cache = true;
			$this->add_http_header( $post_id, true );

			// Return the contents.
			return $contents;
		}

		// Set cached to false.
		$cache = false;
		$this->add_http_header( $post_id, false );

		// Convert the the post.
		$jsonld = $this->converter->convert( $post_id, $references );

		// Cache the results.
		$this->set_cache( $post_id, $references, $jsonld );

		// Finally return the JSON-LD.
		return $jsonld;
	}

	/**
	 * Try to get the cached contents.
	 *
	 * @since 3.16.0
	 *
	 * @param int   $post_id    The {@link WP_Post} id.
	 * @param array $references The referenced posts.
	 *
	 * @return mixed|bool The cached contents or false if the cached isn't found.
	 */
	private function get_cache( $post_id, &$references = array() ) {

		$this->log->trace( "Getting cached contents for post $post_id..." );

		// Get the cache.
		$contents = $this->cache_service->get_cache( $post_id );

		// Bail out if we don't have cached contents or the cached contents are
		// invalid.
		if ( false === $contents || ! isset( $contents['jsonld'] ) || ! isset( $contents['references'] ) ) {
			$this->log->debug( "Cached contents for post $post_id not found." );

			return false;
		}

		// Remap the cache.
		$references = $contents['references'];

		return $contents['jsonld'];
	}

	/**
	 * Set the cache with the provided results.
	 *
	 * The function will prepare the provided results and will ask the {@link Wordlift_Cache_Service}
	 * to cache them.
	 *
	 * @since 3.16.0
	 *
	 * @param int   $post_id    The {@link WP_Post} id.
	 * @param array $references An array of references.
	 * @param array $jsonld     A JSON-LD structure.
	 */
	private function set_cache( $post_id, $references, $jsonld ) {

		$this->log->trace( "Caching result for post $post_id..." );

		$this->cache_service->set_cache( $post_id, array(
			'references' => $references,
			'jsonld'     => $jsonld,
		) );

	}

	/**
	 * Hook to 'save_post', will invalidate the cache for that post.
	 *
	 * @since 3.16.0
	 *
	 * @param int $post_id The {@link WP_Post} id.
	 */
	public function save_post( $post_id ) {

		$this->log->trace( "Post $post_id saved, invalidating cache..." );

		$this->cache_service->delete_cache( $post_id );

		$this->flush_cache_if_publisher( $post_id );

	}

	/**
	 * Hook to meta changed for a {@link WP_Post}, will cause the cause to
	 * invalidate.
	 *
	 * @since 3.16.0
	 *
	 * @param int $id      The {@link WP_Post} meta id.
	 * @param int $post_id The {@link WP_Post} id.
	 */
	public function changed_post_meta( $id, $post_id ) {

		$this->log->trace( "Post $post_id meta changed, invalidating cache..." );

		// Delete the single cache file.
		$this->cache_service->delete_cache( $post_id );

		// Flush the cache if it's the publisher.
		$this->flush_cache_if_publisher( $post_id );

	}

	/**
	 * Hook to WordLift's options changes, will flush the cache.
	 *
	 * @since 3.16.0
	 */
	public function update_option_wl_general_settings() {
		$this->log->trace( "WordLift options changed, flushing cache..." );

		$this->cache_service->flush();
	}

	/**
	 * Hook when permalinks are changed, will flush the cache.
	 *
	 * @since 3.17.0
	 */
	public function permalinks_structure_changed() {
		$this->log->trace( "Permalinks structure changed, flushing cache..." );

		$this->cache_service->flush();
	}

	/**
	 * Hook to WordLift's post/entity relation changes, will invalidate the cache.
	 *
	 * @since 3.16.0
	 *
	 * @param int $post_id The {@link WP_Post} id.
	 */
	public function relation_changed( $post_id ) {
		$this->log->trace( "Post $post_id relations changed, invalidating cache..." );

		$this->cache_service->delete_cache( $post_id );
	}

	/**
	 * When in Ajax, prints an http header with the information whether the
	 * response is cached or not.
	 *
	 * @since 3.16.0
	 *
	 * @param int  $post_id The {@link WP_Post} id.
	 * @param bool $cache   Whether the response fragment is cached.
	 */
	private function add_http_header( $post_id, $cache ) {

		if ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX || headers_sent() ) {
			return;
		}

		header( "X-WordLift-JsonLd-Cache-$post_id: " . ( $cache ? 'HIT' : 'MISS' ) );

	}

	/**
	 * Call the `flush` operation on the {@link Wordlift_Cache_Service} if
	 * the publisher has changed.
	 *
	 * @since 3.16.0
	 *
	 * @param int $post_id The changed {@link WP_Post}'s id.
	 */
	private function flush_cache_if_publisher( $post_id ) {

		// Bail out if it's not the publisher.
		if ( $post_id !== $this->configuration_service->get_publisher_id() ) {
			return;
		}

		// Flush the cache, since the publisher has changed.
		$this->cache_service->flush();

	}

}
