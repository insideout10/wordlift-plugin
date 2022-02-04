<?php

namespace Wordlift\Entity\Remote_Entity_Importer;

use Wordlift\Entity\Remote_Entity\Remote_Entity;

interface Remote_Entity_Importer {

	/**
	 * @return boolean status of the import, true or false.
	 */
	public function import();

}