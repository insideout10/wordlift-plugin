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
 * @group install
 */
class Wordlift_Install_Test extends Wordlift_Unit_Test_Case {

	/**
	 * Test that all terms in the entity taxonomy are properly created,
	 * for starters, all the terms are in the DB and in flat hierarchy.
	 *
	 * @since 3.10.0
	 *
	 **/
	public function test_entity_terms() {

		activate_wordlift();

		$slugs = array(
			'thing',
			'creative-work',
			'event',
			'organization',
			'person',
			'place',
			'local-business',
		);

		foreach ( $slugs as $slug ) {
			$term = get_term_by( 'slug', $slug, Wordlift_Entity_Type_Taxonomy_Service::TAXONOMY_NAME );
			$this->assertNotFalse( $term );
			$this->assertEquals( 0, $term->parent );
		}

	}

}
