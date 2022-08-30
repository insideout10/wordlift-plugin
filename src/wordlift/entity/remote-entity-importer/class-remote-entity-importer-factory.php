<?php

namespace Wordlift\Entity\Remote_Entity_Importer;

use Wordlift\Entity\Remote_Entity\Remote_Entity;
use Wordlift\Entity\Remote_Entity\Valid_Remote_Entity;

class Remote_Entity_Importer_Factory {

	/**
	 * @param $entity Remote_Entity
	 *
	 * @return Remote_Entity_Importer
	 */
	public static function from_entity( $entity ) {

		if ( $entity instanceof Valid_Remote_Entity ) {
			return new Valid_Remote_Entity_Importer( $entity );
		}

		return new Invalid_Remote_Entity_Importer();
	}
}
