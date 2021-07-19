<?php

use Wordlift\Vocabulary\Terms_Compat;

/**
 * @group entity
 */
class Wordlift_Entity_Package_Type_Test extends Wordlift_Unit_Test_Case {


	private $professional_feature_labels = array(
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


		$entity_types = Terms_Compat::get_terms( Wordlift_Entity_Type_Taxonomy_Service::TAXONOMY_NAME, array(
			'hide_empty' => false,
		) );

		$entity_type_labels = array_map( function ( $term ) {
			return $term->name;
		}, $entity_types );

		sort( $entity_type_labels );
		sort( $this->professional_feature_labels );

		$this->assertSame( $this->professional_feature_labels, $entity_type_labels, 'Pro plan features should be present.' );

	}


	public function test_when_package_type_changed_to_business_should_have_only_business_entity_types() {

		$this->configuration_service->set_package_type( 'wl_business' );


		$entity_types = Terms_Compat::get_terms( Wordlift_Entity_Type_Taxonomy_Service::TAXONOMY_NAME, array(
			'hide_empty' => false,
		) );

		$entity_type_labels = array_map( function ( $term ) {
			return $term->name;
		}, $entity_types );

		sort( $entity_type_labels );
		$business_feature_labels = $this->professional_feature_labels;
		sort( $business_feature_labels );

		$this->assertSame( $business_feature_labels, $entity_type_labels, 'Business plan features should be present.' );

	}


}