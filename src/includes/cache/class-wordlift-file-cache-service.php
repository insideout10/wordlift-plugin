<?php
/**
 * Created by PhpStorm.
 * User: david
 * Date: 14.11.17
 * Time: 11:49
 */

class Wordlift_File_Cache_Service implements Wordlift_Cache_Service {

	/**
	 * @var
	 */
	private $cache_dir;

	private $log;
	/**
	 * @var string
	 */
	private $file_extension;

	/**
	 * Wordlift_File_Cache_Service constructor.
	 *
	 * @param        $cache_dir
	 * @param string $file_extension
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

		$this->log->info( "File Cache service initialized on $this->cache_dir." );

	}

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

	function set_cache( $id, $contents ) {

		$filename = $this->get_filename( $id );

		$this->log->trace( "Writing cache contents for $id to $filename..." );

		file_put_contents( $filename, wp_json_encode( $contents ) );

	}

	function delete_cache( $id ) {

		$filename = $this->get_filename( $id );

		wp_delete_file( $filename );

	}

	function flush() {

		// Bail out if the cache dir isn't set.
		if ( empty( $this->cache_dir ) || '/' === $this->cache_dir ) {
			return;
		}

		$handle = @opendir( $this->cache_dir );

		// Bail out if the directory can't be opened.
		if ( false === $handle ) {
			return;
		}

		// Calculate the file extension length for matching file names.
		$file_extension_length = strlen( $this->file_extension );

		// Loop into the directory to delete files.
		while ( false !== ( $entry = readdir( $handle ) ) ) {
			if ( $this->file_extension === substr( $entry, - $file_extension_length ) ) {
				$this->log->trace( "Deleting file $entry..." );
				wp_delete_file( $entry );
			}
		}

		// Finally closed the directory.
		closedir( $handle );

	}

	private function get_filename( $id ) {

		return $this->cache_dir . md5( $id ) . $this->file_extension;
	}

}
