<?php

namespace Wordlift\Duplicate_Markup_Remover;

/**
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * @since 3.35.9
 * Class Recipe_Duplicate_Markup_Remover
 * @package Wordlift\Duplicate_Markup_Remover
 */
class Recipe_Duplicate_Markup_Remover extends Abstract_Duplicate_Markup_Remover {

	public function __construct() {
		parent::__construct( 'Recipe', array() );
	}

}