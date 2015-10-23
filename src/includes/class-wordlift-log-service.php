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
	 * @since 1.0.0
	 * @access private
	 * @var string $class_name The class related to the logs.
	 */
	private $class_name;

	/**
	 * Create an instance of the Log service.
	 * @since 1.0.0
	 *
	 * @param string $class_name The class related to the logs.
	 */
	public function __construct( $class_name ) {

		$this->class_name = $class_name;

	}

	public static function get_logger( $class_name ) {

		return new Wordlift_Log_Service( $class_name );

	}

	/**
	 * Log a message.
	 *
	 * @since 1.0.0
	 *
	 * @param string $level The log level.
	 * @param string $message The message to log.
	 */
	public function log( $level, $message ) {

		error_log( sprintf( self::MESSAGE_TEMPLATE, $level, $this->class_name, $message ) );

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
