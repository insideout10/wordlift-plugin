<?php
/**
 * Test the local autocomplete service.
 *
 * @group autocomplete
 * @author David Riccitelli <david@wordlift.io>
 * @since 3.24.2
 * @package Wordlift\Tests
 */

namespace Wordlift\Autocomplete;


use WP_UnitTest_Generator_Sequence;

class Local_Autocomplete_Service_Test extends \Wordlift_Unit_Test_Case {

	/**
	 * Test that we don't get more than 50 results, even if there are more.
	 */
	public function test_max_50_results() {

		$post_ids = $this->factory()->post->create_many( 100, array(
			'post_type' => 'entity',
		), array(
			'post_title' => new WP_UnitTest_Generator_Sequence( 'Local Autocomplete Service Test %s' ),
		) );

		for ( $i = 0; $i < count( $post_ids ); $i ++ ) {
			$post_id = $post_ids[ $i ];
			update_post_meta( $post_id, 'entity_url', "http://example.org/$i" );

			wp_add_object_terms( $post_id, 'thing', \Wordlift_Entity_Type_Taxonomy_Service::TAXONOMY_NAME );
		}

		$autocomplete_service = new Local_Autocomplete_Service();
		$results              = $autocomplete_service->query( 'Autocomplete Service', 'local', 'http://example.org/0' );

		$this->assertCount( 50, $results, 'We don`t expect more than 50 results.' );

	}

	/**
	 * Test that only entities are returned.
	 */
	public function test_only_entities() {

		$post_ids = $this->factory()->post->create_many( 25, array(
			'post_type' => 'post',
		), array(
			'post_title' => new WP_UnitTest_Generator_Sequence( 'Local Autocomplete Service Test Post %s' ),
		) );

		$post_ids = $this->factory()->post->create_many( 25, array(
			'post_type' => 'entity',
		), array(
			'post_title' => new WP_UnitTest_Generator_Sequence( 'Local Autocomplete Service Test Entity %s' ),
		) );

		$this->assertCount( 25, $post_ids );

		for ( $i = 0; $i < count( $post_ids ); $i ++ ) {
			$post_id = $post_ids[ $i ];
			update_post_meta( $post_id, 'entity_url', "http://example.org/$i" );

			wp_add_object_terms( $post_id, 'thing', \Wordlift_Entity_Type_Taxonomy_Service::TAXONOMY_NAME );
		}

		$autocomplete_service = new Local_Autocomplete_Service();
		$results              = $autocomplete_service->query( 'Autocomplete Service', 'local', 'http://example.org/0' );

		$this->assertCount( 25, $results, 'We expect 25 entities.' );

	}

	public function test_synonyms() {

		$post_id_1 = $this->factory()->post->create( array(
			'post_type'  => 'entity',
			'post_title' => 'Local Autocomplete Service Test test_synonyms 1'
		) );
		add_post_meta( $post_id_1, '_wl_alt_label', 'Local Autocomplete Service Test test_synonyms abracadabra' );

		$post_id_2 = $this->factory()->post->create( array(
			'post_type'  => 'entity',
			'post_title' => 'Local Autocomplete Service Test test_synonyms 2'
		) );
		add_post_meta( $post_id_2, '_wl_alt_label', 'Local Autocomplete Service Test test_synonyms 2' );

		$post_id_3 = $this->factory()->post->create( array(
			'post_type'  => 'entity',
			'post_title' => 'Local Autocomplete Service Test test_synonyms 3'
		) );

		$post_id_4 = $this->factory()->post->create( array(
			'post_type'  => 'entity',
			'post_title' => 'Local Autocomplete Service Test test_synonyms 4 abracadabra'
		) );
		add_post_meta( $post_id_4, '_wl_alt_label', 'Local Autocomplete Service Test test_synonyms 4' );

		$autocomplete_service = new Local_Autocomplete_Service();
		$results              = $autocomplete_service->query( 'abracadabra', 'local', 'http://example.org/0' );

		$this->assertCount( 2, $results, 'We expect 1 result.' );

	}

}
