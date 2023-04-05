<?php

namespace Wordlift\Modules\Dashboard\Post_Entity_Match;

use Wordlift\Modules\Dashboard\Match\Match_Sort;

class Sort extends Match_Sort {

	public function get_field_name() {
		$tmp_sort_field_name = substr( $this->sort, 1 );
		if ( 'id' === $tmp_sort_field_name ) {
			return 'p.ID';
		} else {
			return 'p.post_title';
		}
	}

}
