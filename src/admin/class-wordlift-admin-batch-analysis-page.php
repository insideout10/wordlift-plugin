<?php
/**
 * Pages: Batch analysis admin page.
 *
 * Handles the WordLift batch analysis admin page.
 *
 * @since      3.14.0
 * @package    Wordlift
 * @subpackage Wordlift/admin
 */

/**
 * Define the {@link Wordlift_Batch_analysis_page} class.
 *
 * @since      3.14.0
 * @package    Wordlift
 * @subpackage Wordlift/admin
 */
class Wordlift_Batch_Analysis_page  extends Wordlift_Admin_Page {

	/**
	 * The {@link Wordlift_Batch_analysis_Service} instance.
	 *
	 * @since 3.14.0
	 *
	 * @var \Wordlift_Batch_analysis_Service $batch_analyze_service The {@link Wordlift_Batch_analysis_Service} instance.
	 */
	public $batch_analysis_service;

	/**
	 * The {@link Wordlift_Batch_Analysis_page} instance.
	 *
	 * @since 3.14.0
	 *
	 * @var \Wordlift $plugin The {@link Wordlift} plugin instance.
	 */
	public function __construct( $batch_analysis_service ) {
		$this->batch_analysis_service = $batch_analysis_service;
	}

	/**
	 * @inheritdoc
	 */
	function get_parent_slug() {

		return 'wl_admin_menu';
	}

	/**
	 * @inheritdoc
	 */
	function get_capability() {

		return 'manage_options';
	}

	/**
	 * @inheritdoc
	 */
	function get_page_title() {

		return __( 'Batch Analysis', 'wordlift' );
	}

	/**
	 * @inheritdoc
	 */
	function get_menu_title() {

		return __( 'Batch Analysis', 'wordlift' );
	}

	/**
	 * @inheritdoc
	 */
	function get_menu_slug() {

		return 'wl_batch_analysis_menu';
	}

	/**
	 * @inheritdoc
	 */
	function get_partial_name() {

		return 'wordlift-admin-batch-analysis-page.php';
	}

	public function batch_screen() {
?>
		<div class="wrap">
			<h2><?php esc_html_e( 'WordLift Batch Analyze Monitor', 'wordlift' )?></h2>
		<?php
		if ( isset( $_POST['wl_linktype'] ) ) {
			$pids = explode( ',', $_POST['wl_pids'] );
			$batch = get_option( 'wl_analyze_batch', array(
														'queue' => array(),
														'processing' => array(),
			) );
			foreach ( $pids as $pid ) {
				if ( intval( $pid ) != $pid ) {
					continue;
				}
				$batch['queue'][ $pid ] = array( 'id' => $pid, 'link' => $_POST['wl_linktype'] );
				update_option( 'wl_analyze_batch', $batch );
				$this->batch_analyze();
			}
		} elseif ( isset( $_GET['pid'] ) ) {
			$pids = explode( ',', $_GET['pid'] );
			?>
			<form method="post" action="">
				<p><?php esc_html_e( 'You are about to send the following posts for batch analyzing', 'wordlift' );?></p>
				<ul>
					<?php
					foreach ( $pids as $pid ) {
						echo '<li><a href="' . get_edit_post_link( $pid ) . '">' . get_the_title( $pid ) . '</a></li>';
					}
					?>
				</ul>
				<p>
					<label for="wl_linktype">
						<?php esc_html_e( 'Type of entity linking', 'wordlif' ); ?>
					</label>
					<select id="wl_linktype" name="wl_linktype">
						<option value="default"><?php esc_html_e( 'Ignore', 'wordlift' )?></option>
						<option value="yes"><?php esc_html_e( 'Link', 'wordlift' )?></option>
						<option value="no"><?php esc_html_e( 'No Link', 'wordlift' )?></option>
						<option value="first"><?php esc_html_e( 'Link first', 'wordlift' )?></option>
					</select>
					<input type="hidden" name="wl_pids" value="<?php echo esc_attr( $_GET['pid'] );?>">
				</p>
				<?php submit_button( __( 'Start', 'wordlift' ) ); ?>
			</form>
		<?php } ?>

			<?php
				$batch = get_option( 'wl_analyze_batch', array(
														'queue' => array(),
														'processing' => array(),
				) );
			?>
			<h3><?php esc_html_e( 'Posts in queue', 'wordlift' )?></h3>
			<?php
			$queue = $batch['queue'];
			if ( empty( $queue ) ) {
				esc_html_e( 'Nothing is currently in queue', 'wordlift' );
			} else {
				echo '<ul>';
				foreach ( $queue as $item ) {
					$pid = $item['id'];
					echo '<li><a href="' . get_edit_post_link( $pid ) . '">' . get_the_title( $pid ) . '</a></li>';
				}
				echo '</ul>';
			}
			?>
			<h3><?php esc_html_e( 'Posts being processed', 'wordlift' )?></h3>
			<?php
			$queue = $batch['processing'];
			if ( empty( $queue ) ) {
				esc_html_e( 'Nothing is currently in queue', 'wordlift' );
			} else {
				echo '<ul>';
				foreach ( $queue as $item ) {
					$pid = $item['id'];
					echo '<li><a href="' . get_edit_post_link( $pid ) . '">' . get_the_title( $pid ) . '</a></li>';
				}
				echo '</ul>';
			}
			?>
		</div>
		<?php
	}

	public function batch_analyze() {
		$batch = get_option( 'wl_analyze_batch', array(
												'queue' => array(),
												'processing' => array(),
		) );
		if ( ! empty( $batch['queue'] ) ) {
			/*
			 * If we have any post waiting in the queue, send a request
			 * to the wordlift server to process it, when the requests includes
			 * the content and the id of the post.
			 */
			$item = array_pop( $batch['queue'] );
			if ( $item ) { // just being extra careful.
				$post = get_post( $item['id'] );
				$url = wl_configuration_get_batch_analysis_url();
				$param = array(
					'id'	=> $item['id'],
					'key'	=> wl_configuration_get_key(),
					'content' => $post->post_content,
					'contentLanguage' => Wordlift_Configuration_Service::get_instance()->get_language_code(),
					'version' => $this->plugin->get_version(),
					'links' => $item['link'],
				);
				$args = array_merge_recursive( unserialize( WL_REDLINK_API_HTTP_OPTIONS ), array(
					'method'      => 'POST',
					'headers'     => array(
						'Accept'       => 'application/json',
						'Content-type' => 'application/json; charset=UTF-8',
					),
					// we need to downgrade the HTTP version in this case since chunked encoding is dumping numbers in the response.
					'httpversion' => '1.0',
					'body'        => wp_json_encode( $param ),
				) );

				$result = wp_remote_post( $url, $args );
				// If it's an error log it.
				if ( is_wp_error( $response ) ) {
					$batch['queue'][ $post_id ] = $item;
					$message = "An error occurred while requesting a batch analysis to $url: {$response->get_error_message()}";
					Wordlift_Log_Service::get_logger( 'wl_analyze_content' )->error( $message );
					throw new Exception( $response->get_error_message(), $response->get_error_code() );
				} else {
					$batch['processing'][ $item['id'] ] = $item;
				}
			}
		}
		if ( ! empty( $batch['processing'] ) ) {
			/*
		 	 * If we have any post waiting for a reply to any post, send a status
			 * request to the server.
			 */
			$item = array_pop( $batch['processing'] );
			if ( $item ) { // just being extra careful.
				$post = get_post( $item['id'] );
				$apiurl = wl_configuration_get_batch_analysis_url();
				$id	= $item['id'];
				$key = wl_configuration_get_key();
				$url = $apiurl . '/' . $id . '?key=' . $key;
				$result = wp_remote_get( $url, unserialize( WL_REDLINK_API_HTTP_OPTIONS ) );
				// If it's an error log it.
				if ( is_wp_error( $response ) ) {
					$batch['queue'][ $post_id ] = $item;
					$message = "An error occurred while requesting a batch analysis to $url: {$response->get_error_message()}";
					Wordlift_Log_Service::get_logger( 'wl_analyze_content' )->error( $message );
					throw new Exception( $response->get_error_message(), $response->get_error_code() );
				}
			}
		}
		update_option( 'wl_analyze_batch', $batch );
		if ( ! empty( $batch['queue'] ) || ! empty( $batch['processing'] ) ) {
			wp_schedule_single_event( time(), 'wl_batch_analyze' );
		}
	}
}
