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
	 * Transient Name
	 * 
	 * @since  3.4.0
	 */
	const TRANSIENT_NAME = 'wl_dashboard_stats';

	/**
	 * Transient Expiration (in seconds)
	 * 
	 * @since  3.4.0
	 */
	const TRANSIENT_EXPIRATION = 86400;

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
	 * Return stats
	 *
	 * @since 3.4.0
	 *
	 * @return string markup
	 */
	public function dashboard_widget_callback( $post ) {

		echo <<<EOF
	<div id="wl_dashboard_widget_inner_wrapper">
		<div class="wl-stat-card">
			<div class="wl-stat-graph-wrapper">
			<h4>enriched posts</h4>
			<svg id="wl-posts-pie-chart" viewBox="0 0 32 32">
  				<circle r="16" cx="16" cy="16" />
			</svg>
			</div>
			<p>
			<span id="wl-dashboard-widget-annotated_posts_percentage" class="wl-stat-value"></span>% of your <a href="">posts</a> are annotated. This means <span id="wl-dashboard-widget-annotated_posts" class="wl-stat-value"></span> 
			on a total number of <span id="wl-dashboard-widget-posts" class="wl-stat-value"></span>%.
			</p>
		</div>
		<div class="wl-stat-card">
			<div class="wl-stat-graph-wrapper">
			<h4>avarage entity rating</h4>
			<svg id="wl-entities-gauge-chart" viewBox="0 0 32 32">
  				<circle r="16" cx="16" cy="16" class="baseline" />
				<circle r="16" cx="16" cy="16" class="stat" />
			</svg>
			</div>
			<p>
			You have <span id="wl-dashboard-widget-entities" class="wl-stat-value"></span> 
			entities in the <a href="">vocabulary</a> with an avarage rating of <span id="wl-dashboard-widget-rating" class="wl-stat-value"></span>%.
			</p>
		</div>
		<div class="wl-stat-card">
			<div class="wl-stat-graph-wrapper">
			
			<h4>facts in your graph</h4>
			<div class="wl-triples">
				<span id="wl-dashboard-widget-triples"></span>
			</div>

			</div>
			<p>
			Your graphs size corresponds to <span id="wl-dashboard-widget-wikidata" class="wl-stat-value"></span>% of Wikidata.
			</p>

		</div>
	</div>
EOF;

	}

	/**
	 * Return stats
	 *
	 * @since 3.4.0
	 *
	 * @return string markup
	 */
	public function add_dashboard_widgets() {
		wp_add_dashboard_widget('wordlift_dashboard_widget', 'Wordlift Dashboard', array( $this, 'dashboard_widget_callback' ) );
	}

	/**
	 * Return stats
	 * @uses https://codex.wordpress.org/Function_Reference/set_transient
	 *
	 * @since 3.4.0
	 *
	 * @return string JSON obj with all available stats.
	 */
	public function ajax_get_stats() {
		
		// Try to retrieve the transient
		$stats = get_transient( self::TRANSIENT_NAME );

		if ( !$stats ) {
			// Calculate stats
			$stats = array(
				'entities'	=>	$this->count_entities(),
				'posts'	=>	$this->count_posts(),
				'annotated_posts'	=>	$this->count_annotated_posts(),
				'triples'	=> $this->count_triples(),
				'rating' => $this->avarage_entities_rating(),
			);	
			// Cache stats results trough transient
			set_transient( self::TRANSIENT_NAME, $stats, self::TRANSIENT_EXPIRATION );
		}
		// Return stats as json object
		wl_core_send_json( $stats );
	}

	/**
	 * Calculate total number of published posts
	 * @uses https://codex.wordpress.org/it:Riferimento_funzioni/wp_count_posts
	 * @since 3.4.0
	 *
	 * @return int Total number of published posts.
	 */
	public function count_posts() {
		
		return (int) wp_count_posts()->publish;		
	}

	/**
	 * Calculate total number of annotated published posts
	 * @since 3.4.0
	 *
	 * @return int Total number of annotated published posts.
	 */
	public function count_annotated_posts() {
		
		// Prepare interaction with db
    	global $wpdb;
    	// Retrieve Wordlift relation instances table name
    	$table_name = wl_core_get_relation_instances_table_name();
    	// Calculate sql statement
		$sql_statement = <<<EOF
    		SELECT COUNT(*) FROM $wpdb->posts as p JOIN $table_name as r ON p.id = r.subject_id AND p.post_type = 'post' AND p.post_status = 'publish';
EOF;
		// Perform the query
		return (int) $wpdb->get_var( $sql_statement ); 
		
	}

	/**
	 * Calculate the avarage entities rating
	 * @since 3.4.0
	 *
	 * @return int Avarage entities rating.
	 */
	public function avarage_entities_rating() {
		
		// Prepare interaction with db
    	global $wpdb;
		$query = $wpdb->prepare( 
			"SELECT AVG(meta_value) FROM $wpdb->postmeta where meta_key = %s",
   			Wordlift_Entity_Service::RATING_RAW_SCORE_META_KEY
			);
		// Perform the query
		return $this->entity_service->convert_raw_score_to_percentage( $wpdb->get_var( $query ) ); 	
	}

	/**
	 * Calculate total number of published entities
	 * @uses https://codex.wordpress.org/it:Riferimento_funzioni/wp_count_posts
	 * @since 3.4.0
	 *
	 * @return int Total number of posts.
	 */
	public function count_entities() {
		
		return (int) wp_count_posts( Wordlift_Entity_Service::TYPE_NAME )->publish;		
	}

	/**
	 * Calculate total number of published rdf triples
	 * @since 3.4.0
	 *
	 * @return int Total number of triples.
	 */
	public function count_triples() {
		
		// Set the SPARQL query.
		$sparql = 'SELECT (COUNT(*) AS ?no) { ?s ?p ?o  }';
		// Send the request.
		$response = $this->rl_sparql_select( $sparql );

		// Return the error in case of failure.
		if ( is_wp_error( $response ) || 200 !== (int) $response['response']['code'] ) {
			return (int) FALSE;
		}

		// Get the body.
		$body = $response['body'];
		// Get the values.
		$matches = array();
		if ( 1 === preg_match( '/(\d+)/im', $body, $matches ) && 2 === count( $matches ) ) {
			// Return the counts.
			return (int) $matches[1];
		}
		
		return (int) FALSE;
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
