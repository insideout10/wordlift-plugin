<html>
<head>
	<?php

	// Enqueue styles (do we need wordlift-reloaded here?).
	wp_enqueue_style( 'wordlift-admin-setup', plugin_dir_url( dirname( __FILE__ ) ) . 'css/wordlift-admin-setup.css' );
	wp_enqueue_script( 'wordlift-admin-setup', plugin_dir_url( dirname( __FILE__ ) ) . 'js/wordlift-admin-setup.js', array( 'jquery' ) );

	do_action( 'admin_print_styles' );
	do_action( 'admin_print_scripts' );

	?>
    <!-- Defining responsive ambient. -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Importing font awesome. -->
    <link rel="stylesheet" href="node_modules/font-awesome/css/font-awesome.min.css">
    <!-- Playground css mus be replaced w/ style. -->
    <link rel="stylesheet" href="css/style.css">

    <script src="node_modules/jquery/dist/jquery.min.js"></script>
    <script src="js/playground.js"></script>

    <!-- Pane 1 content -->
    <script type="text/html" id="page-1">
        <h2 class="page-title">Welcome</h2>
        <p class="page-txt">
            Thank you for downloading WordLift.
            Now you can boost your website with a double-digit growth. WordLift helps you with:
        </p>
        <ul class="page-list">
            <li>
                <span class="fa fa-university"></span>
                Trustworthiness
            </li>

            <li>
                <span class="fa fa-map-marker"></span>
                Enrichment
            </li>

            <li>
                <span class="fa fa-heart"></span>
                Engagement
            </li>

            <li>
                <span class="fa fa-hand-o-right"></span>
                Smart Navigation
            </li>

            <li>
                <span class="fa fa-google"></span>
                SEO Optimization
            </li>

            <li>
                <span class="fa fa-group"></span>
                Content Marketing
            </li>
        </ul>
        <div class="btn-wrapper">
            <input type="button" value="Learn More">
            <input type="button" class="wl-next" value="Next">
        </div>
    </script>

    <!-- Pane 2 content -->
    <script type="text/html" id="page-2">
        <h2 class="page-title">License Key</h2>
        <p class="page-txt">
            If you already puchased a plan, check your email, get the activation key from your inbox and insert it in
            the field below. Otherwise ....
        </p>
        <input
                type="text"
                id="key" name="key" value=""
                autocomplete="off" placeholder="Activation Key">
        <div class="btn-wrapper">
            <input type="button" value="Grab a Key!">
            <input type="button" class="wl-next" value="Next">
        </div>
    </script>

    <!-- Pane 3 content -->
    <script type="text/html" id="page-3">
        <h2 class="page-title">Vocabulary</h2>
        <p class="page-txt">
            All new pages created with WordLift, will be stored inside your internal vocabulary. You can customize the
            url pattern of these pages in the field below. Check our FAQs if you need more info.
        </p>
        <input
                type="text"
                class="wl-key"
                id="vocabulary" name="vocabulary"
                autocomplete="off" placeholder="/vocabulary/">
        <p class="page-det">
            Leave it empty to place your entities in the root folder of your website
        </p>
        <div class="btn-wrapper">
            <input type="button" class="wl-next" value="Next">
        </div>
    </script>

    <!-- Pane 4 content -->
    <script type="text/html" id="page-4">
        <h2 class="page-title">Language</h2>
        <p class="page-txt">
            Each WordLift key can be used only in one language. Pick yours.
        </p>
        <select
                id="language" name="language"
                placeholder="Choose your language">
            <option value="English">English</option>
            <option value="Troll">Troll</option>
            <option value="Jedi">Jedi</option>
            <option value="Jubberish">Jubberish</option>
            <option value="Verlant">Verlant</option>
        </select>
        <div class="btn-wrapper">
            <input type="button" class="wl-next" value="Next">
        </div>
    </script>

    <!-- Pane 5 content -->
    <script type="text/html" id="page-5">
        <h2 class="page-title">Publisher</h2>
        <p class="page-txt">
            Are you going to publish as an individual or as a company?
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
        <input
                type="text"
                id="key" name="key" value=""
                autocomplete="off" placeholder="What's your name?">
        <a class="add-logo" href="javascript:void(0);">
            Add your logo
        </a>
        <div class="btn-wrapper">
            <input type="button" class="wl-next" value="Next">
        </div>
    </script>

</head>
<body>

<div class="container">

    <!-- This header do not include bullets. Those are generated via JS -->
    <header>
        <h1><strong>Word</strong>Lift</h1>
        <img
                src="img/shapes.png"
                alt="Tipical WordLift shapes. ¯\_(ツ)_/¯"
                title="Tipical WordLift shapes. ¯\_(ツ)_/¯"/>
    </header>

    <div class="viewport"></div>
</div>

</body>
</html>