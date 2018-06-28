<?php
/**
 * Define the Wordlift_Jsonld_Service class to support JSON-LD.
 *
 * @since   3.8.0
 * @package Wordlift
 */

/**
 * This class exports an entity using JSON-LD.
 *
 * @since 3.8.0
 */
class Wordlift_Jsonld_Service {

	/**
	 * A {@link Wordlift_Entity_Service} instance.
	 *
	 * @since  3.8.0
	 * @access private
	 * @var Wordlift_Entity_Service $entity_service A {@link Wordlift_Entity_Service} instance.
	 */
	private $entity_service;

	/**
	 * A {@link Wordlift_Post_Converter} instance.
	 *
	 * @since  3.8.0
	 * @access private
	 * @var \Wordlift_Post_Converter A {@link Wordlift_Post_Converter} instance.
	 */
	private $converter;


	/**
	 * A {@link Wordlift_Website_Jsonld_Converter} instance.
	 *
	 * @since  3.14.0
	 * @access private
	 * @var \Wordlift_Website_Jsonld_Converter A {@link Wordlift_Website_Jsonld_Converter} instance.
	 */
	private $website_converter;

	/**
	 * The singleton instance for the JSON-LD service.
	 *
	 * @since 3.15.1
	 *
	 * @var \Wordlift_Jsonld_Service $instance The singleton instance for the JSON-LD service.
	 */
	private static $instance;

	/**
	 * Create a JSON-LD service.
	 *
	 * @since 3.8.0
	 *
	 * @param \Wordlift_Entity_Service $entity_service A {@link Wordlift_Entity_Service} instance.
	 * @param \Wordlift_Post_Converter $converter A {@link Wordlift_Uri_To_Jsonld_Converter} instance.
	 * @param \Wordlift_Website_Jsonld_Converter $website_converter A {@link Wordlift_Website_Jsonld_Converter} instance.
	 */
	public function __construct( $entity_service, $converter, $website_converter ) {

		$this->entity_service    = $entity_service;
		$this->converter         = $converter;
		$this->website_converter = $website_converter;

		self::$instance = $this;

	}

	/**
	 * Get the singleton instance for the JSON-LD service.
	 *
	 * @since 3.15.1
	 *
	 * @return \Wordlift_Jsonld_Service The singleton instance for the JSON-LD service.
	 */
	public static function get_instance() {

		return self::$instance;
	}

	/**
	 * Process calls to the AJAX 'wl_jsonld' endpoint.
	 *
	 * @since 3.8.0
	 */
	public function get() {
		// Clear the buffer to be sure someone doesn't mess with our response.
		//
		// See https://github.com/insideout10/wordlift-plugin/issues/406.
		// See https://codex.wordpress.org/AJAX_in_Plugins.
		@ob_clean();

		// Get the parameter from the request.
		$is_homepage = isset( $_REQUEST['homepage'] );
		$post_id     = isset( $_REQUEST['id'] ) && is_numeric( $_REQUEST['id'] ) ? intval( $_REQUEST['id'] ) : null;

		// Send the generated JSON-LD.
		$this->send_jsonld( $this->get_jsonld( $is_homepage, $post_id ) );

	}

	/**
	 * A close of WP's own `wp_send_json` function which uses `application/ld+json` as content type.
	 *
	 * @since 3.18.5
	 *
	 * @param mixed $response Variable (usually an array or object) to encode as JSON,
	 *                           then print and die.
	 * @param int $status_code The HTTP status code to output.
	 */
	private function send_jsonld( $response, $status_code = null ) {
		@header( 'Content-Type: application/ld+json; charset=' . get_option( 'blog_charset' ) );
		echo wp_json_encode( $response );
		if ( apply_filters( 'wp_doing_ajax', defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
			wp_die();
		} else {
			die;
		}
	}

	/**
	 * Get the JSON-LD.
	 *
	 * @since 3.15.1
	 *
	 * @param bool $is_homepage Whether the JSON-LD for the homepage is being requested.
	 * @param int|null $post_id The JSON-LD for the specified {@link WP_Post} id.
	 *
	 * @return array A JSON-LD structure.
	 */
	public function get_jsonld( $is_homepage = false, $post_id = null ) {

		// Tell NewRelic to ignore us, otherwise NewRelic customers might receive
		// e-mails with a low apdex score.
		//
		// See https://github.com/insideout10/wordlift-plugin/issues/521
		Wordlift_NewRelic_Adapter::ignore_apdex();

		// Switch to Website converter if is home page.
		if ( $is_homepage ) {
			/**
			 * Filter: 'wordlift_disable_website_json_ld' - Allow disabling of the json+ld output.
			 *
			 * @since  3.14.0
			 * @api    bool $display_search Whether or not to display json+ld search on the frontend.
			 */
			if ( ! apply_filters( 'wordlift_disable_website_json_ld', false ) ) {
				// Set a reference to the website_converter.
				$website_converter = $this->website_converter;

				// Send JSON-LD.
				return $website_converter->create_schema();
			}
		}

		// If no id has been provided return an empty array.
		if ( ! isset( $post_id ) ) {
			return array();
		}

		// An array of references which is captured when converting an URI to a
		// json which we gather to further expand our json-ld.
		$references = array();

		// Set a reference to the entity_to_jsonld_converter to use in the closures.
		$entity_to_jsonld_converter = $this->converter;

		// Convert each URI to a JSON-LD array, while gathering referenced entities.
		// in the references array.
		$jsonld = array_merge(
			array( $entity_to_jsonld_converter->convert( $post_id, $references ) ),
			// Convert each URI in the references array to JSON-LD. We don't output
			// entities already output above (hence the array_diff).
			array_filter( array_map( function ( $item ) use ( $entity_to_jsonld_converter, $references ) {

				// "2nd level properties" may not output here, e.g. a post
				// mentioning an event, located in a place: the place is referenced
				// via the `@id` but no other properties are loaded.
				return $entity_to_jsonld_converter->convert( $item, $references );
			}, $references ) ) );

		// Finally send the JSON-LD.
		return $jsonld;
	}

	/**
	 * Write the JSON-LD in the head.
	 *
	 * This function isn't actually used, but may be used to quickly enable writing the JSON-LD synchronously to the
	 * document head, using the `wp_head` hook.
	 *
	 * @since 3.18.5
	 */
	public function wp_head() {

		// Determine whether this is the home page or whether we're displaying a single post.
		$is_homepage = is_home() || is_front_page();
		$post_id     = is_singular() ? get_the_ID() : null;

		$jsonld = json_encode( $this->get_jsonld( $is_homepage, $post_id ) );
		?>
        <script type="application/ld+json"><?php echo $jsonld; ?></script><?php
	}

}
