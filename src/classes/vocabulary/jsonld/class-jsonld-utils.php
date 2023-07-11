<?php
/**
 * @since 3.31.2
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */

namespace Wordlift\Vocabulary\Jsonld;

use Wordlift\Vocabulary\Data\Entity_List\Entity_List_Factory;

class Jsonld_Utils {

	/**
	 * @param $term_id
	 *
	 * @return array|array[]|mixed
	 */
	public static function get_matched_entities_for_term( $term_id ) {

		$entity = Entity_List_Factory::get_instance( $term_id );

		return $entity->get_jsonld_data();
	}

}
