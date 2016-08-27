<?php

/**
 *
 */
class Wordlift_Page_Service {

	/**
	 * @var Wordlift_Log_Service
	 */
	private $log;

	public function __construct() {

		$this->log = Wordlift_Log_Service::get_logger( 'Wordlift_Page_Service' );

	}

	public function wp_head() {

		ob_start( array( $this, 'handler' ) );

	}

	public function wp_footer() {

		ob_end_flush();

	}

	public function handler( $buffer ) {

		$regexs = array(
			'/<article ([^>]*)class="([^"]*)type-post([^"]*)"([^>]*)/i',
			'/<h1 ([^>]*)class="([^"]*)entry-title([^"]*)"([^>]*)/i',
		);

		$replacements = array(
			'<article itemscope itemtype="http://schema.org/Article" ${1}class="${2}type-post${3}"${4}',
			'<h1 itemprop="name" ${1}class="${2}entry-title${3}"${4}',
		);

		return preg_replace( $regexs, $replacements, $buffer );
	}

}