<?php
/**
 * Async Tasks: Batch Analysis Request Async Task.
 *
 * Request a batch analysis asynchronously.
 *
 * @since      3.14.2
 * @package    Wordlift
 * @subpackage Wordlift/includes/wp-async-task
 */

/**
 * Define the {@link Wordlift_Sparql_Query_Async_Task} {@link WP_Async_Task}.
 *
 * @since      3.14.2
 * @package    Wordlift
 * @subpackage Wordlift/includes/wp-async-task
 */
class Wordlift_Batch_Analysis_Request_Async_Task extends WP_Async_Task {

	/**
	 * The protected $action property should be set to the action to which you
	 * wish to attach the asynchronous task. For example, if you want to spin
	 * off an asynchronous task whenever a post gets saved, you would set this
	 * to save_post.
	 *
	 * @since  3.14.2
	 * @access protected
	 * @var string $action The action to which you wish to attach the
	 *                     asynchronous task.
	 */
	protected $action = 'wl_batch_analysis_request';

	/**
	 * A {@link Wordlift_Log_Service} instance.
	 *
	 * @since  3.14.2
	 * @access private
	 * @var \Wordlift_Log_Service $log A {@link Wordlift_Log_Service} instance.
	 */
	private $log;

	/**
	 * Create a {@link Wordlift_Sparql_Query_Async_Task} instance.
	 *
	 * @since 3.14.2
	 *
	 * @param int $auth_level The authentication level to use (see above)
	 */
	public function __construct( $auth_level = self::BOTH ) {
		parent::__construct( $auth_level );

		$this->log = Wordlift_Log_Service::get_logger( 'Wordlift_Batch_Analysis_Request_Async_Task' );

	}

	/**
	 * @inheritdoc
	 */
	protected function prepare_data( $data ) {

		// Return the link setting.
		return array( 'link' => $data[0] );
	}

	/**
	 * @inheritdoc
	 */
	protected function run_action() {

		// Bail out if there's no link setting.
		if ( ! isset( $_POST['link'] ) ) {
			$this->log->warn( '`link` parameter not found.' );

			return;
		}

		// Run the asynchronous action.
		do_action( "wp_async_$this->action", $_POST['link'] );

	}

}
