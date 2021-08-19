<?php

use Wordlift\Relation\Object_Relation_Service;

/**
 * @since 3.32.6
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */

class Test_No_Editor_Analysis_Response extends Wordlift_No_Editor_Analysis_Unit_Test_Case  {


	public function test_when_no_editor_analysis_is_enabled_should_add_a_fake_occurrence() {


		$post_id = $this->factory()->post->create(array('post_type' => 'no-editor-analysis'));

		// Create a local entity with sameAs set to cloud entity uri.
		$entity = $this->factory()->post->create( array( 'post_type' => 'entity' ) );
		// set sameAs to a cloud entity uri.
		add_post_meta( $entity, 'entity_same_as', 'http://dbpedia.org/resource/Microsoft_Outlook' );
		wl_core_add_relation_instance( $post_id, WL_WHAT_RELATION, $entity );

		$request_body            = file_get_contents( dirname( __FILE__ ) . '/assets/content-analysis-request-4.json' );
		$request_body            = json_decode( $request_body, true );


		$_REQUEST['postId'] = $post_id;

		$local_entity_uri                                               = wl_get_entity_uri( $entity );
		$this->url_patterns_json_response_map['@analysis/v2/analyze$@'] = $this->get_response( $local_entity_uri );

		$json = wl_analyze_content( json_encode( $request_body ), 'text/html' );
		// convert json to associative array for easy comparisons.
		$json = json_decode( json_encode( $json ), true );
		$this->assertCount( 1, $json['entities'], '1 entity should not be present' );
		$this->assertCount( 1, $json['entities'][$local_entity_uri]['occurrences'], '1 occurence should be present' );

	}


	public function get_response( $local_entity_uri ) {

		$response = file_get_contents( __DIR__ . '/assets/content-analysis-response-4.json' );
		$response = str_replace( '{{LOCAL_ENTITY_URI}}', $local_entity_uri, $response );
		return $response;
	}


}