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
	 * @since 3.11.0
	 *
	 * @param array $args An array of parameters.
	 *
	 * @return \Wordlift_Admin_Element The element instance.
	 */
	public function render( $args ) {

		// Enqueue the jQuery UI Tabs script.
		wp_enqueue_script( 'jquery-ui-tabs' );

		// Parse the arguments and merge with default values.
		$params = wp_parse_args( $args, array(
			'tabs'   => array(),
			'active' => 0,
		) );

		// Following is the HTML code:
		//  - the labels are printed, using the tab's `label`,
		//  - the panels are printed, using the tab's `callback`.
		?>
		<div class="wl-tabs-element"
		     data-active="<?php echo esc_attr( $params['active'] ); ?>">
			<ul class="nav-tab-wrapper">
				<?php foreach ( $params['tabs'] as $index => $tab ) { ?>
					<li class="nav-tab">
						<a href="#tabs-<?php echo $index + 1; ?>"><?php
							echo esc_html_x( $tab['label'], 'wordlift' ); ?></a>
					</li>
				<?php } ?>
			</ul>
			<?php foreach ( $params['tabs'] as $index => $tab ) { ?>
				<div id="tabs-<?php echo $index + 1; ?>">
					<p><?php call_user_func( $tab['callback'] ); ?></p>
				</div>
			<?php } ?>
		</div>

		<?php

		return $this;
	}

}
