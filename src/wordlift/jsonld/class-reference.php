<?php
namespace Wordlift\Jsonld;

/**
 * This interface represents a reference
 *
 * @since 3.32.0
 * @package Wordlift\Jsonld
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */

interface Reference {

	/**
	 * @return int
	 */
	public function get_type();

	/**
	 * @return int Identifier
	 */
	public function get_id();

}

