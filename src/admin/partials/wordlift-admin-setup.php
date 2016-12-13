<html>
<head>
    <!-- Defining responsive ambient. -->
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>WordLift Setup</title>

	<?php

	// Enqueue styles and scripts.
	wp_enqueue_style( 'wl-font-awesome', plugin_dir_url( dirname( dirname( __FILE__ ) ) ) . 'css/font-awesome.min.css' );
	wp_enqueue_style( 'wordlift-admin-setup', plugin_dir_url( dirname( __FILE__ ) ) . 'css/wordlift-admin-setup.css', array( 'wl-font-awesome' ) );
	wp_enqueue_script( 'wordlift-admin-setup', plugin_dir_url( dirname( __FILE__ ) ) . 'js/wordlift-admin-setup.js', array( 'jquery' ) );

	// Set configuration settings.
	wp_localize_script( 'wordlift-admin-setup', '_wlAdminSetup', array(
		'ajaxUrl' => parse_url( self_admin_url( 'admin-ajax.php' ), PHP_URL_PATH ),
		'action'  => 'wl_validate_key',
		'media'   => array(
			'title'  => __( 'WordLift Choose Logo', 'wordlift' ),
			'button' => array( 'text' => __( 'Choose Logo', 'wordlift' ) ),
		),
	) );

	// Finally print styles and scripts.
	do_action( 'admin_print_styles' );
	do_action( 'admin_print_scripts' );

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
            <a href="https://wordlift.io/blogger" target="_tab"
               class="button"><?php esc_html_e( 'Learn More', 'wordlift' ); ?></a>
            <input type="button" class="wl-next" value="<?php esc_attr_e( 'Next', 'wordlift' ); ?>">
        </div>
    </script>

    <!-- Pane 2 content -->
    <script type="text/html" id="page-1">
        <h2 class="page-title"><?php esc_html_e( 'License Key', 'wordlift' ); ?></h2>
        <p class="page-txt">
			<?php esc_html_e( 'If you already puchased a plan, check your email, get the activation key from your inbox and insert it in the field below. Otherwise ....', 'wordlift' ); ?>
        </p>
        <input type="text" class="wl-key" id="key" name="key" value="" autocomplete="off" placeholder="Activation Key">
        <div class="btn-wrapper">
            <a href="https://wordlift.io/#plan-and-price" target="_tab"
               type="button"><?php esc_html_e( 'Grab a Key!', 'wordlift' ); ?></a>
            <input type="button" class="wl-next" value="<?php esc_attr_e( 'Next', 'wordlift' ); ?>">
        </div>
    </script>

    <!-- Pane 3 content -->
    <script type="text/html" id="page-2">
        <h2 class="page-title"><?php esc_html_e( 'Vocabulary', 'wordlift' ); ?></h2>
        <p class="page-txt">
			<?php esc_html_e( 'All new pages created with WordLift, will be stored inside your internal vocabulary. You can customize the url pattern of these pages in the field below. Check our FAQs if you need more info.', 'wordlift' ); ?>
        </p>
        <input type="text" id="vocabulary" name="vocabulary" autocomplete="off" value="vocabulary"
               data-wl-vocabulary="wl-vocabulary">
        <p class="page-det">
			<?php esc_html_e( 'Leave it empty to place your entities in the root folder of your website', 'wordlift' ); ?>
        </p>
        <div class="btn-wrapper">
            <input type="button" class="wl-next" value="<?php esc_attr_e( 'Next', 'wordlift' ); ?>">
        </div>
    </script>

    <!-- Pane 4 content -->
    <script type="text/html" id="page-3">
        <h2 class="page-title"><?php esc_html_e( 'Language', 'wordlift' ); ?></h2>
        <p class="page-txt">
			<?php esc_html_e( 'Each WordLift key can be used only in one language. Pick yours.', 'wordlift' ); ?>
        </p>
        <select
                id="language" name="language"
                placeholder="<?php esc_attr_e( 'Choose your language', 'wordlift' ); ?>">
            <option value="English">English</option>
            <option value="Troll">Troll</option>
            <option value="Jedi">Jedi</option>
            <option value="Jubberish">Jubberish</option>
            <option value="Verlant">Verlant</option>
        </select>
        <div class="btn-wrapper">
            <input type="button" class="wl-next" value="<?php esc_attr_e( 'Next', 'wordlift' ); ?>">
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
                <input id="personal" type="radio" name="user_type" value="personal" checked>
                <span class="radio"><span class="check"></span></span>
                <span class="label">
          Personal
        </span>
            </label>
            <label for="company">
                <input id="company" type="radio" name="user_type" value="company">
                <span class="radio"><span class="check"></span></span>
                <span class="label">
          Company
        </span>
            </label>
        </div>
        <input type="text" id="key" name="key" value="" autocomplete="off"
               placeholder="<?php esc_attr_e( "What's your name?", 'wordlift' ); ?>">
        <a class="add-logo" href="javascript:void(0);">
			<?php esc_html_e( 'Add your logo', 'wordlift' ); ?>
        </a>
        <div class="btn-wrapper">
            <input type="button" class="wl-next" value="<?php esc_attr_e( 'Next', 'wordlift' ); ?>">
        </div>
    </script>

</head>
<body>

<div class="container">

    <header>
        <h1><strong>Word</strong>Lift</h1>
        <img src="<?php echo plugin_dir_url( dirname( dirname( __FILE__ ) ) ) . 'images/shapes.png'; ?>"/>
    </header>

    <div class="viewport"></div>

</div>

</body>
</html>