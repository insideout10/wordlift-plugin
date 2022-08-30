<?php
/**
 * Services: User Service.
 *
 * @since 3.1.7
 * @package Wordlift
 * @subpackage Wordlift/includes
 */

/**
 * Manage user-related functions. This class receives notifications when a post is created/updated and pushes the author's
 * data to the triple store. It does NOT receive notification when a user is create/updated because we don't want to send
 * to the triple stores users that eventually do not write posts (therefore if user data change, the triple store is updated
 * only when the user creates/updates a new post).
 *
 * @since 3.1.7
 * @package Wordlift
 * @subpackage Wordlift/includes
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
	 * Create an instance of the User service.
	 *
	 * @since 3.1.7
	 */
	protected function __construct() {

		$this->log_service = Wordlift_Log_Service::get_logger( 'Wordlift_User_Service' );

		add_filter( 'user_has_cap', array( $this, 'has_cap' ), 10, 3 );

	}

	/**
	 * The singleton instance of the User service.
	 *
	 * @since  3.1.7
	 * @access private
	 * @var \Wordlift_User_Service $user_service The singleton instance of the User service.
	 */
	private static $instance = null;

	/**
	 * Get the singleton instance of the User service.
	 *
	 * @return \Wordlift_User_Service The singleton instance of the User service.
	 * @since 3.1.7
	 */
	public static function get_instance() {

		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Get the URI for a user.
	 *
	 * @param int $user_id The user id
	 *
	 * @return false|string The user's URI or false in case of failure.
	 * @since 3.1.7
	 */
	public function get_uri( $user_id ) {

		// Try to get the URI stored in the user's meta and return it if available.
		$dataset_uri = wl_configuration_get_redlink_dataset_uri();
		$user_uri    = $this->_get_uri( $user_id );
		if ( ! empty( $dataset_uri ) && ! empty( $user_uri ) && 0 === strpos( $user_uri, $dataset_uri ) ) {
			return $user_uri;
		}

		// Try to build an URI, return false in case of failure.
		$user_uri = $this->_build_uri( $user_id );
		if ( false === $user_uri ) {
			return false;
		}

		// Store the URI for future requests (we need a "permanent" URI).
		$this->_set_uri( $user_id, $user_uri );

		return $user_uri;
	}

	/**
	 * Set the `id` of the entity representing a {@link WP_User}.
	 *
	 * If the `id` is set to 0 (or less) then the meta is deleted.
	 *
	 * @param int $user_id The {@link WP_User}.
	 * @param int $value The entity {@link WP_Post} `id`.
	 *
	 * @return bool|int  Meta ID if the key didn't exist, true on successful update, false on failure.
	 * @since 3.14.0
	 */
	public function set_entity( $user_id, $value ) {

		return 0 < $value
			? update_user_meta( $user_id, self::ENTITY_META_KEY, $value )
			: delete_user_meta( $user_id, self::ENTITY_META_KEY );
	}

	/**
	 * Get the {@link WP_Post} `id` of the entity representing a {@link WP_User}.
	 *
	 * @param int $user_id The {@link WP_User}'s `id`.
	 *
	 * @return string|false The entity {@link WP_Post} `id` or an empty string if not set or false if the object id is invalid
	 * @since 3.14.0
	 */
	public function get_entity( $user_id ) {

		return get_user_meta( $user_id, self::ENTITY_META_KEY, true );
	}

	/**
	 * Get the user's URI stored in the user's meta.
	 *
	 * @param int $user_id The user id.
	 *
	 * @return false|string The user's URI or false if not found.
	 * @since 3.1.7
	 */
	// phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
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
	 * @param int $user_id The user's id.
	 *
	 * @return false|string The user's URI or false in case of failure.
	 * @since 3.1.7
	 */
	// phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
	private function _build_uri( $user_id ) {

		// Get the user, return false in case of failure.
		$user = get_userdata( $user_id );
		if ( false === $user ) {
			return false;
		};

		// If the nicename is not set, return a failure.
		if ( empty( $user->user_nicename ) ) {
			return false;
		}

		/**
		 * @since 3.27.7 changed `user` to `author` to avoid potential clashes with CPTs ( `author` is reserved
		 *  https://developer.wordpress.org/reference/functions/register_post_type/#reserved-post-types )
		 */
		return untrailingslashit( wl_configuration_get_redlink_dataset_uri() ) . "/author/$user->user_nicename";
	}

	/**
	 * Store the URI in user's meta.
	 *
	 * @param int    $user_id The user's id.
	 * @param string $user_uri The user's uri.
	 *
	 * @return int|bool Meta ID if the key didn't exist, true on successful update, false on failure.
	 * @since 3.1.7
	 */
	// phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
	private function _set_uri( $user_id, $user_uri ) {

		return update_user_meta( $user_id, self::URI_META_KEY, $user_uri );
	}

	/**
	 * Mark an editor user as denied from editing entities.
	 * Does nothing if the user is not an editor
	 *
	 * @param integer $user_id The ID of the user
	 *
	 * @since 3.14.0
	 */
	public function deny_editor_entity_create( $user_id ) {

		// Bail out if the user is not an editor.
		if ( ! $this->is_editor( $user_id ) ) {
			return;
		}

		// The user explicitly do not have the capability.
		update_user_option( $user_id, self::DENY_ENTITY_CREATE_META_KEY, 'yes' );

	}

	/**
	 * Remove the "deny entity editing" mark from an editor user.
	 * Does nothing if the user is not an editor
	 *
	 * @param integer $user_id The ID of the user
	 *
	 * @since 3.14.0
	 */
	public function allow_editor_entity_create( $user_id ) {

		// Bail out if the user is not an editor.
		if ( ! $this->is_editor( $user_id ) ) {
			return;
		}

		// The user explicitly do not have the capability.
		delete_user_option( $user_id, self::DENY_ENTITY_CREATE_META_KEY );

	}

	/**
	 * Get whether the 'deny editor entity editing' flag is set.
	 *
	 * @param int $user_id The {@link WP_User} `id`.
	 *
	 * @return int bool True if editing is denied otherwise false.
	 * @since 3.14.0
	 */
	public function is_deny_editor_entity_create( $user_id ) {

		return 'yes' === get_user_option( self::DENY_ENTITY_CREATE_META_KEY, $user_id );
	}

	/**
	 * Check whether the {@link WP_User} with the specified `id` is an editor,
	 * i.e. has the `editor` role.
	 *
	 * @param int $user_id The {@link WP_User} `id`.
	 *
	 * @return bool True if the {@link WP_User} is an editor otherwise false.
	 * @since 3.14.0
	 */
	public function is_editor( $user_id ) {

		// Get the user.
		$user = get_user_by( 'id', $user_id );

		// Return true, if the user is found and has the `editor` role.
		return is_a( $user, 'WP_User' ) && in_array( 'editor', (array) $user->roles, true );
	}

	/**
	 * Check if an editor can create entities.
	 *
	 * @param int $user_id The user id of the user being checked.
	 *
	 * @return bool    false if it is an editor that is denied from edit entities, true otherwise.
	 * @since 3.14.0
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
	 * @param array $allcaps All the capabilities of the user
	 * @param array $cap [0] Required capability
	 * @param array $args [0] Requested capability
	 *                       [1] User ID
	 *                       [2] Associated object ID
	 *
	 * @return array The capabilities array.
	 * @since 3.14.0
	 */
	public function has_cap( $allcaps, $cap, $args ) {
		/*
		 * For entity management/editing related capabilities
		 * check that an editor was not explicitly denied (in user profile)
		 * the capability.
		 */

		/*
		 * Need protection against the case of edit_user and likes which do not
		 * require a capability, just request one.
		 */
		if ( empty( $cap ) || ! isset( $cap[0] ) ) {
			return $allcaps;
		}

		if (
			( 'edit_wordlift_entity' === $cap[0] ) ||
			( 'edit_wordlift_entities' === $cap[0] ) ||
			( 'edit_others_wordlift_entities' === $cap[0] ) ||
			( 'publish_wordlift_entities' === $cap[0] ) ||
			( 'read_private_wordlift_entities' === $cap[0] ) ||
			( 'delete_wordlift_entity' === $cap[0] ) ||
			( 'delete_wordlift_entities' === $cap[0] ) ||
			( 'delete_others_wordlift_entities' === $cap[0] ) ||
			( 'delete_published_wordlift_entities' === $cap[0] ) ||
			( 'delete_private_wordlift_entities' === $cap[0] )
		) {
			$user_id = $args[1];

			if ( ! $this->editor_can_create_entities( $user_id ) ) {
				$allcaps[ $cap[0] ] = false;
			}
		}

		return $allcaps;
	}

}
