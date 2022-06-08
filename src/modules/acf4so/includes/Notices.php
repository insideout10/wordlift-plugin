<?php

namespace Wordlift\Modules\Acf4so;

class Notices {

	/**
	 * @var Plugin
	 */
	private $acf4so_plugin;

	function __construct( Plugin $plugin ) {
		$this->acf4so_plugin = $plugin;
	}

	public function register_hooks() {
		add_action( 'admin_notices', [ $this, 'admin_notices' ] );
	}

	public function admin_notices() {

		$is_package_type_supported = $this->is_package_type_supported();

		$is_woocommerce_plugin_installed = defined( 'WL_WOO_VERSION' );

		if ( ! $is_package_type_supported && ! $is_woocommerce_plugin_installed ) {
			// Dont display notice.
			return;
		}

		/**
		 * 1. When package type is supported and acf4so not installed or activated then the notice should appear.
		 * 2. When woocommerce plugin installed and acf4so not installed or activated then the notice should appear.
		 */
		if ( ! $this->acf4so_plugin->is_plugin_installed() ) {
			$this->display_notice(
				__( "WordLift detected that <b>Advanced Custom Fields for Schema.org</b> is not installed and, you're loosing out on full Schema.org support.",'wordlift' ),
				__( 'Reinstall & Activate', 'wordlift' )
			);
			// Dont display notice.
			return;
		}

		if ( ! $this->acf4so_plugin->is_plugin_activated() ) {
			$this->display_notice(
				__( "WordLift detected that <b>Advanced Custom Fields for Schema.org</b> is deactivated and, you're loosing out on full Schema.org support.", 'wordlift' ),
				__( 'Reactivate', 'wordlift' )
			);
            return;
		}

	}


	private function display_notice( $message, $button_text ) {
		?>

        <script>
            window.addEventListener("load", function() {
                const pluginInstallationNotice = document.getElementById("wordlift_acf4so_plugin_installation_notice")
                const installPlugin = (ajaxUrl) => fetch(`${ajaxUrl}?action=wl_install_and_activate_advanced-custom-fields-for-schema-org`)
                        .then(response => response.ok ?  response.json() :   Promise.reject())
                const ajaxUrl = "<?php echo esc_html( parse_url( admin_url( 'admin-ajax.php' ), PHP_URL_PATH ) ); ?>"
                window.wordliftInstallAcf4so = function(installBtn) {
                    installBtn.innerHTML = 'Installing <span class="spinner is-active"></span>'
                        installPlugin(ajaxUrl)
                        .catch(e => {
                            pluginInstallationNotice.innerHTML = '<p>Wordlift: Advanced Custom Fields for Schema.org</b> Installation failed, please retry or contact support@wordlift.io</p>' +
                                '<button class="button action" onclick="wordliftInstallAcf4so(this)">Retry</button>'
                        })
                        .then(() => {
                            pluginInstallationNotice.innerHTML = '<p>Wordlift: <b>Advanced Custom Fields for Schema.org</b> plugin installed and activated.</p>'
                            pluginInstallationNotice.classList.remove('notice-error')
                            pluginInstallationNotice.classList.add('notice-success')
                        })

                };
            })
        </script>


        <div class="notice notice-error" id="wordlift_acf4so_plugin_installation_notice">
            <p>
                <?php echo $message; ?>
                <button class="button action right" onclick="wordliftInstallAcf4so(this)">
		            <?php esc_html_e( $button_text ); ?>

                </button>
            </p>
            <br/>
        </div>
		<?php
	}

	/**
	 * @return bool
	 */
	private function is_package_type_supported() {
		return apply_filters( 'wl_feature__enable__entity-types-professional', false ) ||
		       apply_filters( 'wl_feature__enable__entity-types-business', false );
	}

}