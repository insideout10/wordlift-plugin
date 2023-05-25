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

	/**
	 * The option name for the version of the Schema.org taxonomy.
	 *
	 * @since 3.20.0
	 */
	const OPTION_NAME = 'wl_schemaorg_version';

	/**
	 * {@inheritdoc}
	 */
	protected static $version = '3.20.0';

	/**
	 * Perform the installation.
	 *
	 * @since 3.20.0
	 */
	public function install() {

		$this->log->info( 'Installing `All Entity Types` configuration...' );

		// Get the Schema.org sync service instance.
		$schema_sync_service = Wordlift_Schemaorg_Sync_Service::get_instance();

		// Try to load the Schema.org taxonomy and, if successful, update the local Schema.org version.
		if ( $schema_sync_service->load_from_file() ) {
			$this->log->debug( 'Updating `All Entity Types` configuration to 1.0.0.' );

			update_option( self::OPTION_NAME, '1.0.0', true );
		}

	}

	/**
	 * Whether the installation procedure must run.
	 *
	 * @since 3.20.0
	 *
	 * @return bool True if the installation procedure must run otherwise false.
	 */
	public function must_install() {

		// phpcs:ignore WordPress.NamingConventions.ValidHookName.UseUnderscores
		return apply_filters( 'wl_feature__enable__all-entity-types', WL_ALL_ENTITY_TYPES ) && version_compare( '1.0.0', get_option( self::OPTION_NAME, '0.0.0' ), '>' );
	}

}
