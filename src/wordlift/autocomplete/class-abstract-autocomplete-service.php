<?php
/**
 * A base class for {@link \Wordlift\Autocomplete\Autocomplete_Service} which provides a convenience filter method.
 *
 * @author David Riccitelli <david@wordlift.io>
 * @since 3.25.1
 * @package Wordlift\Autocomplete
 */

namespace Wordlift\Autocomplete;

abstract class Abstract_Autocomplete_Service implements Autocomplete_Service {

	/**
	 * Filter out results that are in the excludes list.
	 *
	 * @param array $results {
	 * An array of results.
	 *
	 * @type array The result's data.
	 * }
	 *
	 * @param array $excludes An array of URLs.
	 *
	 * @return array The filtered array of results.
	 */
	protected function filter( $results, $excludes ) {

		$excludes_array = (array) $excludes;

		return array_filter(
			$results,
			function ( $item ) use ( $excludes_array ) {

				return 0 === count(
					array_intersect(
						array_merge( (array) $item['id'], $item['sameAss'] ),
						$excludes_array
					)
				);
			}
		);
	}

}
