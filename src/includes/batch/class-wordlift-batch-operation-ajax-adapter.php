<?php
/**
 * Adapters: Batch Operation Ajax Adapter.
 *
 * @since 3.20.0
 * @package Wordlift
 * @subpackage Wordlift/includes/batch
 */

/**
 * Class Wordlift_Batch_Operation_Ajax_Adapter
 *
 * @since 3.20.0
 */
class Wordlift_Batch_Operation_Ajax_Adapter {

	/**
	 * The access levels.
	 *
	 * @since 3.20.0
	 */
	const ACCESS_ANONYMOUS = 1;
	const ACCESS_ADMIN     = 2;
	const ACCESS_ALL       = 3;

	/**
	 * A {@link Wordlift_Batch_Operation_Interface} instance.
	 *
	 * @since 3.20.0
	 * @access private
	 * @var \Wordlift_Batch_Operation_Interface $operation A {@link Wordlift_Batch_Operation_Interface} instance.
	 */
	private $operation;

	/**
	 * The ajax action name.
	 *
	 * @since 3.20.0
	 * @access private
	 * @var string $action The ajax action name.
	 */
	private $action;

	/**
	 * Wordlift_Batch_Operation_Ajax_Adapter constructor.
	 *
	 * @param \Wordlift_Batch_Operation_Interface $operation The batch operation.
	 * @param string                              $action The action name.
	 * @param int                                 $access The access level.
	 */
	public function __construct( $operation, $action, $access = self::ACCESS_ADMIN ) {

		$this->operation = $operation;

		if ( $access & self::ACCESS_ADMIN ) {
			add_action( "wp_ajax_$action", array( $this, 'process' ) );
			add_action( "wp_ajax_{$action}_count", array( $this, 'count' ) );

			// Add the nonce for the `schemaorg_sync` action.
			add_filter( 'wl_admin_settings', array( $this, 'add_nonce' ) );
		}

		if ( $access & self::ACCESS_ANONYMOUS ) {
			add_action( "wp_ajax_nopriv_$action", array( $this, 'process' ) );
			add_action( "wp_ajax_nopriv_{$action}_count", array( $this, 'count' ) );
		}

		$this->action = $action;
	}

	/**
	 * Hook to `wl_admin_settings`, adds the nonce.
	 *
	 * @param array $params An array of settings.
	 *
	 * @return array The updated array of settings.
	 * @since 3.20.0
	 */
	public function add_nonce( $params ) {

		return array_merge(
			$params,
			array(
				"{$this->action}_nonce" => $this->create_nonce(),
			)
		);
	}

	/**
	 * Process the requested operation.
	 *
	 * @since 3.20.0
	 */
	public function process() {
		$nonce = isset( $_POST['_nonce'] ) ? sanitize_text_field( wp_unslash( (string) $_POST['_nonce'] ) ) : '';
		// Validate the nonce.
		if ( ! wp_verify_nonce( $nonce, $this->action ) ) {
			wp_send_json_error( 'Invalid nonce.' );
		}

		$offset = isset( $_POST['offset'] ) ? (int) $_POST['offset'] : 0;
		$limit  = isset( $_POST['limit'] ) ? (int) $_POST['limit'] : 10;

		// Run the batch operation.
		$result = $this->operation->process( $offset, $limit );

		// Send the results along with a potentially updated nonce.
		wp_send_json_success(
			array_merge(
				$result,
				array(
					'_nonce' => $this->create_nonce(),
				)
			)
		);

	}

	/**
	 * Count the number of elements that would be affected by the operation.
	 *
	 * @since 3.20.0
	 */
	public function count() {

		// Validate the nonce.
		$nonce = isset( $_POST['_nonce'] ) ? sanitize_text_field( wp_unslash( (string) $_POST['_nonce'] ) ) : '';
		if ( ! wp_verify_nonce( $nonce, $this->action ) ) {
			wp_send_json_error( 'Invalid nonce.' );
		}

		// Run the batch operation.
		$result = $this->operation->count();

		// Send the results along with a potentially updated nonce.
		wp_send_json_success(
			array(
				'count'  => $result,
				'_nonce' => $this->create_nonce(),
			)
		);

	}

	/**
	 * Create a nonce for the ajax operation.
	 *
	 * @return string The nonce.
	 * @since 3.20.0
	 */
	public function create_nonce() {

		return wp_create_nonce( $this->action );
	}

}
