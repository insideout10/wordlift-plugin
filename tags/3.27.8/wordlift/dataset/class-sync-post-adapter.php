<?php

namespace Wordlift\Dataset;

use Wordlift\Object_Type_Enum;

class Sync_Post_Adapter extends Abstract_Sync_Object_Adapter {
	/**
	 * @var int
	 */
	private $post_id;

	/**
	 * Sync_User_Adapter constructor.
	 *
	 * @param int $post_id
	 *
	 * @throws \Exception
	 */
	function __construct( $post_id ) {
		parent::__construct( Object_Type_Enum::POST, $post_id );

		$this->post_id = $post_id;
	}

	function is_published() {
		return ( 'publish' === get_post_status( $this->post_id ) );
	}

	function is_public() {
		// Check if the post type is public.
		$post_type     = get_post_type( $this->post_id );
		$post_type_obj = get_post_type_object( $post_type );

		return $post_type_obj->public;
	}

}
