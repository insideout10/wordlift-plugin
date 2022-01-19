<?php

namespace Wordlift\Duplicate_Markup_Remover;

class How_To_Duplicate_Markup_Remover extends Abstract_Duplicate_Markup_Remover {

	public function __construct() {

		// 'name', 'description' are omitted intentionally, as they belong to
		// type http://schema.org/Thing, we remove HowTo if its the only schema type in the page
		// if it's combined with other schema type, they would need Thing Properties.
		$properties_to_remove = array(
			'estimatedCost',
			'totalTime',
			'supply',
			'tool',
			'step'
		);
		parent::__construct( 'HowTo', $properties_to_remove );
	}

}