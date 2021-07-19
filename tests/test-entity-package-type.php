<?php
/**
 * @group entity
 */
class Wordlift_Entity_Package_Type_Test extends Wordlift_Unit_Test_Case {


	public function test_when_package_type_changed_to_starter_should_have_only_starter_entity_types() {

		$this->configuration_service->set_package_type('wl_starter' );

		$starter_feature_labels = array(
			'Person',
			'Thing',
			'Place',
			'CreativeWork',
			'Organization',
			'Article',
			'WebSite',
			'NewsArticle',
			'AboutPage',
			'ContactPage'
		);

		\Wordlift\Vocabulary\Terms_Compat::get_terms( Wordlift_Entity_Type_Taxonomy_Service::TAXONOMY_NAME, array() )



	}



}