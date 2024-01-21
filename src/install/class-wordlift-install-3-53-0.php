<?php

/**
 * Add a new term to the Entity Type taxonomy
 *
 * @since 3.53.0
 */

class Wordlift_Install_3_53_0 extends Wordlift_Install {

	/**
	 * @inheritdoc
	 */
	protected static $version = '3.53.0';

	/**
	 * The OnlineBusiness Entity Type term we would like to add.
	 *
	 * @var array $term The entity type term.
	 *
	 * @since 3.53.0
	 */
	private static $term = array(
		'slug'        => 'online-business',
		'label'       => 'OnlineBusiness',
		// @@todo update description
		'description' => 'An online business.',
	);

	/**
	 * @inheritdoc
	 */
	public function install() {
		$this->log->debug( 'Updating Entity Type terms...' );

		// Get the slug of the term we want to add.
		$term = self::$term;
		$slug = $term['slug'];

		// Check whether the term exists and create it if not.
		$term_id = $this->get_term_or_create_if_not_exists( $slug );

		// Bail if the term doesn't exist or wasn't created.
		if ( empty( $term_id ) ) {
			$this->log->debug( 'Entity Type terms update failed.' );
			return;
		}

		wp_update_term(
			$term_id,
			Wordlift_Entity_Type_Taxonomy_Service::TAXONOMY_NAME,
			array(
				'name'        => $term['label'],
				'slug'        => $slug,
				'description' => $term['description'],
				// We give to WP taxonomy just one parent.
				'parent'      => 0,
			)
		);

		$this->log->debug( 'Entity Type terms updated.' );
	}

	/**
	 * Checks whether the term exists and create it if it doesn't.
	 *
	 * @param string $slug Term slug.
	 *
	 * @return mixed Term id if the term exists or if it's created. False on failure
	 *
	 * @since 3.53.0
	 */
	private function get_term_or_create_if_not_exists( $slug ) {
		// Create the term if it does not exist, then get its ID.
		$term_id = term_exists( $slug, Wordlift_Entity_Type_Taxonomy_Service::TAXONOMY_NAME );

		if ( empty( $term_id ) ) {
			// The term doesn't exist, so create it.
			$maybe_term = wp_insert_term( $slug, Wordlift_Entity_Type_Taxonomy_Service::TAXONOMY_NAME );
		} else {
			// Get the term.
			$maybe_term = get_term( $term_id['term_id'], Wordlift_Entity_Type_Taxonomy_Service::TAXONOMY_NAME, ARRAY_A );
		}

		// Check for errors.
		if ( is_wp_error( $maybe_term ) ) {
			$this->log->info( 'wl_install_entity_type_data [ ' . $maybe_term->get_error_message() . ' ]' );

			return false;
		}

		// Finally return the term id.
		return $maybe_term['term_id'];
	}
}
