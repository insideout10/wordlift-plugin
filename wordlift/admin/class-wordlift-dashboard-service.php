<?php
/**
 * Wordlift Dashboard Widget
 *
 * @since      3.4.0
 * @package    Wordlift
 * @subpackage Wordlift/admin
 */

/**
 * Wordlift_Dashboard_Service Class
 *
 * Handles the dashboard widget.
 *
 * @since      3.4.0
 * @package    Wordlift
 * @subpackage Wordlift/admin
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
	 * The {@link Wordlift_Entity_Service} instance.
	 *
	 * @since  3.15.0
	 * @access private
	 * @var \Wordlift_Entity_Service $entity_service The {@link Wordlift_Entity_Service} instance.
	 */
	private $entity_service;

	/**
	 * Create a Wordlift_Entity_List_Service.
	 *
	 * @since 3.4.0
	 */
	protected function __construct() {

		$this->rating_service = Wordlift_Rating_Service::get_instance();
		$this->entity_service = Wordlift_Entity_Service::get_instance();

	}

	private static $instance;

	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Return stats layout
	 *
	 * @since 3.4.0
	 */
    // phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
	public function dashboard_widget_callback( $post ) {
		$caption_kses           = array( 'a' => array( 'href' => array() ) );
		$enriched_posts_title   = __( 'enriched posts', 'wordlift' );
		$enriched_posts_caption = sprintf(
			wp_kses(
				/* translators: 1: Percentage of annotated posts, 2: Link to the edit screen, 3: Number of annotated posts, 4: Total posts. */
				__( '%1$s, of your <a href="%2$s">posts</a> are annotated. This means %3$s annotated posts on %4$s.', 'wordlift' ),
				array( 'a' => array( 'href' => array() ) )
			),
			$this->render_stat_param( 'annotatedPostsPercentage' ),
			esc_url( admin_url( 'edit.php' ) ),
			$this->render_stat_param( 'annotated_posts' ),
			$this->render_stat_param( 'posts' )
		);

		$rating_title   = __( 'average entity rating', 'wordlift' );
		$rating_caption = sprintf(
			wp_kses(
					/* translators: 1: The entities count, 2: The link to the vocabulary, 3: The average rating. */
				__( 'You have %1$s entities in your <a href="%2$s">vocabulary</a> with an average rating of %3$s.', 'wordlift' ),
				array( 'a' => array( 'href' => array() ) )
			),
			$this->render_stat_param( 'entities' ),
			esc_url( admin_url( 'edit.php?post_type=entity' ) ),
			$this->render_stat_param( 'rating' )
		);

		$graph_title   = __( 'triples in your graph', 'wordlift' );
		$graph_caption = sprintf(
			wp_kses(
					/* translators: 1: The percentage of the graph size compared to Wikidata, 2: The link to Wikidata. */
				__( 'Your graphs size corresponds to %1$s of <a href="%2$s">Wikidata</a>.', 'wordlift' ),
				array( 'a' => array( 'href' => array() ) )
			),
			$this->render_stat_param( 'wikidata' ),
			esc_url( 'https://www.wikidata.org/' )
		);

		$triples_label = __( 'triples', 'wordlift' );

		?>
		<div id="wl-dashboard-widget-inner-wrapper">
			<div class="wl-stat-card">
				<div class="wl-stat-graph-wrapper">
					<h4><?php echo esc_html( $enriched_posts_title ); ?> <a
								href="http://docs.wordlift.it/en/latest/faq.html#what-is-content-enrichment"
								target="_blank"><i class="wl-info"></i></a></h4>
					<svg id="wl-posts-pie-chart" viewBox="0 0 32 32">
						<circle r="16" cx="16" cy="16"/>
					</svg>
				</div>
				<p><?php echo wp_kses( $enriched_posts_caption, $caption_kses ); ?> </p>
			</div>
			<div class="wl-stat-card">
				<div class="wl-stat-graph-wrapper">
					<h4><?php echo esc_html( $rating_title ); ?> <a
								href="http://docs.wordlift.it/en/latest/faq.html#what-factors-determine-the-rating-of-an-entity"
								target="_blank"><i class="wl-info"></i></a></h4>
					<svg id="wl-entities-gauge-chart" viewBox="0 0 32 32">
						<circle r="16" cx="16" cy="16" class="baseline"/>
						<circle r="16" cx="16" cy="16" class="stat"/>
					</svg>
				</div>
				<p><?php echo wp_kses( $rating_caption, $caption_kses ); ?></p>
			</div>
			<div class="wl-stat-card">
				<div class="wl-stat-graph-wrapper">
					<h4><?php echo esc_html( $graph_title ); ?><a href="http://docs.wordlift.it/en/latest/faq.html#what-is-a-triple"
										target="_blank"><i class="wl-info"></i></a></h4>
					<div class="wl-triples">
						<span id="wl-dashboard-widget-triples"></span>
						<span class="wl-triples-label"><?php echo esc_html( $triples_label ); ?></span>
					</div>
				</div>
				<p><?php echo wp_kses( $graph_caption, $caption_kses ); ?></p>
			</div>
		</div>
		<?php

	}

	/**
	 * Return stats
	 *
	 * @since 3.4.0
	 */
	public function add_dashboard_widgets() {
		wp_add_dashboard_widget(
			'wordlift-dashboard-widget',
			'WordLift Dashboard',
			array(
				$this,
				'dashboard_widget_callback',
			)
		);
	}

	/**
	 * Return stats
	 *
	 * @uses  https://codex.wordpress.org/Function_Reference/set_transient
	 *
	 * @since 3.4.0
	 */
	public function ajax_get_stats() {

		// If needed, the transient is force to reloaded.
		if ( isset( $_GET['force_reload'] ) ) { //phpcs:ignore WordPress.Security.NonceVerification.Recommended
			delete_transient( self::TRANSIENT_NAME );
		}

		// Try to retrieve the transient
		$stats = get_transient( self::TRANSIENT_NAME );

		if ( ! $stats ) {
			// Calculate stats
			$count_triples = $this->count_triples();
			$stats         = array(
				'entities'        => $this->entity_service->count(),
				'posts'           => $this->count_posts(),
				'annotated_posts' => $this->count_annotated_posts(),
				'triples'         => $count_triples ? $count_triples : '-',
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
	 *
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
	 *
	 * @return int Total number of annotated published posts.
	 * @since 3.4.0
	 */
	public function count_annotated_posts() {

		// Prepare interaction with db
		global $wpdb;
		// Retrieve Wordlift relation instances table name
		// $table_name = wl_core_get_relation_instances_table_name();
		// Calculate sql statement

		// Perform the query
		return (int) $wpdb->get_var(
			"SELECT COUNT(distinct(p.id)) FROM $wpdb->posts as p JOIN {$wpdb->prefix}wl_relation_instances as r ON p.id = r.subject_id AND p.post_type = 'post' AND p.post_status = 'publish'"
		);

	}

	/**
	 * Calculate the average entities rating.
	 *
	 * @return int Average entities rating.
	 * @since 3.4.0
	 *
	 * @since 3.20.0 this method is public.
	 */
	public function average_entities_rating() {

		// Prepare interaction with db
		global $wpdb;

		// Perform the query.
		return $this->rating_service->convert_raw_score_to_percentage(
			$wpdb->get_var(
				$wpdb->prepare(
					"SELECT AVG(meta_value) FROM $wpdb->postmeta where meta_key = %s",
					Wordlift_Rating_Service::RATING_RAW_SCORE_META_KEY
				)
			)
		);
	}

}
