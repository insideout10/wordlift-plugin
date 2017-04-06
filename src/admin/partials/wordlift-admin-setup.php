<?php
/**
 * The `wl-setup` page html code. This page is loaded from the class-wordlift-admin-setup.php file.
 *
 * @since   3.9.0
 * @package WordLift.
 */
?>
<!doctype html>
<html>
<head>
	<!-- Defining responsive ambient. -->
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<title><?php esc_html_e( 'WordLift Setup', 'wordlift' ); ?></title>

	<?php

	// Enqueue wp.media functions.
	wp_enqueue_media();

	// Enqueue styles and scripts.
	wp_enqueue_style( 'wl-font-awesome', plugin_dir_url( dirname( dirname( __FILE__ ) ) ) . 'css/font-awesome.min.css' );
	wp_enqueue_style( 'wordlift-admin-setup', plugin_dir_url( dirname( __FILE__ ) ) . 'css/wordlift-admin-setup.css', array(
		'wp-admin',
		'wl-font-awesome',
	) );
	wp_enqueue_script( 'wordlift-admin-setup', plugin_dir_url( dirname( __FILE__ ) ) . 'js/wordlift-admin-setup.js', array( 'jquery' ) );

	// Set configuration settings.
	wp_localize_script( 'wordlift-admin-setup', '_wlAdminSetup', array(
		'ajaxUrl' => parse_url( self_admin_url( 'admin-ajax.php' ), PHP_URL_PATH ),
		'action'  => 'wl_validate_key',
		'media'   => array(
			'title' => __( 'WordLift Choose Logo', 'wordlift' ),
			'button' => array( 'text' => __( 'Choose Logo', 'wordlift' ) ),
		),
	) );

	// Finally print styles and scripts.
	wp_print_styles();
	wp_print_scripts();
	//	do_action( 'admin_print_styles' );
	//	do_action( 'admin_print_scripts' );

	?>

	<!-- Pane 1 content -->
	<script type="text/html" id="page-0">
		<h2 class="page-title"><?php esc_html_e( 'Welcome', 'wordlift' ); ?></h2>
		<p class="page-txt">
			<?php esc_html_e( 'Thank you for downloading WordLift. Now you can boost your website with a double-digit growth. WordLift helps you with:', 'wordlift' ); ?>
		</p>
		<ul class="page-list">
			<li>
				<span class="fa fa-university"></span>
				<?php esc_html_e( 'Trustworthiness', 'wordlift' ); ?>
			</li>

			<li>
				<span class="fa fa-map-marker"></span>
				<?php esc_html_e( 'Enrichment', 'wordlift' ); ?>
			</li>

			<li>
				<span class="fa fa-heart"></span>
				<?php esc_html_e( 'Engagement', 'wordlift' ); ?>
			</li>

			<li>
				<span class="fa fa-hand-o-right"></span>
				<?php esc_html_e( 'Smart Navigation', 'wordlift' ); ?>
			</li>

			<li>
				<span class="fa fa-google"></span>
				<?php esc_html_e( 'SEO Optimization', 'wordlift' ); ?>
			</li>

			<li>
				<span class="fa fa-group"></span>
				<?php esc_html_e( 'Content Marketing', 'wordlift' ); ?>
			</li>
		</ul>
		<div class="btn-wrapper">
			<a href="https://wordlift.io/blogger/?utm_campaign=wl_activation_learn_more" target="_tab"
			   class="button"><?php esc_html_e( 'Learn More', 'wordlift' ); ?></a>
			<input type="button" data-wl-next="wl-next" class="wl-default-action"
			       value="<?php esc_attr_e( 'Next', 'wordlift' ); ?>">
		</div>
	</script>

	<!-- Pane 2 content -->
	<script type="text/html" id="page-1">
		<h2 class="page-title"><?php esc_html_e( 'License Key', 'wordlift' ); ?></h2>
		<p class="page-txt">
			<?php esc_html_e( 'If you already puchased a plan, check your email, get the activation key from your inbox and insert it in the field below. Otherwise ....', 'wordlift' ); ?>
		</p>
		<input type="text" data-wl-key="wl-key" class="invalid untouched" id="key" name="key" value=""
		       autocomplete="off" placeholder="Activation Key">
		<div class="btn-wrapper">
			<a
				href="https://wordlift.io/?utm_campaign=wl_activation_grab_the_key#plan-and-price" target="_tab"
				class="button"><?php esc_html_e( 'Grab a Key!', 'wordlift' ); ?></a><input
				type="button" data-wl-next="wl-next" class="wl-default-action"
				value="<?php esc_attr_e( 'Next', 'wordlift' ); ?>">
		</div>
	</script>

	<!-- Pane 3 content -->
	<script type="text/html" id="page-2">
		<h2 class="page-title"><?php esc_html_e( 'Vocabulary', 'wordlift' ); ?></h2>
		<p class="page-txt">
			<?php esc_html_e( 'All new pages created with WordLift, will be stored inside your internal vocabulary. You can customize the url pattern of these pages in the field below. Check our FAQs if you need more info.', 'wordlift' ); ?>
		</p>
		<input type="text" id="vocabulary" name="vocabulary" autocomplete="off" value="vocabulary"
		       class="valid untouched" data-wl-vocabulary="wl-vocabulary">
		<p class="page-det">
			<?php esc_html_e( 'Leave it empty to place your entities in the root folder of your website', 'wordlift' ); ?>
		</p>
		<div class="btn-wrapper">
			<input type="button" data-wl-next="wl-next" class="wl-default-action"
			       value="<?php esc_attr_e( 'Next', 'wordlift' ); ?>">
		</div>
	</script>

	<!-- Pane 4 content -->
	<script type="text/html" id="page-3">
		<h2 class="page-title"><?php esc_html_e( 'Language', 'wordlift' ); ?></h2>
		<p class="page-txt">
			<?php esc_html_e( 'Each WordLift key can be used only in one language. Pick yours.', 'wordlift' ); ?>
		</p>
		<select id="language" name="language" placeholder="<?php esc_attr_e( 'Choose your language', 'wordlift' ); ?>">
			<?php

			// Get WordLift's supported languages.
			$languages = Wordlift_Languages::get_languages();

			// Get WP's locale.
			$locale = get_locale();

			// Get the language locale part.
			$parts = explode( '_', $locale );

			// If we support WP's configured language, then use that, otherwise use English by default.
			$language = isset( $languages[ $parts[0] ] ) ? $parts[0] : 'en';

			// Print all the supported language, preselecting the one configured in WP (or English if not supported).
			foreach ( $languages as $code => $label ) { ?>
				<option
					value="<?php echo esc_attr( $code ) ?>" <?php echo selected( $code, $language, false ) ?>><?php echo esc_html( $label ) ?></option>
			<?php } ?>
		</select>

		<div class="btn-wrapper">
			<input type="button" data-wl-next="wl-next" class="wl-default-action"
			       value="<?php esc_attr_e( 'Next', 'wordlift' ); ?>">
		</div>
	</script>

	<!-- Pane 5 content -->
	<script type="text/html" id="page-4">
		<h2 class="page-title"><?php esc_html_e( 'Publisher', 'wordlift' ); ?></h2>
		<p class="page-txt">
			<?php esc_html_e( 'Are you going to publish as an individual or as a company?', 'wordlift' ); ?>
		</p>
		<div class="radio-wrapper">
			<label for="personal">
				<input id="personal" type="radio" name="user_type" value="person" checked>
				<span class="radio"><span class="check"></span></span>
				<span class="label"><?php esc_html_e( 'Personal', 'wordlift' ); ?></span>
			</label>
			<label for="company">
				<input id="company" type="radio" name="user_type" value="organization">
				<span class="radio"><span class="check"></span></span>
				<span class="label"><?php esc_html_e( 'Company', 'wordlift' ); ?></span>
			</label>
		</div>
		<input type="text" id="name" name="name" data-wl-name="wl-name" value="" autocomplete="off"
		       class="untouched invalid"
		       placeholder="<?php esc_attr_e( "What's your name?", 'wordlift' ); ?>">

		<div data-wl-logo="wl-logo">
			<input type="hidden" name="logo"/>
			<div data-wl-logo-preview="wl-logo-preview" class="wl-logo-preview">
				<a data-wl-remove-logo="wl-remove-logo" href="javascript:void(0);" class="fa fa-times"></a>
			</div>
			<a data-wl-add-logo="wl-add-logo" class="add-logo" href="javascript:void(0);">
				<?php esc_html_e( 'Add your logo', 'wordlift' ); ?>
			</a>
		</div>
		<div class="btn-wrapper">
			<input type="submit" id="btn-finish" class="wl-default-action"
			       value="<?php esc_attr_e( 'Finish', 'wordlift' ); ?>">
		</div>
	</script>

</head>
<body>

<div class="wl-container">

	<a href="<?php echo esc_url( admin_url() ); ?> " class="fa fa-times wl-close"></a>

	<header>
		<h1><strong>Word</strong>Lift</h1>
		<img src="<?php echo plugin_dir_url( dirname( dirname( __FILE__ ) ) ) . 'images/shapes.png'; ?>"/>
	</header>


	<form method="post">
		<?php wp_nonce_field( 'wl-save-configuration' ); ?>
		<input type="hidden" name="action" value="wl-save-configuration"/>
		<div class="viewport"></div>
	</form>

</div>

<?php do_action( 'admin_footer' ); ?>

</body>
</html>
