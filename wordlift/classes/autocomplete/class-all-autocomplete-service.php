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

		/**
		 * Filter to show local entities on the entity autocompletion field.
		 *
		 * @param $state bool
		 *
		 * @return bool Whether to show local entities in the page or not.
		 * @since 3.26.1
		 */
		$show_local_entities = apply_filters( 'wl_show_local_entities', false );

		$autocomplete_services = $this->autocomplete_services;

		// Remove the local autocomplete services.
		if ( ! $show_local_entities ) {
			$autocomplete_services = array_filter(
				$autocomplete_services,
				function ( $service ) {
					return ! $service instanceof Local_Autocomplete_Service;
				}
			);
		}

		// Query each Autocomplete service and merge the results.
		return array_reduce(
			$autocomplete_services,
			function ( $carry, $item ) use ( $query, $scope, $excludes ) {

				$results = $item->query( $query, $scope, $excludes );

				return array_merge( $carry, $results );
			},
			array()
		);

	}

}
