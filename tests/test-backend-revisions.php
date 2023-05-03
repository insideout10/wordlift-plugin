<?php
/**
 * Tests: Revision generation and restore.
 *
 * @since      3.12.0
 * @package    Wordlift
 * @subpackage Wordlift/tests
 */

/**
 * Define the {@link Wordlift_Revision_Generation_Test} test class.
 *
 * @since      3.12.0
 * @package    Wordlift
 * @subpackage Wordlift/tests
 * @group backend
 */
class Wordlift_Revision_Generation_Test extends Wordlift_Unit_Test_Case {

	/**
	 * Test that revisions are generated for entities
	 *
	 * @since 3.12.0
	 */
	function testRevisionGenerationForEntity() {
		$entity_id = wl_create_post( 'Original content', 'entity-1', uniqid( 'entity', true ), 'publish', 'entity' );
		$revisions = wp_get_post_revisions( $entity_id );

		// Just a simple sanity check.
		$this->assertCount( 0, $revisions );

		// Check that a revision is generated on change.
		$post_data = array(
			'ID'           => $entity_id,
			'post_content' => 'This is the updated content.',
		);

		wp_update_post( $post_data );
		$revisions = wp_get_post_revisions( $entity_id );
		$this->assertCount( 1, $revisions );
	}

	/**
	 * Test that post and entities are properly connected
	 * when post revision is changed
	 *
	 * @since 3.12.0
	 */
	function testPostEntityRelationshipWhenRevisionChange() {

		\Wordlift_Configuration_Service::get_instance()->set_dataset_uri( 'http://data.example.org/data/' );

		// create two entities
		$entity_1_id = wl_create_post( '', 'entity-1', uniqid( 'entity', true ), 'draft', 'entity' );
		$entity_2_id = wl_create_post( '', 'entity-2', uniqid( 'entity', true ), 'draft', 'entity' );

		$entity_1_uri = wl_get_entity_uri( $entity_1_id );
		$entity_2_uri = wl_get_entity_uri( $entity_2_id );

		$body_1 = <<<EOF
            <span class="textannotation disambiguated" itemid="$entity_1_uri">Entity 1</span>
            <span class="textannotation disambiguated" itemid="$entity_2_uri">Entity 2</span>
EOF;

		// Create a post connected to the entities
		$post_1_id = wl_create_post( '', 'post-1', uniqid( 'post', true ), 'publish', 'post' );
		wp_update_post( array(
			'ID'           => $post_1_id,
			'post_content' => $body_1,
		) );

		// Sanity check.
		$this->assertCount( 2, wl_core_get_related_entity_ids( $post_1_id ) );

		// Create revision with only one entity.
		$body_2 = <<<EOF
            <span class="textannotation disambiguated" itemid="$entity_1_uri">Entity 1</span>
EOF;

		$post_data = array(
			'ID'           => $post_1_id,
			'post_content' => $body_2,
		);

		wp_update_post( $post_data );
		$this->assertCount( 1, wl_core_get_related_entity_ids( $post_1_id ) );
		$entity = wl_core_get_related_entity_ids( $post_1_id );
		$this->assertEquals( $entity_1_id, $entity[0] );

		// Restore to the original revision and check.
		$revisions = wp_get_post_revisions( $post_1_id );
		$this->assertCount( 2, $revisions );

		// Revisions are returned sorted by reverse creation time.
		// First one therefor is the last.
		$original = end( $revisions );
		wp_restore_post_revision( $original->ID );
		$this->assertCount( 2, wl_core_get_related_entity_ids( $post_1_id ) );
	}

	/**
	 * Test that entity is properly connected to others
	 * when entity revision is changed
	 *
	 * @since 3.12.0
	 */
	function testEntityToEntityRelationshipWhenRevisionChange() {

		\Wordlift_Configuration_Service::get_instance()->set_dataset_uri( 'http://data.example.org/data/' );

		// create two entities
		$entity_1_id = wl_create_post( '', 'entity-1', uniqid( 'entity', true ), 'draft', 'entity' );
		$entity_2_id = wl_create_post( '', 'entity-2', uniqid( 'entity', true ), 'draft', 'entity' );

		$entity_1_uri = wl_get_entity_uri( $entity_1_id );
		$entity_2_uri = wl_get_entity_uri( $entity_2_id );

		$body_1 = <<<EOF
            <span class="textannotation disambiguated" itemid="$entity_1_uri">Entity 1</span>
            <span class="textannotation disambiguated" itemid="$entity_2_uri">Entity 2</span>
EOF;

		// Create an entity connected to the entities
		$entity_3_id = wl_create_post( '', 'post-1', uniqid( 'post', true ), 'publish', 'entity' );
		// Sanity check.
		wp_update_post( array(
			'ID'           => $entity_3_id,
			'post_content' => $body_1,
		) );
		$this->assertCount( 2, wl_core_get_related_entity_ids( $entity_3_id ) );

		// Create revision with only one entity.
		$body_2 = <<<EOF
            <span class="textannotation disambiguated" itemid="$entity_1_uri">Entity 1</span>
EOF;

		$post_data = array(
			'ID'           => $entity_3_id,
			'post_content' => $body_2,
		);

		wp_update_post( $post_data );
		$this->assertCount( 1, wl_core_get_related_entity_ids( $entity_3_id ) );
		$entity = wl_core_get_related_entity_ids( $entity_3_id );
		$this->assertEquals( $entity_1_id, $entity[0] );

		// Restore to the original revision and check.
		$revisions = wp_get_post_revisions( $entity_3_id );

		// Revisions are returned sorted by reverse creation time.
		// First one therefor is the last.
		$original = end( $revisions );
		wp_restore_post_revision( $original->ID );
		$this->assertCount( 2, wl_core_get_related_entity_ids( $entity_3_id ) );
	}

}
