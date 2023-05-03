<?php

namespace Wordlift\Relation;

/**
 * This interface mimics https://www.php.net/manual/en/class.ds-hashable
 *
 * When we would cut support for PHP 5.x we could migrate to the official Hashable.
 */
interface Hashable_Interface {

	public function equals( Hashable_Interface $obj );

	public function hash();

}
