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
class Wordlift_Install_3_10_0 implements Wordlift_Install {

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
	 * @var \Wordlift_Install_3_10_0 $instance A {@link Wordlift_Install_3_10_0} instance.
	 */
	private static $instance;

	/**
	 * Wordlift_Install_3_10_0 constructor.
	 *
	 * @since 3.18.0
	 */
	public function __construct() {
		$this->log = Wordlift_Log_Service::get_logger( 'Wordlift_Install_3_10_0' );

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
		return '3.10.0';
	}

	/**
	 * @inheritdoc
	 */
	public function install() {
		$this->log->trace( 'Installing version 3.10.0...' );

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

		$this->log->debug( 'Version 3.10.0 installed.' );
	}

}
