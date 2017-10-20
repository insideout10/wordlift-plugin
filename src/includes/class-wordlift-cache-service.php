<?php
/**
 * Services: Cache Service.
 *
 * Define the cache Service.
 *
 * @since      3.16.0
 * @package    Wordlift
 * @subpackage Wordlift/includes
 */

/**
 * The Wordlift_Cache_Service provides functions to cache information.
 *
 * @since      3.16.0
 * @package    Wordlift
 * @subpackage Wordlift/includes
 */
class Wordlift_Cache_Service {

	/**
	 * Pattern used to sanitize types and ids to ensure valid file names will be
	 * generated with them
	 *
	 * @since  3.16.0
	 */
	const VALIDATION_PATTERM = '/[^a-z0-9\_]/';

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

		$this->type = preg_replace( self::VALIDATION_PATTERM, '', $type );

		if ( WP_DEBUG && ( $this->type !== $type ) ) {
			throw new Exception( "type is not simple lowercase string type=$type" );
		}
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

		$id = self::sanitize_id( $id );

		$dir = WL_TEMP_DIR . $this->type;

		$filename = $dir . '/' . $id;

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

		$id = self::sanitize_id( $id );

		$dir = WL_TEMP_DIR . $this->type;

		if ( ! file_exists( $dir ) ) {
			mkdir( $dir );
		}

		$filename = $dir . '/' . $id;

		// Create the content saved in the file. It consist of a json of
		// array containing the expired time and the value.
		$content = array(
			'expire' => (0 !== $expiry) ? 0 : time() + expiry,
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

		$id = self::sanitize_id( $id );

		$dir = WL_TEMP_DIR . $this->type;

		$filename = $dir . '/' . $id;
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

		$dir = WL_TEMP_DIR . $this->type;

		if ( ! file_exists( $dir ) ) {
			return;
		}

		$files = glob( "$dir/*" );
		if ( $files ) {
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
		$s_id = preg_replace( self::VALIDATION_PATTERM, '', $id );

		if ( WP_DEBUG && ( $s_id !== (string) $id ) ) {
			throw new Exception( "id is not a simple lower case string id=$id" );
		}

		return $s_id;
	}
}
