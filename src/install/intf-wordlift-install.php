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
interface Wordlift_Install {

	/**
	 * Return the current version of the installation.
	 *
	 * @since 3.18.0
	 */
	public function get_version();

	/**
	 * Install the version required functionalities
	 *
	 * @since 3.18.0
	 */
	public function install();

}
