<?php

class WordLift_SettingsValidator
{

    public $message;
    public $consumerKeyOptionName;

    public function validate()
    {

        // if (!ini_get("allow_url_fopen")) {
        //     echo("<div class=\"error\"><p>");
        //     echo("<strong>Error</strong>: WordLift requires the"
        //             . " <em>allow_url_fopen</em>"
        //             . " setting to be set to <em>On</em> in your"
        //             . " <em>php.ini</em> configuration file.");
        //     echo("</p></div>");
        // }

        if (defined("WP_DEBUG") && true === constant("WP_DEBUG")) {
            echo("<div class=\"error\"><p>");
            echo("<strong>Warning</strong>: setting <em>WP_DEBUG</em>"
                    . " to <em>true</em> may interfere with WordLift.");
            echo("</p></div>");
        }

      
        $consumerKey = get_option($this->consumerKeyOptionName);

        if (!empty($consumerKey))
            return "";

echo <<<EOF

    <div class="error">
        $this->message
    </div>

EOF;

        return "";
    }
}

?>