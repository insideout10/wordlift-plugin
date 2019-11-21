<?php
/**
 * An adapter to access and manipulate single post data for WordLift needs
 *
 * @since      3.14.0
 * @package    Wordlift
 * @subpackage Wordlift/admin
 */

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
	const TYPE_TERM_LINK = 1;

	/**
	 * Create the {@link Wordlift_Post_Adatpter} instance.
	 *
	 * @param integer $post_id the post ID of the post the adopter relates to.
	 *
	 * @since 3.14.0
	 *
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
	 *
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

		return self::str_word_count_utf8( strip_tags( strip_shortcodes( $post_content ) ) );
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

		return count( preg_split( '~[^\p{L}\p{N}\']+~u', $str ) );
	}

	/**
	 * Get the {@link WP_Post} permalink allowing 3rd parties to alter the URL.
	 *
	 * @param int $post_id Post ID.
	 *
	 * @return string The post permalink.
	 * @since 3.20.0
	 *
	 */
	public static function get_production_permalink( $post_id ) {

		/**
		 * The `wl_production_permalink` filter allows to change the permalink, this is useful in contexts
		 * when the production environment is copied over from a staging environment with staging
		 * URLs.
		 *
		 * @param string $permalink_url The default permalink.
		 * @param int    $post_id The post id.
		 *
		 * @since 3.23.0 we check whether the entity is bound to a term and, in that case, we link to the term.
		 * @since 3.20.0
		 *
		 * @see https://github.com/insideout10/wordlift-plugin/issues/850
		 *
		 */

		// Get all the URIs for the entity, i.e. itemid and sameAs.
		$uris = array_merge(
			(array) Wordlift_Entity_Service::get_instance()->get_uri( $post_id ),
			get_post_meta( $post_id, Wordlift_Schema_Service::FIELD_SAME_AS )
		);

		// Try to find one term matching the entity.
		$terms = get_terms( array(
			'fields'                 => 'ids',
			'number'                 => 1,
			'hide_empty'             => false,
			'update_term_meta_cache' => false,
			'meta_key'               => '_wl_entity_id',
			'meta_value'             => $uris,
			'meta_compare'           => 'IN',
		) );

		// If found use the term link, otherwise the permalink.
		if ( 1 === count( $terms ) ) {
			$permalink = get_term_link( current( $terms ) );
			$type      = self::TYPE_TERM_LINK;
		} else {
			$permalink = get_permalink( $post_id );
			$type      = self::TYPE_ENTITY_LINK;
		}

		/**
		 * Apply the `wl_production_permalink` filter.
		 *
		 * @param string $permalink The permalink.
		 * @param int    $post_id The post id.
		 * @param int    $type The permalink type: 0 = entity permalink, 1 = term link.
		 *
		 * @since 3.23.0 add the permalink type.
		 *
		 */
		return apply_filters( 'wl_production_permalink', $permalink, $post_id, $type );
	}

}
