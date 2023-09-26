<?php
/**
 * Installs: Install 3.23.4.
 *
 * Install the WebPage term.
 *
 * @since 3.23.4
 */

/**
 * Define the {@link Wordlift_Install_3_23_4} class.
 *
 * @since 3.23.4
 */
class Wordlift_Install_3_23_4 extends Wordlift_Install {

	/**
	 * {@inheritdoc}
	 */
	protected static $version = '3.23.4';

	public function install() {

		$existing_term = get_term_by( 'slug', 'web-page', Wordlift_Entity_Type_Taxonomy_Service::TAXONOMY_NAME );

		// Bail out if term exists.
		if ( false !== $existing_term ) {
			return;
		}

		$term = wp_insert_term(
			'WebPage',
			Wordlift_Entity_Type_Taxonomy_Service::TAXONOMY_NAME,
			array(
				'slug'        => 'web-page',
				'description' => 'A Web Page.',
			)
		);

		update_term_meta( $term['term_id'], '_wl_name', 'WebPage' );
		update_term_meta( $term['term_id'], '_wl_uri', 'http://schema.org/WebPage' );

	}

}
