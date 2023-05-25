<?php
/**
 * Add
 *
 * @since 3.34.1
 *
 * @see
 */

use Wordlift\Task\All_Posts_Task;
use Wordlift\Task\Background\Background_Task;
use Wordlift\Task\Background\Background_Task_Page;
use Wordlift\Task\Background\Background_Task_Route;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Check if the feature is enabled.
if ( ! apply_filters( 'wl_feature__enable__cleanup', false ) ) {
	return;
}

$task = new All_Posts_Task( array( 'Wordlift\Cleanup\Post_Handler', 'fix' ) );

$background_task       = Background_Task::create( $task );
$background_task_route = Background_Task_Route::create( $background_task, '/cleanup' );
Background_Task_Page::create( __( 'Cleanup', 'wordlift' ), 'cleanup', $background_task_route );
