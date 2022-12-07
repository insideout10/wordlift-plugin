<?php

namespace Wordlift\Admin;

class Installation_Complete_Notice {

	public function init() {

		$this->handle_notice_close();

		add_action(
			'wordlift_admin_notices',
			function () {
				if ( \Wordlift_Configuration_Service::get_instance()->get_skip_installation_notice() ) {
					return;
				}
				?>
				<div class="updated">
					<H3><?php echo esc_html( get_plugin_data( WORDLIFT_PLUGIN_FILE )['Name'] ); ?> <?php esc_html_e( 'has been successfully installed on your site!', 'wordlift' ); ?></H3>
					<p><?php esc_html_e( 'we\'re now automatically enriching the structured data on your posts to create the best representation of your content that search engines will understand. Time to look forward to an increase in organic traffic!', 'wordlift' ); ?></p>
					<p><u>
							<a
									href="<?php echo esc_url( wp_nonce_url( add_query_arg( 'wl_hide_installation_notice', true ), 'wordlift_hide_installation_notice_nonce', '_wl_hide_installation_notice_nonce' ) ); ?>">
								<?php esc_html_e( 'Dismiss', 'wordlift' ); ?>
							</a>
						</u>
					</p>
				</div>
				<?php
			}
		);

	}

	public function handle_notice_close() {
		if ( ! isset( $_GET['wl_hide_installation_notice'] ) || ! isset( $_GET['_wl_hide_installation_notice_nonce'] ) ) {
			return;
		}

		if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_wl_hide_installation_notice_nonce'] ) ), 'wordlift_hide_installation_notice_nonce' )
		|| ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'Action failed.', 'wordlift' ) );
		}

		\Wordlift_Configuration_Service::get_instance()->set_skip_installation_notice( true );

	}

}
