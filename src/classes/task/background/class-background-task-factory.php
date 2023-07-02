<?php

namespace Wordlift\Task\Background;

use Exception;
use Wordlift\Assertions;
use Wordlift\Task\Task;

/**
 * A Factory class that creates {@link Background_Task}s.
 *
 * @author David Riccitelli <david@wordlift.io>
 * @since 3.39.0
 */
class Background_Task_Factory {

	/**
	 * Create a {@link Background_Task} given the provided task and route.
	 *
	 * @param Task   $task The target task.
	 * @param string $route The route name.
	 * @param string $page_id The page id.
	 * @param string $page_title The page title.
	 *
	 * @return Background_Task
	 * @throws Exception in case of invalid arguments.
	 */
	public static function create( $task, $route, $page_id, $page_title ) {
		self::assertions( $task, $route );
		$background_task       = Background_Task::create( $task );
		$background_task_route = Background_Task_Route::create( $background_task, $route );
		Background_Task_Page::create( $page_title, $page_id, $background_task_route );
		return $background_task;
	}

	/**
	 * Create a {@link Background_Task} given the provided task and route.
	 *
	 * @param Task   $task The target task.
	 * @param string $route The route name.
	 * @param string $page_id The page id.
	 * @param string $page_title The page title.
	 *
	 * @return \Wordlift\Task\Action_Scheduler\Background_Task
	 * @throws Exception in case of invalid arguments.
	 */
	public static function create_action_scheduler_task( $hook, $group, $task, $route, $page_id, $page_title, $batch_size = 5 ) {
		self::assertions( $task, $route );
		$background_task       = new \Wordlift\Task\Action_Scheduler\Background_Task(
			$hook,
			$group,
			$task,
			"_{$hook}_",
			$batch_size
		);
		$background_task_route = Background_Task_Route::create( $background_task, $route );
		Background_Task_Page::create( $page_title, $page_id, $background_task_route );
		return $background_task;
	}

	/**
	 * @param Task  $task
	 * @param $route
	 *
	 * @return void
	 * @throws Exception in case of invalid arguments.
	 */
	private static function assertions( Task $task, $route ) {
		Assertions::is_a( $task, 'Wordlift\Task\Task' );
		Assertions::starts_with( $route, '/', __( 'The route must start with a slash.', 'wordlift' ) );
	}

}
