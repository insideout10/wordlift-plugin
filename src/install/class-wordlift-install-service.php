<?php
/**
 * Created by PhpStorm.
 * User: david
 * Date: 19.02.18
 * Time: 15:42
 */

class Wordlift_Install_Service {

	/**
	 * Wordlift_Install_Service constructor.
	 *
	 * @param array $installs
	 */
	public function __construct(  ) {
		$this->installs = array(
			new Wordlift_Install_1_0_0(),
		);

		/** @var Wordlift_Install $install */
		foreach ($this->installs as $install) {
			if (version_compare($this->get_current_version(), $install->get_version(), '>=')) {
				$install->install();
				set_option('wl_install_version', ...);
			}
		}
	}

	private function get_current_version() {

		return get_option('wl_install_version', '0.0.0');
	}


}