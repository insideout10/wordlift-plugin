<?php

namespace Wordlift\Autocomplete;

abstract class Abstract_Autocomplete_Service implements Autocomplete_Service {

	protected function filter( $results, $excludes ) {

		$excludes_array = (array) $excludes;

		return array_filter( $results, function ( $item ) use ( $excludes_array ) {

			return 0 === count( array_intersect(
					array_merge( (array) $item['id'], $item['sameAss'] ),
					$excludes_array
				) );
		} );
	}

}