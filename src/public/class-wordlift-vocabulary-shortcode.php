<?php
/**
 * Shortcodes: Glossary Shortcode.
 *
 * `wl_vocabulary` implementation.
 *
 * @since      3.16.0
 * @package    Wordlift
 * @subpackage Wordlift/includes
 */

/**
 * Define the {@link Wordlift_Glossary_Shortcode} class.
 *
 * @since      3.16.0
 * @package    Wordlift
 * @subpackage Wordlift/includes
 */
class Wordlift_Vocabulary_Shortcode extends Wordlift_Shortcode {

	/**
	 * The shortcode.
	 *
	 * @since  3.17.0
	 */
	const SHORTCODE = 'wl_vocabulary';

	/**
	 * A {@link Wordlift_Log_Service} instance.
	 *
	 * @since  3.17.0
	 * @access private
	 * @var \Wordlift_Log_Service $log A {@link Wordlift_Log_Service} instance.
	 */
	private $log;

	/**
	 * The vocabulary id
	 *
	 * @since  3.18.3
	 * @access private
	 * @var int $vocabulary_id The vocabulary unique id.
	 */
	private static $vocabulary_id = 0;

	/**
	 * Create a {@link Wordlift_Glossary_Shortcode} instance.
	 *
	 * @since 3.16.0
	 */
	public function __construct() {
		parent::__construct();

		$this->log = Wordlift_Log_Service::get_logger( get_class() );

		$this->register_block_type();

	}

	/**
	 * Check whether the requirements for this shortcode to work are available.
	 *
	 * @return bool True if the requirements are satisfied otherwise false.
	 * @since 3.17.0
	 */
	private static function are_requirements_satisfied() {

		return function_exists( 'mb_strlen' ) &&
			   function_exists( 'mb_substr' ) &&
			   function_exists( 'mb_strtolower' ) &&
			   function_exists( 'mb_strtoupper' ) &&
			   function_exists( 'mb_convert_case' );
	}

	/**
	 * Render the shortcode.
	 *
	 * @param array $atts An array of shortcode attributes as set by the editor.
	 *
	 * @return string The output html code.
	 * @since 3.16.0
	 */
	public function render( $atts ) {

		// Bail out if the requirements aren't satisfied: we need mbstring for
		// the vocabulary widget to work.
		if ( ! self::are_requirements_satisfied() ) {
			$this->log->warn( "The vocabulary widget cannot be displayed because this WordPress installation doesn't satisfy its requirements." );

			return '';
		}

		wp_enqueue_style( 'wl-vocabulary-shortcode', dirname( plugin_dir_url( __FILE__ ) ) . '/public/css/wordlift-vocabulary-shortcode.css', array(), WORDLIFT_VERSION );

		// Extract attributes and set default values.
		$atts = shortcode_atts(
			array(
				// The entity type, such as `person`, `organization`, ...
				'type'    => 'all',
				// Limit the number of posts to 100 by default. Use -1 to remove the limit.
				'limit'   => 100,
				// Sort by title.
				'orderby' => 'post_date',
				// Sort DESC.
				'order'   => 'DESC',
				// Allow to specify the category ID.
				'cat'     => '',
			),
			$atts
		);

		// Get the posts. Note that if a `type` is specified before, then the
		// `tax_query` from the `add_criterias` call isn't added.
		$posts = $this->get_posts( $atts );

		// Get the alphabet.
		$language_code = Wordlift_Configuration_Service::get_instance()->get_language_code();
		$alphabet      = Wordlift_Alphabet_Service::get( $language_code );

		// Add posts to the alphabet.
		foreach ( $posts as $post ) {
			$this->add_to_alphabet( $alphabet, $post->ID );
		}

		$header   = '';
		$sections = '';

		// Get unique id for each vocabulary shortcode.
		$vocabulary_id = self::get_and_increment_vocabulary_id();

		// Generate the header.
		foreach ( $alphabet as $item => $translations ) {
			$template = ( empty( $translations )
				? '<span class="wl-vocabulary-widget-disabled">%s</span>'
				: '<a href="#wl-vocabulary-%3$d-%2$s">%1$s</a>' );

			$header .= sprintf( $template, esc_html( $item ), esc_attr( $item ), $vocabulary_id );
		}

		// Generate the sections.
		foreach ( $alphabet as $item => $translations ) {
			// @since 3.19.3 we use `mb_strtolower` and `mb_strtoupper` with a custom function to handle sorting,
			// since we had `AB` being placed before `Aa` with `asort`.
			//
			// Order the translations alphabetically.
			// asort( $translations );
			uasort(
				$translations,
				function ( $a, $b ) {
					if ( mb_strtolower( $a ) === mb_strtolower( $b )
						 || mb_strtoupper( $a ) === mb_strtoupper( $b ) ) {
						return 0;
					}

					return ( mb_strtolower( $a ) < mb_strtolower( $b ) ) ? - 1 : 1;
				}
			);
			$sections .= $this->get_section( $item, $translations, $vocabulary_id );
		}

		// Return HTML template.
		ob_start();
		?>
		<div class='wl-vocabulary'>
			<nav class='wl-vocabulary-alphabet-nav'>
				<?php
				echo wp_kses(
					$header,
					array(
						'span' => array( 'class' => array() ),
						'a'    => array( 'href' => array() ),
					)
				);
				?>
			</nav>
			<div class='wl-vocabulary-grid'>
				<?php
				echo wp_kses(
					$sections,
					array(
						'div'   => array(
							'class' => array(),
							'id'    => array(),
						),
						'aside' => array( 'class' => array() ),
						'ul'    => array( 'class' => array() ),
						'li'    => array(),
						'a'     => array( 'href' => array() ),
					)
				);
				?>
			</div>
		</div>
		<?php
		$html = ob_get_clean();

		return $html;

	}

	private function register_block_type() {

		$scope = $this;

		add_action(
			'init',
			function () use ( $scope ) {
				if ( ! function_exists( 'register_block_type' ) ) {
					// Gutenberg is not active.
					return;
				}

				register_block_type(
					'wordlift/vocabulary',
					array(
						'editor_script'   => 'wl-block-editor',
						'render_callback' => function ( $attributes ) use ( $scope ) {
							$attr_code = '';
							foreach ( $attributes as $key => $value ) {
								$attr_code .= $key . '="' . htmlentities( $value ) . '" ';
							}

							return '[' . $scope::SHORTCODE . ' ' . $attr_code . ']';
						},
						'attributes'      => array(
							'type'        => array(
								'type'    => 'string',
								'default' => 'all',
							),
							'limit'       => array(
								'type'    => 'number',
								'default' => 100,
							),
							'orderby'     => array(
								'type'    => 'string',
								'default' => 'post_date',
							),
							'order'       => array(
								'type'    => 'string',
								'default' => 'DESC',
							),
							'cat'         => array(
								'type'    => 'string',
								'default' => '',
							),
							'preview'     => array(
								'type'    => 'boolean',
								'default' => false,
							),
							'preview_src' => array(
								'type'    => 'string',
								'default' => WP_CONTENT_URL . '/plugins/wordlift/images/block-previews/vocabulary.png',
							),
						),
					)
				);
			}
		);
	}

	/**
	 * Generate the html code for the section.
	 *
	 * @param string $letter The section's letter.
	 * @param array  $posts An array of `$post_id => $post_title` associated with
	 *                                the section.
	 * @param int    $vocabulary_id Unique vocabulary id.
	 *
	 * @return string The section html code (or an empty string if the section has
	 *                no posts).
	 * @since 3.17.0
	 */
	private function get_section( $letter, $posts, $vocabulary_id ) {

		// Return an empty string if there are no posts.
		if ( 0 === count( $posts ) ) {
			return '';
		}

		return sprintf(
			'
			<div class="wl-vocabulary-letter-block" id="wl-vocabulary-%d-%s">
				<aside class="wl-vocabulary-left-column">%s</aside>
				<div class="wl-vocabulary-right-column">
					<ul class="wl-vocabulary-items-list">
						%s
					</ul>
				</div>
			</div>
		',
			$vocabulary_id,
			esc_attr( $letter ),
			esc_html( $letter ),
			$this->format_posts_as_list( $posts )
		);
	}

	/**
	 * Format an array post `$post_id => $post_title` as a list.
	 *
	 * @param array $posts An array of `$post_id => $post_title` key, value pairs.
	 *
	 * @return string A list.
	 * @since 3.17.0
	 */
	private function format_posts_as_list( $posts ) {

		return array_reduce(
			array_keys( $posts ),
			function ( $carry, $item ) use ( $posts ) {
				return $carry . sprintf( '<li><a href="%s">%s</a></li>', esc_attr( get_permalink( $item ) ), esc_html( $posts[ $item ] ) );
			},
			''
		);
	}

	/**
	 * Get the posts from WordPress using the provided attributes.
	 *
	 * @param array $atts The shortcode attributes.
	 *
	 * @return array An array of {@link WP_Post}s.
	 * @since 3.17.0
	 */
	private function get_posts( $atts ) {
		// The default arguments for the query.
		$args = array(
			// phpcs:ignore WordPress.WP.PostsPerPage.posts_per_page_posts_per_page
			'posts_per_page'         => intval( $atts['limit'] ),
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
			'orderby'                => $atts['orderby'],
			'order'                  => $atts['order'],
			// Exclude the publisher.
			'post__not_in'           => array( Wordlift_Configuration_Service::get_instance()->get_publisher_id() ),
		);

		// Limit the based entity type if needed.
		if ( 'all' !== $atts['type'] ) {
			$args['tax_query'] = array(
				array(
					'taxonomy' => Wordlift_Entity_Type_Taxonomy_Service::TAXONOMY_NAME,
					'field'    => 'slug',
					'terms'    => $atts['type'],
				),
			);
		}

		if ( ! empty( $atts['cat'] ) ) {
			$args['cat'] = $atts['cat'];
		}

		// Get the posts. Note that if a `type` is specified before, then the
		// `tax_query` from the `add_criterias` call isn't added.
		return get_posts( Wordlift_Entity_Service::add_criterias( $args ) );

	}

	/**
	 * Populate the alphabet with posts.
	 *
	 * @param array $alphabet An array of letters.
	 * @param int   $post_id The {@link WP_Post} id.
	 *
	 * @since 3.17.0
	 */
	private function add_to_alphabet( &$alphabet, $post_id ) {

		// Get the title without accents.
		$title = $this->get_the_title( $post_id );

		// Get the initial letter.
		$letter = $this->get_first_letter_in_alphabet_or_hash( $alphabet, $title );

		// Add the post.
		$alphabet[ $letter ][ $post_id ] = $title;

	}

	/**
	 * Get the title without accents.
	 * If the post is not of type `entity`, use first synonym if synonyms exists.
	 *
	 * @see https://github.com/insideout10/wordlift-plugin/issues/1096
	 *
	 * @since 3.27.0
	 *
	 * @param int $post_id The {@link WP_Post} id.
	 *
	 * @return string
	 */
	private function get_the_title( $post_id ) {

		$title = get_the_title( $post_id );

		if ( get_post_type( $post_id ) !== Wordlift_Entity_Service::TYPE_NAME ) {
			$alternative_labels = Wordlift_Entity_Service::get_instance()->get_alternative_labels( $post_id );

			if ( count( $alternative_labels ) > 0 ) {
				$title = $alternative_labels[0];
			}
		}

		return remove_accents( $title );

	}

	/**
	 * Find the first letter in the alphabet.
	 *
	 * In some alphabets a letter is a compound of letters, therefore this function
	 * will look for groups of 2 or 3 letters in the alphabet before looking for a
	 * single letter. In case the letter is not found a # (hash) key is returned.
	 *
	 * @param array  $alphabet An array of alphabet letters.
	 * @param string $title The title to match.
	 *
	 * @return string The initial letter or a `#` key.
	 * @since 3.17.0
	 */
	private function get_first_letter_in_alphabet_or_hash( $alphabet, $title ) {

		// Need to handle letters which consist of 3 and 2 characters.
		for ( $i = 3; $i > 0; $i -- ) {
			$letter = mb_convert_case( mb_substr( $title, 0, $i ), MB_CASE_UPPER );
			if ( isset( $alphabet[ $letter ] ) ) {
				return $letter;
			}
		}

		return '#';
	}

	/**
	 * Get and increment the `$vocabulary_id`.
	 *
	 * @return int The incremented vocabulary id.
	 * @since  3.18.3
	 */
	private static function get_and_increment_vocabulary_id() {
		return self::$vocabulary_id ++;
	}

}
