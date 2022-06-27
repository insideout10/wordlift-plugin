<?php

namespace Wordlift\Content\Wordpress;

use Wordlift\Content\Content;
use Wordlift\Object_Type_Enum;
use WP_Post;
use WP_Term;
use WP_User;

class Wordpress_Content implements Content {

	/**
	 * The actual content.
	 *
	 * @var WP_Post|WP_Term|WP_User $bag
	 */
	private $bag;

	/**
	 * Creates a WordPress content instance.
	 *
	 * @param WP_Post|WP_Term|WP_User $bag
	 */
	public function __construct( $bag ) {
		$this->bag = $bag;
	}

	/**
	 * The actual content.
	 *
	 * @return WP_Post|WP_Term|WP_User
	 */
	function get_bag() {
		return $this->bag;
	}

	function get_id() {
		if ( ! is_object( $this->bag ) ) {
			return null;
		}

		switch ( get_class( $this->bag ) ) {
			case 'WP_Post':
			case 'WP_User':
				return $this->bag->ID;
			case 'WP_Term':
				return $this->bag->term_id;
		}

		return null;
	}

	function get_object_type_enum() {
		return Object_Type_Enum::from_wordpress_instance( $this->bag );
	}

	function get_permalink() {
		switch ( get_class( $this->bag ) ) {
			case 'WP_Post':
				return get_permalink( $this->get_bag()->ID );
			case 'WP_User':
				return get_author_posts_url( $this->get_bag()->ID );
			case 'WP_Term':
				return get_term_link( $this->bag->term_id );
		}

		return null;
	}

	function get_edit_link() {
		switch ( get_class( $this->bag ) ) {
			case 'WP_Post':
				// We need to return & character as &, by default context is set to display.
				// so &  will be returned as &amp; breaking header location redirects.
				// By setting context to none we prevent this issue.
				return get_edit_post_link( $this->get_bag()->ID, 'none' );
			case 'WP_User':
				return get_edit_user_link( $this->get_bag()->ID );
			case 'WP_Term':
				return get_edit_term_link( $this->bag->term_id );
		}

		return null;
	}
}