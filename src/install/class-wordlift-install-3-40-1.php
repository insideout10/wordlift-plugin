<?php

/**
 * Flush the rewrite rules as the entity post has_archive is changed.
 *
 * @since 3.40.0
 */
class Wordlift_Install_3_40_1 extends Wordlift_Install {

	/**
	 * {@inheritdoc}
	 */
	protected static $version = '3.40.1';

	public function install() {
		// Flush the rewrite rules.
		flush_rewrite_rules();
	}
}
