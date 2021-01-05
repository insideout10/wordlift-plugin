<?php
/**
 * Installs: Install Version 3.10.0.
 *
 * @since      3.18.0
 * @package    Wordlift
 * @subpackage Wordlift/install
 */

/**
 * Define the {@link Wordlift_Install_3_10_0} interface.
 *
 * @since      3.18.0
 * @package    Wordlift
 * @subpackage Wordlift/install
 */
class Wordlift_Install_3_10_0 extends Wordlift_Install {
	/**
	 * @inheritdoc
	 */
	protected static $version = '3.10.0';

	/**
	 * @inheritdoc
	 */
	public function install() {

		$term_slugs = array(
			'thing',
			'creative-work',
			'event',
			'organization',
			'person',
			'place',
			'localbusiness',
		);

		foreach ( $term_slugs as $slug ) {

			$term = get_term_by( 'slug', $slug, Wordlift_Entity_Type_Taxonomy_Service::TAXONOMY_NAME );

			// Set the term's parent to 0.
			if ( $term ) {
				wp_update_term(
					$term->term_id,
					Wordlift_Entity_Type_Taxonomy_Service::TAXONOMY_NAME,
					array(
						'parent' => 0,
					)
				);
			}
		}
	}

}
