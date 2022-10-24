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
	 * @return void
	 * @throws Exception in case of invalid arguments.
	 */
	public static function create( $task, $route, $page_id, $page_title ) {
		Assertions::is_a( $task, 'Wordlift\Task\Task' );
		Assertions::starts_with( $route, '/', __( 'The route must start with a slash.', 'wordlift' ) );

		$background_task       = Background_Task::create( $task );
		$background_task_route = Background_Task_Route::create( $background_task, $route );
		Background_Task_Page::create( $page_title, $page_id, $background_task_route );
	}

}
