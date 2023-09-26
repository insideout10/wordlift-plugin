<?php
/**
 * Pages: Analytics Settings
 *
 * @since   3.21.0
 * @package Wordlift/admin
 */

?>
<div class="wrap" id="wl-settings-page">
	<?php settings_errors(); ?>
	<form action="options.php" method="post">
		<?php
		settings_fields( 'wl_analytics_settings' );
		do_settings_sections( 'wl_analytics_settings' );
		submit_button();
		?>
	</form>
	<div class="info">
		<b><?php esc_html_e( 'For Google Tag Manager there is additional setup steps needed in the GTM interface. These are the general instructions:', 'wordlift' ); ?></b>
		<ol>
			<li><?php esc_html_e( 'You should already have a configuration variable setup to pass data along to your provider. This is assumed to be Google Analytics and a google analytics configuration object. If you don\'t have this setup then set it up now.', 'wordlift' ); ?></li>
			<li>
				<?php
				printf(
						/* translators: 1: Variables, 2: Data Layer Variable. */
					esc_html__( 'Visit the GTM dashboard and head to the %1$s menu. Add a new user-defined variable of type %2$s for each of the following items:', 'wordlift' ),
					'<b>Variables</b>',
					'<i>Data Layer Variable</i>'
				);
				?>
				<ol>
					<li><i>event</i></li>
					<li><i>wl_event_action</i></li>
					<li><i>wl_event_category</i></li>
					<li><i>wl_event_label</i></li>
					<li><i>wl_event_value</i></li>
					<li><i>wl_event_uri</i> <?php esc_html_e( '(which is the first custom dimension number the plugin offers)', 'wordlift' ); ?></li>
					<li><i>wl_index_uri</i> <?php esc_html_e( '(this is the index number sent to use as the custom dimention for uri)', 'wordlift' ); ?></li>
					<li><i>wl_event_type</i> <?php esc_html_e( '(which is the second custom dimension number the plugin offers)', 'wordlift' ); ?></li>
					<li><i>wl_index_type</i> <?php esc_html_e( '(this is the index number sent to use as the custom dimention for type)', 'wordlift' ); ?></li>

				</ol>
			</li>
			<li>
				<?php
				printf(
				/* translators: 1: Google Analytics Settings. */
					esc_html__( 'Create another variable to pass along the 2 custom dimensions with the type %1$s. For each of the 2 items set the following:', 'wordlift' ),
					'<i>Google Analytics Settings</i>'
				);
				?>
				<ol>
					<li><?php esc_html_e( 'Set the index to the index of the custom event you want to push this into at Google Analytics, this will usually be a number between 1 and 9.', 'wordlift' ); ?></li>
					<li>
						<?php
						printf(
						/* translators: 1: wl_index_uri, 2: wl_index_type. */
							esc_html__( 'Set the values of each one to the %1$s and the %2$s respectively.', 'wordlift' ),
							'<i>wl_index_uri</i> - <i>wl_event_uri</i>',
							'<i>wl_index_type</i> - <i>wl_event_type</i>'
						);
						?>
					</li>
				</ol>
			</li>
			<li>
				<?php
				printf(
				/* translators: 1: Triggers, 2: Custom Events. */
					esc_html__( 'Go to the %1$s menu and create a new trigger of the type: %2$s.', 'wordlift' ),
					'<b>Triggers</b>',
					'<i>Custom Event</i>'
				);
				?>
				<ol>
					<li>

						<?php
						printf(
						/* translators: 1: Event name, 2: Mentions. */
							esc_html__( 'In the %1$s field input %2$s.', 'wordlift' ),
							'<i>Event name</i>',
							'<i>Mentions</i>'
						);
						?>
					</li>
					<li>

						<?php
						printf(
						/* translators: 1: Some Custom Events. */
							esc_html__( 'Set this to fire on %1$s and in the filter set:', 'wordlift' ),
							'<i>Some Custom Events</i>'
						);
						?>
						<ol>
							<li><i>event_action</i> - <i>equals</i> - <i>Mentions</i>.</li>
						</ol>
					</li>
				</ol>
			</li>
			<li>
				<?php
				printf(
				/* translators: 1: Tags. */
					esc_html__( 'Go to the %1$s menu and create a new tag.', 'wordlift' ),
					'<b>Tags</b>'
				);
				?>
				<ol>
					<li>

						<?php
						printf(
						/* translators: 1: Google Analytics. */
							esc_html__( 'In the tag configuration section choose %1$s. Assuming you have used variable names that match those mentioned enter:', 'wordlift' ),
							'<i>Google Analytics - Universal Analytics</i>'
						);
						?>
						<ol>
							<li>
								<?php
								printf(
								/* translators: 1: Track Type, 2: Event. */
									esc_html__( 'Set the %1$s to %2$s.', 'wordlift' ),
									'Track Type',
									'<i>Event</i>'
								);
								?>
							</li>
							<li>
								<?php
								printf(
								/* translators: 1: wl_event_category. */
									esc_html__( 'Set Category to %1$s.', 'wordlift' ),
									'<i>{{wl_event_category}}</i>'
								);
								?>
							</li>
							<li>
								<?php
								printf(
								/* translators: 1: wl_event_action. */
									esc_html__( 'Set Action to %1$s.', 'wordlift' ),
									'<i>{{wl_event_action}}</i>'
								);
								?>
							</li>
							<li>
								<?php
								printf(
								/* translators: 1: wl_event_label. */
									esc_html__( 'Set Label to %1$s.', 'wordlift' ),
									'<i>{{wl_event_label}}</i>'
								);
								?>
							</li>
							<li>
								<?php
								printf(
								/* translators: 1: wl_event_value. */
									esc_html__( 'Set Value to %1$s.', 'wordlift' ),
									'<i>{{wl_event_value}}</i>'
								);
								?>
							</li>
							<li>
								<?php
								printf(
								/* translators: 1: True. */
									esc_html__( 'Set Non-Interaction Hit to %1$s.', 'wordlift' ),
									'<i>True</i>'
								);
								?>
							</li>
							<li>
								<?php
								printf(
								/* translators: 1: Google Analytics Settings. */
									esc_html__( 'Set the %1$s dropdown to the settings object for the Google Analytics tracking.', 'wordlift' ),
									'<i>Google Analytics Settings</i>'
								);
								?>
							</li>
						</ol>
					</li>
					<li><?php esc_html_e( 'In the triggering tag select the firing trigger you created previously.', 'wordlift' ); ?></li>
				</ol>
			</li>
		</ol>
	</div>

</div>
