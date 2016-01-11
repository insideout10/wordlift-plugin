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
	 * Calculate total number of published posts
	 * @uses https://codex.wordpress.org/it:Riferimento_funzioni/wp_count_posts
	 * @since 3.4.0
	 *
	 * @return int Total number of published posts.
	 */
	public function count_posts( ) {
		
		return wp_count_posts()->publish;		
	}

	/**
	 * Calculate total number of annotated published posts
	 * @since 3.4.0
	 *
	 * @return int Total number of annotated published posts.
	 */
	public function count_annotated_posts( ) {
		
		// Prepare interaction with db
    	global $wpdb;
    	// Retrieve Wordlift relation instances table name
    	$table_name = wl_core_get_relation_instances_table_name();
    	// Calculate sql statement
		$sql_statement = <<<EOF
    		SELECT COUNT(p.*) FROM $wpdb->posts as p JOIN $table_name as r ON p.id = r.subject_id AND p.post_type = 'posts' AND p.post_status = 'publish';
EOF;
		// Perform the query
		return $wpdb->get_var( $sql_statement ); 
		
	}

	/**
	 * Calculate total number of published entities
	 * @uses https://codex.wordpress.org/it:Riferimento_funzioni/wp_count_posts
	 * @since 3.4.0
	 *
	 * @return int Total number of posts.
	 */
	public function count_entities( ) {
		
		return wp_count_posts( Wordlift_Entity_Service::TYPE_NAME )->publish;		
	}

}
