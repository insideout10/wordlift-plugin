<?php

namespace Wordlift\Vocabulary\Cache;

interface Cache {

	public function get( $cache_key );

	public function put( $cache_key, $value );

	public function flush_all();

}
