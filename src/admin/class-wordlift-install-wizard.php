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
			if (!in_array($step,array('welcome','license','vocabulary','language','publisher','finish')))
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
			case 'finish' : $this->finish();
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
					background:#f5f5f5;
				}
				
				.wl-setup {
					color:white;
					padding:20px;
					background: #2e92ff;
					margin:100px auto;
					position:relative;
					width: 718px;
					min-height: 400px;
					/* mobile fallback */
					max-width: 100%;
					max-height: 100%;
				}
				
				#close {
					position:absolute;
					top:-10px;
					right:-6px;
					background:white;
					color:#2e92ff;
					font-size: 20px;
					border-radius: 20px;
					border: 1px #2e92ff solid;
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

				#buzzcont {
					width:500px;
					margin:0 auto;
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
								
				.input, .select {
					display: block;
					height: 40px;
					/* width in deskop
					 in mobile must be 270px*/
					width: 400px;
					margin: 0 auto 8px;
					/* defining internal child position */
					box-sizing: border-box;
					padding: 8px 40px 8px 8px;
					/*defining text */
					font-size: 16px;
					line-height: 24px;
					/*reset default*/
					outline: none; 
					border: none;
					background: #FFFFFF;
					box-shadow: inset 0 0 0 2px #FFFFFF,  inset 0 0 0 4px #2E92FF;
					border-radius: 4px;
				}  
				
				.select {
					background-image: url('data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACAAAAAgCAYAAABzenr0AAAAAXNSR0IArs4c6QAAATNJREFUWAljYBgFoyEwGgKjITAaAgMcAowg+82X/Of7/o5hHRMjw6kLuYxV+NxkMPl/27//DGacQgxBJ2MYP+FTS4wck+e2/+xAy3cCFTsDDa4EWtCNSyNIDqQGpBakB6QXl1pixZlePWbgACqWgWkAWlCCzRFQy0tg6kB6oHqRhEhnMp1NZ/zIys7gANT6BKYd3RFYLH8C0gPSC9NDLg1OAyDNxjP/K//+xbCf4T+DLMwwYJroAbFBDoKJMTAyPGZlY3AEWn4XLkYBA+4AkBnYHIFiNpUtB5nNhGwByFfszMDoAFqELA5mA8VActTyOcx8lBCACZpO+6/08y/DAXh0QC0/ncV4D6aGWjRWB4AMhzsCyAb5nBaWE/SE3vT/YiBMUOGogtEQGA2B0RAYDYGhHAIAbVluGopK7kIAAAAASUVORK5CYII=');
					background-position: 100%;
					background-repeat: no-repeat;
					-webkit-appearance: none;
					-moz-appearance: none;
					text-indent: 1px;
					text-overflow: '';
				}
				
				#input .input[data-verify="valid"], .input:valid {
					background-image: url('<?php echo plugins_url('images/valid.png',dirname(__FILE__ ))?>');
					background-position: 98%;
					background-repeat: no-repeat;					
				}
				
				#input input[data-verify="invalid"], .input:invalid {
					background-image: url('<?php echo plugins_url('images/invalid.png',dirname(__FILE__ ))?>');
					background-position: 98%;
					background-repeat: no-repeat;					
				}

				#addlogo {
					margin:10px 0;
					text-align:center;
				}
				
				#addlogo a, #addlogo a:visited {
					color:white;
				}
				
				#radio {
					text-align:center;
					margin:10px 0;
				}
				
				#radio label {
					margin-right:20px;
				}
				
				#logo {
					position:relative;
					margin-top:20px;
				}
				
				#logo img {
					margin:0 auto;
					display:block;
				}
				
				#deletelogo {
					position:absolute;
					top:-10px;
					right:295px;
					background:white;
					color:#2e92ff;
					font-size: 20px;
					border-radius: 20px;
					border: 1px #2e92ff solid;
					display: block;
					padding: 0 2px;
				}
				
			</style>
			<?php 
				wp_enqueue_script('jquery');
				wp_enqueue_media(); 
				wp_enqueue_style('common');
				wp_print_styles();
				wp_print_scripts();
			?>
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
			<?php do_action( 'admin_footer');?>
			</body>
		</html>
		<?php
	}
	
	/**
	 * Output the html for the welcome page
	 *
	 * @since    3.9.0
	 *
	 */
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
			<a href="https://wordlift.io/blogger" target="_tab" class="button-primary"><?php _e( 'Learn More', 'wordlift' ); ?></a>
			<a id="nextstep" href="<?php echo esc_url( admin_url( 'admin.php?page=wl-setup&step=license' ) ); ?>"><?php _e( 'Get started', 'wordlift' ); ?></a>
		</div>
		<?php
	}
	
	/**
	 * Output the html for the license page
	 *
	 * @since    3.9.0
	 *
	 */
	public function license_page() {
		$key = '';
		if (isset($_COOKIE['wl_key']))
			$key = $_COOKIE['wl_key'];
		$valid = 'invalid';
		if ($this->validate_key($key))
			$valid = 'valid';
		?>
		<div id="title"><?php _e('License Key','wordlift')?></div>
		<div id="message"><?php _e('If you already puchased a plan, check your email, get<br>the activation key from your inbox and insert it in<br>the field below. Otherwise ....','wordlift')?></div>
		<div id="input"><input oninput="keychange();" class="input" id="key" type="text" name="key" data-verify="<?php echo esc_attr($valid)?>" value="<?php echo esc_attr($key)?>" autocomplete="off" placeholder="<?php _e('Activation Key','wordlift')?>"></div>
		<div id="buttons">
			<a href="https://wordlift.io/#plan-and-price" target="_tab" class="button-primary"><?php _e( 'Grab Key!', 'wordlift' ); ?></a>
			<a id="nextstep" onclick="savevalue()" href="<?php echo esc_url( admin_url( 'admin.php?page=wl-setup&step=vocabulary' ) ); ?>"><?php _e( 'Next Step', 'wordlift' ); ?></a>
		</div>
		<script type="text/javascript">
			function keychange() {
				var ajaxurl = '<?php echo admin_url( 'admin-ajax.php', 'relative' ); ?>';
				var key = jQuery('#key').val();
				jQuery.post(ajaxurl , {'action':'wl_validate_key' ,'key':key}, function (data) {
					if (data.valid)
						jQuery('#key').attr('data-verify','valid');
					else
						jQuery('#key').attr('data-verify','invalid');
					}, 'json');
			}
			
			function savevalue() {
				var key = jQuery('#key').val();
				document.cookie = "wl_key="+key+';path='+'<?php echo admin_url()?>';
			} 
		</script>
		<?php
	}
	
	/**
	 * Output the html for the vocabulary page
	 *
	 * @since    3.9.0
	 *
	 */
	public function vocabulary_page() {
		$slug = '/'.__('vocabulary','wordlift').'/';
		if (isset($_COOKIE['wl_slug']))
			$slug = $_COOKIE['wl_slug'];
		?>
		<div id="title"><?php _e('Vocabulary','wordlift')?></div>
		<div id="message"><?php _e('All new pages created with WordLift will be stored<br>inside yourinternal vocabulary. You can customize<br>the url pattern of these pages in the field below','wordlift')?></div>
		<div id="input"><input class="input" id="key" type="text" name="key" pattern="/[a-zA-Z0-9/]+/" autocomplete="off" value="<?php echo esc_attr($slug)?>"></div>
		<div id="buttons">
			<a id="nextstep" onclick="savevalue()" href="<?php echo esc_url( admin_url( 'admin.php?page=wl-setup&step=language' ) ); ?>"><?php _e( 'Next Step', 'wordlift' ); ?></a>
		</div>
		<script type="text/javascript">
			function savevalue() {
				var slug = jQuery('#key').val();
				document.cookie = "wl_slug="+slug+';path='+'<?php echo admin_url()?>';
			} 
		</script>
		<?php
	}
	
	/**
	 * Output the html for the language page
	 *
	 * @since    3.9.0
	 *
	 */
	public function language_page() {
		
		$langs = array(
				'' => 'English',
				'zh' => '中文',
				'es' => 'Español',
				'ru' => 'Русский',
				'pt' => 'Português',
				'fr' => 'Français',
				'it' => 'Italiano',
				'nl' => 'Nederlands',
				'sv' => 'Svenska',
				'da' => 'Dansk',
				'tr' => 'Türkçe',
				);

		$locale = get_locale();
		$parts = explode('_',$locale);
		$lang = $parts[0];

		if (isset($_COOKIE['wl_lang']))
			$lang = $_COOKIE['wl_lang'];
		
		if (!isset($langs[$lang]))
			$lang = '';
		?>
		<div id="title"><?php _e('Language','wordlift')?></div>
		<div id="message"><?php _e('Each WordLift key can be used only in one language.<br>Pick yours.','wordlift')?></div>
		<div id="input">
			<select class="select" id="language" autocomplete="off">
				<?php 
					foreach ($langs as $code => $label) {
						echo '<option value="'.esc_attr($code).'" '.selected($code,$lang,false).'>'.esc_html($label).'</option>';
					}
				?>
			</select>
		</div>
		<div id="buttons">
			<a id="nextstep" onclick="savevalue()" href="<?php echo esc_url( admin_url( 'admin.php?page=wl-setup&step=publisher' ) ); ?>"><?php _e( 'Next Step', 'wordlift' ); ?></a>
		</div>
		<script type="text/javascript">
			function savevalue() {
				var lang = jQuery('#input select').val();
				document.cookie = "wl_lang="+lang+';path='+'<?php echo admin_url()?>';
			} 
		</script>
		<?php
	}
	
	/**
	 * Output the html for the publisher page
	 *
	 * @since    3.9.0
	 *
	 */
	public function publisher_page() {
		$type = 'personal';
		if (isset($_COOKIE['wl_type']))
			$type = $_COOKIE['wl_type'];
		
		$name = '';
		if (isset($_COOKIE['wl_name']))
			$name = $_COOKIE['wl_name'];

		$image_id = 0;
		if (isset($_COOKIE['wl_image_id']))
			$image_id = $_COOKIE['wl_image_id'];
		
		$image_url = 0;
		if (isset($_COOKIE['wl_image_url']))
			$image_url = $_COOKIE['wl_image_url'];
		?>
		<div id="title"><?php _e('Publisher','wordlift')?></div>
		<div id="message"><?php _e('Are you going to publish as an individual<br>or as a company?','wordlift')?></div>
		<div id="radio">
			<label for="personal"><input id="personal" type="radio" name="user_type" value="personal" <?php checked($type,'personal')?>><?php _e('Personal','wordlift')?></label>
			<label for="company"><input id="company" type="radio" name="user_type" value="company" <?php checked($type,'company')?>><?php _e('Company','wordlift')?></label>
		</div>
		<div id="input"><input class="input" id="key" type="text" name="key" value="<?php echo esc_attr($name)?>" placeholder="<?php _e('Name','wordlift')?>"></div>
		<div id="addlogo" <?php if ($image_id != 0) echo 'style="display:none"'?>><a href="#" onclick="return addlogo();"><?php _e('Add your logo','wordlift')?></a></div>
		<div id="logo" <?php if ($image_id == 0) echo 'style="display:none"'?>>
			<img src="<?php echo esc_attr($image_url)?>" data-id="<?php echo esc_attr($image_id)?>" width="100" height="100">
			<a id="deletelogo" onclick="return deletelogo()" href="#" title="<?php _e('Remove the logo','wordlift')?>"><span class="fa fa-times"></a>
		</div>
		<div id="buttons">
			<a id="nextstep" onclick="savevalue()" href="<?php echo esc_url( wp_nonce_url( add_query_arg( 'step', 'finish' ), 'wordlift_finish_nonce', '_wl_finish_nonce' ) ); ?>"><?php _e( 'Finish', 'wordlift' ); ?></a>
		</div>
		<script type="text/javascript">
			var mediaUploader;
			
			function addlogo() {
				mediaUploader = wp.media({
				  title: '<?php _e('WordLift Choose Logo','wordlift')?>',
				  button: {
				  text: '<?php _e('Choose Logo','wordlift')?>'
				}, multiple: false });

				// When a file is selected, grab the URL and set it as the text field's value
				mediaUploader.on('select', function() {
				  attachment = mediaUploader.state().get('selection').first().toJSON();
				  jQuery('#logo img').attr('src',attachment.url);
				  jQuery('#logo img').attr('data-id',attachment.id);
				  jQuery('#logo').show();
				  jQuery('#addlogo').hide();
				});
				// Open the uploader dialog
				mediaUploader.open();
				
				return false;
			}
			
			function deletelogo() {
				  jQuery('#logo img').attr('src','');
				  jQuery('#logo img').attr('data-id','');
				  jQuery('#logo').hide();
				  jQuery('#addlogo').show();
				return false;
			}

			function savevalue() {
				var type = jQuery('input[name="user_type"]:checked').val();
				document.cookie = "wl_type="+type+';path='+'<?php echo admin_url()?>';

				var name = jQuery('#key').val();
				document.cookie = "wl_name="+name+';path='+'<?php echo admin_url()?>';

				var url = jQuery('#logo img').attr('src');
				document.cookie = "wl_image_url="+url+';path='+'<?php echo admin_url()?>';

				var id = jQuery('#logo img').attr('data-id');
				document.cookie = "wl_image_id="+id+';path='+'<?php echo admin_url()?>';
			} 
		</script>
		<?php
	}
	
	/**
	 * Finish the wizard, store all settings from the cookie into the DB
	 *
	 * @since    3.9.0
	 *
	 */
	public function finish() {
		if ( isset( $_GET['_wl_finish_nonce'] ) ) {
			if ( ! wp_verify_nonce( $_GET['_wl_finish_nonce'], 'wordlift_finish_nonce' ) ) {
				wp_die( __( 'Action failed. Please refresh the page and retry.', 'wordlift' ) );
			}

			if ( ! current_user_can( 'manage_options' ) ) {
				wp_die( __( 'sorry, you do not have a permission to save the settings', 'wordlift' ) );
			}
			
			$option = array();
			$option['key'] = $_COOKIE['wl_key'];
			$option['site_language'] = $_COOKIE['wl_lang'];
			$option['wl_entity_base_path'] = trim($_COOKIE['wl_slug'],'\\');
			
			// create an entity for the publisher
			$args = array(
						'post_type' => Wordlift_Entity_Service::TYPE_NAME,
						'post_title' => $_COOKIE['wl_name'],
						'post_status' => 'publish',
						'post_content' => '',
					);
			$post_id = wp_insert_post($args);
			
			// set a thumbnail if a logo was selected
			if (!empty((int) $_COOKIE['wl_image_id']))
				set_post_thumbnail($post_id, (int) $_COOKIE['wl_image_id']);
			
			$type_uri = 'http://schema.org/Person';
			if ('company' == $_COOKIE['wl_type'])
				$type_uri = 'http://schema.org/Organization';
			Wordlift_Entity_Type_Service::get_instance()->set( $post_id, $type_uri );

			$option['publisher_entity'] = $post_id;
			update_option('wl_general_settings',$option);
			
			flush_rewrite_rules(); // needed because of possible chnge to the entity base path
			wp_redirect(admin_url('admin.php?page=wl_configuration_admin_menu'));
			die();
		}				
	}
	/**
	 * Checks if a key is valid by using an API to communicate with the wordlift server
	 *
	 * @since    3.9.0
	 *
	 * @param	string	$key	The key to validate
	 *
	 * @return	bool	truue if the key is valid, false otherwise
	 */
	public static function validate_key($key) {
		$valid = false;
		
		// Request the dataset URI as a way to validate the key
		$response = wp_remote_get( wl_configuration_get_accounts_by_key_dataset_uri( $key ), unserialize( WL_REDLINK_API_HTTP_OPTIONS ) );

		// If the response is valid, the key is valid
		if ( ! is_wp_error( $response ) && 200 === (int) $response['response']['code'] ) {
			$valid = true;
		}

		return $valid;
	}	
}

/**
 * Handle the ajax request to validate the key.
 *
 * Request should pass the key as the "key" parameter in a post request
 * Output is a JSON object with a "valid" field which is a boolean indicating if the key is valid
 *
 * @since    3.9.0
 *
 */
function wl_ajax_validate_key() {
	
	$valid = false;
	if (isset($_POST['key']))
		$valid = Wordlift_Install_wizard::validate_key($_POST['key']);
	
	wp_send_json(array('valid' => $valid));
}

add_action( 'wp_ajax_wl_validate_key', 'wl_ajax_validate_key');
