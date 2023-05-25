<?php

use Wordlift\Cache\Ttl_Cache;

/**
 * Clear the cache.
 *
 * @since 3.44.1
 */
class Wordlift_Install_3_44_1 extends Wordlift_Install {

	/**
	 * {@inheritdoc}
	 */
	protected static $version = '3.44.1';

	public function install() {
		Ttl_Cache::flush_all();
	}
}
