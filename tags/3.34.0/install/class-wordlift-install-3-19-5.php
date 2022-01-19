<?php
/**
 * Installs: Install Version 3.19.5.
 *
 * @since      3.19.5
 * @package    Wordlift
 * @subpackage Wordlift/install
 */

/**
 * Define the {@link Wordlift_Install_3_19_5} interface.
 *
 * @since      3.19.5
 * @package    Wordlift
 * @subpackage Wordlift/install
 */
class Wordlift_Install_3_19_5 extends Wordlift_Install {

	/**
	 * {@inheritdoc}
	 */
	protected static $version = '3.19.5';

	/**
	 * {@inheritdoc}
	 */
	public function install() {

		/*
		 * Flush all the caches, since we changed some JSON-LD publishing rules.
		 *
		 * @see https://github.com/insideout10/wordlift-plugin/issues/858
		 */
		Wordlift_File_Cache_Service::flush_all();

	}

}
