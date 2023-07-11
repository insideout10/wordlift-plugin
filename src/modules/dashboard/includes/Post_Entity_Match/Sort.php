<?php

namespace Wordlift\Modules\Dashboard\Post_Entity_Match;

use Wordlift\Modules\Dashboard\Match\Match_Sort;

/**
 * Class Sort
 *
 * @package Wordlift\Modules\Dashboard\Post_Entity_Match
 */
class Sort extends Match_Sort {

	/**
	 * Get field name.
	 *
	 * @return string
	 */
	public function get_field_name() {
		$tmp_sort_field_name = substr( $this->sort, 1 );
		if ( 'id' === $tmp_sort_field_name ) {
			return 'p.ID';
		} elseif ( 'date_modified_gmt' === $tmp_sort_field_name ) {
			return 'p.post_modified_gmt';
		} else {
			return 'p.post_title';
		}
	}

}
