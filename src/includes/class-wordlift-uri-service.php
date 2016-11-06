<?php
/**
 * Define the {@link Wordlift_Uri_Service} responsible for managing entity URIs
 * (for posts, entities, authors, ...).
 */

/**
 */
class Wordlift_Uri_Service {

	/**
	 * A {@link Wordlift_Log_Service} instance.
	 *
	 * @since  3.6.0
	 * @access private
	 * @var \Wordlift_Log_Service $log A {@link Wordlift_Log_Service} instance.
	 */
	private $log;

	/**
	 * The global WordPress database connection.
	 *
	 * @since  3.6.0
	 * @access private
	 * @var \wpdb $wpdb The global WordPress database connection.
	 */
	private $wpdb;

	/**
	 * The {@link Wordlift_Uri_Service} singleton instance.
	 *
	 * @since  3.7.2
	 * @access private
	 * @var \Wordlift_Uri_Service The {@link Wordlift_Uri_Service} singleton instance.
	 */
	private static $instance;

	/**
	 * Create an instance of Wordlift_Uri_Service.
	 *
	 * @since 3.6.0
	 *
	 * @param \wpdb $wpdb The global WordPress database connection.
	 */
	public function __construct( $wpdb ) {

		$this->log = Wordlift_Log_Service::get_logger( 'Wordlift_Uri_Service' );

		$this->wpdb = $wpdb;

		self::$instance = $this;

	}

	/**
	 * Get the {@link Wordlift_Uri_Service} singleton instance.
	 *
	 * @since 3.7.2
	 * @return \Wordlift_Uri_Service The {@link Wordlift_Uri_Service} singleton instance.
	 */
	public static function get_instance() {

		return self::$instance;
	}

	/**
	 * Delete all generated URIs from the database.
	 *
	 * @since 3.6.0
	 */
	public function delete_all() {

		// Delete URIs associated with posts/entities.
		$this->wpdb->delete( $this->wpdb->postmeta, array( 'meta_key' => 'entity_url' ) );

		// Delete URIs associated with authors.
		$this->wpdb->delete( $this->wpdb->usermeta, array( 'meta_key' => '_wl_uri' ) );

	}

	/**
	 * Sanitizes an URI path by replacing the non allowed characters with an underscore.
	 *
	 * @since 3.7.2
	 * @uses  sanitize_title() to manage not ASCII chars
	 *
	 * @see   https://codex.wordpress.org/Function_Reference/sanitize_title
	 *
	 * @param string $path The path to sanitize.
	 * @param string $char The replacement character (by default an underscore).
	 *
	 * @return string The sanitized path.
	 */
	public function sanitize_path( $path, $char = '_' ) {

		// Ensure the path is ASCII.
		// see https://github.com/insideout10/wordlift-plugin/issues/386
		$path_ascii = mb_convert_encoding( $path, 'ASCII' );

		// wl_write_log( "wl_sanitize_uri_path [ path :: $path ][ char :: $char ]" );

		// According to RFC2396 (http://www.ietf.org/rfc/rfc2396.txt) these characters are reserved:
		// ";" | "/" | "?" | ":" | "@" | "&" | "=" | "+" |
		// "$" | ","
		// Plus the ' ' (space).
		// TODO: We shall use the same regex used by MediaWiki (http://stackoverflow.com/questions/23114983/mediawiki-wikipedia-url-sanitization-regex)

		return sanitize_title( preg_replace( '/[;\/?:@&=+$,\s]/', $char, stripslashes( $path_ascii ) ) );
	}

}
