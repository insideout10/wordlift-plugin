<?php

use Wordlift\Relation\Object_Relation_Service;

/**
 * @since 3.32.6
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */

class Test_No_Editor_Analysis_Save_content extends Wordlift_No_Editor_Analysis_Unit_Test_Case  {


	public function test_when_feature_is_active_should_create_relations_without_entity_on_content() {

		$wl_entities                                                                           = array();
		$wl_entities['http://dbpedia.org/resource/Welcome_(Erick_Sermon_song)']['uri']         = 'http://dbpedia.org/resource/Welcome_(Erick_Sermon_song)';
		$wl_entities['http://dbpedia.org/resource/Welcome_(Erick_Sermon_song)']['label']       = 'Welcome';
		$wl_entities['http://dbpedia.org/resource/Welcome_(Erick_Sermon_song)']['description'] = 'test';
		$wl_entities['http://dbpedia.org/resource/Welcome_(Erick_Sermon_song)']['main_type']   = 'wl-creative-work';
		$wl_entities['http://dbpedia.org/resource/Welcome_(Erick_Sermon_song)']['type'][]      = 'creative-work';
		$wl_entities['http://dbpedia.org/resource/Welcome_(Erick_Sermon_song)']['sameas'][]    = 'http://en.dbpedia.org/resource/Welcome_(Erick_Sermon_song)';

		$wl_boxes           = array();
		$wl_boxes['what'][] = 'http://dbpedia.org/resource/Welcome_(Erick_Sermon_song)';


		$_POST['wl_entities'] = $wl_entities;
		$_POST['wl_boxes'] = $wl_boxes;

		$post_id = $this->factory()->post->create(array('post_type' => 'no-editor-analysis'));


		$relations = Object_Relation_Service::get_instance()
			->get_references( $post_id,\Wordlift\Object_Type_Enum::POST );

		$this->assertCount( 1, $relations, 'One relation should be created even if the entity is not on the content');

	}


}