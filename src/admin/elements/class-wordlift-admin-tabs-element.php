<?php
/**
 * Elements: Tabs.
 *
 * A tabbed element.
 *
 * @since      3.11.0
 * @package    Wordlift
 * @subpackage Wordlift/admin
 */

/**
 * Define the {@link Wordlift_Admin_Tabs_Element} class.
 *
 * @since      3.11.0
 * @package    Wordlift
 * @subpackage Wordlift/admin
 */
class Wordlift_Admin_Tabs_Element implements Wordlift_Admin_Element {

	/**
	 * Render the element.
	 *
	 * @param array $args {
	 *      Parameters controlling the html being output.
	 *
	 * @type integer $active The index of the active panel on first render
	 *                              a zero based number of the tab actual placement
	 *
	 * @type array $tabs {
	 *          The array of tabs to be rendered.
	 *          The index of the elements is expected to be an ascending integers
	 *          tabs with lower index values will be render first (on the left)
	 *
	 * @type string $label The label used for the tab.
	 * @type callable $callback The callback to call to render the
	 *                                      Tab "panel".
	 * @type array $args The arguments array passed to the callback.
	 *          }
	 *      }
	 *
	 * @return \Wordlift_Admin_Element The element instance.
	 * @since 3.11.0
	 */
	public function render( $args ) {

		// Enqueue the jQuery UI Tabs script.
		wp_enqueue_script( 'jquery-ui-tabs' );

		// Parse the arguments and merge with default values.
		$params = wp_parse_args(
			$args,
			array(
				'tabs'   => array(),
				'active' => 0,
			)
		);

		// Following is the HTML code:
		// - the labels are printed, using the tab's `label`,
		// - the panels are printed, using the tab's `callback`.
		?>
		<div
				class="wl-tabs-element"
				data-active="<?php echo esc_attr( $params['active'] ); ?>"
		>
			<ul class="nav-tab-wrapper">
				<?php foreach ( $params['tabs'] as $index => $tab ) : ?>
					<li class="nav-tab">
						<a href="#tabs-<?php echo esc_html( $index + 1 ); ?>">
							<?php echo esc_html( $tab['label'] ); ?>
						</a>
					</li>
				<?php endforeach; ?>
			</ul>
			<?php foreach ( $params['tabs'] as $index => $tab ) : ?>
				<div id="tabs-<?php echo esc_html( $index + 1 ); ?>">
					<?php call_user_func( $tab['callback'], $tab['args'] ); ?>
				</div>
			<?php endforeach; ?>
		</div>

		<?php

		return $this;
	}

}
