<?php
/**
 * Installs: Install Service.
 *
 * The Installation Service.
 *
 * @since      3.18.0
 * @package    Wordlift
 * @subpackage Wordlift/install
 */

/**
 * Define the {@link Wordlift_Install_Service} interface.
 *
 * @since      3.18.0
 * @package    Wordlift
 * @subpackage Wordlift/install
 */
class Wordlift_Install_Service {

	/**
	 * The singleton instance.
	 *
	 * @since  3.18.0
	 * @access private
	 * @var \Wordlift_Install_Service $instance A {@link Wordlift_Install_Service} instance.
	 */
	private static $instance;


	/**
	 * Wordlift_Install_Service constructor.
	 *
	 * @since 3.18.0
	 */
	public function __construct() {

		self::$instance = $this;
	}

	/**
	 * Get the singleton instance.
	 *
	 * @since 3.18.0
	 */
	public static function get_instance() {

		return self::$instance;
	}

	/**
	 * Loop thought all versions and install the updates.
	 *
	 * @since 3.18.0
	 *
	 * @return void
	 */
	public function install() {

		// Get the install services.
		$installs = array(
			new Wordlift_Install_1_0_0(),
			new Wordlift_Install_3_10_0(),
			new Wordlift_Install_3_12_0(),
			new Wordlift_Install_3_14_0(),
			new Wordlift_Install_3_15_0(),
		);

		/** @var Wordlift_Install $install */
		foreach ( $installs as $install ) {
			// Get the install version.
			$version = $install->get_version();

			if ( version_compare( $version, $this->get_current_version(), '>=' ) ) {
				// Install version.
				$install->install();

			}
		}

		// Bump the `wl_db_version`.
		update_option( 'wl_db_version', $version );
	}

	/**
	 * Retrieve the current db version.
	 *
	 * @return type
	 */
	private function get_current_version() {
		return get_option( 'wl_db_version', '0.0.0' );
	}

}
