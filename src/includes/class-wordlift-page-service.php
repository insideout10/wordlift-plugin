<?php

/**
 * The Wordlift_Page_Service alters the page output to add schema.org markup in order to provide sharing services
 * such as Google+ the correct page title (otherwise they would read the first entity name).
 *
 * See https://github.com/insideout10/wordlift-plugin/issues/262
 *
 * @since 3.5.3
 */
class Wordlift_Page_Service {

	/**
	 * Hook to wp_head and create a response buffer.
	 *
	 * @since 3.5.3
	 */
	public function wp_head() {

		// When the buffer is flushed, have the handler markup the content.
		ob_start( array( $this, 'handler' ) );

	}

	/**
	 * Hook to wp_footer and flush the response buffer.
	 *
	 * @since 3.5.3
	 */
	public function wp_footer() {

		ob_end_flush();

	}

	/**
	 * Handle the buffer, by inserting schema.org microdata markup with itemscope/itemtype/itemprop.
	 *
	 * @since 3.5.3
	 *
	 * @param string $buffer The output buffer.
	 *
	 * @return string The processed output buffer.
	 */
	public function handler( $buffer ) {

		// Look for the following regexs.
		$regexs = array(
			'/<article ([^>]*)class="([^"]*)type-post([^"]*)"([^>]*)/i',
			'/<h1 ([^>]*)class="([^"]*)entry-title([^"]*)"([^>]*)/i',
		);

		// Replacements.
		$replacements = array(
			'<article itemscope itemtype="http://schema.org/Article" ${1}class="${2}type-post${3}"${4}',
			'<h1 itemprop="name" ${1}class="${2}entry-title${3}"${4}',
		);

		// Perform the replacements in the buffer.
		return preg_replace( $regexs, $replacements, $buffer );
	}

}
