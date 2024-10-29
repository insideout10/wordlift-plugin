<?php

namespace Wordlift\Modules\Common\Api;

class Cursor_Page implements \Serializable, \JsonSerializable {

	private $items;

	/**
	 * @var string
	 */
	private $position;

	/**
	 * @var string
	 */
	private $element;

	/**
	 * @var string
	 */
	private $direction;

	/**
	 * @var string
	 */
	private $sort;

	private $limit;
	/**
	 * @var array
	 */
	private $query;

	/**
	 * @var string
	 */
	private $position_field_name;

	/**
	 * @param $items
	 */
	public function __construct( $items = array(), $position = '', $element = 'INCLUDED', $direction = 'ASCENDING', $sort = '+id', $limit, $query = array() ) {
		$this->items     = $items;
		$this->position  = $position;
		$this->element   = $element;
		$this->direction = $direction;
		$this->sort      = $sort;
		$this->limit     = $limit;
		$this->query     = $query;

		$this->position_field_name = substr( $sort, 1 );
	}

	public function get_self() {
		// `base64_encode` used to push the cursor to the query string.
		// phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode
		return base64_encode(
			wp_json_encode(
				array(
					'position'  => $this->position,
					'element'   => $this->element,
					'direction' => $this->direction,
					'sort'      => $this->sort,
					'limit'     => $this->limit,
					'query'     => $this->query,
				)
			)
		);
	}

	public function get_first() {
		// `base64_encode` used to push the cursor to the query string.
		// phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode
		return base64_encode(
			wp_json_encode(
				array(
					'position'  => null,
					'element'   => 'INCLUDED',
					'direction' => 'ASCENDING',
					'sort'      => $this->sort,
					'limit'     => $this->limit,
					'query'     => $this->query,
				)
			)
		);

	}

	public function get_prev() {
		return ! $this->has_prev() ? null :
			// `base64_encode` used to push the cursor to the query string.
			// phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode
			base64_encode(
				wp_json_encode(
					array(
						'position'  => $this->get_prev_position(),
						'element'   => 'EXCLUDED',
						'direction' => 'DESCENDING',
						'sort'      => $this->sort,
						'limit'     => $this->limit,
						'query'     => $this->query,
					)
				)
			);
	}

	private function has_prev() {
		return ( $this->direction === 'ASCENDING' && isset( $this->position ) )
			   || ( $this->direction === 'DESCENDING' && count( $this->items ) > $this->limit );
	}

	public function get_next() {
		// The items always have one more item beyond the limit to calculate the next cursor.
		return ! $this->has_next() ? null :
			// `base64_encode` used to push the cursor to the query string.
			// phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode
			base64_encode(
				wp_json_encode(
					array(
						'position'  => $this->get_next_position(),
						'element'   => 'EXCLUDED',
						'direction' => 'ASCENDING',
						'sort'      => $this->sort,
						'limit'     => $this->limit,
						'query'     => $this->query,
					)
				)
			);
	}

	private function has_next() {
		return ( $this->direction === 'ASCENDING' && count( $this->items ) > $this->limit )
			   || ( $this->direction === 'DESCENDING' && isset( $this->position ) );
	}

	public function get_last() {
		// `base64_encode` used to push the cursor to the query string.
		// phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode
		return base64_encode(
			wp_json_encode(
				array(
					'position'  => null,
					'element'   => 'INCLUDED',
					'direction' => 'DESCENDING',
					'sort'      => $this->sort,
					'limit'     => $this->limit,
					'query'     => $this->query,
				)
			)
		);
	}

	private function get_prev_position() {
		$item = current( $this->items );

		return $item->{$this->position_field_name};
	}

	private function get_next_position() {
		$item = $this->items[ $this->limit - 1 ];

		return $item->{$this->position_field_name};
	}

	public function __serialize() {
		return array(
			'items' => $this->items,
		);
	}

	public function __unserialize( array $data ) {
		$this->items = isset( $data['items'] ) ? (array) $data['items'] : array();
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

	/**
	 * Controls how the object is reconstructed from a PHP serialized representation.
	 *
	 * @param string $data The PHP serialized representation of the object.
	 *
	 * @return void
	 */
	public function unserialize( $data ) {
		// phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.serialize_unserialize
		$this->__unserialize( unserialize( $data ) );
	}

	#[\ReturnTypeWillChange]
	public function jsonSerialize() {
		return array(
			'self'  => $this->get_self(),
			'first' => $this->get_first(),
			'prev'  => $this->get_prev(),
			'next'  => $this->get_next(),
			'last'  => $this->get_last(),
			'items' => array_slice( $this->items, 0, $this->limit ),
		);
	}
}
