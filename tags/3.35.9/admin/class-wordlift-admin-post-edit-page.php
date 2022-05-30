<?php
/**
 * Pages: Post Edit Page.
 *
 * A 'ghost' page which loads additional scripts and style for the post edit page.
 *
 * @since      3.11.0
 * @package    Wordlift
 * @subpackage Wordlift/admin
 */

use Wordlift\Scripts\Scripts_Helper;

/**
 * Define the {@link Wordlift_Admin_Post_Edit_Page} page.
 *
 * @since      3.11.0
 * @package    Wordlift
 * @subpackage Wordlift/admin
 */
class Wordlift_Admin_Post_Edit_Page {

	/**
	 * Constants to be used instead of text inside FAQ
	 * helper methods.
	 */
	const GUTENBERG = 'gutenberg';
	const TINY_MCE = 'tiny_mce';
	const FAQ_LIST_BOX_ID = 'wl-faq-meta-list-box';

	/** Constant to be used for translation domain */
	const WORDLIFT_TEXT_DOMAIN = 'wordlift';

	/**
	 * The {@link Wordlift} plugin instance.
	 *
	 * @since 3.11.0
	 *
	 * @var \Wordlift $plugin The {@link Wordlift} plugin instance.
	 */
	private $plugin;

	/**
	 * A {@link Wordlift_Log_Service} instance.
	 *
	 * @since 3.15.4
	 *
	 * @var \Wordlift_Log_Service $log A {@link Wordlift_Log_Service} instance.
	 */
	private $log;

	/**
	 * Create the {@link Wordlift_Admin_Post_Edit_Page} instance.
	 *
	 * @param \Wordlift $plugin The {@link Wordlift} plugin instance.
	 *
	 * @since 3.11.0
	 *
	 */
	function __construct( $plugin ) {

		$this->log = Wordlift_Log_Service::get_logger( get_class() );

		add_action( 'enqueue_block_editor_assets', array( $this, 'enqueue_scripts_gutenberg', ) );

		// Bail out if we're in the UX Builder editor.
		if ( $this->is_ux_builder_editor() ) {
			$this->log->info( 'WordLift will not show, since we are in UX Builder editor.' );

			return;
		}

		// Define the callbacks.
		$callback = array( $this, 'enqueue_scripts', );
		// Set a hook to enqueue scripts only when the edit page is displayed.
		add_action( 'admin_print_scripts-post.php', $callback );
		add_action( 'admin_print_scripts-post-new.php', $callback );

		$this->plugin = $plugin;
	}

	/**
	 * Check whether the current post opens with G'berg or not.
	 *
	 * @return bool True if G'berg is used otherwise false.
	 * @since 3.22.3
	 */
	function is_gutenberg_page() {
		if ( function_exists( 'is_gutenberg_page' ) && is_gutenberg_page() ) {
			// The Gutenberg plugin is on.
			return TRUE;
		}

		$current_screen = get_current_screen();
		if ( method_exists( $current_screen, 'is_block_editor' ) && $current_screen->is_block_editor() ) {
			// Gutenberg page on 5+.
			return TRUE;
		}

		return FALSE;
	}

	/**
	 * Check if we're in UX builder.
	 *
	 * @see   https://github.com/insideout10/wordlift-plugin/issues/691
	 *
	 * @since 3.15.4
	 *
	 * @return bool True if we're in UX builder, otherwise false.
	 */
	private function is_ux_builder_editor() {

		return function_exists( 'ux_builder_is_editor' )
		       && ux_builder_is_editor();
	}

	/**
	 * Enqueue scripts and styles for the edit page.
	 *
	 * @since 3.11.0
	 */
	public function enqueue_scripts() {

		// Dequeue potentially conflicting ontrapages angular scripts which any *are not* used on the edit screen.
		//
		// @see https://github.com/insideout10/wordlift-plugin/issues/832
		wp_dequeue_script( 'ontrapagesAngular' );
		wp_dequeue_script( 'ontrapagesApp' );
		wp_dequeue_script( 'ontrapagesController' );

		// Bail out if this is G'berg.
		if ( $this->is_gutenberg_page() ) {
			return;
		}

		// If Gutenberg is enabled for the post, do not load the legacy edit.js.
		if ( function_exists( 'use_block_editor_for_post' ) && use_block_editor_for_post( get_post() ) ) {
			return;
		}

		// Bail out if classification sidebar is not enabled via hook
		if ( ! apply_filters( 'wl_feature__enable__classification-sidebar', true ) ) {
			return;
		}


		/*
		 * Enqueue the edit screen JavaScript. The `wordlift-admin.bundle.js` file
		 * is scheduled to replace the older `wordlift-admin.min.js` once client-side
		 * code is properly refactored.
		 *
		 * @link https://github.com/insideout10/wordlift-plugin/issues/761
		 *
		 * @since 3.20.0 edit.js has been migrated to the new webpack configuration.
		 */
		$script_name = plugin_dir_url( dirname( __FILE__ ) ) . 'js/dist/edit';

		/**
		 * Scripts_Helper introduced.
		 *
		 * @since 3.25.0 Scripts are loaded using script helper to ensure WP 4.4 compatibiility.
		 * @since 3.25.1 The handle is used to hook the wp_localize_script for the _wlEntityTypes global object.
		 */
		Scripts_Helper::enqueue_based_on_wordpress_version(
			'wl-classic-editor',
			$script_name,
			array(
				$this->plugin->get_plugin_name(),
				'jquery',
				// Require wp.ajax.
				'wp-util',
				// @@todo: provide the following dependencies when we're in WP < 5.0 (i.e. when these dependencies aren't already defined).
				'react',
				'react-dom',
				'wp-element',
				'wp-polyfill',
				/*
				* Angular isn't loaded anymore remotely, but it is loaded within wordlift-reloaded.js.
				*
				* See https://github.com/insideout10/wordlift-plugin/issues/865.
				*
				* @since 3.19.6
				*/
				//				// Require Angular.
				//				'wl-angular',
				//				'wl-angular-geolocation',
				//				'wl-angular-touch',
				//				'wl-angular-animate',
				/**
				 * We need the `wp.hooks` global to allow the edit.js script to send actions.
				 *
				 * @since 3.23.0
				 */
				'wp-hooks',
			)
		);

		wp_enqueue_style( 'wl-classic-editor', "$script_name.css", array(), $this->plugin->get_version() );
		// Disable Faq Editor.
		// $this->load_faq_scripts_and_styles();
		// $this->load_faq_settings( self::TINY_MCE );
	}

	/**
	 * Enqueue the scripts and styles needed for FAQ
	 */
	private function load_faq_scripts_and_styles() {
		wp_enqueue_style(
			'wl-faq-metabox-style',
			plugin_dir_url( dirname( __FILE__ ) ) . 'js/dist/faq.css',
			array()
		);
		Scripts_Helper::enqueue_based_on_wordpress_version(
			'wl-faq-metabox-script',
			plugin_dir_url( dirname( __FILE__ ) ) . 'js/dist/faq',
			array( 'wp-polyfill' ),
			true
		);
	}

	/**
	 * Get FAQ settings array
	 *
	 * @return array
	 */
	public function get_faq_settings() {
		return array(
			'restUrl'                 => get_rest_url( NULL, WL_REST_ROUTE_DEFAULT_NAMESPACE . '/faq' ),
			'listBoxId'               => self::FAQ_LIST_BOX_ID,
			'nonce'                   => wp_create_nonce( 'wp_rest' ),
			'postId'                  => get_the_ID(),
			// Translation for warning, error message.
			'invalidTagMessage'       => sprintf( __( 'Invalid tags %s is present in answer', self::WORDLIFT_TEXT_DOMAIN ), "{INVALID_TAGS}" ),
			'invalidWordCountMessage' => sprintf( __( 'Answer word count must not exceed %s words', self::WORDLIFT_TEXT_DOMAIN ), "{ANSWER_WORD_COUNT_WARNING_LIMIT}" ),
			'questionText'            => __( 'Question', self::WORDLIFT_TEXT_DOMAIN ),
			'answerText'              => __( 'Answer', self::WORDLIFT_TEXT_DOMAIN ),
			'addQuestionOrAnswerText' => __( 'Add Question / Answer', self::WORDLIFT_TEXT_DOMAIN ),
			'addQuestionText'         => __( 'Add Question', self::WORDLIFT_TEXT_DOMAIN ),
			'addAnswerText'           => __( 'Add Answer', self::WORDLIFT_TEXT_DOMAIN ),
			'noFaqItemsText'          => __( 'Highlight a question in content, then click Add Question.', self::WORDLIFT_TEXT_DOMAIN ),
			'updatingText'          => __( 'Updating...', self::WORDLIFT_TEXT_DOMAIN )
		);
	}

	/**
	 * Load FAQ settings to the add/edit post page
	 *
	 * @param $editor string specifying which text editor needed to be used.
	 */
	private function load_faq_settings( $editor ) {
		// This script also provides translations to gutenberg.
		wp_localize_script( 'wl-faq-metabox-script', '_wlFaqSettings', $this->get_faq_settings() );

		// Enqueue the FAQ style
		if ( $editor === self::GUTENBERG ) {
			Scripts_Helper::enqueue_based_on_wordpress_version(
				'wl-faq-gutenberg-plugin',
				plugin_dir_url( dirname( __FILE__ ) ) . 'js/dist/block-editor-faq-plugin',
				array( 'wp-polyfill' ),
				TRUE
			);
		}
	}

	/**
	 * Enqueue scripts and styles for the gutenberg edit page.
	 *
	 * @since 3.21.0
	 */
	public function enqueue_scripts_gutenberg() {
		// Load FAQ settings. - Disabled for now
		// $this->load_faq_scripts_and_styles();
		//$this->load_faq_settings( self::GUTENBERG );

		wp_register_script(
			'wl-block-editor',
			plugin_dir_url( dirname( __FILE__ ) ) . 'js/dist/block-editor.js',
			array(
				'react',
				'wordlift',
				'wp-hooks',
				'wp-data',
				'wp-rich-text',
				'wp-blocks',
				'wp-plugins',
				'wp-edit-post',
			),
			$this->plugin->get_version()
		);
		wp_localize_script( 'wl-block-editor', '_wlBlockEditorSettings', array(
			'root'  => esc_url_raw( rest_url() ),
			'nonce' => wp_create_nonce( 'wp_rest' )
		) );

		/*
		 * @since 3.25.1 The hook is used by the wp_localize_script to register the _wlEntityTypes global object.
		 */
		wp_enqueue_style(
			'wl-block-editor',
			plugin_dir_url( dirname( __FILE__ ) ) . 'js/dist/block-editor.css',
			array(),
			$this->plugin->get_version()
		);

		wp_enqueue_script(
			'wl-autocomplete-select',
			plugin_dir_url( dirname( __FILE__ ) ) . 'js/dist/autocomplete-select.js',
			array(),
			$this->plugin->get_version(),
			TRUE
		);

		wp_enqueue_style(
			'wl-autocomplete-select',
			plugin_dir_url( dirname( __FILE__ ) ) . 'js/dist/autocomplete-select.css',
			array(),
			$this->plugin->get_version()
		);

	}

}
