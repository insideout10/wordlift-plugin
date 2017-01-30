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
class Wordlift_DB_Upgrade_Test extends WP_UnitTestCase {

	/**
	* Test the 1.0 to 3.10 DB upgrade.
	*
	* That upgrade path is focused on "flattening" the hirarchy of the
	* term in the entuty type taxonomy that were created in installs of pre 3.10
	* release.
	*
	* @since   3.10.0
	**/
	public function test_1_0_to_3_10_upgrade() {
		// first reconstruct the pre 3.10.0 DB for the entities taxonomy
		// based on the 3.9.x code.

		// make sure the taxonomy is empty of terms.

		$terms = get_terms( 'wl_entity_type', array( 'fields' => 'ids', 'hide_empty' => false ) );
		foreach ( $terms as $value ) {
			 wp_delete_term( $value, 'wl_entity_type' );
		}

		// now setup to pre 3.10 structure.
		$terms = array(
			'thing'         => array(
				'label'       => 'Thing',
				'description' => 'A generic thing (something that doesn\'t fit in the previous definitions.',
			),
			'creative-work' => array(
				'label'       => 'CreativeWork',
				'description' => 'A creative work (or a Music Album).',
				'parents'     => array( 'thing' ), // give term slug as parent
			),
			'event'         => array(
				'label'       => 'Event',
				'description' => 'An event.',
				'parents'     => array( 'thing' ),
			),
			'organization'  => array(
				'label'       => 'Organization',
				'description' => 'An organization, including a government or a newspaper.',
				'parents'     => array( 'thing' ),
			),
			'person'        => array(
				'label'       => 'Person',
				'description' => 'A person (or a music artist).',
				'parents'     => array( 'thing' ),
			),
			'place'         => array(
				'label'       => 'Place',
				'description' => 'A place.',
				'parents'     => array( 'thing' ),
			),
			'localbusiness' => array(
				'label'       => 'LocalBusiness',
				'description' => 'A local business.',
				'parents'     => array( 'place', 'organization' ),
			),
		);

		foreach ( $terms as $slug => $term ) {
			$result = wp_insert_term( $slug, 'wl_entity_type' );
			// Check if 'parent' corresponds to an actual term and get its ID.
			if ( ! isset( $term['parents'] ) ) {
				$term['parents'] = array();
			}
			$parent_ids = array();
			foreach ( $term['parents'] as $parent_slug ) {
				$parent_id    = get_term_by( 'slug', $parent_slug, 'wl_entity_type' );
				$parent_ids[] = intval( $parent_id->term_id );
			}
			// Define a parent in the WP taxonomy style (not important for WL)
			if ( empty( $parent_ids ) ) {
				// No parent
				$parent_id = 0;
			} else {
				// Get first parent
				$parent_id = $parent_ids[0];
			}
			// Update term with description, slug and parent
			wp_update_term( $result['term_id'], 'wl_entity_type', array(
				'name'        => $term['label'],
				'slug'        => $slug,
				'description' => $term['description'],
				'parent'      => $parent_id,
			) );
		}

		update_site_option( 'wl_db_version', '3.9' );

		// now call the upgrade routine and check that everything is Flatten
		wl_core_upgrade_db_1_0_to_3_10();
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
			$term = get_term_by( 'slug', $slug, 'wl_entity_type' );
			$this->assertEquals( 0, $term->parent );
		}
	}

	/**
	* Test that the 1.0 to 3.10 DB upgrade is not run when db version is 3.10
	*
	* @since   3.10.0
	**/
	public function test_3_10_to_3_10_upgrade() {

		wp_insert_term( 'dummy', 'wl_entity_type', array( 'parent' => 1 ) );

		update_site_option( 'wl_db_version', '3.10' );

		// now call the upgrade routine and check that the dummy term still has a parent
		wl_core_upgrade_db_1_0_to_3_10();

		$term = get_term_by( 'slug', 'dummy', 'wl_entity_type' );
		$this->assertEquals( 1, $term->parent );
	}
}
