<?php
/**
 * Services: URI Service.
 *
 * Define the {@link Wordlift_Uri_Service} responsible for managing entity URIs
 * (for posts, entities, authors, ...).
 *
 * @since   3.7.1
 * @package Wordlift
 */

/**
 * The {@link Wordlift_Uri_Service} class.
 *
 * @since   3.7.1
 * @package Wordlift
 */
class Wordlift_Uri_Service {

	/**
	 * The title regex to sanitize titles in paths.
	 *
	 * According to RFC2396 (http://www.ietf.org/rfc/rfc2396.txt) these characters are reserved:
	 * ";" | "/" | "?" | ":" | "@" | "&" | "=" | "+" |
	 * "$" | ","
	 *
	 * We also remove the space and the UTF-8 BOM sequence.
	 *
	 * @since 3.7.1
	 */
	const INVALID_CHARACTERS = "/[ ;\\/?:@&=\\+\\\$,]|(?:\\xEF\\xBB\\xBF)/";

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
//		$path_ascii = mb_convert_encoding( $path, 'ASCII' );

		return sanitize_title( preg_replace( self::INVALID_CHARACTERS, $char, stripslashes( $path ) ) );
	}

	/**
	 * Build an entity uri for a given title. The uri is composed using a given
	 * post_type and a title. If already exists an entity e2 with a given uri a
	 * numeric suffix is added. If a schema type is given entities with same label
	 * and same type are overridden.
	 *
	 * @since 3.5.0
	 *
	 * @param string  $title           A post title.
	 * @param string  $post_type       A post type. Default value is 'entity'
	 * @param string  $schema_type     A schema org type.
	 * @param integer $increment_digit A digit used to call recursively the same function.
	 *
	 * @return string Returns an uri.
	 */
	public function build_uri( $title, $post_type, $schema_type = null, $increment_digit = 0 ) {

		// Get the entity slug suffix digit
		$suffix_digit = $increment_digit + 1;

		// Get a sanitized uri for a given title
		$entity_slug = ( 0 == $increment_digit ) ?
			wl_sanitize_uri_path( $title ) :
			wl_sanitize_uri_path( $title . '_' . $suffix_digit );

		// Compose a candidate uri.
		$new_entity_uri = sprintf( '%s/%s/%s',
			wl_configuration_get_redlink_dataset_uri(),
			$post_type,
			$entity_slug
		);

		$this->log->trace( "Going to check if uri is used [ new_entity_uri :: $new_entity_uri ] [ increment_digit :: $increment_digit ]" );

		global $wpdb;

		// Check if the candidated uri already is used
		$stmt = $wpdb->prepare(
			"SELECT post_id FROM $wpdb->postmeta WHERE meta_key = %s AND meta_value = %s LIMIT 1",
			WL_ENTITY_URL_META_NAME,
			$new_entity_uri
		);

		// Perform the query
		$post_id = $wpdb->get_var( $stmt );

		// If the post does not exist, then the new uri is returned
		if ( ! is_numeric( $post_id ) ) {
			$this->log->trace( "Going to return uri [ new_entity_uri :: $new_entity_uri ]" );

			return $new_entity_uri;
		}

		// If schema_type is equal to schema org type of post x, then the new uri is returned
		$schema_post_type = wl_entity_type_taxonomy_get_type( $post_id );

		// @todo: we shouldn't rely on css classes to take such decisions.
		if ( $schema_type === $schema_post_type['css_class'] ) {
			$this->log->trace( "An entity with the same title and type already exists! Return uri [ new_entity_uri :: $new_entity_uri ]" );

			return $new_entity_uri;
		}

		// Otherwise the same function is called recursively
		return $this->build_uri( $title, $post_type, $schema_type, ++ $increment_digit );
	}

}
