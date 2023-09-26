<?php
namespace Wordlift\Jsonld;

use Wordlift\Object_Type_Enum;

/**
 * This class represents an Term reference
 *
 * @since 3.32.0
 * @package Wordlift\Jsonld
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */

class Term_Reference extends Abstract_Reference {

	public function get_type() {
		return Object_Type_Enum::TERM;
	}
}
