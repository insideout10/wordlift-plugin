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
	 * A {@link Wordlift_Rating_Service} instance.
	 *
	 * @since  3.10.0
	 * @access private
	 * @var \Wordlift_Rating_Service $rating_service A {@link Wordlift_Rating_Service} instance.
	 */
	private $rating_service;

	/**
	 * Create a Wordlift_Entity_List_Service.
	 *
	 * @since 3.4.0
	 *
	 * @param \Wordlift_Rating_Service $rating_service A {@link Wordlift_Rating_Service} instance.
	 */
	public function __construct( $rating_service ) {

		$this->rating_service = $rating_service;

	}

	/**
	 * Return stats layout
	 *
	 * @since 3.4.0
	 *
	 * @return string Dashboard widget html markup
	 */
	public function dashboard_widget_callback( $post ) {

		$enriched_posts_title   = __( 'enriched posts', 'wordlift' );
		$enriched_posts_caption = sprintf( wp_kses(
			__( '%1$s, of your <a href="%2$s">posts</a> are annotated. This means %3$s annotated posts on %4$s.', 'wordlift' ),
			array( 'a' => array( 'href' => array() ) ) ),
			$this->render_stat_param( 'annotatedPostsPercentage' ),
			esc_url( admin_url( 'edit.php' ) ),
			$this->render_stat_param( 'annotated_posts' ),
			$this->render_stat_param( 'posts' )
		);

		$rating_title   = __( 'average entity rating', 'wordlift' );
		$rating_caption = sprintf( wp_kses(
			__( 'You have %1$s entities in your <a href="%2$s">vocabulary</a> with an average rating of %3$s.', 'wordlift' ),
			array( 'a' => array( 'href' => array() ) ) ),
			$this->render_stat_param( 'entities' ),
			esc_url( admin_url( 'edit.php?post_type=entity' ) ),
			$this->render_stat_param( 'rating' )
		);

		$graph_title   = __( 'triples in your graph', 'wordlift' );
		$graph_caption = sprintf( wp_kses(
			__( 'Your graphs size corresponds to %1$s of <a href="%2$s">Wikidata</a>.', 'wordlift' ),
			array( 'a' => array( 'href' => array() ) ) ),
			$this->render_stat_param( 'wikidata' ),
			esc_url( 'https://www.wikidata.org/' )
		);

		$triples_label = __( 'triples', 'wordlift' );

		echo <<<EOF
	<div id="wl-dashboard-widget-inner-wrapper">
		<div class="wl-stat-card">
			<div class="wl-stat-graph-wrapper">
				<h4>$enriched_posts_title <a href="http://docs.wordlift.it/en/latest/faq.html#what-is-content-enrichment" target="_blank"><i class="wl-info"></i></a></h4>
				<svg id="wl-posts-pie-chart" viewBox="0 0 32 32"><circle r="16" cx="16" cy="16" /></svg>
			</div>
			<p>$enriched_posts_caption</p>
		</div>
		<div class="wl-stat-card">
			<div class="wl-stat-graph-wrapper">
				<h4>$rating_title <a href="http://docs.wordlift.it/en/latest/faq.html#what-factors-determine-the-rating-of-an-entity" target="_blank"><i class="wl-info"></i></a></h4>
				<svg id="wl-entities-gauge-chart" viewBox="0 0 32 32"><circle r="16" cx="16" cy="16" class="baseline" /><circle r="16" cx="16" cy="16" class="stat" /></svg>
			</div>
			<p>$rating_caption</p>
		</div>
		<div class="wl-stat-card">
			<div class="wl-stat-graph-wrapper">
				<h4>$graph_title <a href="http://docs.wordlift.it/en/latest/faq.html#what-is-a-triple" target="_blank"><i class="wl-info"></i></a></h4>
				<div class="wl-triples">
					<span id="wl-dashboard-widget-triples"></span>
					<span class="wl-triples-label">$triples_label</span>
				</div>
			</div>
			<p>$graph_caption</p>
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
		wp_add_dashboard_widget( 'wordlift-dashboard-widget', 'WordLift Dashboard', array(
			$this,
			'dashboard_widget_callback',
		) );
	}

	/**
	 * Return stats
	 * @uses  https://codex.wordpress.org/Function_Reference/set_transient
	 *
	 * @since 3.4.0
	 *
	 * @return string JSON obj with all available stats.
	 */
	public function ajax_get_stats() {

		// If needed, the transient is force to reloaed
		if ( isset( $_GET['force_reload'] ) ) {
			delete_transient( self::TRANSIENT_NAME );
		}

		// Try to retrieve the transient
		$stats = get_transient( self::TRANSIENT_NAME );

		if ( ! $stats ) {
			// Calculate stats
			$stats = array(
				'entities'        => $this->count_entities(),
				'posts'           => $this->count_posts(),
				'annotated_posts' => $this->count_annotated_posts(),
				'triples'         => $this->count_triples() ?: '-',
				'rating'          => $this->average_entities_rating(),
			);
			// Cache stats results trough transient
			set_transient( self::TRANSIENT_NAME, $stats, self::TRANSIENT_EXPIRATION );
		}
		// Return stats as json object
		wl_core_send_json( $stats );
	}

	/**
	 * Calculate total number of published posts
	 * @uses  https://codex.wordpress.org/it:Riferimento_funzioni/wp_count_posts
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
    		SELECT COUNT(distinct(p.id)) FROM $wpdb->posts as p JOIN $table_name as r ON p.id = r.subject_id AND p.post_type = 'post' AND p.post_status = 'publish';
EOF;

		// Perform the query
		return (int) $wpdb->get_var( $sql_statement );

	}

	/**
	 * Calculate the average entities rating.
	 *
	 * @since 3.4.0
	 *
	 * @return int Average entities rating.
	 */
	private function average_entities_rating() {

		// Prepare interaction with db
		global $wpdb;
		$query = $wpdb->prepare(
			"SELECT AVG(meta_value) FROM $wpdb->postmeta where meta_key = %s",
			Wordlift_Rating_Service::RATING_RAW_SCORE_META_KEY
		);

		// Perform the query.
		return $this->rating_service->convert_raw_score_to_percentage( $wpdb->get_var( $query ) );
	}

	/**
	 * Calculate total number of published entities.
	 *
	 * @uses  https://codex.wordpress.org/it:Riferimento_funzioni/wp_count_posts
	 *
	 * @since 3.4.0
	 *
	 * @return int Total number of posts.
	 */
	private function count_entities() {

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
			return (int) false;
		}

		// Get the body.
		$body = $response['body'];
		// Get the values.
		$matches = array();
		if ( 1 === preg_match( '/(\d+)/im', $body, $matches ) && 2 === count( $matches ) ) {
			// Return the counts.
			return (int) $matches[1];
		}

		return (int) false;
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

	private function render_stat_param( $param ) {

		return '<span id="wl-dashboard-widget-' . $param . '" class="wl-stat-value"></span>';
	}

}
