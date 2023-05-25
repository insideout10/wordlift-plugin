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

use Wordlift\Cache\Ttl_Cache;
use Wordlift\Relation\Relations;

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
	 * A list of meta keys that do not cause the cache to update.
	 *
	 * @since 3.17.3
	 * @var array An array of ignored meta keys.
	 */
	private static $ignored_meta_keys = array(
		'_edit_lock',
		'_edit_last',
		'_wp_page_template',
		'_wp_attachment_is_custom_background',
		'_wp_attachment_backup_sizes',
		'_wp_attachment_is_custom_header',
	);
	/**
	 * @var Ttl_Cache
	 */
	private $cache;

	/**
	 * Wordlift_Cached_Post_Converter constructor.
	 *
	 * @param \Wordlift_Post_Converter $converter The {@link Wordlift_Post_Converter} implementation.
	 * @param Ttl_Cache                $cache The {@link Ttl_Cache} cache instance.
	 */
	public function __construct( $converter, $cache ) {

		$this->log = Wordlift_Log_Service::get_logger( get_class() );

		$this->cache     = $cache;
		$this->converter = $converter;
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

		add_action(
			'added_post_meta',
			array(
				$this,
				'changed_post_meta',
			),
			10,
			3
		);
		add_action(
			'updated_post_meta',
			array(
				$this,
				'changed_post_meta',
			),
			10,
			3
		);
		add_action(
			'deleted_post_meta',
			array(
				$this,
				'changed_post_meta',
			),
			10,
			3
		);

		// Flush cache when wordlift settings were updated.
		add_action(
			'update_option_wl_general_settings',
			array(
				$this,
				'update_option_wl_general_settings',
			)
		);

		// Flushes the cache when permalink structure is changed.
		add_action(
			'update_option_permalink_structure',
			array(
				$this,
				'permalinks_structure_changed',
			)
		);

		// Invalid cache on relationship change.
		add_action( 'wl_relation_added', array( $this, 'relation_changed' ) );
		add_action( 'wl_relation_deleted', array( $this, 'relation_changed' ) );

	}

	/**
	 * Note that the `&$cache` parameter here is used only to report whether the response comes from the cache. It
	 * used by `test-issue-626.php` and nowhere else in code.
	 *
	 * @inheritdoc
	 */
	// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
	public function convert( $post_id, &$references = array(), &$references_infos = array(), $relations = null, &$cache = false ) {

		// Ensure post ID is `int`. Otherwise we may have issues with caching, since caching is strict about
		// key var types.
		$post_id = (int) $post_id;

		$this->log->trace( "Converting post $post_id..." );

		// Try to get a cached result.
		$arr = $this->get_cache( $post_id );

		// Return the cached contents if any.
		if ( false !== $arr ) {
			$contents = $arr['jsonld'];

			// Add the cached relations if any.
			if ( is_a( $arr['relations'], 'Wordlift\Relation\Relations' ) ) {
				$relations->add( ...$arr['relations']->toArray() );
			}

			if ( is_array( $arr['references'] ) ) {
				$references = $arr['references'];
			}

			$this->log->debug( "Cached contents found for post $post_id." );

			// Inform the caller that this is cached result.
			// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
			$cache = true;
			$this->add_http_header( $post_id, true );

			// Return the contents.
			return $contents;
		}

		// Set cached to false.
		// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
		$cache = false;
		$this->add_http_header( $post_id, false );

		// Convert the post.
		$jsonld = $this->converter->convert( $post_id, $references, $references_infos, $relations );

		/**
		 * @since 3.32.0
		 * We cant apply json_encode on the objects {@link \Wordlift\Jsonld\Reference}, so we decode
		 * it here before saving it on cache.
		 */
		// Cache the results.
		$this->set_cache( $post_id, $references, $jsonld, $relations );

		// Finally return the JSON-LD.
		return $jsonld;
	}

	/**
	 * Try to get the cached contents.
	 *
	 * @param int $post_id The {@link WP_Post} id.
	 * @param array $references The referenced posts.
	 *
	 * @return mixed|bool The cached contents or false if the cached isn't found.
	 * @since 3.16.0
	 */
	// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
	private function get_cache( $post_id ) {

		// Ensure post ID is int, because cache is strict about var types.
		$post_id = (int) $post_id;

		$this->log->trace( "Getting cached contents for post $post_id..." );

		// Get the cache.
		$modified_date_time = get_post_datetime( $post_id, 'modified', 'gmt' );
		$contents           = $this->cache->get( $post_id, $modified_date_time ? $modified_date_time->getTimestamp() : 0 );

		// Bail out if we don't have cached contents or the cached contents are
		// invalid.
		if ( null === $contents || ! isset( $contents['jsonld'] ) || ! isset( $contents['references'] ) || ! isset( $contents['relations'] ) ) {
			$this->log->debug( "Cached contents for post $post_id not found." );

			return false;
		}

		return $contents['jsonld']
			? array(
				'jsonld'     => $contents['jsonld'],
				'relations'  => Relations::from_json( $contents['relations'] ),
				'references' => $contents['references'],
			)
			: false;
	}

	/**
	 * Set the cache with the provided results.
	 *
	 * The function will prepare the provided results and will ask the {@link Ttl_Cache} to cache them.
	 *
	 * @param int   $post_id The {@link WP_Post} id.
	 * @param array $references An array of references.
	 * @param array $jsonld A JSON-LD structure.
	 *
	 * @since 3.16.0
	 */
	private function set_cache( $post_id, $references, $jsonld, $relations ) {

		$this->log->trace( "Caching result for post $post_id..." );

		$this->cache->put(
			$post_id,
			array(
				// @@todo check the `references`.
				'references' => $references,
				'jsonld'     => $jsonld,
				'relations'  => $relations,
			)
		);

	}

	/**
	 * Hook to 'save_post', will invalidate the cache for that post.
	 *
	 * @param int $post_id The {@link WP_Post} id.
	 *
	 * @since 3.16.0
	 */
	public function save_post( $post_id ) {

		$this->log->trace( "Post $post_id saved, invalidating cache..." );

		$this->cache->delete( $post_id );

		$this->flush_cache_if_publisher( $post_id );

	}

	/**
	 * Hook to meta changed for a {@link WP_Post}, will cause the cause to
	 * invalidate.
	 *
	 * @param int    $id The {@link WP_Post} meta id.
	 * @param int    $post_id The {@link WP_Post} id.
	 * @param string $meta_key The meta key.
	 *
	 * @since 3.16.0
	 */
	public function changed_post_meta( $id, $post_id, $meta_key ) {

		if ( in_array( $meta_key, self::$ignored_meta_keys, true ) ) {
			$this->log->trace( "Post $post_id meta $meta_key ignored." );

			return;
		}

		$this->log->trace( "Post $post_id meta $meta_key changed, invalidating cache..." );

		// Delete the single cache file.
		$this->cache->delete( $post_id );

		// Flush the cache if it's the publisher.
		$this->flush_cache_if_publisher( $post_id );

	}

	/**
	 * Hook to WordLift's options changes, will flush the cache.
	 *
	 * @since 3.16.0
	 */
	public function update_option_wl_general_settings() {
		$this->log->trace( 'WordLift options changed, flushing cache...' );

		$this->cache->flush();
	}

	/**
	 * Hook when permalinks are changed, will flush the cache.
	 *
	 * @since 3.17.0
	 */
	public function permalinks_structure_changed() {
		$this->log->trace( 'Permalinks structure changed, flushing cache...' );

		$this->cache->flush();
	}

	/**
	 * Hook to WordLift's post/entity relation changes, will invalidate the cache.
	 *
	 * @param int $post_id The {@link WP_Post} id.
	 *
	 * @since 3.16.0
	 */
	public function relation_changed( $post_id ) {
		$this->log->trace( "Post $post_id relations changed, invalidating cache..." );

		$this->cache->delete( $post_id );
	}

	/**
	 * When in Ajax, prints an http header with the information whether the
	 * response is cached or not.
	 *
	 * @param int  $post_id The {@link WP_Post} id.
	 * @param bool $cache Whether the response fragment is cached.
	 *
	 * @since 3.16.0
	 */
	private function add_http_header( $post_id, $cache ) {

		if ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX || headers_sent() ) {
			return;
		}

		header( "X-WordLift-JsonLd-Cache-$post_id: " . ( $cache ? 'HIT' : 'MISS' ) );

	}

	/**
	 * Call the `flush` operation on the {@link Ttl_Cache} if
	 * the publisher has changed.
	 *
	 * @param int $post_id The changed {@link WP_Post}'s id.
	 *
	 * @since 3.16.0
	 */
	private function flush_cache_if_publisher( $post_id ) {

		// Bail out if it's not the publisher.
		if ( Wordlift_Configuration_Service::get_instance()->get_publisher_id() !== $post_id ) {
			return;
		}

		// Flush the cache, since the publisher has changed.
		$this->cache->flush();

	}

}
