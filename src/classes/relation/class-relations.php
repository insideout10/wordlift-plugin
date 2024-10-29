<?php

namespace Wordlift\Relation;

use JsonSerializable;

/**
 * Try to keep this interface to conform to https://www.php.net/manual/en/class.ds-set.php
 *
 * We can't use https://www.php.net/manual/en/class.ds-set.php because we're still PHP 5.6 compatible
 * (as of 2023-04-20).
 */
class Relations implements Relations_Interface, JsonSerializable {

	/**
	 * @var array<Relation> $container
	 */
	private $container = array();

	public static function from_json( $json ) {
		$relations = new self();
		foreach ( $json as $item ) {
			$relations->add( Relation::from_json( $item ) );
		}

		return $relations;
	}

	public function add( Relation ...$values ) {
		foreach ( $values as $value ) {
			if ( ! $this->contains( $value ) ) {
				$this->container[] = $value;
			}
		}
	}

	public function remove( Relation ...$values ) {
		foreach ( $values as $value ) {
			foreach ( $this->container as $offset => $item ) {
				if ( $item->equals( $value ) ) {
					unset( $this->container[ $offset ] );
				}
			}
		}
	}

	public function contains( Relation ...$values ) {
		foreach ( $values as $value ) {
			foreach ( $this->container as $item ) {
				if ( $item->equals( $value ) ) {
					// This value is found, move onto the next.
					continue 2;
				}
			}

			// Value hasn't been found, return false.
			return false;
		}

		// All values have been found, return true.
		return true;
	}

	public function offsetSet( $offset, $value ): void {
		if ( $offset === null ) {
			$this->container[] = $value;
		} else {
			$this->container[ $offset ] = $value;
		}
	}

	public function offsetExists( $offset ): bool {
		return isset( $this->container[ $offset ] );
	}

	public function offsetUnset( $offset ): void {
		unset( $this->container[ $offset ] );
	}

	#[\ReturnTypeWillChange]
	public function offsetGet( $offset ) {
		return isset( $this->container[ $offset ] ) ? $this->container[ $offset ] : null;
	}

	public function toArray() {
		return $this->container;
	}

	#[\ReturnTypeWillChange]
	public function jsonSerialize() {
		return $this->container;
	}
}
