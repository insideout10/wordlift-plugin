<?php
/**
 * @since 3.32.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * This is an abstract class for Link.
 */

namespace Wordlift\Link;

use Wordlift\Common\Singleton;

abstract class Default_Link extends Singleton implements Link {

	public function get_link_title( $id, $label_to_be_ignored ) {

		$entity_labels = $this->get_synonyms( $id );

		foreach ( $entity_labels as $entity_label ) {
			// Return first synonym if it doesnt match the label.
			if ( 0 !== strcasecmp( $entity_label, $label_to_be_ignored ) ) {
				return $entity_label;
			}
		}

		// If the label matches the synonym then dont add title attr.
		return '';
	}

}
