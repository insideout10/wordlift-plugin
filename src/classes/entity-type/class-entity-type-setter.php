<?php
/**
 * @since 3.32.1
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */

namespace Wordlift\Entity_Type;

use Wordlift_Entity_Type_Taxonomy_Service;

class Entity_Type_Setter {

	const STARTER_PLAN = 'entity-types-starter';

	const PROFESSIONAL_PLAN = 'entity-types-professional';

	const BUSINESS_PLAN = 'entity-types-business';

	public function __construct() {
		add_action(
			'wl_feature__change__entity-types-starter',
			array(
				$this,
				'wl_entity_types_feature_changed',
			),
			10,
			3
		);
		add_action(
			'wl_feature__change__entity-types-professional',
			array(
				$this,
				'wl_entity_types_feature_changed',
			),
			10,
			3
		);
		add_action(
			'wl_feature__change__entity-types-business',
			array(
				$this,
				'wl_entity_types_feature_changed',
			),
			10,
			3
		);
	}

	public function wl_entity_types_feature_changed( $new_value, $old_value, $feature_slug ) {

		// If the entity types is not set by server, then return early.
		if ( ! $new_value ) {
			return;
		}

		$entity_types_data = self::get_entity_types_by_feature_flag( $feature_slug );

		// If we dont have entity types returned, then dont reset the entity types, return early.
		if ( ! $entity_types_data ) {
			return;
		}
		// Repopulate the ones returned by package type.
		foreach ( $entity_types_data as $entity_type_data ) {

			$schema_label = $entity_type_data['label'];

			$term_exists = get_term_by( 'name', $schema_label, Wordlift_Entity_Type_Taxonomy_Service::TAXONOMY_NAME ) instanceof \WP_Term;

			if ( $term_exists ) {
				// Dont create term if it already exists.
				continue;
			}

			$term_data = wp_insert_term(
				$schema_label,
				Wordlift_Entity_Type_Taxonomy_Service::TAXONOMY_NAME,
				array(
					'description' => $entity_type_data['description'],
					'slug'        => $entity_type_data['slug'],
				)
			);

			$term_id = $term_data['term_id'];

			update_term_meta( $term_id, '_wl_uri', 'http://schema.org/' . $schema_label );
			update_term_meta( $term_id, '_wl_name', $schema_label );
		}

	}

	public static function get_starter_entity_types() {
		return array(
			array(
				'label'       => 'Person',
				'description' => 'A person (or a music artist).',
				'slug'        => 'person',
			),
			array(
				'label'       => 'Thing',
				'description' => 'A generic thing (something that doesn\'t fit in the previous definitions.',
				'slug'        => 'thing',
			),

			array(
				'label'       => 'Place',
				'description' => 'A place.',
				'slug'        => 'place',
			),

			array(
				'label'       => 'CreativeWork',
				'description' => 'A creative work (or a Music Album).',
				'slug'        => 'creative-work',
			),
			array(
				'label'       => 'Organization',
				'description' => 'An organization, including a government or a newspaper.',
				'slug'        => 'organization',
			),

			array(
				'label'       => 'Article',
				'description' => 'An article, such as a news article or piece of investigative report. Newspapers and magazines have articles of many different types and this is intended to cover them all.',
				'slug'        => 'article',
			),

			array(
				'label'       => 'WebSite',
				'description' => 'A WebSite is a set of related web pages and other items typically served from a single web domain and accessible via URLs.',
				'slug'        => 'web-site',
			),

			array(
				'label'       => 'NewsArticle',
				'description' => 'A NewsArticle is an article whose content reports news, or provides background context and supporting materials for understanding the news.',
				'slug'        => 'news-article',
			),

			array(
				'label'       => 'AboutPage',
				'description' => 'An About page.',
				'slug'        => 'about-page',
			),

			array(
				'label'       => 'ContactPage',
				'description' => 'A Contact Page.',
				'slug'        => 'contact-page',
			),

		);
	}

	public static function get_professional_entity_types() {
		return array(

			array(
				'label'       => 'FAQPage',
				'description' => 'A FAQPage is a WebPage presenting one or more "Frequently asked questions".',
				'slug'        => 'faq-page',
			),
			array(
				'label'       => 'LocalBusiness',
				'description' => 'A particular physical business or branch of an organization. Examples of LocalBusiness include a restaurant, a particular branch of a restaurant chain, a branch of a bank, a medical practice, a club, a bowling alley, etc.',
				'slug'        => 'local-business',

			),
			array(
				'label'       => 'Recipe',
				'description' => 'A recipe',
				'slug'        => 'recipe',
			),
			array(
				'label'       => 'PodcastEpisode',
				'description' => 'A single episode of a podcast series.',
				'slug'        => 'podcast-episode',
			),
			array(
				'label'       => 'Course',
				'description' => 'A description of an educational course which may be offered as distinct instances at which take place at different times or take place at different locations, or be offered through different media or modes of study.',
				'slug'        => 'course',
			),
			array(
				'label'       => 'Event',
				'description' => 'An event happening at a certain time and location, such as a concert, lecture, or festival.',
				'slug'        => 'event',
			),
			array(
				'label'       => 'Review',
				'description' => 'A review of an item - for example, of a restaurant, movie, or store.',
				'slug'        => 'review',
			),

		);
	}

	private static function get_entity_types_by_feature_flag( $package_type ) {

		switch ( $package_type ) {
			case self::STARTER_PLAN:
				return self::get_starter_entity_types();
			case self::BUSINESS_PLAN:
			case self::PROFESSIONAL_PLAN:
				// We return same entity types for professional and business plans.
				// Business plan should have sync schema ui feature enabled, to sync all the entity types.
				return array_merge(
					self::get_starter_entity_types(),
					self::get_professional_entity_types()
				);
			default:
				return array();

		}

	}

}
