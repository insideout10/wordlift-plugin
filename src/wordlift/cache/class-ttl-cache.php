<?php

namespace Wordlift\Cache;

use Wordlift_Log_Service;

/**
 * Define an a time lived cache.
 *
 * Cache has a ttl set by default to 900 seconds. Cached responses are stored in the temp
 * folder returned by WordPress' {@link get_temp_dir} function.
 *
 * Currently the class doesn't cleanup stale cache files.
 *
 * @since 3.21.2
 */
// @@todo: add a hook to clear the cached files now and then.
class Ttl_Cache {

	/**
	 * The cache name.
	 *
	 * @var string $name The cache name.
	 * @access private
	 * @since 3.21.2
	 */
	private $name;

	/**
	 * The TTL of cached responses in seconds.
	 *
	 * @var int $ttl The TTL in seconds.
	 * @access private
	 * @since 3.21.2
	 */
	private $ttl;

	/**
	 * The cache dir where the cached data is written.
	 *
	 * @since 3.21.2
	 * @access private
	 * @var string $cache_dir The cache dir where the cached responses are written.
	 */
	private $cache_dir;

	/**
	 * A {@link Wordlift_Log_Service} instance.
	 *
	 * @var Wordlift_Log_Service $log A {@link Wordlift_Log_Service} instance.
	 * @access private
	 * @since 3.21.2
	 */
	private $log;

	/**
	 * Create a {@link Ttl_Cache} with the specified TTL, default 900 secs.
	 *
	 * @param string $name The cache name.
	 * @param int    $ttl The cache TTL, default 900 secs.
	 *
	 * @since 3.21.2
	 */
	public function __construct( $name, $ttl = 900 ) {

		$this->log = Wordlift_Log_Service::get_logger( get_class() );

		$this->name = $name;
		$this->ttl  = $ttl;

		// Get the temp dir and add the directory separator if missing.
		$temp_dir = get_temp_dir();
		if ( DIRECTORY_SEPARATOR !== substr( $temp_dir, - strlen( DIRECTORY_SEPARATOR ) ) ) {
			$temp_dir .= DIRECTORY_SEPARATOR;
		}
		$this->cache_dir = $temp_dir . 'wl.cache' . DIRECTORY_SEPARATOR . md5( $name );

		$this->log->trace( "Creating the cache folder {$this->cache_dir}..." );
		wp_mkdir_p( $this->cache_dir );

	}

	/**
	 * Get the cached data for the specified key.
	 *
	 * @param mixed $key A serializable key.
	 *
	 * @return mixed|null
	 * @since 3.21.2
	 */
	public function get( $key ) {

		$filename = $this->get_filename( $key );

		// If the cache file exists and it's not too old, then return it.
		if ( file_exists( $filename ) && $this->ttl >= time() - filemtime( $filename ) ) {
			$this->log->trace( "Cache HIT.\n" );

			return json_decode( file_get_contents( $filename ), true );
		}

		$this->log->trace( "Cache MISS, filename $filename.\n" );

		return null;
	}

	public function put( $key, $data ) {

		$filename = $this->get_filename( $key );

		// Cache.
		@unlink( $filename );
		@file_put_contents( $filename, wp_json_encode( $data ) );

	}

	/**
	 * Get the full path for the given `$hash`. The file is not checked for its existence.
	 *
	 * @param string $hash A file hash.
	 *
	 * @return string The full path to the file.
	 * @since 3.21.2
	 */
	private function get_path( $hash ) {

		return $this->cache_dir . DIRECTORY_SEPARATOR . $hash;
	}

	private function get_filename( $key ) {

		// Create a hash and a path to the cache file.
		$hash     = md5( serialize( $key ) );
		$filename = $this->get_path( $hash );

		return $filename;
	}

}
