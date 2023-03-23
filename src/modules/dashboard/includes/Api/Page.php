<?php

namespace Wordlift\Modules\Dashboard\Api;

class Page {

	private $items;
	private $limit;
	private $position;

	public function __construct( $items, $limit, $position ) {
		$this->items    = $items;
		$this->limit    = $limit;
		$this->position = $position;
	}

	public function serialize() {
		return array(
			'first' => 0 === $this->position ? null : $this->cursor( $this->limit, 0, 'forwards' ),
			'last'  => PHP_INT_MAX === $this->position ? null : $this->cursor( $this->limit, PHP_INT_MAX, 'backwards' ),
			'next'  => $this->next(),
			'prev'  => $this->prev(),
			'items' => $this->items,
		);
	}

	private function cursor( $limit, $position, $direction ) {
		return base64_encode(
			json_encode(
				array(
					'limit'     => $limit,
					'position'  => $position,
					'direction' => $direction,
				)
			)
		);
	}

	private function next( ) {
		// Check if we have reached the end of the results
		if ( count( $this->items ) < $this->limit ) {
			return null;
		}

		// Get the position of the last item in the current result set
		$last_item_position = end( $this->items )['id'];

		// Generate the next cursor
		return $this->cursor( $this->limit, $last_item_position, 'forward' );
	}

	private function prev( ) {
		/**
		 * If i want to go to previous page i would need to be sure that such page exists.
		 * I would just need to reverse the direction.
		 */
		if ( $this->position === 0 ) {
			return null;
		}

		if ( count( $this->items ) <= 0 ) {
			return $this->cursor( $this->limit, $this->position, 'backward' );
		}

		return $this->cursor( current( $this->items )['id'], $this->position, 'forward' );
	}

}
