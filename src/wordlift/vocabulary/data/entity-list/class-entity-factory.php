<?php
namespace Wordlift\Vocabulary\Data\Entity_List;
/**
 * @since 3.30.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */
class Entity_Factory {

	public static function get_instance( $term_id ) {
		return new Default_Entity( $term_id, new Legacy_Entity( $term_id ) );
	}

}