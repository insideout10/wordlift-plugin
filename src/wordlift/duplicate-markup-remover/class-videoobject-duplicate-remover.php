<?php

namespace Wordlift\Duplicate_Markup_Remover;

/**
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * @since 3.31.7
 * Class Videoobject_Duplicate_Remover
 * @package Wordlift\Duplicate_Markup_Remover
 */
class Videoobject_Duplicate_Remover {

	public function __construct() {
		add_filter( 'wl_after_get_jsonld', array( $this, 'remove_mentioned_videos' ) );
	}



}