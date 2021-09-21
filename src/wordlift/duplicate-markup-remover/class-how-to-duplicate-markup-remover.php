<?php

namespace Wordlift\Duplicate_Markup_Remover;

class How_To_Duplicate_Markup_Remover extends Abstract_Duplicate_Markup_Remover {

	public function __construct() {

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