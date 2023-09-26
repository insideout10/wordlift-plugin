<?php
$too_complicate_reason_label = sprintf(
	'<a target="_blank" href="%s">%s</a>',
	/* translators: the link https://wordlift.io/contact-us/ should be changed to language version of the page */
	esc_attr__( 'https://wordlift.io/contact-us/', 'wordlift' ),
	esc_html__( 'Contact Us', 'wordlift' )
);

$reasons = array(
	array(
		'id'      => 'TOO_COMPLICATED',
		'text'    => esc_html__( 'It was too complicated and unclear to me', 'wordlift' ),
		'message' => array(
			'text' => sprintf(
				/* translators: %s: Too complicate reason. */
				esc_html__( 'Need help? We are ready to answer your questions. %s', 'wordlift' ),
				$too_complicate_reason_label
			),
		),
	),
	array(
		'id'      => 'NOT_ENOUGH_FEATURES',
		'text'    => esc_html__( 'It misses some important feature to me', 'wordlift' ),
		'message' => array(
			'field' => 'text',
			'text'  => esc_html__( 'Tell us what this feature is.', 'wordlift' ),
		),
	),
	array(
		'id'      => 'COSTS_TOO_MUCH',
		'text'    => esc_html__( 'It costs too much', 'wordlift' ),
		'message' => array(
			'field' => 'text',
			'text'  => esc_html__( 'How much you would like to pay?', 'wordlift' ),
		),
	),
	array(
		'id'      => 'FOUND_ANOTHER_TOOL',
		'text'    => esc_html__( 'I found another tool that I like better', 'wordlift' ),
		'message' => array(
			'field' => 'text',
			'text'  => esc_html__( 'Please tell us some more details.', 'wordlift' ),
		),
	),
	array(
		'id'   => 'I_DONT_USE_IT',
		'text' => esc_html__( 'I\'m not using it right now', 'wordlift' ),
	),
	array(
		'id'   => 'SOMETHING_DIDNT_WORK',
		'text' => esc_html__( 'Something didn\'t work right', 'wordlift' ),
	),
	array(
		'id'      => 'ANOTHER_REASON',
		'text'    => esc_html__( 'Another reason', 'wordlift' ),
		'message' => array(
			'field' => 'textarea',
			'text'  => esc_html__( 'Please tell us the reason so we can improve it.', 'wordlift' ),
		),
	),
);
?>
<div class="wl-modal-deactivation-feedback" style="display: none">
	<div class="wl-modal">
		<div class="wl-modal-body">
			<h2>
				<?php esc_html_e( 'We\'re sorry to see you go!', 'wordlift' ); ?>
			</h2>

			<div class="wl-modal-panel active">
				<h4>
					<?php esc_html_e( 'If you have a moment, please let us know why you are deactivating', 'wordlift' ); ?>
					:
				</h4>

				<ul>
					<?php foreach ( $reasons as $reason ) : ?>
						<li class="wl-reason-item <?php echo ( 'I_DONT_USE_IT' === $reason['id'] ) ? 'selected' : ''; ?>">
							<label>
								<input
										type="radio"
										name="wl-code"
										class="wl-code"
									<?php checked( 'I_DONT_USE_IT', $reason['id'], true ); ?>
										value="<?php echo esc_attr( $reason['id'] ); ?>"
								/>

								<span class="description">
									<?php echo esc_html( $reason['text'] ); ?>
								</span>
							</label>

							<?php if ( ! empty( $reason['message'] ) ) : ?>
								<div class="additional-info <?php echo ( ! empty( $reason['message']['field'] ) ) ? 'has-field' : ''; ?>">
									<?php
									if ( ! empty( $reason['message']['field'] ) ) {
										if ( 'text' === $reason['message']['field'] ) {
											echo '<input type="text" name="wl-details" class="wl-details"/>';
										} else {
											echo '<textarea name="wl-details" class="wl-details"></textarea>';
										}
									}
									echo wp_kses( wpautop( $reason['message']['text'] ), array( 'p' => array() ) )
									?>
								</div>
							<?php endif ?>
						</li>
					<?php endforeach ?>
				</ul>
			</div>

			<div class="notes">
				<p>
					<?php
					echo wp_kses(
						sprintf(
								/* translators: %s: link to the download your data page. */
							__( 'Important notice: Uninstalling the plugin will delete your vocabulary.<br>Maybe you would like to <a href="%s" target="_blank">download your data</a> first.', 'wordlift' ),
							add_query_arg( array( 'page' => 'wl_download_your_data' ), admin_url( 'admin.php' ) )
						),
						array(
							'br' => array(),
							'a'  => array(
								'href'   => array(),
								'target' => array(),
							),
						)
					);
					?>
				</p>
			</div>

			<div class="wl-errors"></div>
		</div>

		<div class="wl-modal-footer">
			<a href="#" class="button button-secondary wl-modal-button-close">
				<?php esc_html_e( 'Cancel', 'wordlift' ); ?>
			</a>

			<a href="#" class="button button-primary wl-modal-button-deactivate">
				<?php esc_html_e( 'Deactivate', 'wordlift' ); ?>
			</a>
			<div class="clear"></div>
		</div>

		<input
				type="hidden"
				name="wl_deactivation_feedback_nonce"
				class="wl_deactivation_feedback_nonce"
				value="<?php echo esc_attr( wp_create_nonce( 'wl_deactivation_feedback_nonce' ) ); ?>"
		>
	</div>
</div>
