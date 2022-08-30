<?php

/**
 * Displays notices in the admin UI.
 *
 * @since 3.2.0
 */
class Wordlift_Notice_Service {

	/**
	 * The template used to display notices. The <em>notice dismissible</em> style classes make this notice dismissible
	 * on the WordPress UI (via a small X button on the right side of the notice).
	 *
	 * @since 3.2.0
	 */
	const TEMPLATE = '<div class="wl-notice notice is-dismissible %s"><p>%s</p></div>';

	/**
	 * The standard WordPress <em>update</em> style class.
	 *
	 * @since 3.2.0
	 */
	const UPDATE = 'update';

	/**
	 * The standard WordPress <em>update-nag</em> style class.
	 *
	 * @since 3.2.0
	 */
	const UPDATE_NAG = 'update-nag';

	/**
	 * The standard WordPress <em>error</em> style class.
	 *
	 * @since 3.2.0
	 */
	const ERROR = 'error';

	/**
	 * A custom WordLift css style class used for WordLift suggestions.
	 *
	 * @since 3.3.0
	 */
	const SUGGESTION = 'wl-suggestion';

	/**
	 * The array of notices.
	 *
	 * @since 3.2.0
	 * @access private
	 * @var array $notices The array of notices.
	 */
	private $notices = array();

	/**
	 * A singleton instance of the Notice service.
	 *
	 * @since 3.2.0
	 * @access private
	 * @var \Wordlift_Notice_Service $instance A singleton instance of the Notice service.
	 */
	private static $instance;

	/**
	 * Create an instance of the Notice service.
	 *
	 * @since 3.2.0
	 */
	public function __construct() {
		/**
		 * Filter: wl_feature__enable__notices.
		 *
		 * @param bool whether the notices needs to be enabled or not.
		 *
		 * @return bool
		 * @since 3.27.6
		 */
		if ( apply_filters( 'wl_feature__enable__notices', true ) ) {
			// Hook to be called when to display notices.
			add_action( 'admin_notices', array( $this, 'admin_notices' ) );
		}
		self::$instance = $this;

	}

	/**
	 * Get the singleton instance of the Notice service.
	 *
	 * @return \Wordlift_Notice_Service The singleton instance of the Notice service.
	 * @since 3.2.0
	 */
	public static function get_instance() {

		return self::$instance;
	}

	/**
	 * Add a notice.
	 *
	 * @param string $class The css class.
	 * @param string $message The message.
	 *
	 * @since 3.2.0
	 */
	public function add( $class, $message ) {

		$this->notices[] = sprintf( self::TEMPLATE, $class, $this->transform( $message ) );

	}

	/**
	 * Add an update notice (message with a white background and a green left border).
	 *
	 * @param string $message The message to display.
	 *
	 * @since 3.2.0
	 */
	public function add_update( $message ) {

		$this->add( self::UPDATE, $message );

	}

	/**
	 * Add an update nag notice (message with a white background and a yellow left border).
	 *
	 * @param string $message The message to display.
	 *
	 * @since 3.2.0
	 */
	public function add_update_nag( $message ) {

		$this->add( self::UPDATE_NAG, $message );

	}

	/**
	 * Add an error notice (message with a white background and a red left border).
	 *
	 * @param string $message The message to display.
	 *
	 * @since 3.2.0
	 */
	public function add_error( $message ) {

		$this->add( self::ERROR, $message );

	}

	/**
	 * Add a suggestion notice (message with a white background and a WordLift brand colored left border).
	 *
	 * @param string $message The message to display.
	 *
	 * @since 3.3.0
	 */
	public function add_suggestion( $message ) {

		$this->add( self::SUGGESTION, $message );

	}

	/**
	 * Print out the notices when the admin_notices action is called.
	 *
	 * @since 3.2.0
	 */
	public function admin_notices() {

		foreach ( $this->notices as $notice ) {
			echo wp_kses(
				$notice,
				array(
					'div' => array( 'class' => array() ),
					'p'   => array(),
				)
			);
		}

	}

	/**
	 * Transform message depending on message type. Return a string
	 *
	 * @param string $message The message.
	 *
	 * @since 3.3.0
	 */
	private function transform( $message ) {

		switch ( gettype( $message ) ) {
			case 'array':
				return implode( '<br />', $message );
			default:
				return $message;
		}

	}

}
