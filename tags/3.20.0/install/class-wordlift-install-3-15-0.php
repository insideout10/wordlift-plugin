<?php
/**
 * Installs: Install Version 3.15.0.
 *
 * @since      3.18.0
 * @package    Wordlift
 * @subpackage Wordlift/install
 */

/**
 * Define the {@link Wordlift_Install_3_15_0} interface.
 *
 * @since      3.18.0
 * @package    Wordlift
 * @subpackage Wordlift/install
 */
class Wordlift_Install_3_15_0 extends Wordlift_Install {

	/**
	 * @inheritdoc
	 */
	protected static $version = '3.15.0';

	/**
	 * @inheritdoc
	 */
	public function install() {

		// Check whether the `article` term exists.
		$article = get_term_by( 'slug', 'article', Wordlift_Entity_Type_Taxonomy_Service::TAXONOMY_NAME );

		// The `article` term doesn't exists, so create it.
		if ( empty( $article ) ) {
			wp_insert_term(
				'Article',
				Wordlift_Entity_Type_Taxonomy_Service::TAXONOMY_NAME,
				array(
					'slug'        => 'article',
					'description' => 'An Article.',
				)
			);
		}
	}

}
