<?php

namespace Wordlift\Autocomplete;

interface Autocomplete_Service {

	/**
	 * @param string $query The query.
	 * @param string $scope The scope.
	 * @param string[] $excludes URLs to exclude.
	 *
	 * @return mixed
	 */
	public function query( $query, $scope, $excludes );

}
