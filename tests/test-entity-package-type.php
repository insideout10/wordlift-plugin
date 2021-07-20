<?php

use Wordlift\Features\Response_Adapter;
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

		$this->set_feature_and_trigger_config_save( 'entity-types-starter' );

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

		$this->verify_term_meta( $entity_types );
	}


	public function test_when_package_type_changed_to_professional_should_have_only_professional_entity_types() {

		$this->set_feature_and_trigger_config_save( 'entity-types-professional' );


		$entity_types = Terms_Compat::get_terms( Wordlift_Entity_Type_Taxonomy_Service::TAXONOMY_NAME, array(
			'hide_empty' => false,
		) );

		$entity_type_labels = array_map( function ( $term ) {
			return $term->name;
		}, $entity_types );

		sort( $entity_type_labels );
		sort( $this->professional_feature_labels );

		$this->assertSame( $this->professional_feature_labels, $entity_type_labels, 'Pro plan features should be present.' );

		$this->verify_term_meta( $entity_types );

	}


	public function test_when_package_type_changed_to_business_should_have_only_business_entity_types() {

		$this->set_feature_and_trigger_config_save( 'entity-types-business' );


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

		$this->verify_term_meta( $entity_types );

	}


	public function test_when_package_type_is_not_recognized_dont_alter_entity_types() {


		$before_entity_types = Terms_Compat::get_terms( Wordlift_Entity_Type_Taxonomy_Service::TAXONOMY_NAME, array(
			'hide_empty' => false,
		) );

		$before_entity_type_labels = array_map( function ( $term ) {
			return $term->name;
		}, $before_entity_types );

		sort( $before_entity_type_labels );

		// we set an unknown value to package type, for now we only recognize wl_starter, wl_professional, wl_business
		// if we dont have any of these value then we shouldnt alter the entity types.
		$this->set_feature_and_trigger_config_save( 'entity-types-blogger' );

		$after_entity_types = Terms_Compat::get_terms( Wordlift_Entity_Type_Taxonomy_Service::TAXONOMY_NAME, array(
			'hide_empty' => false,
		) );

		$after_entity_type_labels = array_map( function ( $term ) {
			return $term->name;
		}, $after_entity_types );

		sort( $after_entity_type_labels );


		$this->assertSame( $before_entity_type_labels, $after_entity_type_labels, 'Entity types should not be affected by unknown package type' );

	}

	private function set_feature_and_trigger_config_save( $feature_slug ) {

		// clear out the features
		update_option( Response_Adapter::WL_FEATURES, array( $feature_slug => true ), true );

		do_action( 'wl_after_configuration_save' );
	}

	/**
	 * @param $terms
	 */
	private function verify_term_meta( $terms ) {
		foreach ( $terms as $term ) {
			$name = $term->name;
			$this->assertSame( "http://schema.org/$name", get_term_meta( $term->term_id, '_wl_uri', true ), 'We should have term _wl_uri for the item' );
			$this->assertSame( $name, get_term_meta( $term->term_id, '_wl_name', true ), 'We should have term _wl_name for the item' );

		}
	}


}