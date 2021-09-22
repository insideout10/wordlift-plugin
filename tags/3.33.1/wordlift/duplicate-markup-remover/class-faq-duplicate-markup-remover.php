<?php

namespace Wordlift\Duplicate_Markup_Remover;

/**
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * @since 3.27.4
 * Class Faq_Duplicate_Markup_Remover
 * @package Wordlift\Duplicate_Markup_Remover
 */
class Faq_Duplicate_Markup_Remover extends Abstract_Duplicate_Markup_Remover {

	public function __construct() {
		parent::__construct( 'FAQPage', array( 'mainEntity' ) );
	}


}