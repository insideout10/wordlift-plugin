<?php
/**
 * @since 3.31.7
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * This class returns instance of store depending on type.
 */

namespace Wordlift\Metabox\Field\Store;

use Wordlift\Metabox\Wl_Abstract_Metabox;

class Store_Factory {


	/**
	 * @param $type
	 *
	 * @return Store
	 */
	public static function get_instance(  $type ) {

		if ( Wl_Abstract_Metabox::POST === $type ) {
			return new Post_Store();
		}
		else if ( Wl_Abstract_Metabox::TERM === $type) {
			return new Term_Store();
		}
		return null;
	}

}