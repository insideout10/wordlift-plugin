<?php
/**
 * Tests: DB version upgrade tests
 *
 * @since   3.10.0
 * @package Wordlift
 */

/**
 * Define the test class.
 *
 * @since   3.10.0
 * @package Wordlift
 */
class Wordlift_Install_Test extends WP_UnitTestCase {

	/**
	 * Test that all terms in the entity taxonomy are properly created,
	 * for starters, all the terms are in the DB and in flat hierarchy.
	 *
	 * @since 3.10.0
	 *
	 **/
	public function test_entity_terms() {

		wl_core_install();

		$slugs = array(
			'thing',
			'creative-work',
			'event',
			'organization',
			'person',
			'place',
			'localbusiness',
		);

		foreach ( $slugs as $slug ) {
			$term = get_term_by( 'slug', $slug, Wordlift_Entity_Types_Taxonomy_Service::TAXONOMY_NAME );
			$this->assertNotNull( $term );
			$this->assertEquals( 0, $term->parent );
		}

	}

}
