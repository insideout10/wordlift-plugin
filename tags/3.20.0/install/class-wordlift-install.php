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

	/**
	 * The default install version. Overridden by the installation packages.
	 *
	 * @since 3.18.0
	 * @access protected
	 * @var string $version The install version.
	 */
	protected static $version = '0.0.0';

	/**
	 * Wordlift_Install_Service constructor.
	 *
	 * @since 3.18.0
	 */
	public function __construct() {

		$this->log = Wordlift_Log_Service::get_logger( get_class( $this ) );

	}

	/**
	 * Return the current version of the installation.
	 *
	 * @since 3.18.0
	 */
	public function get_version() {
		return static::$version;
	}

	/**
	 * Run the install procedure. This function must be implemented by superclasses.
	 *
	 * @since 3.18.0
	 *
	 * @return mixed The result.
	 */
	abstract public function install();

	/**
	 * A custom procedure run by the caller to determine whether the install procedure must be run.
	 *
	 * @since 3.20.0
	 *
	 * @return bool True if the procedure must run.
	 */
	public function must_install() {

		return false;
	}

}
