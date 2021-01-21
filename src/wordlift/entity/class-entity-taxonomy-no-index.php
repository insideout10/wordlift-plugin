<?php

namespace Wordlift\Entity;
/**
 * @since 3.28.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */

/**
 * Filter to remove the entity taxonomy from sitemap.
 * Class Entity_Taxonomy_No_Index
 * @package Wordlift\Entity
 */
class Entity_Taxonomy_No_Index {

	public function __construct() {

		add_filter( 'wp_sitemaps_taxonomies', function ( $taxonomies ) {
			$entity_taxonomy = \Wordlift_Entity_Type_Taxonomy_Service::TAXONOMY_NAME;
			if ( array_key_exists( $entity_taxonomy, $taxonomies ) ) {
				unset( $taxonomies[ $entity_taxonomy ] );
			}
			return $taxonomies;
		} );

	}

}
