<?php
/**
 * Services: Batch Analysis Service.
 *
 * The Batch Analysis service allows to queue analysis operations given a list
 * of URLs.
 *
 * The Batch Analysis service should also allow to queue all the posts/pages
 * that do not contain any annotation (example annotation:
 * <span id="urn:enhancement-{uuid}"
 *       class="textannotation disambiguated wl-{class} [wl-[no-]link]"
 *       itemid="{itemid-url}">{label}</span>
 *
 * We must identify the batch analysis status according to 3 stages:
 *  1. BATCH_ANALYSIS_SUBMIT, i.e. a post/page has been submitted for batch
 *      analysis.
 *  2. BATCH_ANALYSIS_REQUEST, i.e. a post/page batch analysis has been
 *      requested to the remote service.
 *  3. BATCH_ANALYSIS_SUCCESS / BATCH_ANALYSIS_ERROR: the outcome of the batch
 *      analysis.
 *
 * For each state we record the date time, this is especially useful since the
 * remote service doesn't provide a state management, therefore we need to
 * define a timeout on the client side.
 *
 * Upon reception of the results we need to check whether there are some
 * potential warning due to interpolation issues, i.e.
 *
 *  `\w<span id="urn:enhancement-` or `\s</span>` or `</span>\w`
 *
 * and in such a case, set a warning BATCH_ANALYSIS_WARNING in order to provide
 * a list of posts/pages that need manual review and allow the editor to clear
 * the warning flag.
 *
 * All the time-consuming operations must be executed asynchronously.
 *
 * Since setting the post meta for a large number of posts may be time consuming
 * in PHP, we can use prepared queries.
 *
 * @since      3.14.0
 * @package    Wordlift
 * @subpackage Wordlift/includes
 */

/**
 * Define the {@link Wordlift_Batch_Analysis_Service} class.
 *
 * @since      3.14.0
 * @package    Wordlift
 * @subpackage Wordlift/includes
 */
class Wordlift_Batch_Analysis_Service {

	/**
	 * The list of states for the Batch Analysis:
	 *  - STATE_META_KEY: the batch analysis state meta key,
	 *  - STATE_SUBMIT: a post/page has been submitted for analysis,
	 *  - STATE_REQUEST: the plugin requested an analysis for the submitted
	 *      post/page,
	 *  - STATE_SUCCESS: the analysis has completed successfully,
	 *  - STATE_ERROR: the analysis returned an error.
	 *
	 * @since 3.14.2
	 */
	const STATE_META_KEY = '_wl_batch_analysis_state';
	const STATE_SUBMIT = 0;
	const STATE_REQUEST = 1;
	// ### COMPLETE states.
	const STATE_SUCCESS = 2;
	const STATE_ERROR = 2;

	/**
	 * The submit timestamp meta key. A post may have more than one timestamp.
	 *
	 * @since 3.14.2
	 */
	const SUBMIT_TIMESTAMP_META_KEY = '_wl_batch_analysis_submit_timestamp';

	/**
	 * The request timestamp meta key. A post may have more than one timestamp.
	 *
	 * @since 3.14.2
	 */
	const REQUEST_TIMESTAMP_META_KEY = '_wl_batch_analysis_request_timestamp';

	/**
	 * The complete (success or error) timestamp meta key. A post may have more
	 * than one timestamp.
	 *
	 * @since 3.14.2
	 */
	const COMPLETE_TIMESTAMP_META_KEY = '_wl_batch_analysis_complete_timestamp';

	/**
	 * The options setting meta key. A post may have more than one setting.
	 *
	 * @since 3.14.2
	 */
	const BATCH_ANALYSIS_OPTIONS_META_KEY = '_wl_batch_analysis_options';

	/**
	 * The warning timestamp meta key. A post has only zero/one value.
	 *
	 * @since 3.14.2
	 */
	const WARNING_META_KEY = '_wl_batch_analysis_warning';

	/**
	 * Option name.
	 *
	 * @since  3.14.0
	 */
	const OPTION_NAME = 'wl_analyze_batch';

	/**
	 * Name of waiting to be processed queue array inside the option.
	 *
	 * @since  3.14.0
	 */
	const ANALYZE_QUEUE = 'queue';

	/**
	 * Name of waiting for response queue array inside the option.
	 *
	 * @since  3.14.0
	 */
	const RESPONSE_QUEUE = 'processing';

	/**
	 * Regular expressions that match interpolation errors.
	 *
	 * @since  3.17.0
	 */
	private static $interpolation_patterns = array(
		// Matches word before the annotation.
		'~(\w)<[a-z]+ id="urn:[^"]+" class="[^"]+" itemid="[^"]+">(.*?)<\/[a-z]+>~',
		// Matches word after the annotation.
		'~<[a-z]+ id="urn:[^"]+" class="[^"]+" itemid="[^"]+">(.*?)<\/[a-z]+>(\w)~',
		// Matches space in the beginning of annotation name.
		'~<[a-z]+ id="urn:[^"]+" class="[^"]+" itemid="[^"]+">(\s)(.*?)<\/[a-z]+>~',
	);

	/**
	 * The {@link Wordlift} plugin instance.
	 *
	 * @since  3.14.0
	 * @access private
	 * @var \Wordlift $plugin The {@link Wordlift} plugin instance.
	 */
	private $plugin;

	/**
	 * The {@link Wordlift_Configuration_Service} instance.
	 *
	 * @since  3.14.0
	 * @access private
	 * @var \Wordlift_Configuration_Service $configuration_service The {@link Wordlift_Configuration_Service} instance.
	 */
	private $configuration_service;

	/**
	 * The {@link Wordlift_Cache_Service} instance.
	 *
	 * @since  3.17.0
	 * @access protected
	 * @var \Wordlift_Cache_Service $cache_service The {@link Wordlift_Cache_Service} instance.
	 */
	private $cache_service;

	/**
	 * A {@link Wordlift_Log_Service} instance.
	 *
	 * @since  3.14.2
	 * @access private
	 * @var \Wordlift_Log_Service $log A {@link Wordlift_Log_Service} instance.
	 */
	private $log;

	/**
	 * The {@link Class_Wordlift_Batch_Analys_Service} instance.
	 *
	 * @since 3.14.0
	 *
	 * @param \Wordlift                       $plugin                The {@link Wordlift} plugin instance.
	 * @param \Wordlift_Configuration_Service $configuration_service The {@link Wordlift_Configuration_Service} instance.
	 * @param \Wordlift_Cache_Service         $cache_service         The {@link Wordlift_Cache_Service} instance.
	 */
	public function __construct( $plugin, $configuration_service, $cache_service ) {

		$this->log = Wordlift_Log_Service::get_logger( 'Wordlift_Batch_Analysis_Service' );

		$this->plugin                = $plugin;
		$this->configuration_service = $configuration_service;
		$this->cache_service         = $cache_service;

		add_action( 'wl_async_wl_batch_analysis_request', array(
			$this,
			'request',
		) );
		add_action( 'wl_async_wl_batch_analysis_complete', array(
			$this,
			'complete',
		) );

	}

	/**
	 * Submit posts for Batch Analysis.
	 *
	 * @since 3.14.2
	 *
	 * @param array       $args               {
	 *                                        A list of options for the Batch Analysis.
	 *
	 * @type string       $link               Either `default`, `no` or `yes` (`default` is used if not specified):
	 *                                        * `default` doesn't set the link option - entities
	 *                                           will be linked if configured so in WordLift settings.
	 *                                        * `yes` links the entities.
	 *                                        * `no` doesn't link the entities.
	 *                                        This value is forwarded to WLS' Batch Analysis end-point.
	 * @type int          $min_occurrences    The minimum number of occurrences to select
	 *                                        an entity. Default `1`.
	 * @type bool         $include_annotated  Whether to include annotated posts in selection.
	 *                                        Default `false`.
	 * @type array|int    $include            Explicitly include the specified {@link WP_Post}s.
	 * @type array|int    $exclude            Explicitly exclude the specified {@link WP_Post}s.
	 * @type string|null  $from               An optional date from filter (used in `post_date_gmt`).
	 * @type string|null  $to                 An optional date from filter (used in `post_date_gmt`).
	 * @type array|string $post_type          Specify the post type(s), by default only `post`.
	 *                      }
	 *
	 * @return int The number of submitted {@link WP_Post}s or false on error.
	 */
	public function submit( $args ) {
		// Parse the parameters.
		$params = wp_parse_args( $args, array(
			'links'             => 'default',
			'min_occurrences'   => 1,
			'include_annotated' => false,
			'exclude'           => array(),
			'from'              => null,
			'to'                => null,
			'post_type'         => 'post',
		) );

		// Validation.
		if ( ! in_array( $params['links'], array( 'default', 'yes', 'no' ) ) ) {
			wp_die( '`link` must be one of the following: `default`, `yes` or `no`.' );
		}

		if ( ! is_numeric( $params['min_occurrences'] ) || 1 > $params['min_occurrences'] ) {
			wp_die( '`min_occurrences` must greater or equal 1.' );
		}

		// Get the sql query.
		$query = Wordlift_Batch_Analysis_Sql_Helper::get_sql( $params );

		// Set the post metas and get the value of the posts.
		$submitted_posts = $this->update_posts_meta( $params, $query );

		// Request Batch Analysis (the operation is handled asynchronously).
		do_action( 'wl_batch_analysis_request' );

		// Return the count of the posts.
		return $submitted_posts;
	}

	/**
	 * Submit one or more {@link WP_Posts} for Batch Analysis.
	 *
	 * @param array    $args            {
	 *                                  An array of arguments.
	 *
	 * @type string    $link            The link option: `default`, `yes` or
	 *                                  `no`. If not set `default`.
	 * @type int       $min_occurrences The minimum number of occurrences. If
	 *                                  not set `1`.
	 * @type array|int $ids             An array of {@link WP_Post}s' ids or one
	 *                                  single numeric {@link WP_Post} id.
	 *                    }
	 *
	 * @return float|int
	 */
	public function submit_posts( $args ) {
		// Parse the parameters.
		$params = wp_parse_args( $args, array(
			'links'           => 'default',
			'min_occurrences' => 1,
			'ids'             => array(),
		) );

		// Validation.
		if ( empty( $params['ids'] ) ) {
			wp_die( '`ids` cannot be empty.' );
		}

		// Get the query,
		$query = Wordlift_Batch_Analysis_Sql_Helper::get_sql_for_ids( $params );

		// Set the post metas and get the value of the posts.
		$submitted_posts = $this->update_posts_meta( $params, $query );

		// Request Batch Analysis (the operation is handled asynchronously).
		do_action( 'wl_batch_analysis_request' );

		// Return the count of the posts.
		return $submitted_posts;
	}

	/**
	 * Add metas to the posts that should be analysed.
	 *
	 * @param array  $params The request params.
	 * @param string $query  The mysql query.
	 *
	 * @since 3.18.0
	 *
	 * @return int The number of posts found/submitted.
	 */
	public function update_posts_meta( $params, $query ) {
		global $wpdb;

		// Get the link options.
		$link_options = array(
			'links'           => $params['links'],
			'min_occurrences' => $params['min_occurrences'],
		);

		// Get the posts that should be submitted for analysis.
		$posts = $wpdb->get_results( $query ); // WPCS: cache ok, db call ok.

		// Bail if there are no posts found.
		if ( empty( $posts ) ) {
			return 0;
		}

		// Add the post metas.
		foreach ( $posts as $p ) {
			add_post_meta( $p->ID, self::STATE_META_KEY, 0 );
			add_post_meta( $p->ID, self::SUBMIT_TIMESTAMP_META_KEY, gmdate( 'Y-m-d H:i:s' ) );
			add_post_meta( $p->ID, self::BATCH_ANALYSIS_OPTIONS_META_KEY, $link_options );
		}

		// Finally return the posts count.
		return count( $posts );
	}

	/**
	 * Cancel the Batch Analysis request for the specified {@link WP_Post}s.
	 *
	 * @since 3.14.2
	 *
	 * @param int|array $post_ids A single {@link WP_Post}'s id or an array of
	 *                            {@link WP_Post}s' ids.
	 *
	 * @return false|int The number of cancelled {@link WP_Post}s or false on
	 *                   error.
	 */
	public function cancel( $post_ids ) {
		global $wpdb;

		return $wpdb->query( $wpdb->prepare(
			"
			DELETE FROM $wpdb->postmeta
			WHERE meta_key = %s
				AND meta_value IN ( %d, %d )
				AND post_id IN( " . implode( ',', wp_parse_id_list( $post_ids ) ) . " )
			",
			self::STATE_META_KEY,
			self::STATE_SUBMIT,
			self::STATE_REQUEST
		) ); // WPCS: cache ok, db call ok.

	}

	/**
	 * Request the batch analysis for submitted posts.
	 *
	 * @since 3.14.2
	 */
	public function request() {

		$this->log->debug( 'Requesting analysis...' );

		// By default 5 posts of any post type are returned.
		$posts = get_posts( array(
			'fields'     => 'ids',
			'meta_key'   => self::STATE_META_KEY,
			'meta_value' => self::STATE_SUBMIT,
			'orderby'    => 'ID',
			'post_type'  => 'any',
		) );

		// Bail out if there are no submitted posts.
		if ( empty( $posts ) ) {
			$this->log->debug( 'No posts to submit found, checking for completed requests...' );

			do_action( 'wl_batch_analysis_complete' );

			return;
		}

		// Send a request for each post.
		foreach ( $posts as $id ) {

			$this->log->debug( "Requesting analysis for post $id..." );

			// Send the actual request to the remote service.
			$result = $this->do_request( $id );

			// Set an error if we received an error.
			if ( is_wp_error( $result ) ) {
				$this->log->error( "An error occurred while requesting a batch analysis for post $id: " . $result->get_error_message() );

				$this->set_state( $id, self::STATE_ERROR );
			}

		}

		// Call the `wl_batch_analysis_request` action again. This is going
		// to be handled by the async task.
		do_action( 'wl_batch_analysis_request' );

	}

	/**
	 * Get the results for the Batch Analysis.
	 *
	 * @since 3.14.2
	 */
	public function complete() {

		$this->log->debug( 'Requesting results...' );

		// By default 5 posts of any post type are returned.
		$posts = get_posts( array(
			'fields'     => 'ids',
			'meta_key'   => self::STATE_META_KEY,
			'meta_value' => self::STATE_REQUEST,
			'orderby'    => 'ID',
			'post_type'  => 'any',
		) );

		// Bail out if there are no submitted posts.
		if ( empty( $posts ) ) {
			$this->log->debug( 'No posts in request state found.' );

			return;
		}

		// Send a request for each post.
		foreach ( $posts as $id ) {
			$this->log->debug( "Requesting results for post $id..." );

			// Send the actual request to the remote service.
			$response = $this->do_complete( $id );

			// Move to the next item if we don't have a reply for this one.
			if ( is_wp_error( $response ) || 200 !== (int) $response['response']['code'] || ! isset( $response['body'] ) ) {
				continue;
			}

			$this->log->debug( "Results received for post $id." );

			// Save the returned content as new revision.
			$json = json_decode( $response['body'] );

			// Continue if the content isn't set.
			if ( empty( $json->content ) ) {
				// The post content is empty, so is should be marked as completed.
				$this->log->error( "An error occurred while decoding the batch analysis response for post $id: {$response['body']}" );

				$this->set_state( $id, self::STATE_ERROR );
				continue;
			}

			// Set the warning flag if needed.
			$this->set_warning_based_on_content( $json->content, $id );

			// Get the content, cleaned up if there are interpolation errors.
			$pre_content = $this->fix_interpolation_errors( $json->content, $id );

			/**
			 * Filter: 'wl_batch_analysis_update_post_content' - Allow third
			 * parties to perform additional actions when the post content is
			 * updated.
			 *
			 * @since  3.17.0
			 * @api    string $data The {@link WP_Post}'s content.
			 * @api    int    $id   The {@link WP_Post}'s id.
			 */
			$content = apply_filters( 'wl_batch_analysis_update_post_content', $pre_content, $id );

			// Update the post content.
			wp_update_post( array(
				'ID'           => $id,
				'post_content' => wp_slash( $content ),
			) );

			// Update the status.
			$this->set_state( $id, self::STATE_SUCCESS );

			// Invalidating the cache for the current post.
			$this->cache_service->delete_cache( $id );

			$this->log->debug( "Post $id updated with batch analysis results." );

			// Set default entity type term for posts that didn't have any.
			$this->maybe_set_default_term( $id );

			// @todo: implement a kind of timeout that sets an error if the
			// results haven't been received after a long time.
		}

		// Call the `wl_batch_analysis_request` action again. This is going
		// to be handled by the async task.
		do_action( 'wl_batch_analysis_complete' );

	}

	/**
	 * Set a warning flag on the {@link WP_Post} if its content has suspicious
	 * interpolations.
	 *
	 * @since 3.14.2
	 *
	 * @param string $content The {@link WP_Post}'s content.
	 * @param int    $post_id The {@link WP_Post}'s id.
	 */
	protected function set_warning_based_on_content( $content, $post_id ) {

		// Check for suspicious interpolations.
		$is_warning = $this->has_interpolation_errors( $content );

		// Set the warning flag accordingly.
		$this->set_warning( $post_id, $is_warning );

	}

	private function has_interpolation_errors( $content ) {
		$matches = array();

		// eg:
		// r-pro<span id="urn:local-text-annotation-oxbgy6139gnjgk1n0oxnq9zg62py29pf" class="textannotation disambiguated wl-thing" itemid="http://data.wordlift.it/be2/entity/developing_country">ne region, has shoul</span>dere

		return 0 < preg_match_all( '/\w<[a-z]+ id="urn:[^"]+" class="[^"]+" itemid="[^"]+">/', $content, $matches )
			   || 0 < preg_match_all( ' /<[a-z]+ id="urn:[^"]+ " class="[^"]+" itemid="[^"]+">\s/', $content, $matches );
	}

	/**
	 * Fix interpolation errors raised by Batch Analysis
	 *
	 * @param string $content The {@link WP_Post}'s content.
	 * @param int    $id      The {@link WP_Post}'s id.
	 *
	 * @since 3.17.0
	 *
	 * @return string Post content without interpolations.
	 */
	private function fix_interpolation_errors( $content, $id ) {

		// Bail out if there are no interpolation errors.
		if ( ! $this->has_interpolation_errors( $content ) ) {
			$this->log->trace( "No interpolation errors found for post $id." );

			return $content;
		}

		$this->log->debug( "Fixing post $id interpolations..." );

		// Remove all interpolations from the content.
		return preg_replace( self::$interpolation_patterns, '$1$2', $content );
	}

	/**
	 * Clear the warning flag for the specified {@link WP_Post}s.
	 *
	 * @since 3.14.2
	 *
	 * @param int|array $post_ids A single {@link WP_Post}'s id or an array of
	 *                            {@link WP_Post}s' ids.
	 */
	public function clear_warning( $post_ids ) {

		foreach ( (array) $post_ids as $post_id ) {
			delete_post_meta( $post_id, self::WARNING_META_KEY );
		}

	}

	/**
	 * Set the warning flag for the specified {@link WP_Post}.
	 *
	 * @since 3.14.2
	 *
	 * @param int  $post_id The {@link WP_Post}'s id.
	 * @param bool $value   The flag's value.
	 *
	 * @return int|bool Meta ID if the key didn't exist, true on successful update,
	 *                  false on failure.
	 */
	private function set_warning( $post_id, $value ) {

		return update_post_meta( $post_id, self::WARNING_META_KEY, ( true === $value ? 'yes' : 'no' ) );
	}

//	/**
//	 * Get the post/page batch analysis state.
//	 *
//	 * @since 3.14.2
//	 *
//	 * @param int $post_id The {@link WP_Post}'s id.
//	 *
//	 * @return int|string The post state or an empty string if not set.
//	 */
//	public function get_state( $post_id ) {
//
//		return get_post_meta( $post_id, self::STATE_META_KEY, true );
//	}

	/**
	 * Set the post/page batch analysis state.
	 *
	 * @since 3.14.2
	 *
	 * @param int $post_id The {@link WP_Post}'s id.
	 * @param int $value   The new state.
	 *
	 * @return int|bool Meta ID if the key didn't exist, true on successful update,
	 *                  false on failure.
	 */
	private function set_state( $post_id, $value ) {

		// Update the state.
		$result = update_post_meta( $post_id, self::STATE_META_KEY, $value );

		// Update timestamps as required.
		switch ( $value ) {

			// ### REQUEST state.
			case self::STATE_REQUEST:
				add_post_meta( $post_id, self::REQUEST_TIMESTAMP_META_KEY, current_time( 'mysql', true ) );
				break;

			// ### SUCCESS/ERROR state.
			case self::STATE_SUCCESS:
			case self::STATE_ERROR:
				add_post_meta( $post_id, self::COMPLETE_TIMESTAMP_META_KEY, current_time( 'mysql', true ) );
				break;
		}

		// Finally return the result.
		return $result;
	}

	/**
	 * Get the options setting for a {@link WP_Post}.
	 *
	 * If there are multiple link settings, only the last one is returned.
	 *
	 * @since 3.14.2
	 *
	 * @param int $post_id The {@link WP_Post}'s id.
	 *
	 * @return array The link settings.
	 */
	private function get_options( $post_id ) {

		$values = get_post_meta( $post_id, self::BATCH_ANALYSIS_OPTIONS_META_KEY );

		return end( $values ) ?: array(
			'links'           => 'default',
			'min_occurrences' => 1,
		);
	}

	/**
	 * Get the array of post IDS waiting in the queue to start processing.
	 *
	 * @since 3.14.0
	 *
	 * @return array The waiting to be processed post ids queue.
	 */
	public function waiting_for_analysis() {

		return get_posts( array(
			'posts_per_page' => - 1,
			'fields'         => 'ids',
			'post_status'    => 'any',
			'meta_key'       => self::STATE_META_KEY,
			'meta_value'     => self::STATE_SUBMIT,
			'orderby'        => 'ID',
			'post_type'      => 'any',
			// Add any because posts from multiple posts types may be waiting.
		) );
	}

	/**
	 * Get the array of post IDS waiting for response.
	 *
	 * @deprecated
	 * @since 3.14.0
	 *
	 * @return array The waiting for response post ids queue.
	 */
	public function waiting_for_response() {

		return get_posts( array(
			'posts_per_page' => - 1,
			'fields'         => 'ids',
			'post_status'    => 'any',
			'meta_key'       => self::STATE_META_KEY,
			'meta_value'     => self::STATE_REQUEST,
			'orderby'        => 'ID',
			'post_type'      => 'any',
			// Add any because posts from multiple posts types may be waiting.
		) );
	}

	/**
	 * Request the analysis for the specified {@link WP_Post}.
	 *
	 * @since 3.14.2
	 *
	 * @param int $post_id The {@link WP_Post}'s id.
	 *
	 * @return WP_Error|array The response or WP_Error on failure.
	 */
	private function do_request( $post_id ) {

		// Change the state to `REQUEST`.
		$this->set_state( $post_id, self::STATE_REQUEST );

		// Get the post.
		$post = get_post( $post_id );

		// Bail out if the post isn't found.
		if ( null === $post ) {
			$this->log->warn( "Post $post_id not found." );

			return new WP_Error( 0, "Cannot find post $post_id." );
		}

		// Get the link setting.
		$options = $this->get_options( $post_id );

		$this->log->debug( 'Sending analysis request for post $post_id [ links :: ' . $options['links'] . ', min_occurrences :: ' . $options['min_occurrences'] . ' ] ...' );

		// Get the batch analysis URL.
		$url = $this->configuration_service->get_batch_analysis_url();

		// Prepare the POST parameters.
		$params = array(
			'id'              => $post->ID,
			'key'             => $this->configuration_service->get_key(),
			'content'         => $post->post_content,
			'contentLanguage' => $this->configuration_service->get_language_code(),
			'version'         => $this->plugin->get_version(),
			'scope'           => 'local',
			'links'           => $options['links'],
			'minOccurrences'  => $options['min_occurrences'],
		);

		// Get the HTTP options.
		$args = array_merge_recursive( unserialize( WL_REDLINK_API_HTTP_OPTIONS ), array(
			'method'      => 'POST',
			'headers'     => array(
				'Accept'       => 'application/json',
				'Content-type' => 'application/json; charset=UTF-8',
			),
			// we need to downgrade the HTTP version in this case since chunked encoding is dumping numbers in the response.
			'httpversion' => '1.0',
			'body'        => wp_json_encode( $params ),
		) );

		$this->log->debug( "Posting analysis request for post $post_id to $url..." );

		// Post the parameter.
		return wp_remote_post( $url, $args );
	}

	/**
	 * Get the Batch Analysis results for the specified {@link WP_Post}.
	 *
	 * @since 3.14.2
	 *
	 * @param int $post_id The {@link WP_Post}'s id.
	 *
	 * @return WP_Error|array The response or WP_Error on failure.
	 */
	private function do_complete( $post_id ) {

		$post = get_post( $post_id );

		if ( null === $post ) {
			// Post was possibly deleted, just bailout.
			return new WP_Error( 0, "Post $post_id not found . " );
		}

		$url = $this->configuration_service->get_batch_analysis_url();
		$key = $this->configuration_service->get_key();
		$url = $url . '/' . $post->ID . '?key=' . $key;

		return wp_remote_get( $url, unserialize( WL_REDLINK_API_HTTP_OPTIONS ) );
	}

	/**
	 * Get the {@link WP_Post}s' ids flagged with warnings.
	 *
	 * @since 3.14.2
	 *
	 * @return array An array of {@link WP_Post}s' ids.
	 */
	public function get_warnings() {

		return get_posts( array(
			'fields'      => 'ids',
			'numberposts' => - 1,
			'post_status' => 'any',
			'meta_key'    => self::WARNING_META_KEY,
			'meta_value'  => 'yes',
			'post_type'   => 'any',
			// Add any because posts from multiple posts types may be waiting.
		) );
	}

	/**
	 * Check whether the term has entity type associated and set default term if it hasn't.
	 *
	 * @since 3.17.0
	 *
	 * @param int $id The {@link WP_Post}'s id.
	 */
	private function maybe_set_default_term( $id ) {
		// Check whether the post has any of the WordLift entity types.
		$has_term = has_term( '', Wordlift_Entity_Type_Taxonomy_Service::TAXONOMY_NAME, $id );

		// Bail if the term is associated with entity types already.
		if ( ! empty( $has_term ) ) {
			return;
		}

		// Set the default `article` term.
		wp_set_object_terms( $id, 'article', Wordlift_Entity_Type_Taxonomy_Service::TAXONOMY_NAME );

	}

}
