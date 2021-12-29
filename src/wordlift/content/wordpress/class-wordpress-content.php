<?php

namespace Wordlift\Content\Wordpress;

use Wordlift\Entity\Content;
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

}