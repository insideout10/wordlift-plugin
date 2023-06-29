<?php

namespace Wordlift\Modules\Dashboard\Term_Entity_Match;

use Wordlift\Modules\Dashboard\Match\Match_Sort;

class Sort extends Match_Sort {

	public function get_field_name() {
		$tmp_sort_field_name = substr( $this->sort, 1 );
		if ( 'id' === $tmp_sort_field_name ) {
			return 't.term_id';
		} elseif ( 'ingredient_term' ) {
			return 't.name';
		} elseif ( 'matched_ingredient' ) {
			//@todo -not sure what this is
		} elseif ( 'occurrences' ) {
			//@todo
		} else {
			return 't.name';
		}
	}

}
