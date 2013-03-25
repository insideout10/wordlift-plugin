<?php

class WordLift_SiteKeyValidator
{
    public $message;
    public $activationService;

    public function validate()
    {
        $siteKey = get_option("wordlift_site_key");

        if (!empty($siteKey))
            return "";

        // try to activate and get a site key.
        $this->activationService->activate();

        $siteKey = get_option("wordlift_site_key");

        if (empty($siteKey)) {
            echo("<div class=\"error\">");
            echo($this->message);
            echo("</div>");
        }

        return "";
    }
}

?>