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
use Wordlift_Entity_Type_Taxonomy_Service;
use Wordlift_Post_Excerpt_Helper;

class Local_Autocomplete_Service extends Abstract_Autocomplete_Service {

	/**
	 * @inheritDoc
	 */
	public function query( $query, $scope, $excludes ) {
		global $wpdb;

		$posts = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT * FROM {$wpdb->posts} p"
				. " INNER JOIN {$wpdb->term_relationships} tr"
				. '  ON tr.object_id = p.ID'
				. " INNER JOIN {$wpdb->term_taxonomy} tt"
				. '  ON tt.taxonomy = %s AND tt.term_taxonomy_id = tr.term_taxonomy_id'
				. " INNER JOIN {$wpdb->terms} t"
				. '  ON t.term_id = tt.term_id AND t.name != %s'
				. " LEFT OUTER JOIN {$wpdb->postmeta} pm"
				. '  ON pm.meta_key = %s AND pm.post_id = p.ID'
				. " WHERE p.post_type IN ( '" . implode( "', '", array_map( 'esc_sql', Wordlift_Entity_Service::valid_entity_post_types() ) ) . "' )" // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared,WordPress.DB.PreparedSQLPlaceholders.QuotedDynamicPlaceholderGeneration
				. "  AND p.post_status IN ( 'publish', 'draft', 'private', 'future' ) "
				. '  AND ( p.post_title LIKE %s OR pm.meta_value LIKE %s )'
				. ' LIMIT %d',
				Wordlift_Entity_Type_Taxonomy_Service::TAXONOMY_NAME,
				'article',
				Wordlift_Entity_Service::ALTERNATIVE_LABEL_META_KEY,
				// `prepare` doesn't support argument number, hence we must repeat the query.
				'%' . $wpdb->esc_like( $query ) . '%',
				'%' . $wpdb->esc_like( $query ) . '%',
				50
			)
		);

		$results = array_map(
			function ( $item ) {

				$entity_service = Wordlift_Entity_Service::get_instance();
				$uri            = $entity_service->get_uri( $item->ID );

				return array(
					// see #1074: The value property is needed for autocomplete in category page
					// to function correctly, if value is not provided, then the entity
					// wont be correctly saved.
					'value'        => $uri,
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
			},
			$posts
		);

		$results = array_unique( $results, SORT_REGULAR );

		return $this->filter( $results, $excludes );
	}

}
