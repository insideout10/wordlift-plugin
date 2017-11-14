<?php
/**
 * Created by PhpStorm.
 * User: david
 * Date: 14.11.17
 * Time: 11:07
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
	 * Wordlift_Cached_Post_Converter constructor.
	 *
	 * @param \Wordlift_Post_Converter $converter
	 * @param \Wordlift_Cache_Service  $cache_service
	 */
	public function __construct( $converter, $cache_service ) {

		$this->log = Wordlift_Log_Service::get_logger( get_class() );

		$this->converter     = $converter;
		$this->cache_service = $cache_service;

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

			// Return the contents.
			return $contents;
		}

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

	private function set_cache( $post_id, $references, $jsonld ) {

		$this->log->trace( "Caching result for post $post_id..." );

		$this->cache_service->set_cache( $post_id, array(
			'references' => $references,
			'jsonld'     => $jsonld,
		) );

	}

}
