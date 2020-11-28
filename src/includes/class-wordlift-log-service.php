<?php

/**
 * The Log service.
 *
 * @since 1.0.0
 */
class Wordlift_Log_Service {

	const MESSAGE_TEMPLATE = '%-6s [%-40.40s] %s';

	const ERROR = 4;
	const WARN = 3;
	const INFO = 2;
	const DEBUG = 1;
	const TRACE = 0;

	/**
	 * The class related to the logs.
	 *
	 * @since  1.0.0
	 * @access private
	 * @var string $class_name The class related to the logs.
	 */
	private $class_name;

	/**
	 * The log levels for printing in log lines.
	 *
	 * @var array $levels An array of log levels.
	 */
	private static $levels = array(
		self::TRACE => 'TRACE',
		self::DEBUG => 'DEBUG',
		self::INFO  => 'INFO',
		self::WARN  => 'WARN',
		self::ERROR => 'ERROR',
	);

	/**
	 * A singleton instance for legacy logging.
	 *
	 * @since  3.10.0
	 * @access private
	 * @var \Wordlift_Log_Service $instance A singleton instance for legacy logging.
	 */
	private static $instance = null;

	/**
	 * Create an instance of the Log service.
	 * @since 1.0.0
	 *
	 * @param string $class_name The class related to the logs.
	 */
	public function __construct( $class_name ) {

		$this->class_name = $class_name;

	}

	/**
	 * Get the ROOT logger.
	 *
	 * @since 3.10.0
	 *
	 * @return \Wordlift_Log_Service A singleton instance for legacy logging.
	 */
	public static function get_instance() {

		return self::$instance ?: self::$instance = new Wordlift_Log_Service( 'ROOT' );
	}


	public static function get_logger( $class_name ) {

		return new Wordlift_Log_Service( $class_name );

	}

	/**
	 * Log a message.
	 *
	 * @since 1.0.0
	 *
	 * @param string $level   The log level.
	 * @param string $message The message to log.
	 */
	public function log( $level, $message ) {

		echo( sprintf( self::MESSAGE_TEMPLATE, self::$levels[ $level ], $this->class_name, is_array( $message ) ? implode( ', ', $message ) : $message ) );

		// Bail out if `WL_DEBUG` isn't defined or it's false.
		if ( ! defined( 'WL_DEBUG') || false === WL_DEBUG ) {
			return;
		}

		// Bail out if WordLift log level isn't defined, and WP debug is disabled.
		if ( ! defined( 'WL_LOG_LEVEL' ) && $level < self::ERROR
			 && ( ! defined( 'WP_DEBUG' ) || false === WP_DEBUG ) ) {
			return;
		}

		// Bail out if the log message is below the minimum log level.
		if ( defined( 'WL_LOG_LEVEL' ) && $level < intval( WL_LOG_LEVEL ) ) {
			return;
		}

		// Bail out if there's a filter and we don't match it.
		$class_name = wp_slash( $this->class_name );
		if ( defined( 'WL_LOG_FILTER' ) && 1 !== preg_match( "/(^|,)$class_name($|,)/", WL_LOG_FILTER ) ) {
			return;
		}

		// Finally log the message.
		error_log( sprintf( self::MESSAGE_TEMPLATE, self::$levels[ $level ], $this->class_name, is_array( $message ) ? implode( ', ', $message ) : $message ) );

	}

	public function error( $message ) {

		$this->log( self::ERROR, $message );

	}

	public function warn( $message ) {

		$this->log( self::WARN, $message );

	}

	public function info( $message ) {

		$this->log( self::INFO, $message );

	}

	public function debug( $message ) {

		$this->log( self::DEBUG, $message );

	}

	public function trace( $message ) {

		$this->log( self::TRACE, $message );

	}

}
