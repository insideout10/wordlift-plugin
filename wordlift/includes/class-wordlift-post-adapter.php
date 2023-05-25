<?php
/**
 * An adapter to access and manipulate single post data for WordLift needs
 *
 * @since      3.14.0
 * @package    Wordlift
 * @subpackage Wordlift/admin
 */

use Wordlift\Link\Object_Link_Provider;
use Wordlift\Object_Type_Enum;

/**
 * Define the {@link Wordlift_Post_Adatpter}.
 *
 * @since      3.14.0
 * @package    Wordlift
 * @subpackage Wordlift/admin
 */
class Wordlift_Post_Adapter {

	/**
	 * The post id to which the adopter relates.
	 *
	 * @since 3.14.0
	 *
	 * @var integer $post_id .
	 */
	private $post_id;

	const TYPE_ENTITY_LINK = 0;
	const TYPE_TERM_LINK   = 1;

	/**
	 * Create the {@link Wordlift_Post_Adatpter} instance.
	 *
	 * @param integer $post_id the post ID of the post the adopter relates to.
	 *
	 * @since 3.14.0
	 */
	public function __construct( $post_id ) {

		$this->post_id = $post_id;

	}

	/**
	 * Get the word count of the post content.
	 *
	 * The count is calculated over the post content after stripping shortcodes and html tags.
	 *
	 * @return integer the number of words in the content after stripping shortcodes and html tags..
	 * @since 3.14.0
	 */
	public function word_count() {

		$post = get_post( $this->post_id );

		/*
		 * Apply the `wl_post_content` filter, in case 3rd parties want to change the post content, e.g.
		 * because the content is written elsewhere.
		 *
		 * @since 3.20.0
		 */
		$post_content = apply_filters( 'wl_post_content', $post->post_content, $post );

		return self::str_word_count_utf8( wp_strip_all_tags( strip_shortcodes( $post_content ) ) );
	}

	/**
	 * Count words in the string, taking into account UTF-8 characters.
	 *
	 * @see https://github.com/insideout10/wordlift-plugin/issues/884
	 *
	 * @since 3.20.0
	 *
	 * @param string $str The target string.
	 *
	 * @return int The number of words.
	 */
	private static function str_word_count_utf8( $str ) {

		$words = preg_split( '~[^\p{L}\p{N}\']+~u', $str );

		return $words ? count( $words ) : 0;
	}

	/**
	 * Get the {@link WP_Post} permalink allowing 3rd parties to alter the URL.
	 *
	 * @param int $post_id Post ID.
	 *
	 * @return string The post permalink.
	 * @since 3.20.0
	 */
	public static function get_production_permalink( $post_id, $object_type = Object_Type_Enum::POST ) {

		$object_link_service = Object_Link_Provider::get_instance();
		$permalink           = $object_link_service->get_permalink( $post_id, $object_type );

		/**
		 * WordPress 4.4 doesn't support meta queries for terms, therefore we only support post permalinks here.
		 *
		 * Later on for WordPress 4.5+ we look for terms bound to the entity and we use the term link instead of the
		 * post permalink if we find them.
		 */
		global $wp_version;
		if ( version_compare( $wp_version, '4.5', '<' ) ) {
			return apply_filters( 'wl_production_permalink', $permalink, $post_id, self::TYPE_ENTITY_LINK, null );
		}

		/**
		 * The `wl_production_permalink` filter allows to change the permalink, this is useful in contexts
		 * when the production environment is copied over from a staging environment with staging
		 * URLs.
		 *
		 * @param string $permalink_url The default permalink.
		 * @param int $post_id The post id.
		 *
		 * @since 3.23.0 we check whether the entity is bound to a term and, in that case, we link to the term.
		 * @since 3.20.0
		 *
		 * @see https://github.com/insideout10/wordlift-plugin/issues/850
		 */

		$uris = $object_link_service->get_same_as_uris(
			$post_id,
			$object_type
		);

		// Only try to link a term if `WL_ENABLE_TERM_LINKING` is enabled.
		$terms = array();
		if ( defined( 'WL_ENABLE_TERM_LINKING' ) && WL_ENABLE_TERM_LINKING ) {
			// Try to find one term matching the entity.
			$terms = get_terms(
				array(
					'number'                 => 1,
					'hide_empty'             => false,
					'update_term_meta_cache' => false,
					'meta_key'               => '_wl_entity_id',
					'meta_value'             => $uris,
					'meta_compare'           => 'IN',
				)
			);
		}

		$type = self::TYPE_ENTITY_LINK;
		$term = null;
		// If found use the term link, otherwise the permalink.
		if ( 1 === count( $terms ) ) {
			$term      = current( $terms );
			$permalink = get_term_link( $term );
			$type      = self::TYPE_TERM_LINK;
		}

		/**
		 * Apply the `wl_production_permalink` filter.
		 *
		 * @param string $permalink The permalink.
		 * @param int $post_id The post id.
		 * @param int $type The permalink type: 0 = entity permalink, 1 = term link.
		 * @param WP_Term $term The term if type is term link, otherwise null.
		 *
		 * @since 3.23.0 add the permalink type and term parameters.
		 */
		return apply_filters( 'wl_production_permalink', $permalink, $post_id, $type, $term );
	}

	/**
	 * Get comma separated tags to be used as keywords
	 *
	 * @return string|void Comma separated tags
	 *
	 * @since 3.27.2
	 */
	public function keywords() {
		$tags = get_the_tags( $this->post_id );

		if ( empty( $tags ) ) {
			return;
		}

		return implode(
			',',
			array_map(
				function ( $tag ) {
					return $tag->name;
				},
				$tags
			)
		);
	}

	/**
	 * Get comma separated categories to be used as article section
	 *
	 * @return string[] Comma separated categories
	 *
	 * @since 3.27.2
	 */
	public function article_section() {
		$categories = get_the_category( $this->post_id );

		return wp_list_pluck( $categories, 'cat_name' );
	}

	/**
	 * Get comment count
	 *
	 * @return string|void Comment count
	 *
	 * @since 3.27.2
	 */
	public function comment_count() {
		return get_comments_number( $this->post_id );
	}

	/**
	 * Get Language
	 * Try WPML, Polylang for post specific languages, else fallback on get_locale()
	 *
	 * @return string|void Language code (locale)
	 *
	 * @since 3.27.2
	 */
	public function locale() {
		$language = 'en-US';

		if ( function_exists( 'wpml_get_language_information' ) ) {
			// WPML handling
			// WPML: Updated function signature.
			// function wpml_get_language_information( $empty_value = null, $post_id = null )
			$post_language = wpml_get_language_information( null, $this->post_id );
			if ( ! $post_language instanceof WP_Error ) {
				$language = $post_language['locale'];
			}
		} elseif ( function_exists( 'pll_get_post_language' ) ) {
			// Polylang handling
			$language = pll_get_post_language( $this->post_id, 'locale' );
		} else {
			$language = get_locale();
		}

		return str_replace( '_', '-', $language );
	}

}
