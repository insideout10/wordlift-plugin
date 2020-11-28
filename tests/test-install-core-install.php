<?php
/**
 * Tests: Core Install Test.
 *
 * Tests functions in `wordlift_core_install.php`.
 *
 * @since      3.14.0
 * @package    Wordlift
 * @subpackage Wordlift/tests
 */

/**
 * Define the {@link Wordlift_Core_Install_Test} class.
 *
 * @since      3.14.0
 * @package    Wordlift
 * @subpackage Wordlift/tests
 * @group install
 */
class Wordlift_Core_Install_Test extends Wordlift_Unit_Test_Case {

	/**
	 * Test the Recipe {@lin WP_Term} added in 3.14.0.
	 *
	 * @since 3.14.0
	 */
	function test_3_14_0_recipe() {

		$term = get_term_by( 'slug', 'Recipe', Wordlift_Entity_Type_Taxonomy_Service::TAXONOMY_NAME );

		$this->isInstanceOf( 'WP_Term', $term );

		$this->assertEquals( 'recipe', $term->slug );

	}

}