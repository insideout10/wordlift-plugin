<?php
/**
 * Installs: Install 3.20.0.
 *
 * @since 3.20.0
 */

/**
 * Define the {@link Wordlift_Install_3_20_0} class.
 *
 * @since 3.20.0
 */
class Wordlift_Install_3_20_0 extends Wordlift_Install {

	/**
	 * {@inheritdoc}
	 */
	protected static $version = '3.20.0';

	public function install() {

		$this->add_names_to_existing_terms();
		$this->rename_localbusiness_to_local_business();

	}

	private function add_names_to_existing_terms() {

		$this->log->debug( 'Adding names and URIs to existing terms...' );

		$schema_names = array(
			'article'       => 'Article',
			'thing'         => 'Thing',
			'creative-work' => 'CreativeWork',
			'event'         => 'Event',
			'organization'  => 'Organization',
			'person'        => 'Person',
			'place'         => 'Place',
			'localbusiness' => 'LocalBusiness',
			'recipe'        => 'Recipe',
			'web-page'      => 'WebPage',
			'offer'         => 'Offer',
		);

		foreach ( $schema_names as $slug => $name ) {
			$term = get_term_by( 'slug', $slug, Wordlift_Entity_Type_Taxonomy_Service::TAXONOMY_NAME );

			if ( false === $term ) {
				$this->log->warn( "Term `$slug` not found." );

				continue;
			}

			// $this->log->debug( "Adding $name and http://schema.org/$name as name and URI of term `$slug` ({$term->term_id})." );

			// We don't use the references to the Wordlift_Schemaorg_Class_Service because it might not be
			// loaded if the `WL_ALL_ENTITY_TYPES` constant isn't set.
			update_term_meta( $term->term_id, '_wl_name', $name );
			update_term_meta( $term->term_id, '_wl_uri', "http://schema.org/$name" );

			// $this->log->debug( 'name :: ' . var_export( $result_1, true ) . ' URI :: ' . var_export( $result_2, true ) );

		}

	}

	private function rename_localbusiness_to_local_business() {

		$term = get_term_by( 'slug', 'localbusiness', Wordlift_Entity_Type_Taxonomy_Service::TAXONOMY_NAME );

		if ( false === $term ) {
			return;
		}

		wp_update_term( $term->term_id, Wordlift_Entity_Type_Taxonomy_Service::TAXONOMY_NAME, array( 'slug' => 'local-business' ) );

	}

}
