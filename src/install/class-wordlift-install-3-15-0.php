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
class Wordlift_Install_3_15_0 implements Wordlift_Install {

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
	 * @var \Wordlift_Install_3_15_0 $instance A {@link Wordlift_Install_3_15_0} instance.
	 */
	private static $instance;

	/**
	 * Wordlift_Install_3_15_0 constructor.
	 *
	 * @since 3.18.0
	 */
	public function __construct() {
		$this->log = Wordlift_Log_Service::get_logger( 'Wordlift_Install_3_15_0' );

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
		return '3.15.0';
	}

	/**
	 * @inheritdoc
	 */
	public function install() {
		$this->log->trace( 'Installing version 3.15.0...' );

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

		// The following is disabled because on large installations it may slow the
		// web site.
		// See: https://github.com/insideout10/wordlift-plugin/commit/fa3cfe296c60828b434897f12a01ead021045fca#diff-b6b016ed02839e76bcfe4a5491f3aa2eR280

		$this->log->debug( 'Version 3.15.0 installed.' );
	}

}
