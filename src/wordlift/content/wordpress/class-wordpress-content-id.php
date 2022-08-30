<?php

/**
 * Represents an entity ID in WordPress.
 */

namespace Wordlift\Content\Wordpress;

use Wordlift\Content\Content_Id;
use Wordlift\Object_Type_Enum;

// phpcs:ignore WordPress.WP.CapitalPDangit.MisspelledClassName
class Wordpress_Content_Id implements Content_Id {

	/**
	 * @var int $id The post/page/term/user ID.
	 */
	private $id;

	/**
	 * @var Object_Type_Enum $type The content type, post/page/term/user.
	 */
	private $type;

	public static function create_post( $id ) {
		return new self( $id, Object_Type_Enum::POST );
	}

	public static function create_term( $id ) {
		return new self( $id, Object_Type_Enum::TERM );
	}

	public static function create_user( $id ) {
		return new self( $id, Object_Type_Enum::USER );
	}

	/**
	 * @param int $id The post/page/term/user ID.
	 * @param int $type The content type, post/page/term/user.
	 */
	public function __construct( $id, $type ) {
		$this->id   = $id;
		$this->type = $type;
	}

	public function get_id() {
		return $this->id;
	}

	public function get_type() {
		return $this->type;
	}

}
