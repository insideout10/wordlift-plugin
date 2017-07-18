<?php

/**
 * Manage user-related functions. This class receives notifications when a post is created/updated and pushes the author's
 * data to the triple store. It does NOT receive notification when a user is create/updated because we don't want to send
 * to the triple stores users that eventually do not write posts (therefore if user data change, the triple store is updated
 * only when the user creates/updates a new post).
 *
 * @since 3.1.7
 */
class Wordlift_User_Service {

	/**
	 * The meta key where the user's URI is stored.
	 *
	 * @since 3.1.7
	 */
	const URI_META_KEY = '_wl_uri';

	/**
	 * The user meta key where the deny entity edit flag is stored.
	 *
	 * @since 3.14.0
	 */
	const DENY_ENTITY_CREATE_META_KEY = '_wl_deny_entity_create';

	/**
	 * The meta key holding the entity id representing a {@link WP_User}.
	 *
	 * @since 3.14.0
	 */
	const ENTITY_META_KEY = '_wl_entity';

	/**
	 * The Log service.
	 *
	 * @since  3.1.7
	 * @access private
	 * @var \Wordlift_Log_Service $log_service The Log service.
	 */
	private $log_service;

	/**
	 * The singleton instance of the User service.
	 *
	 * @since  3.1.7
	 * @access private
	 * @var \Wordlift_User_Service $user_service The singleton instance of the User service.
	 */
	private static $instance;

	/**
	 * Create an instance of the User service.
	 *
	 * @since 3.1.7
	 */
	public function __construct() {

		$this->log_service = Wordlift_Log_Service::get_logger( 'Wordlift_User_Service' );

		self::$instance = $this;

		add_filter( 'user_has_cap', array( $this, 'has_cap' ), 10, 3 );
	}

	/**
	 * Get the singleton instance of the User service.
	 *
	 * @since 3.1.7
	 * @return \Wordlift_User_Service The singleton instance of the User service.
	 */
	public static function get_instance() {

		return self::$instance;
	}

	/**
	 * Get the URI for a user.
	 *
	 * @since 3.1.7
	 *
	 * @param int $user_id The user id
	 *
	 * @return false|string The user's URI or false in case of failure.
	 */
	public function get_uri( $user_id ) {

		// Try to get the URI stored in the user's meta and return it if available.
		if ( false !== ( $user_uri = $this->_get_uri( $user_id ) ) ) {
			return $user_uri;
		}

		// Try to build an URI, return false in case of failure.
		if ( false === ( $user_uri = $this->_build_uri( $user_id ) ) ) {
			return false;
		}

		// Store the URI for future requests (we need a "permanent" URI).
		$this->_set_uri( $user_id, $user_uri );

		return $user_uri;
	}

	/**
	 * Receives wp_insert_post events.
	 *
	 * @since 3.1.7
	 *
	 * @param int     $post_id Post ID.
	 * @param WP_Post $post    Post object.
	 * @param bool    $update  Whether this is an existing post being updated or not.
	 */
	public function wp_insert_post( $post_id, $post, $update ) {

		// If the post is not published, return.
		if ( 'publish' !== get_post_status( $post_id ) ) {
			return;
		}

		// We expect a numeric author id.
		if ( ! is_numeric( $post->post_author ) ) {
			return;
		}

		// Get the delete query,or return in case of failure.
		if ( false === ( $delete = $this->get_delete_query( $post->post_author ) ) ) {
			return;
		}

		// Get the insert query,or return in case of failure.
		if ( false === ( $insert = $this->get_insert_query( $post->post_author ) ) ) {
			return;
		}

		// Send the query to the triple store.
		rl_execute_sparql_update_query( $delete . $insert );

	}

	/**
	 * Set the `id` of the entity representing a {@link WP_User}.
	 *
	 * If the `id` is set to 0 (or less) then the meta is deleted.
	 *
	 * @since 3.14.0
	 *
	 * @param int $user_id The {@link WP_User}.
	 * @param int $value   The entity {@link WP_Post} `id`.
	 *
	 * @return bool|int  Meta ID if the key didn't exist, true on successful update, false on failure.
	 */
	public function set_entity( $user_id, $value ) {

		return 0 < $value
			? update_user_meta( $user_id, self::ENTITY_META_KEY, $value )
			: delete_user_meta( $user_id, self::ENTITY_META_KEY );
	}

	/**
	 * Get the {@link WP_Post} `id` of the entity representing a {@link WP_User}.
	 *
	 * @since 3.14.0
	 *
	 * @param int $user_id The {@link WP_User}'s `id`.
	 *
	 * @return string The entity {@link WP_Post} `id` or an empty string if not set.
	 */
	public function get_entity( $user_id ) {

		return get_user_meta( $user_id, self::ENTITY_META_KEY, true );
	}

	/**
	 * Get the user's URI stored in the user's meta.
	 *
	 * @since 3.1.7
	 *
	 * @param int $user_id The user id.
	 *
	 * @return false|string The user's URI or false if not found.
	 */
	private function _get_uri( $user_id ) {

		$user_uri = get_user_meta( $user_id, self::URI_META_KEY, true );

		if ( empty( $user_uri ) ) {
			return false;
		}

		return $user_uri;
	}

	/**
	 * Build an URI for a user.
	 *
	 * @since 3.1.7
	 *
	 * @param int $user_id The user's id.
	 *
	 * @return false|string The user's URI or false in case of failure.
	 */
	private function _build_uri( $user_id ) {

		// Get the user, return false in case of failure.
		if ( false === ( $user = get_userdata( $user_id ) ) ) {
			return false;
		};

		// If the nicename is not set, return a failure.
		if ( empty( $user->user_nicename ) ) {
			return false;
		}

		return wl_configuration_get_redlink_dataset_uri() . "/user/$user->user_nicename";
	}

	/**
	 * Store the URI in user's meta.
	 *
	 * @since 3.1.7
	 *
	 * @param int    $user_id  The user's id.
	 * @param string $user_uri The user's uri.
	 *
	 * @return int|bool Meta ID if the key didn't exist, true on successful update, false on failure.
	 */
	private function _set_uri( $user_id, $user_uri ) {

		return update_user_meta( $user_id, self::URI_META_KEY, $user_uri );
	}

	/**
	 * Get the delete query.
	 *
	 * @since 3.1.7
	 *
	 * @param int $user_id The user id.
	 *
	 * @return false|string The delete query or false in case of failure.
	 */
	private function get_delete_query( $user_id ) {

		// Get the URI, return if there's none.
		if ( false === ( $user_uri = $this->get_uri( $user_id ) ) ) {
			return false;
		}

		// Build the delete query.
		$query = Wordlift_Query_Builder::new_instance()->delete()
		                               ->statement( $user_uri, Wordlift_Query_Builder::RDFS_TYPE_URI, '?o' )
		                               ->build()
		         . Wordlift_Query_Builder::new_instance()->delete()
		                                 ->statement( $user_uri, Wordlift_Query_Builder::RDFS_LABEL_URI, '?o' )
		                                 ->build()
		         . Wordlift_Query_Builder::new_instance()->delete()
		                                 ->statement( $user_uri, Wordlift_Query_Builder::SCHEMA_GIVEN_NAME_URI, '?o' )
		                                 ->build()
		         . Wordlift_Query_Builder::new_instance()->delete()
		                                 ->statement( $user_uri, Wordlift_Query_Builder::SCHEMA_FAMILY_NAME_URI, '?o' )
		                                 ->build()
		         . Wordlift_Query_Builder::new_instance()->delete()
		                                 ->statement( $user_uri, Wordlift_Query_Builder::SCHEMA_URL_URI, '?o' )
		                                 ->build();

		return $query;
	}

	/**
	 * Get the insert query.
	 *
	 * @since 3.1.7
	 *
	 * @param int $user_id The user id.
	 *
	 * @return false|string The insert query or false in case of failure.
	 */
	private function get_insert_query( $user_id ) {

		// Get the URI, return if there's none.
		if ( false === ( $user_uri = $this->get_uri( $user_id ) ) ) {
			return false;
		}

		// Try to get the user data, in case of failure return false.
		if ( false === ( $user = get_userdata( $user_id ) ) ) {
			return false;
		};

		// Build the insert query.
		$query = Wordlift_Query_Builder::new_instance()
		                               ->insert()
		                               ->statement( $user_uri, Wordlift_Query_Builder::RDFS_TYPE_URI, Wordlift_Query_Builder::SCHEMA_PERSON_URI )
		                               ->statement( $user_uri, Wordlift_Query_Builder::RDFS_LABEL_URI, $user->display_name )
		                               ->statement( $user_uri, Wordlift_Query_Builder::SCHEMA_GIVEN_NAME_URI, $user->user_firstname )
		                               ->statement( $user_uri, Wordlift_Query_Builder::SCHEMA_FAMILY_NAME_URI, $user->user_lastname )
		                               ->statement( $user_uri, Wordlift_Query_Builder::SCHEMA_URL_URI, ( ! empty( $user->user_url ) ? $user->user_url : get_author_posts_url( $user_id ) ) )
		                               ->build();

		return $query;
	}

	/**
	 * Mark an editor user as denied from editing entities.
	 * Does nothing if the user is not an editor
	 *
	 * @since 3.14.0
	 *
	 * @param integer $user_id The ID of the user
	 */
	public function deny_editor_entity_create( $user_id ) {

		// Bail out if the user is not an editor.
		if ( ! $this->is_editor( $user_id ) ) {
			return;
		}

		// The user explicitly do not have the capability.
		update_user_meta( $user_id, self::DENY_ENTITY_CREATE_META_KEY, 'yes' );

	}

	/**
	 * Remove the "deny entity editing" mark from an editor user.
	 * Does nothing if the user is not an editor
	 *
	 * @since 3.14.0
	 *
	 * @param integer $user_id The ID of the user
	 */
	public function allow_editor_entity_create( $user_id ) {

		// Bail out if the user is not an editor.
		if ( ! $this->is_editor( $user_id ) ) {
			return;
		}

		// The user explicitly do not have the capability.
		delete_user_meta( $user_id, self::DENY_ENTITY_CREATE_META_KEY );

	}

	/**
	 * Get whether the 'deny editor entity editing' flag is set.
	 *
	 * @since 3.14.0
	 *
	 * @param int $user_id The {@link WP_User} `id`.
	 *
	 * @return int bool True if editing is denied otherwise false.
	 */
	public function is_deny_editor_entity_create( $user_id ) {

		return 'yes' === get_user_meta( $user_id, self::DENY_ENTITY_CREATE_META_KEY, true );
	}

	/**
	 * Check whether the {@link WP_User} with the specified `id` is an editor,
	 * i.e. has the `editor` role.
	 *
	 * @since 3.14.0
	 *
	 * @param int $user_id The {@link WP_User} `id`.
	 *
	 * @return bool True if the {@link WP_User} is an editor otherwise false.
	 */
	public function is_editor( $user_id ) {

		// Get the user.
		$user = get_user_by( 'id', $user_id );

		// Return true, if the user is found and has the `editor` role.
		return is_a( $user, 'WP_User' ) && in_array( 'editor', (array) $user->roles );
	}

	/**
	 * Check if an editor can create entities.
	 *
	 * @since 3.14.0
	 *
	 * @param int $user_id The user id of the user being checked.
	 *
	 * @return bool    false if it is an editor that is denied from edit entities, true otherwise.
	 */
	public function editor_can_create_entities( $user_id ) {

		// Return true if not an editor.
		if ( ! $this->is_editor( $user_id ) ) {
			return true;
		}

		// Check if the user explicitly denied.
		return ! $this->is_deny_editor_entity_create( $user_id );
	}

	/**
	 * Filter capabilities of user.
	 *
	 * Deny the capability of managing and editing entities for some users.
	 *
	 * @since 3.14.0
	 *
	 * @param array $allcaps All the capabilities of the user
	 * @param array $cap     [0] Required capability
	 * @param array $args    [0] Requested capability
	 *                       [1] User ID
	 *                       [2] Associated object ID
	 *
	 * @return array The capabilities array.
	 */
	public function has_cap( $allcaps, $cap, $args ) {
		/*
		 * For entity management/editing related capabilities
		 * check that an editor was not explicitly denied (in user profile)
		 * the capability.
		 */
		if (
			( 'edit_wordlift_entity' == $cap[0] ) ||
			( 'edit_wordlift_entities' == $cap[0] ) ||
			( 'edit_others_wordlift_entities' == $cap[0] ) ||
			( 'publish_wordlift_entities' == $cap[0] ) ||
			( 'read_private_wordlift_entities' == $cap[0] ) ||
			( 'delete_wordlift_entity' == $cap[0] )
		) {
			$user_id = $args[1];

			if ( ! $this->editor_can_create_entities( $user_id ) ) {
				$allcaps[ $cap[0] ] = false;
			}
		}

		return $allcaps;
	}
}
