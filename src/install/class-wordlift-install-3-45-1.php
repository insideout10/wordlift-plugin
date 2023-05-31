<?php

use Wordlift\Cache\Ttl_Cache;

/**
 * @since 3.45.1
 */
class Wordlift_Install_3_45_1 extends Wordlift_Install {

	/**
	 * {@inheritdoc}
	 */
	protected static $version = '3.45.1';

	public function install() {
		Ttl_Cache::flush_all();
	}
}
