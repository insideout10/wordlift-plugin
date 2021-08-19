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


		$request_body            = file_get_contents( dirname( __FILE__ ) . '/assets/content-analysis-request-4.json' );
		$request_body            = json_decode( $request_body, true );


		$_REQUEST['postId'] = $post_id;
		$json = wl_analyze_content( json_encode( $request_body ), 'text/html' );
		// convert json to associative array for easy comparisons.
		$json = json_decode( json_encode( $json ), true );
		$this->assertCount( 1, $json['entities'], '1 entity should not be present' );
		$this->assertCount( 1, $json['entities']['http://dbpedia.org/resource/Microsoft_Outlook']['occurences'], '1 occurence should be present' );

	}


}