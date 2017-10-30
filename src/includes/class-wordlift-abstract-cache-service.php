<?php
/**
 * Services: Abstract Cache Service.
 *
 * Define the Abstract cache Service.
 *
 * While it can be used by itself to instantiate cache controllers, the
 * recommended design pattern is to inherit it to create a specific caching service.
 * Specifically the abstract implementation lack good cache invalidation, something
 * that most likely need to be part of any specific caching scheme.
 *
 * @since      3.16.0
 * @package    Wordlift
 * @subpackage Wordlift/includes
 */

/**
 * The Wordlift_Abstract_Cache_Service provides functions to cache information.
 *
 * @since      3.16.0
 * @package    Wordlift
 * @subpackage Wordlift/includes
 */
abstract class Wordlift_Abstract_Cache_Service {

	/**
	 * Pattern used to sanitize types and ids to ensure valid file names will be
	 * generated with them
	 *
	 * @since  3.16.0
	 */
	const VALIDATION_PATTERN = '/[^a-z0-9\_]/';

	/**
	 * Suffix to be used for cache file names.
	 *
	 * @since  3.16.0
	 */
	const FILE_SUFFIX = '.wlcache';

	/**
	 * A string identifying the group of item being cached by this object.
	 *
	 * @since  3.16.0
	 * @access private
	 * @var string
	 */
	private $type;

	/**
	 * Wordlift_Cache_Service constructor.
	 *
	 * @param string $type   A string identifying the group of item being cached
	 *                       by this object. Should be simple lower case string.
	 *
	 * @since 3.16.0
	 *
	 * @throws Exception An exception is thrown if $type or $id are not simple strings.
	 */
	public function __construct( $type ) {

		$this->type = preg_replace( self::VALIDATION_PATTERN, '', $type );

		if ( WP_DEBUG && ( $this->type !== $type ) ) {
			throw new Exception( "type is not simple lowercase string type=$type" );
		}
	}

	/**
	 * Get the path of the directory under which the cache files are located.
	 *
	 * @return string The directory in which the cache files will be located.
	 */
	private function get_directory_name() {
		$dir = WL_TEMP_DIR . $this->type;

		return $dir;
	}

	/**
	 * Get the path of a file which store data associated with a specific id.
	 *
	 * @param string $id The identifier of the specific data.
	 *
	 * @return string The directory in which the cache files will be located.
	 */
	private function get_filename( $id ) {
		$dir = $this->get_directory_name();

		// whoever is calling is going to try to attempt to access the file,
		// so make sure at least the directory is there.
		if ( ! file_exists( $dir ) ) {
			mkdir( $dir );
		}

		$id = self::sanitize_id( $id );

		return $dir . '/' . $id . self::FILE_SUFFIX;
	}

	/**
	 * Get a value from the cache based on type and id.
	 *
	 * @since 3.16.0
	 *
	 * @param string $id      A unique identifier for the information.
	 * @param mixed  $default The value to return if there is no cached value, defaults to false.
	 *
	 * @return mixed $default if there is no value in the cache (or expired), otherwise the cached string.
	 */
	public function get( $id, $default = false ) {

		$filename = $this->get_filename( $id );

		if ( ! file_exists( $filename ) ) {
			return $default;
		}

		$content = file_get_contents( $filename );

		// Content consist of a a json formatted
		// array containing the expired time and the value.
		$content = json_decode( $content, true );

		if ( is_null( $content ) ) { // garbage found instead of proper json.
			return $default;
		}

		if ( $content['expire'] > time() ) { // If expired.
			return $default;
		}

		return $content['value'];
	}

	/**
	 * Store a value in the cache associated with an id.
	 *
	 * @since 3.16.0
	 *
	 * @param string $id   A unique identifier for the information.
	 * @param mixed  $value The value to be stored.
	 * @param int    $expiry The maximum time in seconds until the item is expired.
	 *               Special value of 0 indicates that the cache never expires.
	 */
	public function set( $id, $value, $expiry ) {

		$filename = $this->get_filename( $id );

		// Create the content saved in the file. It consist of a json of
		// array containing the expired time and the value.
		$content = array(
			'expire' => (0 === $expiry) ? 0 : time() + $expiry,
			'value' => $value,
		);

		file_put_contents( $filename, wp_json_encode( $content ) );
	}

	/**
	 * Delete a value in the cache associated with an id.
	 *
	 * @since 3.16.0
	 *
	 * @param string $id   A unique identifier for the information.
	 */
	public function delete( $id ) {

		$filename = $this->get_filename( $id );

		if ( ! file_exists( $filename ) ) {
			return;
		}

		unlink( $filename );
	}

	/**
	 * Clear the cache if possible.
	 *
	 * @since 3.16.0
	 */
	public function flush() {

		$dir = $this->get_directory_name();

		if ( ! file_exists( $dir ) ) {
			return;
		}

		$files = glob( "$dir/*" . self::FILE_SUFFIX );
		if ( ! empty( $files ) ) {
			foreach ( $files as $file ) { // iterate files.
				if ( is_file( $file ) ) {
					unlink( $file ); // delete file.
				}
			}
		}
	}

	/**
	 * Sanitize id values into lower case strings that can be used as file names.
	 *
	 * @since 3.16.0
	 *
	 * @param string $id      A unique identifier for the information.
	 *
	 * @return string sanitized version of $id.
	 *
	 * @throws Exception An exception is thrown $id is not simple lower case string.
	 */
	static private function sanitize_id( $id ) {
		// sanitize proper file names from $id.
		$s_id = preg_replace( self::VALIDATION_PATTERN, '', $id );

		if ( WP_DEBUG && ( $s_id !== (string) $id ) ) {
			throw new Exception( "id is not a simple lower case string id=$id" );
		}

		return $s_id;
	}
}
