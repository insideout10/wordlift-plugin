<?php
/**
 * Installs: Install Version 1.0.0.
 *
 * @since      3.18.0
 * @package    Wordlift
 * @subpackage Wordlift/install
 */

/**
 * Define the {@link Wordlift_Install_1_0_0} interface.
 *
 * @since      3.18.0
 * @package    Wordlift
 * @subpackage Wordlift/install
 */
class Wordlift_Install_1_0_0 extends Wordlift_Install {
	/**
	 * @inheritdoc
	 */
	protected static $version = '1.0.0';

	/**
	 * The WordLift entity type terms.
	 *
	 * @since  3.18.0
	 * @access private
	 * @var array $terms The entity type terms.
	 */
	private static $terms = array(
		'thing'         => array(
			'label'       => 'Thing',
			'description' => 'A generic thing (something that doesn\'t fit in the previous definitions.',
		),
		'creative-work' => array(
			'label'       => 'CreativeWork',
			'description' => 'A creative work (or a Music Album).',
		),
		'event'         => array(
			'label'       => 'Event',
			'description' => 'An event.',
		),
		'organization'  => array(
			'label'       => 'Organization',
			'description' => 'An organization, including a government or a newspaper.',
		),
		'person'        => array(
			'label'       => 'Person',
			'description' => 'A person (or a music artist).',
		),
		'place'         => array(
			'label'       => 'Place',
			'description' => 'A place.',
		),
		'localbusiness' => array(
			'label'       => 'LocalBusiness',
			'description' => 'A local business.',
		),
	);

	/**
	 * @inheritdoc
	 */
	public function install() {
		// Set the dataset uri.
		$this->set_dataset_uri();

		// Create entity type terms.
		$this->create_entity_type_terms();

		// Create relations table.
		$this->create_relation_instance_table();
	}

	/**
	 * Create required entity type terms
	 *
	 * @since 3.18.0
	 *
	 * @return void
	 */
	private function create_entity_type_terms() {
		$this->log->debug( 'Installing Entity Type data...' );

		// Set the taxonomy data.
		// Note: parent types must be defined before child types.
		foreach ( self::$terms as $slug => $term ) {

			// Check whether the term exists and create it if it doesn't.
			$term_id = $this->get_term_or_create_if_not_exists( $slug );

			// Bail if the term doesn't exists or it's not created.
			if ( empty( $term_id ) ) {
				continue;
			}

			// Update term with description, slug and parent.
			$term = wp_update_term(
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

			$this->log->trace( "Entity Type $slug installed with ID {$term['term_id']}." );

		}

		$this->log->debug( 'Entity Type data installed.' );
	}

	/**
	 * Install custom relations table.
	 *
	 * @since 3.8.0
	 *
	 * @return void
	 */
	private function create_relation_instance_table() {
		global $wpdb;

		$table_name      = $wpdb->prefix . WL_DB_RELATION_INSTANCES_TABLE_NAME;
		$charset_collate = $wpdb->get_charset_collate();

		// Sql statement for the relation instances custom table.
		$sql = "
			CREATE TABLE $table_name (
					id int(11) NOT NULL AUTO_INCREMENT,
					subject_id int(11) NOT NULL,
					predicate char(10) NOT NULL,
					object_id int(11) NOT NULL,
					UNIQUE KEY id (id),
					KEY subject_id_index (subject_id),
					KEY object_id_index (object_id)
			) $charset_collate;
";

		// @see: https://codex.wordpress.org/Creating_Tables_with_Plugins
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( $sql );

	}

	/**
	 * Configure the dataset uri.
	 *
	 * @since 3.18.0
	 *
	 * @return void
	 */
	private function set_dataset_uri() {
		// Get the configuration service and load the key.
		$configuration_service = Wordlift_Configuration_Service::get_instance();
		$key                   = $configuration_service->get_key();

		// If the key is not empty then set the dataset URI while sending
		// the site URL.
		if ( ! empty( $key ) ) {
			$this->log->info( 'Updating the remote dataset URI...' );

			$configuration_service->get_remote_dataset_uri( $key );
		}

	}

	/**
	 * Checks whether the term exists and create it if it doesn't.
	 *
	 * @since 3.18.0
	 *
	 * @param string $slug Term slug.
	 *
	 * @return mixed Term id if the term exists or if it's created. False on failure
	 */
	private function get_term_or_create_if_not_exists( $slug ) {
		// Create the term if it does not exist, then get its ID.
		$term_id = term_exists( $slug, Wordlift_Entity_Type_Taxonomy_Service::TAXONOMY_NAME );

		if ( empty( $term_id ) ) {
			// The term doesn't exists, so create it.
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
