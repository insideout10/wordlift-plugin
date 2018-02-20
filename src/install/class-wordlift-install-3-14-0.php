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
class Wordlift_Install_3_14_0 implements Wordlift_Install {

	/**
	 * A {@link Wordlift_Log_Service} instance.
	 *
	 * @since  3.18.0
	 * @access private
	 * @var \Wordlift_Log_Service $log A {@link Wordlift_Log_Service} instance.
	 */
	private $log;

	/**
	 * The singleton instance.
	 *
	 * @since  3.18.0
	 * @access private
	 * @var \Wordlift_Install_3_14_0 $instance A {@link Wordlift_Install_3_14_0} instance.
	 */
	private static $instance;

	/**
	 * Wordlift_Install_3_14_0 constructor.
	 *
	 * @since 3.18.0
	 */
	public function __construct() {
		$this->log = Wordlift_Log_Service::get_logger( 'Wordlift_Install_3_14_0' );

		self::$instance = $this;
	}

	/**
	 * Get the singleton instance.
	 *
	 * @since 3.18.0
	 */
	public static function get_instance() {

		return self::$instance;
	}

	/**
	 * @inheritdoc
	 */
	public function get_version() {
		return '3.14.0';
	}

	/**
	 * @inheritdoc
	 */
	public function install() {
		$this->log->trace( 'Installing version 3.14.0...' );

		// Check whether the `recipe` term exists.
		$recipe = get_term_by( 'slug', 'article', Wordlift_Entity_Type_Taxonomy_Service::TAXONOMY_NAME );

		// The recipe term doesn't exists, so create it.
		if ( empty( $recipe ) ) {
			$result = wp_insert_term(
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

		// Assign capabilities to manipulate entities to editors.
		$editors = get_role( 'editor' );

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

		$this->log->debug( 'Version 3.14.0 installed.' );
	}

}
