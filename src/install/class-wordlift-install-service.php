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
	 * A {@link Wordlift_Log_Service} instance.
	 *
	 * @since  3.18.0
	 * @access private
	 * @var \Wordlift_Log_Service $log A {@link Wordlift_Log_Service} instance.
	 */
	private $log;

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

		/** Installs. */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'install/class-wordlift-install.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'install/class-wordlift-install-1-0-0.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'install/class-wordlift-install-3-10-0.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'install/class-wordlift-install-3-12-0.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'install/class-wordlift-install-3-14-0.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'install/class-wordlift-install-3-15-0.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'install/class-wordlift-install-3-18-0.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'install/class-wordlift-install-3-18-3.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'install/class-wordlift-install-3-19-5.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'install/class-wordlift-install-3-20-0.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'install/class-wordlift-install-all-entity-types.php';


		self::$instance = $this;

		$this->log = Wordlift_Log_Service::get_logger( get_class() );

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
	 * @since 3.20.0 use a transient to avoid concurrent installation calls.
	 * @since 3.18.0
	 *
	 * @return void
	 */
	public function install() {

		if ( false === get_transient( '_wl_installing' ) ) {
			set_transient( '_wl_installing', true, 5 * MINUTE_IN_SECONDS );

			$this->do_install();

			delete_transient( '_wl_installing' );
		}

	}

	/**
	 * Perform the actual installation.
	 *
	 * @since 3.20.0
	 */
	private function do_install() {

		// Get the install services.
		$installs = array(
			new Wordlift_Install_1_0_0(),
			new Wordlift_Install_3_10_0(),
			new Wordlift_Install_3_12_0(),
			new Wordlift_Install_3_14_0(),
			new Wordlift_Install_3_15_0(),
			new Wordlift_Install_3_18_0(),
			new Wordlift_Install_3_18_3(),
			new Wordlift_Install_3_19_5(),
			new Wordlift_Install_3_20_0(),
			new Wordlift_Install_All_Entity_Types(),
		);

		$version = null;

		/** @var Wordlift_Install $install */
		foreach ( $installs as $install ) {
			// Get the install version.
			$version = $install->get_version();

			if ( version_compare( $version, $this->get_current_version(), '>' )
			     || $install->must_install() ) {

				$class_name = get_class( $install );

				$this->log->info( "Current version is {$this->get_current_version()}, installing $class_name..." );

				// Install version.
				$install->install();

				$this->log->info( "$class_name installed." );

				// Bump the version.
				update_option( 'wl_db_version', $version );

			}

		}

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
