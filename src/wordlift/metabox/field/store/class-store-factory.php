<?php
/**
 * @since 3.32.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * This class returns instance of store depending on type.
 */

namespace Wordlift\Metabox\Field\Store;

use Wordlift\Object_Type_Enum;

class Store_Factory {

	/**
	 * @param $type
	 *
	 * @return Store
	 */
	public static function get_instance( $type ) {

		if ( Object_Type_Enum::POST === $type ) {
			return new Post_Store();
		} elseif ( Object_Type_Enum::TERM === $type ) {
			return new Term_Store();
		}
		return null;
	}

}
