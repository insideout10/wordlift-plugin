<?php
/**
 * Installs: Package Type.
 *
 * Determines the package type if not set.
 *
 * @since 3.20.0
 * @package Wordlift
 * @subpackage Wordlift/install
 */

/**
 * Define the Wordlift_Install_Package_Type class.
 *
 * @since 3.20.0
 */
class Wordlift_Install_Package_Type extends Wordlift_Install {

	/**
	 * The install version.
	 *
	 * @since 3.20.0
	 */
	protected static $version = '3.20.0';

	/**
	 * Run the install procedure.
	 *
	 * @since 3.20.0
	 *
	 * @return mixed The result.
	 */
	public function install() {

		$configuration_service = Wordlift_Configuration_Service::get_instance();
		$key                   = $configuration_service->get_key();

		// Bail out if the `key` isn't set.
		if ( empty( $key ) ) {
			return;
		}

		// Calling this function will get and set the remote dataset and package type.
		$configuration_service->get_remote_dataset_uri( $key );

	}

	/**
	 * Must install when the package type isn't set.
	 *
	 * See https://github.com/insideout10/wordlift-plugin/issues/761
	 *
	 * @since 3.20.0
	 * @return bool True if package type is set, otherwise false.
	 */
	public function must_install() {

		$configuration_service = Wordlift_Configuration_Service::get_instance();
		$key                   = $configuration_service->get_key();
		$package_type          = $configuration_service->get_package_type();

		// We need to determine the package type if the `key` is set, but the `package_type` isn't.
		return is_admin() && ! empty( $key ) && empty( $package_type );
	}

}
