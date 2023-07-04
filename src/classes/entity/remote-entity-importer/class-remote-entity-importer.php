<?php

namespace Wordlift\Entity\Remote_Entity_Importer;

use Wordlift\Content\Wordpress\Wordpress_Content_Id;

interface Remote_Entity_Importer {

	/**
	 * @return Wordpress_Content_Id|boolean
	 * Returns content id or false.
	 */
	public function import();

}
