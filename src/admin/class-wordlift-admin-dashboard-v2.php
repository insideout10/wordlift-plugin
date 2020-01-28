<?php
/**
 *
 */

class Wordlift_Admin_Dashboard_V2 {

	const TODAYS_TIP = 'wl_todays_tip_data';
	const AVERAGE_POSITION = 'wl_search_rankings_average_position';

	/**
	 * The {@link Wordlift_Admin_Search_Rankings_Service} instance.
	 *
	 * @since 3.20.0
	 * @access private
	 * @var \Wordlift_Admin_Search_Rankings_Service $search_rankings_service The {@link Wordlift_Admin_Search_Rankings_Service} instance.
	 */
	private $search_rankings_service;

	/**
	 * The {@link Wordlift_Dashboard_Service} instance.
	 *
	 * @var \Wordlift_Dashboard_Service $dashboard_service The {@link Wordlift_Dashboard_Service} instance.
	 * @access private
	 * @since 3.20.0
	 */
	private $dashboard_service;

	/**
	 * @var \Wordlift_Entity_Service $entity_service
	 */
	private $entity_service;

	/**
	 * Wordlift_Admin_Dashboard_V2 constructor.
	 *
	 * @param \Wordlift_Admin_Search_Rankings_Service $search_rankings_service The {@link Wordlift_Admin_Search_Rankings_Service} instance.
	 * @param                                         $dashboard_service
	 *
	 * @since 3.20.0
	 *
	 */
	public function __construct( $search_rankings_service, $dashboard_service, $entity_service ) {

		add_action( 'wp_dashboard_setup', array( $this, 'dashboard_setup' ) );

		// Define where to access the tip.
		defined( 'WL_TODAYS_TIP_JSON_URL' ) || define( 'WL_TODAYS_TIP_JSON_URL', 'https://wordlift.io/blog' );
		defined( 'WL_TODAYS_TIP_JSON_URL_IT' ) || define( 'WL_TODAYS_TIP_JSON_URL_IT', '/it/wp-json/wp/v2/posts?context=embed&per_page=1&categories=27' );
		defined( 'WL_TODAYS_TIP_JSON_URL_EN' ) || define( 'WL_TODAYS_TIP_JSON_URL_EN', '/en/wp-json/wp/v2/posts?context=embed&per_page=1&categories=38' );

		$this->search_rankings_service = $search_rankings_service;
		$this->dashboard_service       = $dashboard_service;
		$this->entity_service          = $entity_service;

	}

	/**
	 * Set up the dashboard metabox.
	 *
	 * @since 3.20.0
	 */
	public function dashboard_setup() {

		wp_add_dashboard_widget(
			'wl-dashboard-v2',
			__( 'WordLift Dashboard', 'wordlift' ),
			array( $this, 'dashboard_setup_callback' )
		);

	}

	/**
	 * The dashboard setup callback.
	 *
	 * @since 3.20.0
	 */
	public function dashboard_setup_callback() {

		// Get the average position.
		$average_position_string = $this->get_average_position();

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/wordlift-admin-dashboard-v2.php';

	}

	/**
	 * Get the keyword average position.
	 *
	 * @return string The formatted average position string (or `n/a` if not available).
	 * @since 3.20.0
	 *
	 */
	private function get_average_position() {

		// Get the cache value.
		$average_position = get_transient( self::AVERAGE_POSITION );

		// If there's no cached value, load it.
		if ( false === $average_position ) {
			// Get the average position from Search Ranking Service.
			$average_position = @$this->search_rankings_service->get_average_position();

			// If there was an error return 'n/a'.
			if ( false === $average_position ) {
				return esc_html( _x( 'n/a', 'Dashboard', 'wordlift' ) );
			}
		}

		// Store the value for one day.
		set_transient( self::AVERAGE_POSITION, $average_position, 86400 ); // One day.

		// Format the average position with one decimal.
		return number_format( $average_position, 1 );
	}

	/**
	 * Get the top entities.
	 *
	 * @return array|object|null An array of top entities.
	 * @since 3.20.0
	 *
	 */
	private function get_top_entities() {

		// Get the cached results.
		$results = get_transient( '_wl_admin_dashboard_v2__top_entities' );
		if ( false !== $results ) {
			return $results;
		}

		global $wpdb;

		$query = <<<EOF
select p.ID
     , p.post_title
     , coalesce(sum(case when obj_t.slug is null then 1 end), 0)     entities
     , coalesce(sum(case when obj_t.slug is not null then 1 end), 0) posts
     , count(entity.subject_id) as                                   total
from {$wpdb->prefix}wl_relation_instances entity
       inner join {$wpdb->prefix}posts p
                  on p.ID = entity.object_id
       inner join {$wpdb->prefix}term_relationships tr
                  on tr.object_id = entity.object_id
       inner join {$wpdb->prefix}term_taxonomy tt
                  on tt.term_id = tr.term_taxonomy_id
                    and tt.taxonomy = 'wl_entity_type'
       inner join {$wpdb->prefix}terms t
                  on t.term_id = tt.term_id
                    and 'article' != t.slug
       inner join {$wpdb->prefix}term_relationships obj_tr
                  on obj_tr.object_id = entity.subject_id
       inner join {$wpdb->prefix}term_taxonomy obj_tt
                  on obj_tt.term_id = obj_tr.term_taxonomy_id
                    and obj_tt.taxonomy = 'wl_entity_type'
       left outer join {$wpdb->prefix}terms obj_t
                       on obj_t.term_id = obj_tt.term_id
                         and 'article' = obj_t.slug
group by p.ID, p.post_title
order by total desc
limit 100;
EOF;

		$results = $wpdb->get_results( $query );
		set_transient( '_wl_admin_dashboard_v2__top_entities', $results, 900 );

		return $results;
	}

	/**
	 * Get the today's tip block.
	 *
	 * @since 3.20.0
	 */
	public static function get_todays_tip_block() {

		$data = @self::get_todays_tip_data();

		// Unable to get data from the local cache, nor from the remote URL.
		if ( false === $data ) {
			return;
		}
		?>

        <div id="wl-todays-tip" class="wl-dashboard__block wl-dashboard__block--todays-tip">
            <header>
                <h3><?php echo __( "Today's Tip", 'wordlift' ); ?></h3>
            </header>
            <article>
                <p><strong><?php echo esc_html( wp_strip_all_tags( $data['title'] ) ); ?></strong>
					<?php echo esc_html( wp_strip_all_tags( $data['excerpt'] ) ); ?>
                    <a target="_blank"
                       href="<?php echo esc_attr( $data['link'] ); ?>"><?php echo esc_html( __( 'Read more', 'wordlift' ) ); ?></a>
                </p>
        </div>
		<?php

	}

	/**
	 * Get the today's tip data.
	 *
	 * @return array|false The today's tip data or false in case of error.
	 * @since 3.20.0
	 *
	 */
	private static function get_todays_tip_data() {

		// Return the transient.
		if ( false !== get_transient( self::TODAYS_TIP ) ) {
			return get_transient( self::TODAYS_TIP );
		}

		// If the transient isn't available, query the remote web site.
		$url = WL_TODAYS_TIP_JSON_URL
		       . ( 'it' === get_bloginfo( 'language' ) ? WL_TODAYS_TIP_JSON_URL_IT : WL_TODAYS_TIP_JSON_URL_EN );

		$response = wp_remote_get( $url );

		if ( is_wp_error( $response )
		     || ! isset( $response['response']['code'] )
		     || 2 !== (int) $response['response']['code'] / 100 ) {
			return false;
		}

		$json = json_decode( $response['body'], true );

		if ( empty( $json )
		     || ! isset( $json[0]['title']['rendered'] )
		     || ! isset( $json[0]['excerpt']['rendered'] )
		     || ! isset( $json[0]['link'] ) ) {
			return false;
		}

		$value = array(
			'title'   => $json[0]['title']['rendered'],
			'excerpt' => '<!-- cached -->' . $json[0]['excerpt']['rendered'],
			'link'    => $json[0]['link'],
		);

		// Store the results for one day.
		set_transient( self::TODAYS_TIP, $value, 86400 );

		return $value;
	}

}
