<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The install wizard
 *
 * @link       http://wordlift.it
 * @since      3.9.0
 *
 * @package    Wordlift
 * @subpackage Wordlift/admin
 */

/**
 * The install wizard.
 *
 * Methods to track and implement the various steps of the install wizard
 * which is triggered on dirst install of the plugin (when there are no settings)
 *
 * @package    Wordlift
 * @subpackage Wordlift/admin
 * @author     WordLift <hello@wordlift.it>
 */
class Wordlift_Install_wizard {

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    3.9.0
	 *
	 */
	public function __construct(  ) {

		$option = get_option('wl_general_settings_wizard',false);
		if (false === $option)
			$this->step = 1;
		else
			$this->step = $option;
		
		add_action( 'wp_loaded', array( $this, 'hide_notices' ) );
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		add_action( 'admin_init', array( $this, 'show_page' ) );
		add_action( 'admin_notices', array( $this, 'admin_notices' ) );
	}

	/**
	 * Generate an admin notice sugesting to start the wizard if there is no configuration
	 *
	 * @since    3.9.0
	 *
	 */
	public function admin_notices() {

		$show = true;
		$o = get_option('wl_general_settings',false);
		
		// an indication that setting was already done is that there is more to the settings then the key
		// when a user skipps the wizard another index is added to the array in addition to the key
		if (is_array($o) && (count($o) > 1)) 
			$show = false;
			
		if ($show) {
		?>
		<div id="wl-message" class="updated">
			<p><?php _e( '<strong>Welcome to WordLift</strong> &#8211; You&lsquo;re almost ready to start', 'wordlift' ); ?></p>
			<p class="submit"><a href="<?php echo esc_url( admin_url( 'admin.php?page=wl-setup' ) ); ?>" class="button-primary"><?php _e( 'Run the Setup Wizard', 'wordlift' ); ?></a> <a class="button-secondary skip" href="<?php echo esc_url( wp_nonce_url( add_query_arg( 'wl-hide-notice', 'install' ), 'wordlift_hide_notices_nonce', '_wl_notice_nonce' ) ); ?>"><?php _e( 'Skip Setup', 'wordlift' ); ?></a></p>
		</div>
		<?php
		}
	}
	
	/**
	 * handle hiding the wizard notices by user request
	 *
	 * @since    3.9.0
	 *
	 */
	public function hide_notices() {
		if ( isset( $_GET['wl-hide-notice'] ) && isset( $_GET['_wl_notice_nonce'] ) ) {
			if ( ! wp_verify_nonce( $_GET['_wl_notice_nonce'], 'wordlift_hide_notices_nonce' ) ) {
				wp_die( __( 'Action failed. Please refresh the page and retry.', 'wordlift' ) );
			}

			if ( ! current_user_can( 'manage_options' ) ) {
				wp_die( __( 'Cheatin&#8217; huh?', 'wordlift' ) );
			}

			$o = get_option('wl_general_settings',false);
			if (!is_array($o)) {
				$o = array();
			}
			$o['no_wizard'] = true;
			update_option('wl_general_settings',$o);
		}		
	}

	/**
	 * Register the wizard page to be able to access it
	 *
	 * @since    3.9.0
	 *
	 */
	public function admin_menu() {
		add_dashboard_page( '', '', 'manage_options', 'wl-setup', '' );
	}
	
	/**
	 * Displays the wizard page
	 *
	 * @since    3.9.0
	 *
	 */
	public function show_page( ) {
		// first check if we are in the wizard page at all, if not do nothing
		if ( empty( $_GET['page'] ) || 'wl-setup' !== $_GET['page'] ) {
			return;
		}
		
		// check and sanitize the wizard step being requested
		$step = 'welcome';
		if (!empty( $_GET['step'] )) {
			$step = $_GET['step'];
			if (!in_array($step,array('welcome','license','vocabulary','language','publisher')))
				$step = 'welcome';
		}
		
		$this->step = $step;
		
		$this->header();
		switch ($step) {
			case 'welcome' : $this->welcome_page();
				break;
			case 'license' : $this->license_page();
				break;
			case 'vocabulary' : $this->vocabulary_page();
				break;
			case 'language' : $this->language_page();
				break;
			case 'publisher' : $this->publisher_page();
				break;
		}
		$this->footer();
		exit();
	}
	
	/**
	 * Output the html for the header of the page
	 *
	 * @since    3.9.0
	 *
	 */
	public function header( ) {
		?>
		<!DOCTYPE html>
		<html <?php language_attributes(); ?>>
		<head>
			<meta name="viewport" content="width=device-width" />
			<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
			<title><?php _e( 'WordLift &rsaquo; Setup Wizard', 'wordlift' ); ?></title>
			<link rel="stylesheet" href="<?php echo plugins_url('css/wordlift-reloaded.min.css?ver=3.9',dirname(__FILE__ ))?>" type="text/css">
			<style>				
				body {
					background:white;
				}
				
				.wl-setup {
					color:white;
					width:560px;				
					padding:20px;
					background: blue;
					margin:100px auto;
					position:relative;
				}
				
				#close {
					position:absolute;
					top:-10px;
					right:-6px;
					background:white;
					color:blue;
					font-size: 20px;
					border-radius: 20px;
					border: 1px blue solid;
					display: block;
					padding: 0 2px;
				}
				
				#title {
					text-align:center;
					font-size:24px;
					font-weight:bold;
					margin-bottom:30px;
					margin-top:10px;
				}

				#wl-title {
					display:flex;
					line-height:30px;
				}
				
				#message {
					text-align:center;
					font-size:18px;
					margin-bottom:30px;
				}
				
				#bullets {
					text-align:center;
					flex-grow:1;
				}
				
				.bullet {
					height: 8px;
					width: 8px;
					border: 1px solid #fff;
					border-radius: 50%;
					background-color: transparent;
					display:inline-block;
				}
				
				.bullet[data-step="<?php echo $this->step?>"] {
					background-color: #fff;
				}
				
				.bold {
					font-weight:bold;
				}

				#wl-logo {
					width:100px;
					font-size:30px;
				}
				
				#topright {
					width:118px;
					height:30px;
					background:url('<?php echo plugins_url('images/wizard_top_right.png',dirname(__FILE__ ))?>')
				}
				
				.buzz {
					float:left;
					width:33%;
					margin-top:10px;
				}
				
				.buzz .fa {
					margin-right:10px;
				}
				
				#buttons {
					margin-top:40px;
					text-align:center;
				}
				
				#buttons a {
					background: white;
					color: black;
					border-radius:5px;
					padding:5px 10px;
					margin-right:20px;
					text-decoration:none;
				}
				
				#buttons a#nextstep {
					background:black;
					color:white;
				}
				
			</style>
		</head>
		<body>
			<div class="wl-setup">
				<a id="close" title="<?php _e('Exit the wizard','wordlift')?>" href="<?php echo admin_url()?>"><span class="fa fa-times"></span></a>
				<div id="wl-title">
					<div id="wl-logo">
						<span class="bold">Word</span>Lift
					</div>
					<div id="bullets">
						<span class="bullet" data-step="welcome"></span>
						<span class="bullet" data-step="license"></span>
						<span class="bullet" data-step="vocabulary"></span>
						<span class="bullet" data-step="language"></span>
						<span class="bullet" data-step="publisher"></span>
					</div>
					<div id="topright">
					</div>
					<div style="clear:both"></div>
				</div>
		<?php
	}

	/**
	 * Output the html for the footer of the page
	 *
	 * @since    3.9.0
	 *
	 */
	public function footer( ) {
		?>
			</div>
			</body>
		</html>
		<?php
	}
	
	public function welcome_page() {
		?>
		<div id="title"><?php _e('Welcome','wordlift')?></div>
		<div id="message"><?php _e('Thank you for downloading WordLift. Now you can<br>boost your website with double digit growth.','wordlift')?></div>
		<div id="buzzcont">
			<div class="buzz"><span class="fa fa-university"></span><?php _e('Trustworthiness','wordlift')?></div>
			<div class="buzz"><span class="fa fa-map-marker"></span><?php _e('Enrichment','wordlift')?></div>
			<div class="buzz"><span class="fa fa-heart"></span><?php _e('Engagement','wordlift')?></div>
			<div class="buzz"><span class="fa fa-hand-o-right"></span><?php _e('Smart Navigation','wordlift')?></div>
			<div class="buzz"><span class="fa fa-google"></span><?php _e('SEO Optimization','wordlift')?></div>
			<div class="buzz"><span class="fa fa-group"></span><?php _e('Content Marketing','wordlift')?></div>
			<div style="clear:both">
		</div>
		<div id="buttons">
			<a href=" https://wordlift.io/blogger" target="_tab" class="button-primary"><?php _e( 'Learn More', 'wordlift' ); ?></a>
			<a id="nextstep" href="<?php echo esc_url( admin_url( 'admin.php?page=wl-setup&step=license' ) ); ?>"><?php _e( 'Get started', 'wordlift' ); ?></a>
		</div>
		<?php
	}
}
