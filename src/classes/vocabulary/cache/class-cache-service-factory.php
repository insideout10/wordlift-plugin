<?php

namespace Wordlift\Vocabulary\Cache;

class Cache_Service_Factory {

	public static function get_cache_service() {
		return new Options_Cache( 'wordlift-cmkg' );
	}

}
