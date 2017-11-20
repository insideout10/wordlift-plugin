<?php
/**
 * Services: File Cache Service
 *
 * The File Cache Service provides on-disk caching.
 *
 * @since      3.16.0
 * @package    Wordlift
 * @subpackage Wordlift/includes/cache
 */

/**
 * Define the {@link Wordlift_File_Cache_Service} class.
 *
 * @since 3.16.0
 */
class Wordlift_File_Cache_Service implements Wordlift_Cache_Service {

	/**
	 * The cache directory.
	 *
	 * @since  3.16.0
	 * @access private
	 * @var string $cache_dir The root cache directory (ending with a trailing slash).
	 */
	private $cache_dir;

	/**
	 * The file extension for cache files (e.g. `.wlcache`).
	 *
	 * @since  3.16.0
	 * @access private
	 * @var string $file_extension The file extension for cache files (e.g. `.wlcache`).
	 */
	private $file_extension;

	/**
	 * A {@link Wordlift_Log_Service} instance.
	 *
	 * @since  3.16.0
	 * @access private
	 * @var \Wordlift_Log_Service $log A {@link Wordlift_Log_Service} instance.
	 */
	private $log;

	/**
	 * The {@link Wordlift_File_Cache_Service} singleton instance.
	 *
	 * @since  3.16.0
	 * @access private
	 * @var \Wordlift_File_Cache_Service $instance The {@link Wordlift_File_Cache_Service} singleton instance.
	 */
	private static $instance;

	/**
	 * Create a {@link Wordlift_File_Cache_Service} instance.
	 *
	 * The File Cache Service requires a base cache directory (to which a unique
	 * id for the current site will be appended) and a file extension for cache
	 * files (by default `.wlcache`) is used.
	 *
	 * @since 3.16.0
	 *
	 * @param string $cache_dir      The base cache directory.
	 * @param string $file_extension The file extension, by default `.wlcache`.
	 */
	public function __construct( $cache_dir, $file_extension = '.wlcache' ) {

		$this->log = Wordlift_Log_Service::get_logger( get_class() );

		// Set the cache directory using the base directory provided by the caller
		// and appending a hash for the unique site id.
		$this->cache_dir      = trailingslashit( $cache_dir ) . md5( get_site_url() ) . '/';
		$this->file_extension = $file_extension;

		// Create the cache dir.
		if ( ! file_exists( $this->cache_dir ) ) {
			mkdir( $this->cache_dir, 0755, true );
		}

		self::$instance = $this;

		$this->log->info( "File Cache service initialized on $this->cache_dir." );

	}

	/**
	 * Get the {@link Wordlift_File_Cache_Service} singleton instance.
	 *
	 * @since 3.16.0
	 * @return \Wordlift_File_Cache_Service The {@link Wordlift_File_Cache_Service} singleton instance.
	 */
	public static function get_instance() {

		return self::$instance;
	}

	/**
	 * Get the cached response for the specified `id`.
	 *
	 * @since 3.16.0
	 *
	 * @param int $id The cache `id`.
	 *
	 * @return mixed|false The cached contents or false if the cache isn't found.
	 */
	function get_cache( $id ) {

		// Get the filename.
		$filename = $this->get_filename( $id );

		// Bail out if the file doesn't exist.
		if ( ! file_exists( $filename ) ) {
			return false;
		}

		$this->log->trace( "Trying to get cache contents for $id from $filename..." );

		// Try to decode the contents.
		$contents = json_decode( file_get_contents( $filename ), true );

		// Return false if decoding failed, otherwise the decoded contents.
		return $contents ?: false;
	}

	/**
	 * Set the cache contents for the specified `id`.
	 *
	 * @since 3.16.0
	 *
	 * @param int   $id       The cache id.
	 * @param mixed $contents The cache contents.
	 */
	function set_cache( $id, $contents ) {

		$filename = $this->get_filename( $id );

		$this->log->trace( "Writing cache contents for $id to $filename..." );

		file_put_contents( $filename, wp_json_encode( $contents ) );

	}

	/**
	 * Delete the cache for the specified `id`.
	 *
	 * @since 3.16.0
	 *
	 * @param int $id The cache `id`.
	 */
	function delete_cache( $id ) {

		$filename = $this->get_filename( $id );

		$this->log->trace( "Deleting cache contents for $id, file $filename..." );

		wp_delete_file( $filename );

	}

	/**
	 * Flush the whole cache.
	 *
	 * @since 3.16.0
	 */
	function flush() {

		// Bail out if the cache dir isn't set.
		if ( empty( $this->cache_dir ) || '/' === $this->cache_dir ) {
			return;
		}

		$this->log->trace( 'Flushing cache contents...' );

		$handle = @opendir( $this->cache_dir );

		// Bail out if the directory can't be opened.
		if ( false === $handle ) {
			return;
		}

		// Calculate the file extension length for matching file names.
		$file_extension_length = strlen( $this->file_extension );

		// Loop into the directory to delete files.
		while ( false !== ( $entry = readdir( $handle ) ) ) {
			if ( substr( $entry, - $file_extension_length ) === $this->file_extension ) {
				$this->log->trace( "Deleting file {$this->cache_dir}{$entry}..." );
				wp_delete_file( $this->cache_dir . $entry );
			}
		}

		// Finally closed the directory.
		closedir( $handle );

	}

	/**
	 * Get the filename holding the cache contents for the specified `id`.
	 *
	 * @since 3.16.0
	 *
	 * @param int $id The cache `id`.
	 *
	 * @return string The filename.
	 */
	private function get_filename( $id ) {

		return $this->cache_dir . md5( $id ) . $this->file_extension;
	}

}
