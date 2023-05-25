<?php
/**
 * Installs: Install Version 3.12.0.
 *
 * @since      3.18.0
 * @package    Wordlift
 * @subpackage Wordlift/install
 */

/**
 * Define the {@link Wordlift_Install_3_12_0} interface.
 *
 * @since      3.18.0
 * @package    Wordlift
 * @subpackage Wordlift/install
 */
class Wordlift_Install_3_12_0 extends Wordlift_Install {
	/**
	 * @inheritdoc
	 */
	protected static $version = '3.12.0';

	/**
	 * @inheritdoc
	 */
	public function install() {
		/*
		 * As this upgrade functionality runs on the init hook, and the AMP plugin
		 * initialization does the same, avoid possible race conditions by
		 * deferring the actual flush to a later hook.
		 */
		add_action(
			'wp_loaded',
			function () {
				flush_rewrite_rules();
			}
		);
	}

}
