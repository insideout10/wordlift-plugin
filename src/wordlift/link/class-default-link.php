<?php
/**
 * @since 3.32.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * This is an abstract class for Link.
 */

namespace Wordlift\Link;

use Wordlift\Common\Singleton;

abstract class Default_Link extends Singleton implements Link {

	function get_link_title( $id, $label_to_be_ignored ) {

		$entity_labels = $this->get_synonyms( $id );
		// Select the first entity_label which is not to be ignored.
		$title = '';
		foreach ( $entity_labels as $entity_label ) {
			if ( 0 !== strcasecmp( $entity_label, $label_to_be_ignored ) ) {
				$title = $entity_label;
				break;
			}
		}

		return $title;
	}

}