<?php
/**
 * Installs: Install Version 3.14.0.
 *
 * @since      3.18.0
 * @package    Wordlift
 * @subpackage Wordlift/install
 */

/**
 * Define the {@link Wordlift_Install_3_14_0} interface.
 *
 * @since      3.18.0
 * @package    Wordlift
 * @subpackage Wordlift/install
 */
class Wordlift_Install_3_14_0 extends Wordlift_Install {
	/**
	 * @inheritdoc
	 */
	protected static $version = '3.14.0';

	/**
	 * @inheritdoc
	 */
	public function install() {
		// Check whether the `recipe` term exists.
		$recipe = get_term_by( 'slug', 'article', Wordlift_Entity_Type_Taxonomy_Service::TAXONOMY_NAME );

		// The recipe term doesn't exists, so create it.
		if ( empty( $recipe ) ) {
			wp_insert_term(
				'Recipe',
				Wordlift_Entity_Type_Taxonomy_Service::TAXONOMY_NAME,
				array(
					'slug'        => 'recipe',
					'description' => 'A Recipe.',
				)
			);
		}

		// Assign capabilities to manipulate entities to admins.
		$admins = get_role( 'administrator' );

		$admins->add_cap( 'edit_wordlift_entity' );
		$admins->add_cap( 'edit_wordlift_entities' );
		$admins->add_cap( 'edit_others_wordlift_entities' );
		$admins->add_cap( 'publish_wordlift_entities' );
		$admins->add_cap( 'read_private_wordlift_entities' );
		$admins->add_cap( 'delete_wordlift_entity' );
		$admins->add_cap( 'delete_wordlift_entities' );
		$admins->add_cap( 'delete_others_wordlift_entities' );
		$admins->add_cap( 'delete_published_wordlift_entities' );
		$admins->add_cap( 'delete_private_wordlift_entities' );

		/*
		 * Check that the `editor` role exists before using it.
		 *
		 * @since 3.19.6
		 *
		 * @see https://github.com/insideout10/wordlift-plugin/issues/886
		 */
		$editors = get_role( 'editor' );

		if ( isset( $editors ) ) {
			$editors->add_cap( 'edit_wordlift_entity' );
			$editors->add_cap( 'edit_wordlift_entities' );
			$editors->add_cap( 'edit_others_wordlift_entities' );
			$editors->add_cap( 'publish_wordlift_entities' );
			$editors->add_cap( 'read_private_wordlift_entities' );
			$editors->add_cap( 'delete_wordlift_entity' );
			$editors->add_cap( 'delete_wordlift_entities' );
			$editors->add_cap( 'delete_others_wordlift_entities' );
			$editors->add_cap( 'delete_published_wordlift_entities' );
			$editors->add_cap( 'delete_private_wordlift_entities' );
		}

	}

}
