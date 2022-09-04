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
	 * The {@link Wordlift_File_Cache_Service} registered instances.
	 *
	 * Each {@link Wordlift_File_Cache_Service} adds itself to the registered
	 * instances.
	 *
	 * @since  3.16.3
	 * @access private
	 * @var array $instances An array of {@link Wordlift_File_Cache_Service} instances.
	 */
	private static $instances = array();

	private static $instance;

	/**
	 * Create a {@link Wordlift_File_Cache_Service} instance.
	 *
	 * The File Cache Service requires a base cache directory (to which a unique
	 * id for the current site will be appended) and a file extension for cache
	 * files (by default `.wlcache`) is used.
	 *
	 * @param string $cache_dir The base cache directory.
	 * @param string $file_extension The file extension, by default `.wlcache`.
	 *
	 * @since 3.16.0
	 */
	public function __construct( $cache_dir, $file_extension = '.wlcache' ) {

		$this->log = Wordlift_Log_Service::get_logger( get_class() );

		// Set the cache directory using the base directory provided by the caller
		// and appending a hash for the unique site id.
		$this->cache_dir      = trailingslashit( $cache_dir ) . md5( get_site_url() ) . '/';
		$this->file_extension = $file_extension;

		// Create the cache dir.
		if ( ! file_exists( $this->cache_dir ) ) {
			// phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
			@mkdir( $this->cache_dir, 0755, true );
		}

		// Add ourselves to the list of instances.
		self::$instances[] = $this;

		// Initialize the singleton and the ajax method.
		if ( ! isset( self::$instance ) ) {
			self::$instance = $this;

			add_action( 'wp_ajax_wl_file_cache__flush_all', array( 'Wordlift_File_Cache_Service', 'flush_all' ) );
		}

		$this->log->debug( "File Cache service initialized on $this->cache_dir." );

	}

	/**
	 * Get the cached response for the specified `id`.
	 *
	 * @param int $id The cache `id`.
	 *
	 * @return mixed|false The cached contents or false if the cache isn't found.
	 * @since 3.16.0
	 */
	public function get_cache( $id ) {

		// Bail out if we don't have the cache.
		if ( ! $this->has_cache( $id ) ) {
			return false;
		}

		// Get the filename.
		$filename = $this->get_filename( $id );

		$this->log->trace( "Trying to get cache contents for $id from $filename..." );

		// Try to decode the contents.
		// phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
		$contents = json_decode( file_get_contents( $filename ), true );

		// Return false if decoding failed, otherwise the decoded contents.
		return $contents ? $contents : false;
	}

	/**
	 * Set the cache contents for the specified `id`.
	 *
	 * @param int $id The cache id.
	 *
	 * @return bool True if the `id` has a cache.
	 * @since 3.16.0
	 */
	public function has_cache( $id ) {

		// Get the filename.
		$filename = $this->get_filename( $id );

		// Bail out if the file doesn't exist.
		return file_exists( $filename );
	}

	/**
	 * @inheritdoc
	 */
	public function set_cache( $id, $contents ) {

		$filename = $this->get_filename( $id );

		$this->log->trace( "Writing cache contents for $id to $filename..." );

		// phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged,WordPress.WP.AlternativeFunctions.file_system_read_file_put_contents
		@file_put_contents( $filename, wp_json_encode( $contents ) );

	}

	/**
	 * Delete the cache for the specified `id`.
	 *
	 * @param int $id The cache `id`.
	 *
	 * @since 3.16.0
	 */
	public function delete_cache( $id ) {

		$filename = $this->get_filename( $id );

		$this->log->trace( "Deleting cache contents for $id, file $filename..." );

		if ( file_exists( $filename ) ) {
			// phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
			@unlink( $filename );
		}

	}

	/**
	 * Flush the whole cache.
	 *
	 * @since 3.16.0
	 */
	public function flush() {

		// Bail out if the cache dir isn't set.
		if ( empty( $this->cache_dir ) || '/' === $this->cache_dir ) {
			return;
		}

		$this->log->trace( "Flushing cache contents from $this->cache_dir..." );

		// phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
		$handle = @opendir( $this->cache_dir );

		// Bail out if the directory can't be opened.
		if ( false === $handle ) {
			return;
		}

		// Calculate the file extension length for matching file names.
		$file_extension_length = strlen( $this->file_extension );

		// Loop into the directory to delete files.
		while ( false !== ( $entry = readdir( $handle ) ) ) { //phpcs:ignore WordPress.CodeAnalysis.AssignmentInCondition.FoundInWhileCondition
			if ( substr( $entry, - $file_extension_length ) === $this->file_extension
				 && file_exists( $this->cache_dir . $entry ) ) {
				$this->log->trace( "Deleting file {$this->cache_dir}{$entry}..." );

				// phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
				@unlink( $this->cache_dir . $entry );
			}
		}

		// Finally closed the directory.
		closedir( $handle );

	}

	public static function flush_all() {

		foreach ( self::$instances as $instance ) {
			$instance->flush();
		}

		if ( defined( 'DOING_AJAX' ) && DOING_AJAX
			 && isset( $_REQUEST['action'] ) && 'wl_file_cache__flush_all' === $_REQUEST['action'] ) { //phpcs:ignore WordPress.Security.NonceVerification.Recommended
			wp_send_json_success();
		}

	}

	/**
	 * Get the filename holding the cache contents for the specified `id`.
	 *
	 * @param int $id The cache `id`.
	 *
	 * @return string The filename.
	 * @since 3.16.0
	 */
	private function get_filename( $id ) {

		return $this->cache_dir . md5( $id ) . $this->file_extension;
	}

}
