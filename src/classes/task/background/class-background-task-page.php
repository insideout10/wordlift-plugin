<?php

namespace Wordlift\Task\Background;

use Exception;
use Wordlift;
use Wordlift\Assertions;

class Background_Task_Page {

	private $title;

	private $menu_slug;

	/**
	 * @var Background_Task_Route $background_task_route
	 */
	private $background_task_route;

	/**
	 * @throws Exception if one or more parameters are invalid.
	 */
	public function __construct( $title, $menu_slug, $background_task_route ) {
		Assertions::not_empty( $title, '`$title` cannot be empty.' );
		Assertions::not_empty( $menu_slug, '`$menu_slug` cannot be empty.' );
		Assertions::is_a( $background_task_route, 'Wordlift\Task\Background\Background_Task_Route', '`Background_Task_Route` must be of type `Wordlift\Task\Background\Background_Route`.' );

		add_action( 'admin_menu', array( $this, 'admin_menu' ) );

		$this->title                 = $title;
		$this->menu_slug             = $menu_slug;
		$this->background_task_route = $background_task_route;
	}

	/**
	 * @throws Exception if one or more parameters are invalid.
	 */
	public static function create( $title, $menu_slug, $background_route ) {
		return new self( $title, $menu_slug, $background_route );
	}

	public function admin_menu() {

		add_submenu_page(
			'wl_admin_menu',
			$this->title,
			$this->title,
			'manage_options',
			$this->menu_slug,
			array(
				$this,
				'render',
			)
		);

	}

	public function render() {

		wp_enqueue_style(
			'wl-task-page',
			plugin_dir_url( __FILE__ ) . 'assets/task-page.css',
			array(),
			Wordlift::get_instance()->get_version(),
			'all'
		);

		wp_enqueue_script(
			'wl-task-page',
			plugin_dir_url( __FILE__ ) . 'assets/task-page.js',
			array( 'wp-api' ),
			WORDLIFT_VERSION,
			false
		);

		wp_localize_script( 'wl-task-page', '_wlTaskPageSettings', array( 'rest_path' => $this->background_task_route->get_rest_path() ) );
		?>
		<div class="wrap">
			<h2><?php echo esc_html( $this->title ); ?></h2>

			<div class="wl-task__progress" style="border: 1px solid #23282D; height: 20px; margin: 8px 0;">
				<div class="wl-task__progress__bar"
					 style="width:0;background: #0073AA; text-align: center; height: 100%; color: #fff;"></div>
			</div>

			<button id="wl-start-btn" type="button" class="button button-large button-primary">
			<?php
				esc_html_e( 'Start', 'wordlift' );
			?>
				</button>
			<button id="wl-stop-btn" type="button" class="button button-large button-primary hidden">
			<?php
				esc_html_e( 'Stop', 'wordlift' );
			?>
				</button>

		</div>
		<?php
	}

}
