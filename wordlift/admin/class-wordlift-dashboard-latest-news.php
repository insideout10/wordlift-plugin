<?php
/**
 * Services: Wordlift Latest News Dashboard Widget.
 *
 * Displays the WordLift Latest News Dashboard Widget.
 *
 * @since 3.19.0
 * @package Wordlift
 * @subpackage Wordlift/admin
 */

/**
 * Wordlift_Dashboard_Latest_News Class
 *
 * Handles the latest news dashboard widget.
 *
 * @since 3.19.0
 * @package Wordlift
 * @subpackage Wordlift/admin
 */
class Wordlift_Dashboard_Latest_News {

	/**
	 * Add needed hooks for the latest news widget.
	 */
	public function __construct() {
		add_action(
			'wp_ajax_wordlift_get_latest_news',
			array(
				$this,
				'ajax_get_latest_news',
			)
		);
		add_action(
			'wp_dashboard_setup',
			array(
				$this,
				'add_dashboard_latest_news_widget',
			)
		);

	}

	/**
	 * Return latest news html.
	 */
	public function render() {

		wp_enqueue_script( 'wl-admin-dashboard', plugin_dir_url( __DIR__ ) . 'admin/js/wordlift-admin-dashboard.js', array( 'jquery' ), '3.22.0', true );

		include plugin_dir_path( __FILE__ ) . 'partials/wordlift-admin-news-widget.php';
	}

	/**
	 * Returns latest news data filtered by $start_position.
	 *
	 * @param int $start_position (news array key start position).
	 *
	 * @return array Latest posts data.
	 */
	public function get_last_wordlift_articles( $start_position = 0 ) {
		$feed_articles = $this->get_wordlift_articles_data();

		// Filter articles by $start_position
		if ( ! empty( $feed_articles ) ) {
			return array(
				'posts_data'     => array_slice( $feed_articles, $start_position, 3 ),
				'count'          => count( $feed_articles ),
				'start_position' => $start_position,
			);
		}

		return false;
	}

	/**
	 * Returns latest news array data.
	 *
	 * @uses  https://codex.wordpress.org/Function_Reference/fetch_feed
	 * @uses  https://codex.wordpress.org/Function_Reference/get_locale
	 *
	 * @param int $articles_number (articles total number).
	 *
	 * @return array Latest $articles_number feed posts.
	 */
	public function get_wordlift_articles_data( $articles_number = 10 ) {
		// Init cache class
		$cache_sistem_lib = new Wordlift_File_Cache_Service( WL_TEMP_DIR . 'articles/' );
		$locale           = get_locale();
		$cache_id         = 'news_' . gmdate( 'Y_m_d' ) . '_' . $locale;
		$posts_data       = array();

		// Get latest articles from cache
		$feed_articles = $cache_sistem_lib->get_cache( $cache_id );
		if ( false === $feed_articles ) {
			// Check WordPress installation language to define articles rss url
			$feed_uri = ( 'it_IT' === $locale ) ? 'https://wordlift.io/blog/it/feed/' : 'https://wordlift.io/blog/en/feed/';

			// Get rss feed data, the response is cached by default for 12 hours
			$feed = fetch_feed( $feed_uri );
			if ( ! is_wp_error( $feed ) ) {
				// Build an array of requested $articles_number, starting with element 0
				$feed_articles = $feed->get_items( 0, $articles_number );
				// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
				foreach ( $feed_articles as $key => $item ) {
					$data = array(
						// fetch_feed will return the title html decoded.
						'post_title'       => $item->get_title(),
						'post_date'        => $item->get_date( 'j F Y | g:i a' ),
						'post_url'         => self::add_utm_parameter( $item->get_permalink() ),
						// fetch_feed will return the description html (not decoded).
						'post_description' => $item->get_description(),
					);
					array_push( $posts_data, $data );
				}
				// Set articles in cache.
				$cache_sistem_lib->set_cache( $cache_id, $posts_data );
				$feed_articles = $posts_data;
			}
		}

		return $feed_articles;
	}

	/**
	 * Add the `utm` parameter for GA.
	 *
	 * @param string $url The URL.
	 *
	 * @return string The URL with the `utm` parameter prepended by `&` or by `?`.
	 * @since 3.19.0
	 */
	private static function add_utm_parameter( $url ) {

		if ( false === strpos( $url, '?' ) ) {
			return $url . '?utm=wl_dash';
		}

		return $url . '&utm=wl_dash';
	}

	/**
	 * Ajax call for more latest news.
	 *
	 * @uses  https://codex.wordpress.org/Function_Reference/wp_send_json_success
	 */
	public function ajax_get_latest_news() {
		// Get wordlift articles
		$more_posts_link_id = isset( $_POST['more_posts_link_id'] ) ? sanitize_text_field( wp_unslash( (string) $_POST['more_posts_link_id'] ) ) : '';//phpcs:ignore WordPress.Security.NonceVerification.Missing
		$start_position     = explode( '_', $more_posts_link_id );
		$data               = $this->get_last_wordlift_articles( $start_position[ count( $start_position ) - 1 ] );

		// Return response as json object
		wp_send_json_success( $data );
	}

	/**
	 * Add latest news widget to the administration dashboard.
	 */
	public function add_dashboard_latest_news_widget() {

		/**
		 * Filter name: wl_feature__enable__wordlift-news
		 * Feature flag to enable / disable news widget.
		 *
		 * @since 3.30.0
		 */
		// phpcs:ignore WordPress.NamingConventions.ValidHookName.UseUnderscores
		if ( apply_filters( 'wl_feature__enable__wordlift-news', true ) ) {

			wp_add_dashboard_widget(
				'wordlift-dashboard-latest-news-widget',
				'Latest WordLift News',
				array(
					$this,
					'render',
				)
			);
		}
	}

}
