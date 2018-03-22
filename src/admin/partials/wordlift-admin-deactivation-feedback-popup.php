<?php
$reasons = array(
	array(
		'id'      => 'TOO_COMPLICATED',
		'text'    => __( 'It was too complicated and unclear to me', 'wordlift' ),
		'message' => array(
			'text' => __( 'Need help? We are ready to answer your questions.', 'wordlift' ) . ' <a href="https://wordlift.io/contact-us/" target="_blank">' . __( 'Contact Us', 'wordlift' ) . '</a>',
		),
	),
	array(
		'id'      => 'NOT_ENOUGH_FEATURES',
		'text'    => __( 'It misses some important feature to me', 'wordlift' ),
		'message' => array(
			'field' => 'text',
			'text'  => __( 'Tell us what this feature is.', 'wordlift' ),
		),
	),
	array(
		'id'      => 'COSTS_TOO_MUCH',
		'text'    => __( 'It costs too much', 'wordlift' ),
		'message' => array(
			'field' => 'text',
			'text'  => __( 'How much you would like to pay?', 'wordlift' ),
		),
	),
	array(
		'id'      => 'FOUND_ANOTHER_TOOL',
		'text'    => __( 'I found another tool that I like better', 'wordlift' ),
		'message' => array(
			'field' => 'text',
			'text'  => __( 'Please tell us some more details.', 'wordlift' ),
		),
	),
	array(
		'id'   => 'I_DONT_USE_IT',
		'text' => __( 'I\'m not using it right now', 'wordlift' ),
	),
	array(
		'id'      => 'SOMETHING_DIDNT_WORK',
		'text'    => __( 'Something didn\'t work right', 'wordlift' ),
	),
	array(
		'id'      => 'ANOTHER_REASON',
		'text'    => __( 'Another reason', 'wordlift' ),
		'message' => array(
			'field' => 'textarea',
			'text'  => __( 'Please tell us the reason so we can improve it.', 'wordlift' ),
		),
	),
);
?>
<div class="wl-modal-deactivation-feedback">
	<div class="wl-modal">
		<div class="wl-modal-body">
			<h2>
				<?php _e( 'We\'re sorry to see you go!', 'wordlift' ); ?>
			</h2>

			<div class="wl-modal-panel active">
				<h4>
					<?php _e( 'If you have a moment, please let us know why you are deactivating', 'wordlift' ); ?>:
				</h4>

				<ul>
					<?php foreach ( $reasons as $reason ) : ?>
						<li class="wl-reason-item <?php echo ( $reason['id'] == 'I_DONT_USE_IT' ) ? 'selected' : '' ; ?>">
							<label>
								<input
									type="radio"
									name="wl-reason"
									class="wl-reason"
									<?php checked( 'I_DONT_USE_IT', $reason['id'], true ); ?>
									value="<?php echo esc_attr( $reason['id'] ); ?>"
								/>

								<span class="description">
									<?php echo $reason['text']; ?>		
								</span>
							</label>
							
							<?php if ( ! empty( $reason['message'] ) ) : ?>
								<div class="additional-info <?php echo ( ! empty( $reason['message']['field'] ) ) ? 'has-field' : ''; ?>">
									<?php
									if ( ! empty( $reason['message']['field'] ) ) {
										if ( $reason['message']['field'] === 'text' ) {
											echo '<input type="text" name="wl-reason-info" class="wl-reason-info"/>';
										} else {
											echo '<textarea name="wl-reason-info" class="wl-reason-info"></textarea>';
										}
									}
									echo wpautop( $reason['message']['text'] )
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
					printf(
						__( 'Important notice: Uninstalling the plugin will delete your vocabulary.<br>Maybe you would like to <a href="%s" target="_blank">download your data</a> first.', 'wordlift' ),
						add_query_arg( array( 'page' => 'wl_download_your_data' ), admin_url( 'admin.php' ) )
					);
					?>
				</p>
			</div>

			<div class="wl-errors"></div>
		</div>

		<div class="wl-modal-footer">
			<a href="#" class="button button-secondary wl-modal-button-close">
				<?php _e( 'Cancel', 'wordlift' ); ?>
			</a>
			
			<a href="#" class="button button-primary wl-modal-button-deactivate">
				<?php _e( 'Deactivate', 'wordlift' ); ?>
			</a>
			<div class="clear"></div>
		</div>

		<input
			type="hidden"
			name="wl_deactivation_feedback_nonce"
			class="wl_deactivation_feedback_nonce"
			value="<?php echo wp_create_nonce( 'wl_deactivation_feedback_nonce' ); ?>"
		>
	</div>
</div>
