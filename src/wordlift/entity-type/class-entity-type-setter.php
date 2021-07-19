<?php
/**
 * @since ?.??.??
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */

namespace Wordlift\Entity_Type;

use Wordlift\Vocabulary\Terms_Compat;
use Wordlift_Entity_Type_Taxonomy_Service;

class Entity_Type_Setter {


	const STARTER_PLAN = 'wl_starter';

	const PROFESSIONAL_PLAN = 'wl_professional';

	const BUSINESS_PLAN = 'wl_business';

	private static $subscription_types = array(
		self::STARTER_PLAN,
		self::PROFESSIONAL_PLAN,
		self::BUSINESS_PLAN
	);


	public static function get_starter_features() {
		return array(
			'person' => array(
				'label'       => 'Person',
				'description' => 'A person (or a music artist).',
			),
			'thing'  => array(
				'label'       => 'Thing',
				'description' => 'A generic thing (something that doesn\'t fit in the previous definitions.',
			),

			'place' => array(
				'label'       => 'Place',
				'description' => 'A place.',
			),

			'creative-work' => array(
				'label'       => 'CreativeWork',
				'description' => 'A creative work (or a Music Album).',
			),

			'organization' => array(
				'label'       => 'Organization',
				'description' => 'An organization, including a government or a newspaper.',
			),

			'article' => array(
				'label'       => 'Article',
				'description' => 'An article, such as a news article or piece of investigative report. Newspapers and magazines have articles of many different types and this is intended to cover them all.'
			),

			'web-site' => array(
				'label'       => 'WebSite',
				'description' => 'A WebSite is a set of related web pages and other items typically served from a single web domain and accessible via URLs.'
			),

			'news-article' => array(
				'label'       => 'NewsArticle',
				'description' => 'A NewsArticle is an article whose content reports news, or provides background context and supporting materials for understanding the news.'
			),

			'about-page' => array(
				'label'       => 'AboutPage',
				'description' => 'An About page.'
			),

			'contact-page' => array(
				'label'       => 'ContactPage',
				'description' => 'A Contact Page.'
			)

		);
	}


	private function get_entity_types_by_package_type( $package_type ) {

		switch ( $package_type ) {
			case self::STARTER_PLAN:
				return self::get_starter_features();
			default:
				return array();

		}

	}


	public function __construct() {
		add_action( 'wl_package_type_changed', array( $this, 'wl_package_type_changed' ) );
	}


	public function wl_package_type_changed( $package_type ) {

		// Dont make any changes if we cant identify the subscription type.
		if ( ! $package_type || ! in_array( $package_type, self::$subscription_types ) ) {
			return;
		}

		$entity_types_data = $this->get_entity_types_by_package_type( $package_type );

		// If we dont have entity types returned, then dont reset the entity types, return early.
		if ( ! $entity_types_data ) {
			return;
		}

		// Remove all entity types from db.
		$this->remove_all_entity_types();

		// Repopulate the ones returned by package type.
		foreach ( $entity_types_data as $term_slug => $term_data ) {
			wp_insert_term(
				$term_data['label'],
				Wordlift_Entity_Type_Taxonomy_Service::TAXONOMY_NAME,
				array(
					'slug' => $term_slug,
					'description' => $term_data['description']
				)
			);
		}

	}


	/**
	 * Remove all the entity types from db
	 * @return void
	 */
	private function remove_all_entity_types() {

		$entity_types = Terms_Compat::get_terms( Wordlift_Entity_Type_Taxonomy_Service::TAXONOMY_NAME, array(
			'hide_empty' => false,
			'fields' => 'ids'
		) );
		foreach ( $entity_types as $entity_type_id ) {
			wp_delete_term( $entity_type_id, Wordlift_Entity_Type_Taxonomy_Service::TAXONOMY_NAME );
		}
	}


}