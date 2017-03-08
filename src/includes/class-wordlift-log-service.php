<?php

/**
 * The Log service.
 *
 * @since 1.0.0
 */
class Wordlift_Log_Service {

	const MESSAGE_TEMPLATE = '%-6s [%-40.40s] %s';

	const ERROR = 'ERROR';
	const WARN = 'WARN';
	const INFO = 'INFO';
	const DEBUG = 'DEBUG';
	const TRACE = 'TRACE';


	/**
	 * The class related to the logs.
	 *
	 * @since  1.0.0
	 * @access private
	 * @var string $class_name The class related to the logs.
	 */
	private $class_name;

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

		// If we're tracing or debugging, but the debug flag isn't set, then we don't log.
		if ( ( self::TRACE === $level || self::DEBUG === $level ) && ( ! defined( 'WP_DEBUG' ) || ! WP_DEBUG ) ) {
			return;
		}

		error_log( sprintf( self::MESSAGE_TEMPLATE, $level, $this->class_name, is_array( $message ) ? implode( ', ', $message ) : $message ) );

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
