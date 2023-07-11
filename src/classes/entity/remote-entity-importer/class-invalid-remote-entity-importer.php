<?php

namespace Wordlift\Entity\Remote_Entity_Importer;

/**
 * This importer will be dispatched for invalid entities, it does nothing.
 */
class Invalid_Remote_Entity_Importer implements Remote_Entity_Importer {

	public function import() {
		// Cant import invalid entities.
		return false;
	}

}
