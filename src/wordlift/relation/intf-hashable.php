<?php

namespace Wordlift\Relation;

/**
 * This interface mimics https://www.php.net/manual/en/class.ds-hashable
 *
 * When we would cut support for PHP 5.x we could migrate to the official Hashable.
 */
interface Hashable {

	public function equals( Hashable $obj );

	public function hash();

}
