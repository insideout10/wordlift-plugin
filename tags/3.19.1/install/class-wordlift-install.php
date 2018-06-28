<?php
/**
 * Installs: Install interface.
 *
 * The interface for Installations.
 *
 * @since      3.18.0
 * @package    Wordlift
 * @subpackage Wordlift/install
 */

/**
 * Define the {@link Wordlift_Install} interface.
 *
 * @since      3.18.0
 * @package    Wordlift
 * @subpackage Wordlift/install
 */
abstract class Wordlift_Install {

	/**
	 * A {@link Wordlift_Log_Service} instance.
	 *
	 * @since  3.18.0
	 * @access private
	 * @var \Wordlift_Log_Service $log A {@link Wordlift_Log_Service} instance.
	 */
	protected $log;

	protected static $version = '0.0.0';

	/**
	 * Wordlift_Install_Service constructor.
	 *
	 * @since 3.18.0
	 */
	public function __construct() {

		$this->log = Wordlift_Log_Service::get_logger( 'Wordlift_Install_' . str_replace( '.', '_', static::$version ) );
	}

	/**
	 * Return the current version of the installation.
	 *
	 * @since 3.18.0
	 */
	public function get_version() {
		return static::$version;
	}

	abstract public function install();

}
