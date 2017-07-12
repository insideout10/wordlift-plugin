<?php

/**
 * Define the Wordlift_Sparql_Service class.
 */

/**
 * The Wordlift_Sparql_Service class provides functions related to SPARQL queries.
 *
 * @since 3.6.0
 */
class Wordlift_Sparql_Service {

	/**
	 * A {@link Wordlift_Log_Service} instance.
	 *
	 * @since  3.6.0
	 * @access private
	 * @var \Wordlift_Log_Service $log A {@link Wordlift_Log_Service} instance.
	 */
	private $log;

	/**
	 * The {@link Wordlift_Sparql_Service} singleton instance.
	 *
	 * @since  3.6.0
	 * @access private
	 * @var \Wordlift_Sparql_Service $instance The {@link Wordlift_Sparql_Service} singleton instance.
	 */
	private static $instance;

	/**
	 * Create a {@link Wordlift_Sparql_Service} instance.
	 *
	 * @since 3.6.0
	 */
	public function __construct() {

		$this->log = Wordlift_Log_Service::get_logger( 'Wordlift_Sparql_Service' );

		self::$instance = $this;

	}

	/**
	 * Get the singleton instance of the {@link Wordlift_Sparql_Service}.
	 *
	 * @since 3.6.0
	 * @return \Wordlift_Sparql_Service
	 */
	public static function get_instance() {

		return self::$instance;
	}

	/**
	 * Queue a SPARQL statement for execution.
	 *
	 * @since 3.6.0
	 *
	 * @param string $stmt The SPARQL statement.
	 */
	public function queue( $stmt ) {

		rl_execute_sparql_update_query( $stmt );

	}

	/**
	 * Execute the SELECT query.
	 *
	 * @since 3.12.2
	 *
	 * @param string $query The SELECT query to execute.
	 *
	 * @return WP_Error|array The response or WP_Error on failure.
	 */
	public function select( $query ) {

		// Prepare the SPARQL statement by prepending the default namespaces.
		$sparql = rl_sparql_prefixes() . "\n" . $query;

		// Get the SPARQL SELECT URL.
		$url = wl_configuration_get_query_select_url( 'csv' ) . urlencode( $sparql );

		// Prepare the request.
		$args = unserialize( WL_REDLINK_API_HTTP_OPTIONS );

		return wp_remote_get( $url, $args );
	}

	/**
	 * Formats the provided value according to the specified type in order to
	 * insert the value using SPARQL. The value is also escaped.
	 *
	 * @since 3.6.0
	 *
	 * @param string $value The value.
	 * @param string $type  The value type.
	 *
	 * @return string The formatted value for SPARQL statements.
	 */
	public function format( $value, $type ) {

		// see https://www.w3.org/TR/sparql11-query/.

		switch ( $type ) {

			case Wordlift_Schema_Service::DATA_TYPE_BOOLEAN:

				// SPARQL supports 'true' and 'false', so we evaluate the $value
				// and return true/false accordingly.
				return $value ? 'true' : 'false';

			case Wordlift_Schema_Service::DATA_TYPE_DATE:

				return sprintf( '"%s"^^xsd:date', self::escape( $value ) );


			case Wordlift_Schema_Service::DATA_TYPE_DOUBLE:

				return sprintf( '"%s"^^xsd:double', self::escape( $value ) );

			case Wordlift_Schema_Service::DATA_TYPE_INTEGER:

				return sprintf( '"%s"^^xsd:integer', self::escape( $value ) );

			case Wordlift_Schema_Service::DATA_TYPE_STRING:

				return sprintf( '"%s"^^xsd:string', self::escape( $value ) );

			case Wordlift_Schema_Service::DATA_TYPE_URI:

				return sprintf( '<%s>', self::escape_uri( $value ) );

			default:

				$this->log->warn( "Unknown data type [ type :: $type ]" );

				// Try to insert the value anyway.
				return sprintf( '"%s"', self::escape( $value ) );
		}

	}

	/**
	 * Escapes an URI for a SPARQL statement.
	 *
	 * @since 3.6.0
	 *
	 * @param string $uri The URI to escape.
	 *
	 * @return string The escaped URI.
	 */
	public static function escape_uri( $uri ) {

		// Should we validate the IRI?
		// http://www.w3.org/TR/sparql11-query/#QSynIRI

		$uri = str_replace( '<', '\<', $uri );
		$uri = str_replace( '>', '\>', $uri );

		return $uri;
	}

	/**
	 * Escapes a string for a SPARQL statement.
	 *
	 * @since 3.6.0
	 *
	 * @param string $string The string to escape.
	 *
	 * @return string The escaped string.
	 */
	public static function escape( $string ) {

		// see http://www.w3.org/TR/rdf-sparql-query/
		//    '\t'	U+0009 (tab)
		//    '\n'	U+000A (line feed)
		//    '\r'	U+000D (carriage return)
		//    '\b'	U+0008 (backspace)
		//    '\f'	U+000C (form feed)
		//    '\"'	U+0022 (quotation mark, double quote mark)
		//    "\'"	U+0027 (apostrophe-quote, single quote mark)
		//    '\\'	U+005C (backslash)

		$string = str_replace( '\\', '\\\\', $string );
		$string = str_replace( '\'', '\\\'', $string );
		$string = str_replace( '"', '\\"', $string );
		$string = str_replace( "\f", '\\f', $string );
		$string = str_replace( "\b", '\\b', $string );
		$string = str_replace( "\r", '\\r', $string );
		$string = str_replace( "\n", '\\n', $string );
		$string = str_replace( "\t", '\\t', $string );

		return $string;
	}

}
