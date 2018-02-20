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
	 * Loop thought all versions and install the updates.
	 *
	 * @since 3.18.0
	 *
	 * @return void
	 */
	public function install() {
		$installs = array(
			new Wordlift_Install_1_0_0(),
		);

		/** @var Wordlift_Install $install */
		foreach ( $installs as $install ) {
			$version = $install->get_version();

			if ( version_compare( $this->get_current_version(), $version, '>=' ) ) {
				$install->install();
				update_option( 'wl_db_version', $version );
			}
		}

		// Finally update to current version.
		update_option( 'wl_db_version', WL_DB_VERSION );
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
