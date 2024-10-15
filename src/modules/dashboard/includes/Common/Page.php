<?php

namespace Wordlift\Modules\Dashboard\Common;

class Page implements \Serializable, \JsonSerializable {

	/**
	 * @var mixed
	 */
	private $items;

	private $self;
	private $first;
	private $prev;
	private $next;
	private $last;

	public function __construct( $items, $self, $first, $prev, $next, $last ) {
		$this->items = $items;
		$this->self  = $self;
		$this->first = $first;
		$this->prev  = $prev;
		$this->next  = $next;
		$this->last  = $last;
	}

	public function __serialize() {
		return array(
			'self'  => $this->self,
			'first' => $this->first,
			'prev'  => $this->prev,
			'next'  => $this->next,
			'last'  => $this->last,
			'items' => $this->items,
		);
	}

	/**
	 * Controls how the object is represented during PHP serialization.
	 *
	 * @return string The PHP serialized representation of the object.
	 */
	public function serialize() {
		// phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.serialize_serialize
		return serialize( $this->__serialize() );
	}

	public function unserialize( $data ) {
		// phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.serialize_unserialize
		$this->__unserialize( unserialize( $data ) );
	}

	public function __unserialize( array $data ) {
		$this->self  = $data['self'];
		$this->first = $data['first'];
		$this->prev  = $data['prev'];
		$this->next  = $data['next'];
		$this->last  = $data['last'];
		$this->items = $data['items'];
	}

	#[\ReturnTypeWillChange]
	public function jsonSerialize() {
		return array(
			'self'  => $this->self,
			'first' => $this->first,
			'prev'  => $this->prev,
			'next'  => $this->next,
			'last'  => $this->last,
			'items' => $this->items,
		);
	}

}
