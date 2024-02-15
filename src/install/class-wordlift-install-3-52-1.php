<?php

use Wordlift\Cache\Ttl_Cache;

/**
 * @since 3.52.1
 */
class Wordlift_Install_3_52_1 extends Wordlift_Install {

	/**
	 * {@inheritdoc}
	 */
	protected static $version = '3.52.1';

	public function install() {
		// Since we're fixing JSON-LDs better flush them all.
		Ttl_Cache::flush_all();
	}

}
