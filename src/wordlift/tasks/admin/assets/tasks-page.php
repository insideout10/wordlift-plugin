<?php
/**
 * This page consists of some number inputs, checkbox, button and a progressbar.
 *
 * @link       https://wordlift.io
 * @since      1.0.0
 *
 * @package    Wordlift_Framework\Tasks\Admin\Assets
 */

?>
<div class="wrap">
	<h1><?php esc_html_e( 'Tasks', 'wordlift' ); ?></h1>

	<?php
    // phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UndefinedVariable
	foreach ( $this->task_ajax_adapters_registry->get_task_ajax_adapters() as $task_ajax_adapter ) {
		$task = $task_ajax_adapter->get_task();
		?>
		<div class="wl-task">
			<h2><?php esc_html( $task->get_label() ); ?></h2>
			<div class="wl-task__progress" style="border: 1px solid #23282D; height: 20px; margin: 8px 0;">
				<div class="wl-task__progress__bar"
					 style="width:0;background: #0073AA; text-align: center; height: 100%; color: #fff;"></div>
			</div>

			<button
				type="button"
				class="button button-large button-primary"
				data-action="<?php echo esc_attr( $task->get_id() ); ?>"
				data-nonce="<?php echo esc_attr( wp_create_nonce( $task->get_id() ) ); ?>"
			><?php esc_html_e( 'Start', 'wordlift' ); ?></button>

		</div>
		<?php
	}
	?>

</div>
