<?php

/**
 * Wordlift Dashboard Widget 
 * 
 * @since 3.4.0
 */

/**
 * Wordlift_Dashboard_Service Class
 *
 * Handles the dashboard widget.
 */
class Wordlift_Dashboard_Service {
	
	
	/**
	 * The Entity service.
	 *
	 * @since 3.4.0
	 * @access private
	 * @var \Wordlift_Entity_Service $entity_service The Entity service.
	 */
	private $entity_service;

	/**
	 * Create a Wordlift_Entity_List_Service.
	 *
	 * @since 3.4.0
	 *
	 * @param \Wordlift_Entity_Service $entity_service The Entity service.
	 */
	public function __construct( $entity_service ) {

		$this->log_service = Wordlift_Log_Service::get_logger( 'Wordlift_Dashboard_Service' );

		$this->entity_service = $entity_service;

	}

	/**
	 * Calculate total number of posts
	 * @uses https://codex.wordpress.org/it:Riferimento_funzioni/wp_count_posts
	 * @since 3.4.0
	 *
	 * @return int Total number of posts.
	 */
	public function count_posts( ) {
		
		return wp_count_posts()->publish;		
	}

}
