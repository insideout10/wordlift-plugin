<?php

use Wordlift\Analysis\Occurrences\No_Annotation_Strategy;
use Wordlift\Content\WordPress\Wordpress_Content_Id;
use Wordlift\Content\WordPress\Wordpress_Content_Service;

/**
 * @since 3.32.6
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * @group no-editor
 */
class Test_No_Editor_Analysis_Response extends Wordlift_No_Editor_Analysis_Unit_Test_Case {

	public function test_when_no_editor_analysis_is_enabled_should_add_a_fake_occurrence() {

		$post_id = $this->factory()->post->create( array( 'post_type' => 'no-editor-analysis' ) );

		// Create a local entity with sameAs set to cloud entity uri.
		$entity_id = $this->factory()->post->create( array( 'post_type' => 'entity' ) );

		// set sameAs to a cloud entity uri.
		add_post_meta( $entity_id, 'entity_same_as', 'http://dbpedia.org/resource/Microsoft_Outlook' );
		wl_core_add_relation_instance( $post_id, WL_WHAT_RELATION, $entity_id );

		$request_body = file_get_contents( dirname( __FILE__ ) . '/assets/content-analysis-request-4.json' );
		$request_body = json_decode( $request_body, true );


		$_REQUEST['postId'] = $post_id;

		$local_entity_uri = Wordpress_Content_Service::get_instance()->get_entity_id( Wordpress_Content_Id::create_post( $entity_id ) );

		$this->url_patterns_json_response_map['@analysis/v2/analyze$@'] = $this->get_response( $local_entity_uri );

		$json = wl_analyze_content( json_encode( $request_body ), 'text/html' );
		// convert json to associative array for easy comparisons.
		$json = json_decode( json_encode( $json ), true );
		$this->assertCount( 1, $json['entities'], '1 entity should not be present' );
		$this->assertCount( 1, $json['entities'][ $local_entity_uri ]['occurrences'], '1 occurence should be present' );

	}


	/**
	 * We already rewrite the URIs from the analysis service to local entities, but in a case like an entity created from
	 * the create entity box, the analysis would not return it, we wont be able to add it since there is no annotation,
	 * so we need to add those entities manually in the response.
	 */
	public function test_on_no_annotation_strategy_we_should_have_all_the_local_entities_in_response() {

		// Lets create  a post and 2 linked entities, we should have them in the json.
		$post_id  = $this->factory()->post->create();
		$entity_1 = $this->factory()->post->create( array( 'post_type' => 'entity' ) );
		$entity_2 = $this->factory()->post->create( array( 'post_type' => 'entity' ) );

		$content_service = Wordpress_Content_Service::get_instance();

		$this->assertInstanceOf( '\Wordlift\Content\WordPress\Wordpress_Dataset_Content_Service', $content_service );
		$this->assertNotEmpty( $content_service->get_entity_id( Wordpress_Content_Id::create_post( $post_id ) ) );
		$this->assertNotEmpty( $content_service->get_entity_id( Wordpress_Content_Id::create_post( $entity_1 ) ) );
		$this->assertNotEmpty( $content_service->get_entity_id( Wordpress_Content_Id::create_post( $entity_2 ) ) );

		wl_core_add_relation_instance( $post_id, WL_WHAT_RELATION, $entity_1 );
		wl_core_add_relation_instance( $post_id, WL_WHAT_RELATION, $entity_2 );

		$strategy = No_Annotation_Strategy::get_instance();


		$analysis_response           = new StdClass;
		$analysis_response->entities = new StdClass;


		$json = $strategy->add_occurrences_to_entities( array(), $analysis_response, $post_id );

		$json_arr = json_decode( wp_json_encode( $json ), true );
		$this->assertCount( 2, array_keys( $json_arr['entities'] ) );
	}


	public function test_when_no_annotation_relation_service_should_return_also_article_entities() {

		// create a no editor post type.
		$post     = $this->factory()->post->create( array( 'post_type' => self::NO_EDITOR_ANALYSIS_POST_TYPE ) );
		$entity_1 = $this->factory()->post->create( array( 'post_type' => 'entity' ) );
		$entity_2 = $this->factory()->post->create( array( 'post_type' => 'entity' ) );

		$content_service = Wordpress_Content_Service::get_instance();
		$this->assertNotEmpty( $content_service->get_entity_id( Wordpress_Content_Id::create_post( $post ) ) );
		$this->assertNotEmpty( $content_service->get_entity_id( Wordpress_Content_Id::create_post( $entity_1 ) ) );
		$this->assertNotEmpty( $content_service->get_entity_id( Wordpress_Content_Id::create_post( $entity_2 ) ) );

		wl_core_add_relation_instance( $post, WL_WHAT_RELATION, $entity_1 );
		wl_core_add_relation_instance( $post, WL_WHAT_RELATION, $entity_2 );

		// set Article as schema type for $entity_1.
		Wordlift_Entity_Type_Service::get_instance()
		                            ->set( $entity_1, 'http://schema.org/Article' );


		$json           = new StdClass;
		$json->entities = new StdClass;

		// we should return two entities for no annotation relation service.
		$json = No_Annotation_Strategy::get_instance()
		                              ->add_occurrences_to_entities( array(), $json, $post );

		// convert json to array for easy assertions.
		$json = json_decode( json_encode( $json ), true );
		$this->assertCount( 2, $json['entities'], 'Article entity should also be returned by the analysis' );

	}


	public function get_response( $local_entity_uri ) {

		$response = file_get_contents( __DIR__ . '/assets/content-analysis-response-4.json' );
		$response = str_replace( '{{LOCAL_ENTITY_URI}}', $local_entity_uri, $response );

		return $response;
	}


}