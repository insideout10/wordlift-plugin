<?php
/**
 * Add
 * @since 3.34.1
 *
 * @see
 */

use Wordlift\Cleanup\Cleanup_Task;
use Wordlift\Tasks\Admin\Tasks_Page;
use Wordlift\Tasks\Task_Ajax_Adapters_Registry;
use Wordlift\Tasks\Task_Ajax_Adapter;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/*
if ( apply_filters( 'wl_feature__enable__entity-annotation-cleanup', false ) ) {
	// Setup entity annotation cleanup admin page.
	new Cleanup_Page();
}*/
$task_ajax_adapters_registry = new Task_Ajax_Adapters_Registry();
$task_entity_annotation_cleanup = new Cleanup_Task();
$task_entity_annotation_cleanup_ajax_adapter = new Task_Ajax_Adapter( $task_entity_annotation_cleanup );
$task_ajax_adapters_registry->register( $task_entity_annotation_cleanup_ajax_adapter );

new Tasks_Page( $task_ajax_adapters_registry );


