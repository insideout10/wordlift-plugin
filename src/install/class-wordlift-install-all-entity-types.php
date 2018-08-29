<?php
/**
 * Installs: All Entity Types.
 *
 * Installs the full schema.org taxonomy into wl_entity_types. The installer only runs if `WL_ALL_ENTITY_TYPES` is
 * enabled.
 *
 * @see https://github.com/insideout10/wordlift-plugin/issues/835
 *
 * @since 3.20.0
 */

/**
 * Define the {@link Wordlift_Install_All_Entity_Types} class.
 *
 * @since 3.20.0
 */
class Wordlift_Install_All_Entity_Types extends Wordlift_Install {

	const OPTION_NAME = 'wl_db_schemaorg_version';

	/**
	 * @inheritdoc
	 */
	// @@todo: increase the version number to match WordLift's version.
	protected static $version = '3.20.0';

	public function install() {

		// Check that the schema isn't installed yet.
		if ( ! WL_ALL_ENTITY_TYPES || false !== get_option( self::OPTION_NAME ) ) {
			$this->log->info( 'Skipping `All Entity Types` configuration.' );

			return;
		}

		$this->log->info( 'Installing `All Entity Types` configuration...' );

		// Get the Schema.org sync service instance.
		$schema_sync_service = Wordlift_Schemaorg_Sync_Service::get_instance();

		// Try to load the Schema.org taxonomy and, if successful, update the local Schema.org version.
		if ( $schema_sync_service->load_from_file() ) {
			update_option( self::OPTION_NAME, '1.0.0' );
		}

	}

}
