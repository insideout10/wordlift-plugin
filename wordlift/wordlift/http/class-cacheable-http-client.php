<?php

namespace Wordlift\Http;

/**
 * Define an http-client which caches the responses (provided that the status code is 2xx).
 *
 * Cached responses have a ttl set by default to 900 seconds. Cached responses are stored in the temp
 * folder returned by WordPress' {@link get_temp_dir} function.
 *
 * Currently the class doesn't cleanup stale cache files.
 *
 * @since 1.0.0
 */
// @@todo: add a hook to clear the cached files now and then.
class Cacheable_Http_Client extends Simple_Http_Client {

	/**
	 * The TTL of cached responses in seconds.
	 *
	 * @var int $ttl The TTL in seconds.
	 * @access private
	 * @since 1.0.0
	 */
	private $ttl;

	/**
	 * The cache dir where the cached responses are written.
	 *
	 * @since 1.0.0
	 * @access private
	 * @var string $cache_dir The cache dir where the cached responses are written.
	 */
	private $cache_dir;

	/**
	 * A {@link Wordlift_Log_Service} instance.
	 *
	 * @var \Wordlift_Log_Service $log A {@link Wordlift_Log_Service} instance.
	 * @access private
	 * @since 1.0.0
	 */
	private $log;

	/**
	 * Create a {@link Cacheable_Http_Client} with the specified TTL, default 900 secs.
	 *
	 * @param int $ttl The cache TTL, default 900 secs.
	 *
	 * @since 1.0.0
	 */
	public function __construct( $ttl = 900 ) {

		$this->log = \Wordlift_Log_Service::get_logger( get_class() );

		$this->ttl = $ttl;

		// Get the temp dir and add the directory separator if missing.
		$temp_dir = get_temp_dir();
		if ( DIRECTORY_SEPARATOR !== substr( $temp_dir, - strlen( DIRECTORY_SEPARATOR ) ) ) {
			$temp_dir .= DIRECTORY_SEPARATOR;
		}
		$this->cache_dir = $temp_dir . 'wlfb-http-cache';

		$this->log->trace( "Creating the cache folder {$this->cache_dir}..." );
		wp_mkdir_p( $this->cache_dir );

	}

	/**
	 * @inheritDoc
	 */
	public function request( $url, $options = array() ) {

		// Create a hash and a path to the cache file.
		// phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.serialize_serialize
		$hash     = md5( $url ) . '-' . md5( serialize( $options ) );
		$filename = $this->get_path( $hash );

		// If the cache file exists and it's not too old, then return it.
		if ( file_exists( $filename ) && $this->ttl >= time() - filemtime( $filename ) ) {
			$this->log->trace( "Cache HIT.\n" );

			// phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
			return json_decode( file_get_contents( $filename ), true );
		}

		$this->log->trace( "Cache MISS for URL $url, hash $hash.\n" );

		// Get a fresh response and return it.
		$response = parent::request( $url, $options );

		// Return immediately, do not cache.
		if ( is_wp_error( $response ) ) {
			return $response;
		}

		// Do not cache response with invalid status codes or status code different from 2xx.
		$code = wp_remote_retrieve_response_code( $response );
		if ( ! is_numeric( $code ) || 2 !== intval( $code ) / 100 ) {
			return $response;
		}

		// Cache.
		// phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
		@unlink( $filename );
		// phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged,WordPress.WP.AlternativeFunctions.json_encode_json_encode,WordPress.WP.AlternativeFunctions.file_system_read_file_put_contents
		@file_put_contents( $filename, json_encode( $response ) );

		return $response;
	}

	/**
	 * Get the full path for the given `$hash`. The file is not checked for its existence.
	 *
	 * @param string $hash A file hash.
	 *
	 * @return string The full path to the file.
	 * @since 1.0.0
	 */
	private function get_path( $hash ) {

		return $this->cache_dir . DIRECTORY_SEPARATOR . $hash;
	}

}
