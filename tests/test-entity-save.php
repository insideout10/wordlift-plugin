<?php
/**
 * @since 3.28.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */

use Wordlift\Entity\Entity_No_Index_Flag;

/**
 * Class Entity_Save_Test
 * @group entity
 */
class Entity_Save_Test extends Wordlift_Unit_Test_Case {

	public function setUp() {
		parent::setUp();
	}

	public function test_when_entity_is_saved_for_first_time_should_set_yoast_no_index_flag() {

		new Entity_No_Index_Flag();

		$entity = $this->factory()->post->create( array( 'post_type' => 'entity' ) );

		$result = get_post_meta( $entity, '_yoast_wpseo_meta-robots-noindex', true );

		$this->assertEquals( 1, $result, 'Value should be 1 to denote the flag set by yoast' );

	}


	public function test_when_other_post_type_is_saved_for_first_time_should_not_set_yoast_no_index_flag() {

		new Entity_No_Index_Flag();

		$entity = $this->factory()->post->create();

		$result = get_post_meta( $entity, '_yoast_wpseo_meta-robots-noindex', true );

		$this->assertEquals( '', $result, 'The flag should not be set for other post types' );

	}


	public function test_when_other_post_type_is_updated_should_not_remove_noindex_flag() {

		new Entity_No_Index_Flag();

		$entity = $this->factory()->post->create();

		// Emulate the post has no index flag, it should not be removed on update
		update_post_meta( $entity, Entity_No_Index_Flag::YOAST_POST_NO_INDEX_FLAG, 1 );

		// update the entity
		wp_update_post( array( 'ID' => $entity, 'post_content' => 'test' ) );

		$result = get_post_meta( $entity, '_yoast_wpseo_meta-robots-noindex', true );

		$this->assertEquals( 1, $result, 'The flag should be present for other post type' );

	}


	public function test_when_entity_is_updated_should_remove_yoast_no_index_flag() {

		new Entity_No_Index_Flag();

		$entity = $this->factory()->post->create( array( 'post_type' => 'entity' ) );

		// update the entity
		wp_update_post( array( 'ID' => $entity, 'post_content' => 'test' ) );

		$result = get_post_meta( $entity, '_yoast_wpseo_meta-robots-noindex', true );

		$this->assertEquals( '', $result, 'The no index flag should not be present' );

	}


	public function test_when_entity_is_converted_to_article_should_remove_all_the_synonyms() {

		$this->markTestSkipped( "As of 3.33.9, we don't delete the synonyms since it steals CPU time." );

		$entity = $this->factory()->post->create( array( 'post_type' => 'entity' ) );

		// now set the synonyms.
		Wordlift_Entity_Service::get_instance()
		                       ->set_alternative_labels( $entity, array( 'one', 'two', 'three' ) );

		// convert it to article.
		Wordlift_Entity_Type_Service::get_instance()
		                            ->set( $entity, 'http://schema.org/Article' );

		// update the post, we emulate this because we
		wp_update_post( array(
			'ID'           => $entity,
			'post_content' => 'Entity converted to article'
		) );


		// see https://github.com/insideout10/wordlift-plugin/issues/1429

		// Get the synonyms, we should have none.
		$this->assertSame(
			array(),
			Wordlift_Entity_Service::get_instance()->get_alternative_labels( $entity ),
			'when the entity type is switching to only "Article" then we should erase all the synonyms'
		);


	}

	public function test_should_not_create_entities_for_invalid_non_url_entity_ids() {

		$post_content = <<<EOF
<!-- wp:paragraph -->
<p><span id="urn:enhancement-8e9e7fe8-fedc-4a68-9b62-6393933cbe7b" class="textannotation">Sales</span> and <span id="urn:enhancement-ee3578d2-a7b7-4ccc-b273-503b27c3efb6" class="textannotation">marketing</span> <span id="urn:enhancement-d2af76de-8e05-4e55-970a-66742737b9de" class="textannotation">teams</span> can rely on <span id="urn:enhancement-e534df16-467c-410d-b970-a24641ad3765" class="textannotation">intent</span> <span id="urn:enhancement-8671298f-64d7-4a4d-b76d-894e71c490c0" class="textannotation">data</span> to ensure effective <a href="https://pipeline.zoominfo.com/marketing/how-to-build-gtm-strategy" data-type="post" data-id="16130"><span id="urn:local-annotation-527244" class="textannotation disambiguated" itemid="/post/how_to_build_your_go-to-market_strategy_2">go-to-market strategies</span></a>, accurate <span id="urn:enhancement-731096b1-201f-48b7-a15f-f11038f0fcd3" class="textannotation">segmentation</span>, and personalized outreach to <span id="urn:enhancement-aa0d52c0-461b-44f9-ac3d-429062c72c02" class="textannotation">the right</span> <span id="urn:enhancement-bd13ef59-2207-4631-8c8a-4d1490fad4b6" class="textannotation">people</span>.</p>
<!-- /wp:paragraph -->
<!-- wp:wordlift/classification {"entities":[{"annotations":{"urn:local-annotation-527244":{"start":70,"end":93}},"description":"test", "id":"/post/how_to_build_your_go-to-market_strategy_2", "sameAs":[], "label":["How To Build Your Go-To-Market Strategy"],"mainType":"thing","occurrences":["urn:local-annotation-527244"],"types":["http://schema.org/Thing"]}]} /-->
EOF;
		$post_id      = $this->factory()->post->create( array( 'post_content' => $post_content ) );

		$created_entities = get_posts( array(
			'post_type'   => 'entity',
			'post_status' => 'any'
		) );
		// Filtering the entities here since we also create author entity on post save.
		$filtered_entities = array_filter( $created_entities, function ( $item ) {
			return $item->post_content !== '';
		} );
		$this->assertCount( 0, $filtered_entities, 'No entity should be created for invalid id' );
	}


}
