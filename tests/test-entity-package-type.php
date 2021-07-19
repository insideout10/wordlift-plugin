<?php

use Wordlift\Vocabulary\Terms_Compat;

/**
 * @group entity
 */
class Wordlift_Entity_Package_Type_Test extends Wordlift_Unit_Test_Case {


	public function test_when_package_type_changed_to_starter_should_have_only_starter_entity_types() {

		$this->configuration_service->set_package_type( 'wl_starter' );

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

		$entity_types = Terms_Compat::get_terms( Wordlift_Entity_Type_Taxonomy_Service::TAXONOMY_NAME, array(
			'hide_empty' => false,
		) );

		$entity_type_labels = array_map( function ( $term ) {
			return $term->name;
		}, $entity_types );

		sort( $entity_type_labels );
		sort( $starter_feature_labels );

		$this->assertSame( $starter_feature_labels, $entity_type_labels, ' Entity types should be changed based on package type.' );
	}


	public function test_when_package_type_changed_to_professional_should_have_only_professional_entity_types() {

		$this->configuration_service->set_package_type( 'wl_professional' );

		$professional_feature_labels = array(
			'Person',
			'Thing',
			'Place',
			'CreativeWork',
			'Organization',
			'Article',
			'WebSite',
			'NewsArticle',
			'AboutPage',
			'ContactPage',
			'FAQPage',
			'LocalBusiness',
			'Recipe',
			'PodcastEpisode',
			'Course',
			'Event',
			'Review'
		);

		$entity_types = Terms_Compat::get_terms( Wordlift_Entity_Type_Taxonomy_Service::TAXONOMY_NAME, array(
			'hide_empty' => false,
		) );

		$entity_type_labels = array_map( function ( $term ) {
			return $term->name;
		}, $entity_types );

		sort( $entity_type_labels );
		sort( $professional_feature_labels );

		$this->assertSame( $professional_feature_labels, $entity_type_labels, ' Entity types should be changed based on package type.' );
	}


}