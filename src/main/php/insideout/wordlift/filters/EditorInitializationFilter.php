<?php

class WordLift_EditorInitializationFilter {

    public $logger;

    public function editorInitialize( $configuration ) {

        $this->logger->trace( "Initializing the TinyMCE editor configuration." );

        if ( in_array( "extended_valid_elements", $configuration )
            && "" !== $configuration["extended_valid_elements"] )
            $configuration["extended_valid_elements"] .= ",";

        $configuration["extended_valid_elements"] .= "span[about|class|id|typeof]";

        return $configuration;
    }

    public function stylesheets( $stylesheets ) {

        if ( !empty( $stylesheets ) )
            $stylesheets .= ',';

        $stylesheets .= "../wp-content/plugins/wordlift/sass/css/wordlift.disambiguate.css?refresh"; // "http://localhost:4567/stylesheets/wordlift.disambiguate.css"; //

        return $stylesheets;
    }

}

?>