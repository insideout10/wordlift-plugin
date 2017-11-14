<?php
/**
 * Created by PhpStorm.
 * User: david
 * Date: 14.11.17
 * Time: 11:07
 */

class Wordlift_Cached_Post_Converter implements Wordlift_Post_Converter {

	/**
	 * A {@link Wordlift_Post_Converter} instance.
	 *
	 * @since 3.16.0
	 *
	 * @var \Wordlift_Post_Converter $converter A {@link Wordlift_Post_Converter} instance.
	 */
	private $converter;

	/**
	 * A {@link Wordlift_Log_Service} instance.
	 *
	 * @since 3.16.0
	 *
	 * @var Wordlift_Log_Service \$log A {@link Wordlift_Log_Service} instance.
	 */
	private $log;

	/**
	 * Wordlift_Cached_Post_Converter constructor.
	 *
	 * @param \Wordlift_Post_Converter $converter
	 */
	public function __construct( $converter ) {

		$this->log = Wordlift_Log_Service::get_logger( get_class() );

		$this->converter = $converter;

	}

	/**
	 * @inheritdoc
	 */
	public function convert( $post_id, &$references = array(), &$cache = false ) {

		$this->log->trace( "Converting post $post_id..." );

		// Try to get a cached result.
		$cached = $this->get_cache( $post_id, $references );

		if ( null !== $cached ) {
			$this->log->debug( "Found cached result for post $post_id." );

			// Inform the caller that this is cached result.
			$cache = true;

			return $cached;
		}

		$result = $this->converter->convert( $post_id, $references );

		$this->log->debug( "Post $post_id converted." );

		return $result;
	}

	private function get_cache( $post_id, &$references = array() ) {

		return null;
	}

	private function set_cache( $post_id, $result, $references ) {

	}

}
