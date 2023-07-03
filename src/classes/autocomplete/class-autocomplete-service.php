<?php
/**
 * This file provides the Autocomplete service interface.
 *
 * @author David Riccitelli <david@wordlift.io>
 * @since 3.24.2
 * @package Wordlift\Autocomplete
 */

namespace Wordlift\Autocomplete;

interface Autocomplete_Service {

	/**
	 * Query the service for the specified data.
	 *
	 * @param string          $query The query.
	 * @param string          $scope The scope.
	 * @param string|string[] $excludes URLs to exclude.
	 *
	 * @return array An array of results.
	 */
	public function query( $query, $scope, $excludes );

}
