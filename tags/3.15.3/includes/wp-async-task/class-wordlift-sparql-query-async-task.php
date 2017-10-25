<?php
/**
 * Async Tasks: Sparql Query Async Task.
 *
 * Executes a Sparql Query asynchronously.
 *
 * @since      3.13.2
 * @package    Wordlift
 * @subpackage Wordlift/includes/wp-async-task
 */

/**
 * Define the {@link Wordlift_Sparql_Query_Async_Task} {@link Wordlift_Async_Task}.
 *
 * @since      3.13.2
 * @package    Wordlift
 * @subpackage Wordlift/includes/wp-async-task
 */
class Wordlift_Sparql_Query_Async_Task extends Wordlift_Async_Task {

	/**
	 * The protected $action property should be set to the action to which you
	 * wish to attach the asynchronous task. For example, if you want to spin
	 * off an asynchronous task whenever a post gets saved, you would set this
	 * to save_post.
	 *
	 * @since  3.13.2
	 * @access protected
	 * @var string $action The action to which you wish to attach the
	 *                     asynchronous task.
	 */
	protected $action = 'wl_run_sparql_query';

	/**
	 * A {@link Wordlift_Log_Service} instance.
	 *
	 * @since  3.13.2
	 * @access private
	 * @var \Wordlift_Log_Service $log A {@link Wordlift_Log_Service} instance.
	 */
	private $log;

	/**
	 * Create a {@link Wordlift_Sparql_Query_Async_Task} instance.
	 *
	 * @since 3.13.2
	 *
	 * @param int $auth_level The authentication level to use (see above)
	 */
	public function __construct( $auth_level = self::BOTH ) {
		parent::__construct( $auth_level );

		$this->log = Wordlift_Log_Service::get_logger( 'Wordlift_Sparql_Query_Async_Task' );

	}


	/**
	 * @inheritdoc
	 */
	protected function prepare_data( $data ) {

		// Return the request id, which is used to get the sparql queries stored
		// in the temporary files.
		return array( 'request_id' => $data[0] );
	}

	/**
	 * @inheritdoc
	 */
	protected function run_action() {

		// Bail out if there's no request id.
		if ( ! isset( $_POST['request_id'] ) ) {
			$this->log->warn( 'Request Id not found.' );

			return;
		}

		// Run the asynchronous action.
		do_action( "wl_async_$this->action", $_POST['request_id'] );

	}

}
