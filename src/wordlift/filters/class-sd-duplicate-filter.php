<?php
/**
 * @since 3.28.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */

namespace Wordlift\Filters;

/**
 * This class prevents the duplicate markup appearing on jsonld, for example
 * if a post has FAQPage markup and the linked entity has the same type, the entity
 * jsonld wont appear here.
 *
 * Class Sd_Duplicate_Filter
 * @package Wordlift\Filters
 */
class Sd_Duplicate_Filter {

	private $unique_schema_types = array( 'FAQPage' );

	public function __construct() {

		// Remove filter if exists before.
		remove_filter( 'wl_entity_jsonld_array', array( $this, 'wl_entity_jsonld_array' ) );

		/**
		 * This filter would be called for the post with entity type set to anything
		 * other than article.
		 */
		add_filter( 'wl_entity_jsonld_array', array( $this, 'wl_entity_jsonld_array' ), PHP_INT_MAX, 2 );
	}

	public function wl_entity_jsonld_array( $jsonld, $post_id ) {
		var_dump( "wl_entity_jsonld called for $post_id" );

		return $jsonld;
		/**
		 * Allow external plugins to control the schema type.
		 * Filter name : wl_unique_jsonld_schema_type
		 * @return array An array of unique schema types.
		 * @var $schema_types array An array of unique schema types.
		 * @since 3.28.0
		 */
		$unique_schema_types = apply_filters( 'wl_unique_jsonld_schema_type', $this->unique_schema_types );

		// we need to loop through all the types and validate the logic, alter the jsonld.
		foreach ( $unique_schema_types as $schema_type ) {
			$duplicate_check = $this->check_jsonld_for_duplicates( $jsonld, $schema_type );
			if ( $duplicate_check ) {
				$jsonld = $this->remove_duplicates_from_jsonld( $jsonld, $schema_type );
			}
		}

		return array(
			'jsonld'     => $jsonld,
			'references' => $references
		);
	}

	/**
	 * Check for duplication in schema type.
	 *
	 * @param $jsonld
	 * @param $schema_type
	 *
	 * @return bool True if duplication detected, false if not.
	 */
	private function check_jsonld_for_duplicates( $jsonld, $schema_type ) {

	}

	private function remove_duplicates_from_jsonld( $jsonld, $schema_type ) {
		return $jsonld;
	}

}
