<?php
/**
 * This file provides the All_Autocomplete_Service which calls different {@link \Wordlift\Autocomplete\Autocomplete_Service}
 * instances in sequence.
 *
 * @author David Riccitelli <david@wordlift.io>
 * @since 3.25.0
 * @package Wordlift\Autocomplete
 */

namespace Wordlift\Autocomplete;

class All_Autocomplete_Service implements Autocomplete_Service {

	/**
	 * One ore more {@link Autocomplete_Service} instances.
	 *
	 * @var Autocomplete_Service|Autocomplete_Service[] $autocomplete_services
	 */
	private $autocomplete_services;

	/**
	 * All_Autocomplete_Service constructor.
	 *
	 * @param Autocomplete_Service|Autocomplete_Service[] $autocomplete_services
	 */
	public function __construct( $autocomplete_services ) {

		$this->autocomplete_services = (array) $autocomplete_services;
	}

	/**
	 * {@inheritDoc}
	 */
	public function query( $query, $scope, $excludes ) {

		// Query each Autocomplete service and merge the results.
		return array_reduce( $this->autocomplete_services, function ( $carry, $item ) use ( $query, $scope, $excludes ) {

			$results = $item->query( $query, $scope, $excludes );

			return array_merge( $carry, $results );
		}, array() );

	}

}
