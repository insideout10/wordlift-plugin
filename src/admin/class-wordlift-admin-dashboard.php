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

	/**
	 * Calculate total number of published rdf triples
	 * @since 3.4.0
	 *
	 * @return int Total number of triples.
	 */
	public function count_triples( ) {
		
		// Set the SPARQL query.
		$sparql = 'SELECT (COUNT(*) AS ?no) { ?s ?p ?o  }';
		// Send the request.
		$response = rl_sparql_select( $sparql );

		// Return the error in case of failure.
		if ( is_wp_error( $response ) || 200 !== (int) $response['response']['code'] ) {
			return false;
		}

		// Get the body.
		$body = $response['body'];
		// Get the values.
		$matches = array();
		if ( 1 === preg_match( '/(\d+)/im', $body, $matches ) && 2 === count( $matches ) ) {
			// Return the counts.
			return (int) $matches[1];
		}
		
		return false;
	}

	private function rl_sparql_select( $query ) {

		// Prepare the SPARQL statement by prepending the default namespaces.
		$sparql = rl_sparql_prefixes() . "\n" . $query;
		// Get the SPARQL SELECT URL.
		$url = wl_configuration_get_query_select_url( 'csv' ) . urlencode( $sparql );
		// Prepare the request.
		$args = unserialize( WL_REDLINK_API_HTTP_OPTIONS );
		
		return wp_remote_get( $url, $args );
	}

}
