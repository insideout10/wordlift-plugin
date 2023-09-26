<?php
/**
 * Services: Rating Service.
 *
 * @since   3.10.0
 * @package Wordlift
 */

/**
 * Define the {@link Wordlift_Rating_Service} class.
 *
 * @since   3.10.0
 * @package Wordlift
 */
class Wordlift_Rating_Service {

	/**
	 * Entity rating max.
	 *
	 * @since 3.3.0
	 */
	const RATING_MAX = 7;

	/**
	 * Entity rating score meta key.
	 *
	 * @since 3.3.0
	 */
	const RATING_RAW_SCORE_META_KEY = '_wl_entity_rating_raw_score';

	/**
	 * Entity rating warnings meta key.
	 *
	 * @since 3.3.0
	 */
	const RATING_WARNINGS_META_KEY = '_wl_entity_rating_warnings';

	/**
	 * Entity warning has related post identifier.
	 *
	 * @since 3.3.0
	 */
	const RATING_WARNING_HAS_RELATED_POSTS = 'There are no related posts for the current entity.';

	/**
	 * Entity warning has content post identifier.
	 *
	 * @since 3.3.0
	 */
	const RATING_WARNING_HAS_CONTENT_POST = 'This entity has not description.';

	/**
	 * Entity warning has related entities identifier.
	 *
	 * @since 3.3.0
	 */
	const RATING_WARNING_HAS_RELATED_ENTITIES = 'There are no related entities for the current entity.';

	/**
	 * Entity warning is published identifier.
	 *
	 * @since 3.3.0
	 */
	const RATING_WARNING_IS_PUBLISHED = 'This entity is not published. It will not appear within analysis results.';

	/**
	 * Entity warning has thumbnail identifier.
	 *
	 * @since 3.3.0
	 */
	const RATING_WARNING_HAS_THUMBNAIL = 'This entity has no featured image yet.';

	/**
	 * Entity warning has same as identifier.
	 *
	 * @since 3.3.0
	 */
	const RATING_WARNING_HAS_SAME_AS = 'There are no sameAs configured for this entity.';

	/**
	 * Entity warning has completed metadata identifier.
	 *
	 * @since 3.3.0
	 */
	const RATING_WARNING_HAS_COMPLETED_METADATA = 'Schema.org metadata for this entity are not completed.';

	/**
	 *  A {@link Wordlift_Entity_Type_Service} instance.
	 *
	 * @since  3.10.0
	 * @access private
	 * @var Wordlift_Entity_Type_Service $entity_type_service A {@link Wordlift_Entity_Type_Service} instance.
	 */
	private $entity_type_service;

	/**
	 * The Notice service.
	 *
	 * @since  3.3.0
	 * @access private
	 * @var \Wordlift_Notice_Service $notice_service The Notice service.
	 */
	private $notice_service;

	/**
	 * A {@link Wordlift_Log_Service} instance.
	 *
	 * @since  3.10.0
	 * @access private
	 * @var Wordlift_Log_Service $log A {@link Wordlift_Log_Service} instance.
	 */
	private $log;

	/**
	 * Create a {@link Wordlift_Rating_Service} instance.
	 *
	 * @since 3.10.0
	 */
	protected function __construct() {

		$this->entity_type_service = Wordlift_Entity_Type_Service::get_instance();
		$this->notice_service      = Wordlift_Notice_Service::get_instance();

		$this->log = Wordlift_Log_Service::get_logger( 'Wordlift_Rating_Service' );

	}

	private static $instance;

	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Set rating for a given entity.
	 *
	 * @param int $post_id The entity post id.
	 *
	 * @return array An array representing the rating obj.
	 * @since 3.3.0
	 */
	public function set_rating_for( $post_id ) {

		// phpcs:ignore WordPress.NamingConventions.ValidHookName.UseUnderscores
		if ( ! apply_filters( 'wl_feature__enable__entity-rating', true ) ) {
			return;
		}

		if ( ! Wordlift_Entity_Service::get_instance()->is_entity( $post_id ) ) {
			return;
		}

		// Calculate rating for the given post.
		$rating = $this->calculate_rating_for( $post_id );

		// Store the rating on db as post meta. Please notice that RATING_RAW_SCORE_META_KEY
		// is saved on a different meta to allow score sorting. Both meta are managed as unique.
		//
		// See https://codex.wordpress.org/Function_Reference/update_post_meta
		update_post_meta( $post_id, self::RATING_RAW_SCORE_META_KEY, $rating['raw_score'] );
		update_post_meta( $post_id, self::RATING_WARNINGS_META_KEY, $rating['warnings'] );

		$this->log->trace( sprintf( "Rating set for [ post_id :: $post_id ] [ rating :: %s ]", $rating['raw_score'] ) );

		// Finally returns the rating
		return $rating;
	}

	/**
	 * Get or calculate rating for a given entity
	 *
	 * @param int          $post_id The entity post id.
	 * @param     $force_reload $warnings_needed If true, detailed warnings collection is provided with the rating obj.
	 *
	 * @return array An array representing the rating obj.
	 * @since 3.3.0
	 */
	public function get_rating_for( $post_id, $force_reload = false ) {

		// If forced reload is required or rating is missing.
		if ( $force_reload ) {
			$this->log->trace( "Force rating reload [ post_id :: $post_id ]" );

			return $this->set_rating_for( $post_id );
		}

		$current_raw_score = get_post_meta( $post_id, self::RATING_RAW_SCORE_META_KEY, true );

		if ( ! is_numeric( $current_raw_score ) ) {
			$this->log->trace( "Rating missing for [ post_id :: $post_id ] [ current_raw_score :: $current_raw_score ]" );

			return $this->set_rating_for( $post_id );
		}

		$current_warnings = get_post_meta( $post_id, self::RATING_WARNINGS_META_KEY, true );

		// Finally return score and warnings
		return array(
			'raw_score'           => $current_raw_score,
			'traffic_light_score' => $this->convert_raw_score_to_traffic_light( $current_raw_score ),
			'percentage_score'    => $this->convert_raw_score_to_percentage( $current_raw_score ),
			'warnings'            => $current_warnings,
		);

	}

	/**
	 * Calculate rating for a given entity.
	 *
	 * Rating depends from following criteria:
	 *
	 * 1. Is the current entity related to at least 1 post?
	 * 2. Is the current entity content post not empty?
	 * 3. Is the current entity related to at least 1 entity?
	 * 4. Is the entity published?
	 * 5. There is a a thumbnail associated to the entity?
	 * 6. Has the entity a sameas defined?
	 * 7. Are all schema.org required metadata compiled?
	 *
	 * Each positive check means +1 in terms of rating score.
	 *
	 * @param int $post_id The entity post id.
	 *
	 * @return array An array representing the rating obj.
	 * @since 3.3.0
	 */
	private function calculate_rating_for( $post_id ) {

		// If it's not an entity, return.
		if ( ! Wordlift_Entity_Service::get_instance()->is_entity( $post_id ) ) {
			return array();
		}
		// Retrieve the post object.
		$post = get_post( $post_id );

		// Rating value.
		$score = 0;

		// Store warning messages.
		$warnings = array();

		// Is the current entity related to at least 1 post?
		( 0 < count( wl_core_get_related_post_ids( $post->ID ) ) ) ?
			$score ++ :
			array_push( $warnings, __( 'There are no related posts for the current entity.', 'wordlift' ) );

		// Is the post content not empty?
		( ! empty( $post->post_content ) ) ?
			$score ++ :
			array_push( $warnings, __( 'This entity has not description.', 'wordlift' ) );

		// Is the current entity related to at least 1 entity?
		// Was the current entity already disambiguated?
		( 0 < count( wl_core_get_related_entity_ids( $post->ID ) ) ) ?
			$score ++ :
			array_push( $warnings, __( 'There are no related entities for the current entity.', 'wordlift' ) );

		// Is the entity published?
		( 'publish' === get_post_status( $post->ID ) ) ?
			$score ++ :
			array_push( $warnings, __( 'This entity is not published. It will not appear within analysis results.', 'wordlift' ) );

		// Has a thumbnail?
		( has_post_thumbnail( $post->ID ) ) ?
			$score ++ :
			array_push( $warnings, __( 'This entity has no featured image yet.', 'wordlift' ) );

		// Get all post meta keys for the current post
		global $wpdb;

		// Check intersection between available meta keys and expected ones
		// arrays to detect missing values.
		$available_meta_keys = $wpdb->get_col(
			$wpdb->prepare(
				"SELECT DISTINCT (meta_key) FROM $wpdb->postmeta  WHERE post_id = %d",
				$post->ID
			)
		);

		// If each expected key is contained in available keys array ...
		( in_array( Wordlift_Schema_Service::FIELD_SAME_AS, $available_meta_keys, true ) ) ?
			$score ++ :
			array_push( $warnings, __( 'There are no sameAs configured for this entity.', 'wordlift' ) );

		$schema = $this->entity_type_service->get( $post_id );

		$expected_meta_keys = ( null === $schema['custom_fields'] ) ?
			array() :
			array_keys( $schema['custom_fields'] );

		$intersection = array_intersect( $expected_meta_keys, $available_meta_keys );
		// If each expected key is contained in available keys array ...
		( count( $intersection ) === count( $expected_meta_keys ) ) ?
			$score ++ :
			array_push( $warnings, __( 'Schema.org metadata for this entity are not completed.', 'wordlift' ) );

		// Finally return score and warnings
		return array(
			'raw_score'           => $score,
			'traffic_light_score' => $this->convert_raw_score_to_traffic_light( $score ),
			'percentage_score'    => $this->convert_raw_score_to_percentage( $score ),
			'warnings'            => $warnings,
		);

	}

	/**
	 * Get as rating as input and convert in a traffic-light rating
	 *
	 * @param int $score The rating score for a given entity.
	 *
	 * @return string The input HTML code.
	 * @since 3.3.0
	 */
	private function convert_raw_score_to_traffic_light( $score ) {
		// RATING_MAX : $score = 3 : x
		// See http://php.net/manual/en/function.round.php
		$rating = round( ( $score * 3 ) / self::RATING_MAX, 0, PHP_ROUND_HALF_UP );

		// If rating is 0, return 1, otherwise return rating
		return ( 0 === $rating ) ? 1 : $rating;

	}

	/**
	 * Get as rating as input and convert in a traffic-light rating
	 *
	 * @param int $score The rating score for a given entity.
	 *
	 * @return string The input HTML code.
	 * @since 3.3.0
	 */
	public function convert_raw_score_to_percentage( $score ) {

		// RATING_MAX : $score = 100 : x
		return round( ( $score * 100 ) / self::RATING_MAX, 0, PHP_ROUND_HALF_UP );
	}

	/**
	 * Add admin notices for the current entity depending on the current rating.
	 *
	 * @since 3.3.0
	 */
	public function in_admin_header() {

		// Return safely if get_current_screen() is not defined (yet)
		if ( false === function_exists( 'get_current_screen' ) ) {
			return;
		}

		$screen = get_current_screen();
		// If there is any valid screen nothing to do
		if ( null === $screen ) {
			return;
		}

		// If you're not in the entity post edit page, return.
		if ( Wordlift_Entity_Service::TYPE_NAME !== $screen->id ) {
			return;
		}
		// Retrieve the current global post
		global $post;
		// If it's not an entity, return.
		if ( ! Wordlift_Entity_Service::get_instance()->is_entity( $post->ID ) ) {
			return;
		}
		// Retrieve an updated rating for the current entity
		$rating = $this->get_rating_for( $post->ID, true );

		// If there is at least 1 warning
		if ( isset( $rating['warnings'] ) && 0 < count( $rating['warnings'] ) ) {
			// TODO - Pass Wordlift_Notice_Service trough the service constructor
			$this->notice_service->add_suggestion( $rating['warnings'] );
		}

	}

}
