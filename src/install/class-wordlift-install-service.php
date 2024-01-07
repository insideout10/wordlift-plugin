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
	 * @var array
	 */
	private $installs;

	/**
	 * Wordlift_Install_Service constructor.
	 *
	 * @since 3.18.0
	 */
	public function __construct() {

		/** Installs. */
		require_once plugin_dir_path( __DIR__ ) . 'install/class-wordlift-install.php';
		require_once plugin_dir_path( __DIR__ ) . 'install/class-wordlift-install-1-0-0.php';
		require_once plugin_dir_path( __DIR__ ) . 'install/class-wordlift-install-3-10-0.php';
		require_once plugin_dir_path( __DIR__ ) . 'install/class-wordlift-install-3-12-0.php';
		require_once plugin_dir_path( __DIR__ ) . 'install/class-wordlift-install-3-14-0.php';
		require_once plugin_dir_path( __DIR__ ) . 'install/class-wordlift-install-3-15-0.php';
		require_once plugin_dir_path( __DIR__ ) . 'install/class-wordlift-install-3-18-0.php';
		require_once plugin_dir_path( __DIR__ ) . 'install/class-wordlift-install-3-18-3.php';
		require_once plugin_dir_path( __DIR__ ) . 'install/class-wordlift-install-3-19-5.php';
		require_once plugin_dir_path( __DIR__ ) . 'install/class-wordlift-install-3-20-0.php';
		require_once plugin_dir_path( __DIR__ ) . 'install/class-wordlift-install-3-23-4.php';
		require_once plugin_dir_path( __DIR__ ) . 'install/class-wordlift-install-3-24-2.php';
		require_once plugin_dir_path( __DIR__ ) . 'install/class-wordlift-install-all-entity-types.php';
		require_once plugin_dir_path( __DIR__ ) . 'install/class-wordlift-install-package-type.php';
		require_once plugin_dir_path( __DIR__ ) . 'install/class-wordlift-install-3-25-0.php';
		require_once plugin_dir_path( __DIR__ ) . 'install/class-wordlift-install-3-27-0.php';
		require_once plugin_dir_path( __DIR__ ) . 'install/class-wordlift-install-3-27-1.php';
		require_once plugin_dir_path( __DIR__ ) . 'install/class-wordlift-install-3-28-0.php';
		require_once plugin_dir_path( __DIR__ ) . 'install/class-wordlift-install-3-32-0.php';
		require_once plugin_dir_path( __DIR__ ) . 'install/class-wordlift-install-3-33-9.php';
		require_once plugin_dir_path( __DIR__ ) . 'install/class-wordlift-install-3-36-0.php';
		require_once plugin_dir_path( __DIR__ ) . 'install/class-wordlift-install-3-38-5.php';
		require_once plugin_dir_path( __DIR__ ) . 'install/class-wordlift-install-3-39-1.php';
		require_once plugin_dir_path( __DIR__ ) . 'install/class-wordlift-install-3-40-1.php';
		require_once plugin_dir_path( __DIR__ ) . 'install/class-wordlift-install-3-40-2.php';
		require_once plugin_dir_path( __DIR__ ) . 'install/class-wordlift-install-3-41-0.php';
		require_once plugin_dir_path( __DIR__ ) . 'install/class-wordlift-install-3-42-0.php';
		require_once plugin_dir_path( __DIR__ ) . 'install/class-wordlift-install-3-44-1.php';
		require_once plugin_dir_path( __DIR__ ) . 'install/class-wordlift-install-3-44-4.php';
		require_once plugin_dir_path( __DIR__ ) . 'install/class-wordlift-install-3-45-0.php';
		require_once plugin_dir_path( __DIR__ ) . 'install/class-wordlift-install-3-45-1.php';
		require_once plugin_dir_path( __DIR__ ) . 'install/class-wordlift-install-3-49-2.php';
		require_once plugin_dir_path( __DIR__ ) . 'install/class-wordlift-install-3-52-1.php';

		// Get the install services.
		$this->installs = array(
			new Wordlift_Install_1_0_0(),
			new Wordlift_Install_3_10_0(),
			new Wordlift_Install_3_12_0(),
			new Wordlift_Install_3_14_0(),
			new Wordlift_Install_3_15_0(),
			new Wordlift_Install_3_18_0(),
			new Wordlift_Install_3_18_3(),
			new Wordlift_Install_3_19_5(),
			new Wordlift_Install_3_20_0(),

			/*
			 * This should be enabled with #852.
			 */
			new Wordlift_Install_Package_Type(),
			new Wordlift_Install_3_23_4(),
			new Wordlift_Install_3_24_2(),
			new Wordlift_Install_3_25_0(),
			new Wordlift_Install_3_27_0(),
			new Wordlift_Install_3_27_1(),
			new Wordlift_Install_3_28_0(),
			// Add column to represent term
			new Wordlift_Install_3_32_0(),
			// Add the entities table.
			new Wordlift_Install_3_33_9(),
			// When woocommerce extension installed, acf4so should be installed automatically.
			new Wordlift_Install_3_36_0(),

			new Wordlift_Install_3_38_5(),
			new Wordlift_Install_3_39_1(),

			// See #1621.
			new Wordlift_Install_3_40_1(),

			// @link https://github.com/insideout10/wordlift-plugin/issues/1627
			new Wordlift_Install_3_40_2(),

			new Wordlift_Install_3_41_0(),
			new Wordlift_Install_3_42_0(),

			new Wordlift_Install_3_44_1(),
			new Wordlift_Install_3_44_4(),

			new Wordlift_Install_3_45_0(),
			new Wordlift_Install_3_45_1(),

			new Wordlift_Install_3_49_2(),

			new Wordlift_Install_3_52_1(),
		);
		self::$instance = $this;

		$this->log = Wordlift_Log_Service::get_logger( get_class() );

		add_action( 'init', array( $this, 'install' ) );

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
	 * @return void
	 * @since 3.18.0
	 *
	 * @since 3.20.0 use a transient to avoid concurrent installation calls.
	 */
	public function install() {

		$version = null;

		if ( $this->install_required() && false === get_transient( '_wl_installing' ) ) {
			set_transient( '_wl_installing', true, 5 * MINUTE_IN_SECONDS );
			/** @var Wordlift_Install $install */
			foreach ( $this->installs as $install ) {
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

			// phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
			@delete_transient( '_wl_installing' );

		}

	}

	private function install_required() {

		/** @var Wordlift_Install $install */
		foreach ( $this->installs as $install ) {
			// Get the install version.
			$version = $install->get_version();

			if ( version_compare( $version, $this->get_current_version(), '>' )
				 || $install->must_install() ) {
				return true;
			}
		}

		return false;
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
