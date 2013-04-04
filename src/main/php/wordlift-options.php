<?php
/** Step 1. */
function woocommerce_us_export_menu() {
    add_submenu_page(
        "woocommerce",
        "US Export Settings",
        "US Export" ,
        "manage_woocommerce",
        "woocommerce_us_export",
        "woocommerce_us_export_options"
    );

    // eg_settings_api_init();
    // add_action('admin_init', 'eg_settings_api_init');
}

/** Step 3. */
function woocommerce_us_export_options() {
    if (!current_user_can("manage_woocommerce"))
    {
        wp_die(
            __("You do not have sufficient permissions to access this page.")
        );
    }

    echo("<div class=\"wrap us-export\">");
    screen_icon("us-export");
    echo("<h2>US Export Settings</h2>");
    
    echo("<form action=\"options.php\" method=\"POST\">");
    settings_fields("woocommerce_us_export");
    do_settings_sections("woocommerce_us_export");
    submit_button();
    echo("</form>");
    echo("Some icons from http://p.yusukekamiyamane.com/");
    echo("</div>");
}

// ------------------------------------------------------------------
// Add all your sections, fields and settings during admin_init
// ------------------------------------------------------------------
//

function woocommerce_us_export_settings_init() {
    $configuration = new Configuration();

    // Add the section to reading settings so we can add our
    // fields to it
    add_settings_section(
        "woocommerce_us_export_setting_section",
        "Ranking and E-mail Settings",
        "woocommerce_us_export_section_callback",
        "woocommerce_us_export"
    );

    // Add the field with the names and function to use for our new
    // settings, put it in our new section
    add_settings_field(
        Configuration::OPTION_RANK,
        "Rank Threshold",
        "woocommerce_us_export_settings_callback",
        "woocommerce_us_export",
        "woocommerce_us_export_setting_section",
        array(
            "text",
            "woocommerce_us_export_rank_threshold",
            $configuration->getDefault(Configuration::OPTION_RANK)
        )
    );

    add_settings_field(
        Configuration::OPTION_TO,
        "E-mail Recipient",
        "woocommerce_us_export_settings_callback",
        "woocommerce_us_export",
        "woocommerce_us_export_setting_section",
        array(
            "text",
            "woocommerce_us_export_to",
            $configuration->getDefault(Configuration::OPTION_RANK)
        )
    );

    add_settings_field(
        Configuration::OPTION_SUBJECT,
        "E-mail Subject",
        "woocommerce_us_export_settings_callback",
        "woocommerce_us_export",
        "woocommerce_us_export_setting_section",
        array(
            "text",
            "woocommerce_us_export_subject",
            $configuration->getDefault(Configuration::OPTION_SUBJECT)
        )
    );

    add_settings_field(
        Configuration::OPTION_BODY,
        "E-mail Body",
        "woocommerce_us_export_settings_callback",
        "woocommerce_us_export",
        "woocommerce_us_export_setting_section",
        array(
            "textarea",
            "woocommerce_us_export_body",
            $configuration->getDefault(Configuration::OPTION_BODY)
        )
    );


// Register our setting so that $_POST handling is done for us and
// our callback function just has to echo the <input>
    register_setting(
        "woocommerce_us_export",
        "woocommerce_us_export_rank_threshold"
    );
    register_setting("woocommerce_us_export", "woocommerce_us_export_to");
    register_setting(
        "woocommerce_us_export",
        "woocommerce_us_export_subject"
    );
    register_setting("woocommerce_us_export", "woocommerce_us_export_body");
}// eg_settings_api_init()

add_action("admin_init", "woocommerce_us_export_settings_init");


// ------------------------------------------------------------------
// Settings section callback function
// ------------------------------------------------------------------
//
// This function is needed if we added a new section. This function 
// will be run at the start of our section
//

function woocommerce_us_export_section_callback() {
    echo("<p>Set the options for the US Export extension below:</p>");
}

// ------------------------------------------------------------------
// Callback function for our example setting
// ------------------------------------------------------------------
//
// creates a checkbox true/false option. Other types are surely possible
//

function woocommerce_us_export_settings_callback($args)
{
    $type = $args[0];
    $name = $args[1];
    $defaultValue = $args[2];
    $value = esc_attr(get_option($name, $defaultValue));

    switch ($type)
    {
        case "radio":
            echo '<input name="eg_setting_name" id="gv_thumbnails_insert_into_excerpt" type="checkbox" value="1" class="code" ' . checked( 1, get_option('eg_setting_name'), false ) . ' /> Explanation text';
            break;
        case "textarea":
            echo("<textarea name=\"$name\">$value</textarea>");
            break;
        case "text":
            echo("<input name=\"$name\" type=\"text\" value=\"$value\" />");
            break;
    }  
}
?>