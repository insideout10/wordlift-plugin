<?php

use Wordlift\Content\Content_Migration;

/**
 * This test checks that the Content Migration is moving the `entity_url` data from the post meta table to the
 * wl entities table.
 *
 * @author David Riccitelli
 * @version 3.33.9
 * @group rel-item-id
 */
class Content_Migration_Test extends Wordlift_Unit_Test_Case {

	/**
	 * Test that data is migrated when the method is run the first time.
	 */
	public function test_migrate_first_run() {
		global $wpdb;

		// Simulate first run.
		delete_option( '_wl_content_migration__migrated' );

		$post_id = $this->factory()->post->create();

		$dataset_uri = trailingslashit( Wordlift_Configuration_Service::get_instance()->get_dataset_uri() );
		$wpdb->query( "
			INSERT INTO $wpdb->postmeta( post_id, meta_key, meta_value )
			VALUES
				( $post_id, 'entity_url', '{$dataset_uri}entity/0' ),
			    ( $post_id, 'entity_same_as', 'https://cloud.example.org/data/entity/0' )
		" );

		$content_migration = new Content_Migration();
		$content_migration->migrate();

		$this->assertEquals( 0, $wpdb->get_var( "
			SELECT COUNT( 1 )
			FROM $wpdb->postmeta
			WHERE post_ID = $post_id AND meta_key = 'entity_url'
		" ), 'No row expected, since it must be migrated to the new table.' );

		$this->assertEquals( 1, $wpdb->get_var( "
			SELECT COUNT( 1 )
			FROM $wpdb->postmeta
			WHERE post_ID = $post_id AND meta_key = 'entity_same_as'
		" ), '1 row expected, since it must **not** be migrated to the new table.' );

		$row = $wpdb->get_row( "
			SELECT rel_uri, rel_uri_hash
			FROM {$wpdb->prefix}wl_entities
			WHERE content_id = $post_id AND content_type = 0
		" );

		$this->assertEquals( 'entity/0', $row->rel_uri );
		$this->assertEquals( sha1( 'entity/0' ), $row->rel_uri_hash );

		$this->assertTrue( get_option( '_wl_content_migration__migrated' ), 'The method should set that it migrated contents.' );

	}

	/**
	 * Test that the data is not migrated if the method already ran. We don't want in fact to repeat the SQL execution
	 * each time.
	 */
	public function test_migrate_second_run() {
		global $wpdb;

		$this->assertTrue( (bool) get_option( '_wl_content_migration__migrated' ), 'The method should set that it migrated contents.' );

		$post_id = $this->factory()->post->create();

		$dataset_uri = trailingslashit( Wordlift_Configuration_Service::get_instance()->get_dataset_uri() );
		$wpdb->query( "
			INSERT INTO $wpdb->postmeta( post_id, meta_key, meta_value )
			VALUES( $post_id, 'entity_url', '{$dataset_uri}entity/0' )
		" );

		$content_migration = new Content_Migration();
		$content_migration->migrate();

		$this->assertEquals( 1, $wpdb->get_var( "
			SELECT COUNT( 1 )
			FROM $wpdb->postmeta
			WHERE post_ID = $post_id AND meta_key = 'entity_url'
		" ), 'The data should be still there because the method did not run yet.' );

		$this->assertEquals( 1, $wpdb->get_var( "
			SELECT COUNT( 1 )
			FROM {$wpdb->prefix}wl_entities
			WHERE content_id = $post_id AND content_type = 0
		" ), 'The data should be still there because the method did not run yet.' );

	}

}
