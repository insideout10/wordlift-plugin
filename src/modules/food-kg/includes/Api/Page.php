<?php

namespace Wordlift\Modules\Food_Kg\Api;

class Page {

	const FORWARD  = 'FORWARD';
	const BACKWARD = 'BACKWARD';

	const SORT_ASC  = 'ASC';
	const SORT_DESC = 'DESC';

	private $items;
	private $limit;
	private $position;
	private $self;

	public function __construct( $cursor, $items, $limit, $position ) {
		$this->self     = $cursor;
		$this->items    = $items;
		$this->limit    = $limit;
		$this->position = $position;
	}

	public function serialize() {
		return array(
			'first' => $this->first(),
			'self'  => $this->self,
			'last'  => PHP_INT_MAX === $this->position ? null : $this->cursor( $this->limit, PHP_INT_MAX, self::BACKWARD, self::SORT_DESC ),
			'next'  => PHP_INT_MAX === $this->position ? null : $this->next(),
			'prev'  => $this->prev(),
			'items' => $this->items,
		);
	}

	private function cursor( $limit, $position, $direction, $sort ) {
		return base64_encode(
			json_encode(
				array(
					'limit'     => $limit,
					'position'  => $position,
					'direction' => $direction,
					'sort'      => $sort,
				)
			)
		);
	}

	private function next() {
		// Check if we have reached the end of the results
		if ( count( $this->items ) < $this->limit ) {
			return null;
		}

		// Get the position of the last item in the current result set
		$last_item_position = end( $this->items )['id'];

		// Generate the next cursor
		return $this->cursor( $this->limit, $last_item_position, self::FORWARD, self::SORT_ASC );
	}

	private function prev() {

		/**
		 * If i want to go to previous page i would need to be sure that such page exists.
		 * I would just need to reverse the direction.
		 */
		if ( $this->position === 0 ) {
			return null;
		}

		if ( count( $this->items ) <= 0 ) {
			return $this->cursor( $this->limit, $this->position, self::BACKWARD, self::SORT_ASC );
		}

		if ( current( $this->items )['id'] == $this->position ) {
			return null;
		}

		return $this->cursor( $this->limit, current( $this->items )['id'], self::BACKWARD, self::SORT_ASC );
	}

	private function first() {

		if ( 0 === $this->position ) {
			return null;
		}

		return $this->cursor( $this->limit, 0, self::FORWARD, self::SORT_ASC );
	}

}
