<?php

class WordLift_GoToEntity {

    public $optionName;

    public function redirectToEntity( $e )
    {

        $pageId = get_option($this->optionName);
        $htmlEntityLink = get_page_link($pageId);
        $htmlEntityLink .= (false === strpos($htmlEntityLink, "?") ? "?" : "&");
        $htmlEntityLink .= "e=" . urlencode($e);

        header( "Location: $htmlEntityLink" );
        end();

    }

}

?>