<?php
/**
 * Installs: Install Version 3.12.0.
 *
 * @since      3.18.0
 * @package    Wordlift
 * @subpackage Wordlift/install
 */

/**
 * Define the {@link Wordlift_Install_3_12_0} interface.
 *
 * @since      3.18.0
 * @package    Wordlift
 * @subpackage Wordlift/install
 */
class Wordlift_Install_3_12_0 implements Wordlift_Install {

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
	 * @var \Wordlift_Install_3_12_0 $instance A {@link Wordlift_Install_3_12_0} instance.
	 */
	private static $instance;

	/**
	 * Wordlift_Install_3_12_0 constructor.
	 *
	 * @since 3.18.0
	 */
	public function __construct() {
		$this->log = Wordlift_Log_Service::get_logger( 'Wordlift_Install_3_12_0' );

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
		return '3.12.0';
	}

	/**
	 * @inheritdoc
	 */
	public function install() {
		$this->log->trace( 'Installing version 3.12.0...' );

		/*
		 * As this upgrade functionality runs on the init hook, and the AMP plugin
		 * initialization does the same, avoid possible race conditions by
		 * deferring the actual flush to a later hook.
		 */
		add_action( 'wp_loaded', function () {
			flush_rewrite_rules();
		} );

		$this->log->debug( 'Version 3.12.0 installed.' );
	}

}
