<?php

namespace Wordlift\Relation;

use ArrayAccess;

/**
 * Try to keep this interface to conform to https://www.php.net/manual/en/class.ds-set.php
 *
 * We can't use https://www.php.net/manual/en/class.ds-set.php because we're still PHP 5.6 compatible
 * (as of 2023-04-20).
 */
interface Relations_Interface extends ArrayAccess {

	public function add( Relation ...$values );

	public function remove( Relation ...$values );

	public function contains( Relation ...$values );

	public function toArray();

}
