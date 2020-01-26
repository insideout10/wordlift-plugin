<?php
/**
 * Services: Sparql Service.
 *
 * Define the Wordlift_Sparql_Service class.
 *
 * @since      3.6.0
 * @package    Wordlift
 * @subpackage Wordlift/includes
 */

/**
 * The Wordlift_Sparql_Service class provides functions related to SPARQL queries.
 *
 * @since      3.6.0
 * @package    Wordlift
 * @subpackage Wordlift/includes
 */
class Wordlift_Sparql_Service {

	/**
	 * A {@link Wordlift_Log_Service} instance.
	 *
	 * @since  3.6.0
	 * @access private
	 * @var \Wordlift_Log_Service $log A {@link Wordlift_Log_Service} instance.
	 */
	private static $log;

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

		self::$log = Wordlift_Log_Service::get_logger( 'Wordlift_Sparql_Service' );

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
	 * @param bool   $queue Whether to queue the statement for asynchronous
	 *                      execution.
	 */
	public function execute( $stmt, $queue = WL_ENABLE_SPARQL_UPDATE_QUERIES_BUFFERING ) {

		rl_execute_sparql_update_query( $stmt, $queue );

	}

	/**
	 * Run the SPARQL queries buffered for the specified request id.
	 *
	 * @since 3.13.2
	 *
	 * @param string $request_id A unique request id.
	 */
	public function run_sparql_query( $request_id ) {

		self::$log->debug( "Running SPARQL queries..." );

		// Look for a free temporary filename.
		for ( $index = 1; $index < PHP_INT_MAX; $index ++ ) {
			$filename = WL_TEMP_DIR . $request_id . "-$index.sparql";

			// Bail out if there are no files left.
			if ( ! file_exists( $filename ) ) {
				self::$log->trace( "$filename not found." );

				break;
			}

			self::$log->debug( "Running SPARQL from $filename..." );

			// Get the query saved in the file.
			$query = file_get_contents( $filename );

			// Execute the SPARQL query.
			rl_execute_sparql_update_query( $query, false );

			// Delete the temporary file.
			unlink( $filename );
		}

		// Reindex the triple store.
		wordlift_reindex_triple_store();

	}

	/**
	 * Queue a SPARQL statement for asynchronous execution.
	 *
	 * @since 3.13.2
	 *
	 * @param string $stmt The SPARQL statement.
	 *
	 * @throws Exception
	 */
	public function queue( $stmt ) {

		// Get a temporary filename.
		$filename = $this->get_temporary_file_for_sparql();

		self::$log->debug( "Buffering SPARQL to file $filename..." );

		// Write the contents to the temporary filename.
		@file_put_contents( $filename, $stmt . "\n", FILE_APPEND );

	}

	/**
	 * Get a temporary filename where to store SPARQL queries.
	 *
	 * @since 3.13.2
	 *
	 * @return string The filename.
	 * @throws Exception An exception is thrown if there are already 1.000
	 *                   temporary files for this request.
	 */
	private function get_temporary_file_for_sparql() {

		// Look for a free temporary filename.
		for ( $index = 1; $index < PHP_INT_MAX; $index ++ ) {
			$filename = WL_TEMP_DIR . WL_REQUEST_ID . "-$index.sparql";

			if ( ! file_exists( $filename ) ) {

				// Only if this it the first buffered SPARQL, then launch the
				// action which will be handled by the Async Task. The Async
				// Task will take care of all the buffered files _on shutdown_.
				if ( 1 === $index ) {
					do_action( 'wl_run_sparql_query', WL_REQUEST_ID );
				}

				// Return the temporary filename.
				return $filename;
			}
		}

		throw new Exception( 'Cannot create a temporary file [ ' . WL_TEMP_DIR . WL_REQUEST_ID . ' ].' );
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
		$url = wl_configuration_get_query_select_url() . urlencode( $sparql );

		$http_options = unserialize( WL_REDLINK_API_HTTP_OPTIONS );

		/**
		 * Filter: 'wl_sparql_select_http_args' - Allow third parties to hook and add additional HTTP args.
		 *
		 * @since 3.17.0
		 *
		 * @param   array $http_options Current http options.
		 */
		$args = apply_filters( 'wl_sparql_select_http_args', $http_options );

		return wp_remote_get( $url, $args );
	}

	/**
	 * Formats the provided value according to the specified type in order to
	 * insert the value using SPARQL. The value is also escaped.
	 *
	 * @since 3.6.0
	 *
	 * @param string      $value The value.
	 * @param string      $type The value type.
	 * @param string|null $language The language tag or null if not set.
	 *
	 * @return string The formatted value for SPARQL statements.
	 */
	public static function format( $value, $type = null, $language = null ) {

		// see https://www.w3.org/TR/sparql11-query/.

		switch ( $type ) {

			case Wordlift_Schema_Service::DATA_TYPE_BOOLEAN:
				// SPARQL supports 'true' and 'false', so we evaluate the $value
				// and return true/false accordingly.
				return $value ? 'true' : 'false';

			case Wordlift_Schema_Service::DATA_TYPE_DATE:
				$date       = date_create_from_format( 'Y/m/d', $value );
				$date_value = date_format( $date, 'Y-m-d' );

				return sprintf( '"%s"^^xsd:date', self::escape( $date_value ) );

			case Wordlift_Schema_Service::DATA_TYPE_DATE_TIME:
				$date       = date_create_from_format( 'Y/m/d H:i', $value );
				$date_value = date_format( $date, 'Y-m-d\TH:i:00' );

				return sprintf( '"%s"^^xsd:dateTime', self::escape( $date_value ) );

			case Wordlift_Schema_Service::DATA_TYPE_DURATION:
				$time       = date_create_from_format( 'H:i', $value );
				$time_value = sprintf( 'PT%dH%dM', date_format( $time, 'H' ), intval( date_format( $time, 'i' ) ) );

				return sprintf( '"%s"^^xsd:duration', self::escape( $time_value ) );

			case Wordlift_Schema_Service::DATA_TYPE_DOUBLE:
				return sprintf( '"%s"^^xsd:double', self::escape( $value ) );

			case Wordlift_Schema_Service::DATA_TYPE_INTEGER:
				return sprintf( '"%s"^^xsd:integer', self::escape( $value ) );

			case Wordlift_Schema_Service::DATA_TYPE_STRING:
				return sprintf( '"%s"^^xsd:string', self::escape( $value ) );

			case Wordlift_Schema_Service::DATA_TYPE_URI:
				/**
				 * Allow 3rd parties to change the uri.
				 *
				 * @since 3.20.0
				 *
				 * @see https://github.com/insideout10/wordlift-plugin/issues/850
				 *
				 * @param string $uri The uri.
				 */
				return sprintf( '<%s>', self::escape_uri( apply_filters( 'wl_production_uri', $value ) ) );

			case null:
				$language_tag = ( null !== $language ? "@$language" : '' );

				return sprintf( '"%s"%s', self::escape( $value ), $language_tag );

			default:

				self::$log->warn( "Unknown data type [ type :: $type ]" );

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
