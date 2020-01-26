<?php
/**
 * This file provides an autocomplete service using the local data.
 *
 * @author David Riccitelli <david@wordlift.io>
 * @since 3.24.2
 * @package Wordlift\Autocomplete
 */

namespace Wordlift\Autocomplete;

use Wordlift_Entity_Service;
use Wordlift_Post_Excerpt_Helper;

class Local_Autocomplete_Service extends Abstract_Autocomplete_Service {

	/**
	 * @inheritDoc
	 */
	public function query( $query, $scope, $excludes ) {

		$args = Wordlift_Entity_Service::add_criterias( array(
			'numberposts'         => 50,
			'post_status'         => 'any',
			's'                   => $query,
			'ignore_sticky_posts' => true,
			'suppress_filters'    => true,
		) );

		$posts = get_posts( $args );

		$results = array_map( function ( $item ) {

			$entity_service = Wordlift_Entity_Service::get_instance();
			$uri            = $entity_service->get_uri( $item->ID );

			return array(
				'id'           => $uri,
				'label'        => array( $item->post_title ),
				'labels'       => $entity_service->get_alternative_labels( $item->ID ),
				'descriptions' => array( Wordlift_Post_Excerpt_Helper::get_text_excerpt( $item ) ),
				'scope'        => 'local',
				'sameAss'      => get_post_meta( $item->ID, \Wordlift_Schema_Service::FIELD_SAME_AS ),
				// The following properties are less relevant because we're linking entities that exist already in the
				// vocabulary. That's why we don't make an effort to load the real data.
				'types'        => array( 'http://schema.org/Thing' ),
				'urls'         => array(),
				'images'       => array(),
			);
		}, $posts );

		return $this->filter( $results, $excludes );
	}

}