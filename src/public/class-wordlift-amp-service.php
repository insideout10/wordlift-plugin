<?php
/**
 * Services: AMP Services.
 *
 * The file defines a class for AMP related manipulations.
 *
 * @link       https://wordlift.io
 *
 * @since      3.12.0
 *
 * @package    Wordlift
 * @subpackage Wordlift/public
 */

/**
 * Handles AMP related manipulation in the generated HTML of entity pages
 *
 * @since      3.12.0
 * @package    Wordlift
 * @subpackage Wordlift/public
 */
class Wordlift_AMP_Service {

	/**
	 * The {@link \Wordlift_Jsonld_Service} instance.
	 *
	 * @since 3.19.1
	 * @access private
	 * @var \Wordlift_Jsonld_Service $jsonld_service The {@link \Wordlift_Jsonld_Service} instance.
	 */
	private $jsonld_service;

	/**
	 * Create a {@link Wordlift_AMP_Service} instance.
	 * @since 3.19.1
	 *
	 * @param \Wordlift_Jsonld_Service $jsonld_service
	 */
	function __construct( $jsonld_service ) {

		$this->jsonld_service = $jsonld_service;

		// Integrate with automattic's AMP plugin if it is available
		if ( ! defined( 'AMP__VERSION' ) ) {
			return;
		}

		add_action( 'amp_init', array( $this, 'register_entity_cpt_with_amp_plugin', ) );
		add_action( 'amp_post_template_footer', array( $this, 'amp_post_template_footer', ) );

	}

	/**
	 * Register the `entity` post type with the AMP plugin.
	 *
	 * @since 3.12.0
	 */
	function register_entity_cpt_with_amp_plugin() {

		if ( defined( 'AMP_QUERY_VAR' ) ) {
			add_post_type_support( 'entity', AMP_QUERY_VAR );
		}

	}

	/**
	 * Hook to the `amp_post_template_footer` function to output our **async** script to AMP.
	 *
	 * We're **asynchronous** as requested by the AMP specs:
	 *
	 * ```
	 * Among the biggest optimizations is the fact that it makes everything that comes from external resources
	 * asynchronous, so nothing in the page can block anything from rendering.
	 * ```
	 *
	 * See https://www.ampproject.org/learn/overview/
	 *
	 * @since 3.19.1
	 */
	function amp_post_template_footer() {

		// Determine whether this is the home page or whether we're displaying a single post.
		$is_homepage = is_home() || is_front_page();
		$post_id     = is_singular() ? get_the_ID() : null;

		// Get the actual value.
		$jsonld = wp_json_encode( $this->jsonld_service->get_jsonld( $is_homepage, $post_id ) );

		?>
        <script type="application/ld+json"><?php echo $jsonld; ?></script>
		<?php
	}

}
