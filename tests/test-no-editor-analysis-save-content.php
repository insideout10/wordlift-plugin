<?php

use Wordlift\Content\Wordpress\Wordpress_Content_Id;
use Wordlift\No_Editor_Analysis\No_Editor_Analysis_Feature;
use Wordlift\No_Editor_Analysis\Post_Type;
use Wordlift\Relation\Object_Relation_Service;
use Wordlift\Relation\Relation_Service;

/**
 * @since 3.32.6
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * @group no-editor
 */
class Test_No_Editor_Analysis_Save_content extends Wordlift_No_Editor_Analysis_Unit_Test_Case {


	public function test_when_post_id_is_falsy_should_not_return_true() {
		$this->assertFalse( No_Editor_Analysis_Feature::can_no_editor_analysis_be_used( 0 ), 'Post id is falsy should not enable this no editor analysis feature' );
		$this->assertFalse( No_Editor_Analysis_Feature::can_no_editor_analysis_be_used( false ), 'Post id is falsy should not enable this no editor analysis feature' );
		$this->assertFalse( No_Editor_Analysis_Feature::can_no_editor_analysis_be_used( null ), 'Post id is falsy should not enable this no editor analysis feature' );
		$this->assertFalse( No_Editor_Analysis_Feature::can_no_editor_analysis_be_used( '' ), 'Post id is falsy should not enable this no editor analysis feature' );
		$this->assertFalse( Post_Type::is_no_editor_analysis_enabled_for_post_type( '' ), 'Post type is falsy should not enable this no editor analysis feature' );
		$this->assertFalse( Post_Type::is_no_editor_analysis_enabled_for_post_type( 'unknown_post_type' ), 'Post type is falsy should not enable this no editor analysis feature' );
	}


	public function test_when_feature_is_active_should_create_relations_without_entity_on_content() {
		$wl_entities = array();
		$wl_entities['http://dbpedia.org/resource/Welcome_(Erick_Sermon_song)']['uri']         = 'http://dbpedia.org/resource/Welcome_(Erick_Sermon_song)';
		$wl_entities['http://dbpedia.org/resource/Welcome_(Erick_Sermon_song)']['label']       = 'Welcome';
		$wl_entities['http://dbpedia.org/resource/Welcome_(Erick_Sermon_song)']['description'] = 'test';
		$wl_entities['http://dbpedia.org/resource/Welcome_(Erick_Sermon_song)']['main_type']   = 'wl-creative-work';
		$wl_entities['http://dbpedia.org/resource/Welcome_(Erick_Sermon_song)']['type'][]      = 'creative-work';
		$wl_entities['http://dbpedia.org/resource/Welcome_(Erick_Sermon_song)']['sameas'][]    = 'http://en.dbpedia.org/resource/Welcome_(Erick_Sermon_song)';

		$wl_boxes           = array();
		$wl_boxes['what'][] = 'http://dbpedia.org/resource/Welcome_(Erick_Sermon_song)';

		$_POST['wl_entities'] = $wl_entities;
		$_POST['wl_boxes']    = $wl_boxes;

		$post_id = $this->factory()->post->create( array( 'post_type' => 'no-editor-analysis' ) );

		wl_linked_data_save_post_and_related_entities( $post_id );


		$relations = Relation_Service::get_instance()
		                             ->get_relations( Wordpress_Content_Id::create_post( $post_id ) )->toArray();

		$this->assertCount( 1, $relations, 'One relation should be created even if the entity is not on the content' );

	}


}
