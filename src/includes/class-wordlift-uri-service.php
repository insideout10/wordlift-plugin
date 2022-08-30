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

use Wordlift\Content\Post_Content_Service\Wordpress_Term_Content_Service;
use Wordlift\Content\Wordpress\Wordpress_Content_Service;

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
	 * @param \wpdb $wpdb The global WordPress database connection.
	 *
	 * @since 3.6.0
	 */
	public function __construct( $wpdb ) {

		$this->log = Wordlift_Log_Service::get_logger( 'Wordlift_Uri_Service' );

		$this->wpdb = $wpdb;

		self::$instance = $this;

	}

	/**
	 * Get the {@link Wordlift_Uri_Service} singleton instance.
	 *
	 * @return \Wordlift_Uri_Service The {@link Wordlift_Uri_Service} singleton instance.
	 * @since 3.7.2
	 */
	public static function get_instance() {

		return self::$instance;
	}

	/**
	 * Sanitizes an URI path by replacing the not allowed characters with an underscore.
	 *
	 * @param string $path The path to sanitize.
	 * @param string $char The replacement character (by default an underscore).
	 *
	 * @return string The sanitized path.
	 * @since 3.7.2
	 * @uses  sanitize_title() to manage not ASCII chars
	 *
	 * @see   https://codex.wordpress.org/Function_Reference/sanitize_title
	 */
	public function sanitize_path( $path, $char = '_' ) {

		// Ensure the path is ASCII.
		// see https://github.com/insideout10/wordlift-plugin/issues/386
		// $path_ascii = mb_convert_encoding( $path, 'ASCII' );

		return sanitize_title( preg_replace( self::INVALID_CHARACTERS, $char, stripslashes( $path ) ) );
	}

	/**
	 * Build an entity uri for a given title. The uri is composed using a given
	 * post_type and a title. If already exists an entity e2 with a given uri a
	 * numeric suffix is added. If a schema type is given entities with same label
	 * and same type are overridden.
	 *
	 * @param string  $title A post title.
	 * @param string  $post_type A post type. Default value is 'entity'
	 * @param string  $schema_type A schema org type.
	 * @param integer $increment_digit A digit used to call recursively the same function.
	 *
	 * @return string Returns an uri.
	 * @deprecated don't use anywhere else, since we're refactoring the way URIs are built in the global function wl_build_entity_uri.
	 * @since 3.5.0
	 */
	public function build_uri( $title, $post_type, $schema_type = null, $increment_digit = 0 ) {

		// Get the entity slug suffix digit
		$suffix_digit = $increment_digit + 1;

		// Get a sanitized uri for a given title.
		/*
		 * The call takes into consideration URL encoding.
		 *
		 * @see https://github.com/insideout10/wordlift-plugin/issues/885
		 *
		 * @since 3.20.0
		 */
		$entity_slug = urldecode( wl_sanitize_uri_path( $title ) )
					   . ( 0 === $increment_digit ? '' : '_' . $suffix_digit );

		// Compose a candidate uri.
		$new_entity_uri = sprintf(
			'%s/%s/%s',
			untrailingslashit( wl_configuration_get_redlink_dataset_uri() ),
			$post_type,
			$entity_slug
		);

		$this->log->trace( "Going to check if uri is used [ new_entity_uri :: $new_entity_uri ] [ increment_digit :: $increment_digit ]" );

		$content = Wordpress_Content_Service::get_instance()->get_by_entity_id( $new_entity_uri );
		$post_id = $content && is_a( $content->get_bag(), '\WP_Post' ) ? $content->get_bag()->ID : null;

		// If the post does not exist, then the new uri is returned
		if ( ! is_numeric( $post_id ) ) {
			$this->log->trace( "Going to return uri [ new_entity_uri :: $new_entity_uri ]" );

			return $new_entity_uri;
		}

		// If schema_type is equal to schema org type of post x, then the new uri is returned
		$schema_post_type = Wordlift_Entity_Type_Service::get_instance()->get( $post_id );

		// @todo: we shouldn't rely on css classes to take such decisions.
		if ( $schema_type === $schema_post_type['css_class'] ) {
			$this->log->trace( "An entity with the same title and type already exists! Return uri [ new_entity_uri :: $new_entity_uri ]" );

			return $new_entity_uri;
		}

		// Otherwise the same function is called recursively
		return $this->build_uri( $title, $post_type, $schema_type, ++ $increment_digit );
	}

	/**
	 *
	 * @param string  $title A post title.
	 * @param string  $taxonomy A post type. Default value is 'entity'
	 * @param integer $increment_digit A digit used to call recursively the same function.
	 *
	 * @return string Returns an uri.
	 * @since 3.32.0
	 */
	public function build_term_uri( $title, $taxonomy, $increment_digit = 0 ) {

		// Get the entity slug suffix digit
		$suffix_digit = $increment_digit + 1;

		$entity_slug = urldecode( $title )
					   . ( 0 === $increment_digit ? '' : '_' . $suffix_digit );

		// Compose a candidate uri.
		$new_entity_uri = sprintf(
			'%s/term/%s/%s',
			Wordlift_Configuration_Service::get_instance()->get_dataset_uri(),
			$taxonomy,
			$entity_slug
		);

		$this->log->trace( "Going to check if uri is used [ new_entity_uri :: $new_entity_uri ] [ increment_digit :: $increment_digit ]" );

		$content = Wordpress_Term_Content_Service::get_instance()->get_by_entity_id( $new_entity_uri );
		$term_id = $content ? $content->get_bag()->term_id : null;

		// If the post does not exist, then the new uri is returned
		if ( ! is_numeric( $term_id ) ) {
			$this->log->trace( "Going to return uri [ new_term_entity_uri :: $new_entity_uri ]" );

			return $new_entity_uri;
		}

		// Otherwise the same function is called recursively
		return $this->build_term_uri( $title, $taxonomy, ++ $increment_digit );
	}

}
